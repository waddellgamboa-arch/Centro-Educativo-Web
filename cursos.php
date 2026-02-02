<?php
/**
 * ============================================================
 * CENTRO EDUCATIVO - GESTIÓN DE CURSOS
 * Proyecto IAW - CFGS ASIR
 * ============================================================
 * 
 * Este archivo gestiona los cursos:
 * - Listar todos los cursos
 * - Crear nuevo curso
 */

// Incluir archivos necesarios
require_once 'includes/conexion.php';
require_once 'includes/auth.php';

// Solo admin puede acceder
requiereAdmin();


// ============================================================
// OBTENER ACCIÓN A REALIZAR
// ============================================================
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'listar';
$mensaje = '';
$tipoMensaje = '';

// ============================================================
// PROCESAR FORMULARIO: CREAR CURSO
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    // Obtener y limpiar datos del formulario
    $nombre = limpiarDatos($_POST['nombre']);
    $descripcion = limpiarDatos($_POST['descripcion']);
    $horas = intval($_POST['horas']);
    $idProfesor = !empty($_POST['id_profesor']) ? intval($_POST['id_profesor']) : null;
    $fechaInicio = limpiarDatos($_POST['fecha_inicio']);
    $fechaFin = !empty($_POST['fecha_fin']) ? limpiarDatos($_POST['fecha_fin']) : null;

    // Preparar consulta SQL con parámetros
    $sql = "INSERT INTO cursos (nombre, descripcion, horas, id_profesor, fecha_inicio, fecha_fin) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssiiss", $nombre, $descripcion, $horas, $idProfesor, $fechaInicio, $fechaFin);
    
    // Ejecutar consulta
    if ($stmt->execute()) {
        $mensaje = "Curso creado exitosamente";
        $tipoMensaje = "success";
        $accion = 'listar';
    } else {
        $mensaje = "Error al crear curso: " . $conexion->error;
        $tipoMensaje = "error";
    }
    $stmt->close();
}

// ============================================================
// OBTENER LISTA DE PROFESORES PARA SELECT
// ============================================================
$profesores = [];
$sqlProfesores = "SELECT id_profesor, nombre, apellidos, especialidad FROM profesores WHERE activo = 1 ORDER BY apellidos, nombre";
$resultadoProfesores = $conexion->query($sqlProfesores);
if ($resultadoProfesores) {
    while ($fila = $resultadoProfesores->fetch_assoc()) {
        $profesores[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos - Centro Educativo</title>
    <link rel="stylesheet" href="css/estilos.css?v=5?v=5">
</head>
<body class="animate-fade">

    <header class="main-header">
        <div class="brand">
            <div style="font-size: 2rem;">🏫</div>
            <h1>Centro Educativo</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">📊 Resumen</a></li>
                <li><a href="estudiantes.php">👨‍🎓 Estudiantes</a></li>
                <li><a href="profesores.php">👨‍🏫 Profesores</a></li>
                <li><a href="cursos.php" class="active">📚 Cursos</a></li>
                <li><a href="logout.php" style="color: #fca5a5;">🚪 Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">

        <div class="content glass-card">
            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipoMensaje; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <?php if ($accion === 'listar'): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
                    <h2 class="glow-text" style="font-size: 2.2rem; margin-bottom: 0;"><span>📚</span> Catálogo Académico</h2>
                    <a href="?accion=crear" class="btn btn-primary" style="background: var(--c-accent); border: none; font-weight: 700;">➕ NUEVO CURSO</a>
                </div>
                
                <div style="margin-bottom: 2.5rem;">
                    <input type="text" id="busqueda" placeholder="🔍 Buscar curso o docente responsable..." 
                           onkeyup="filtrarTabla('busqueda', 'tablaCursos')"
                           style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 1.25rem 2rem; border-radius: 18px;">
                </div>

                <div class="table-container" style="border: 1px solid var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(10px);">
                    <table id="tablaCursos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Curso</th>
                                <th>Carga</th>
                                <th>Docente</th>
                                <th>Periodo</th>
                                <th style="text-align: center;">Alumnos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT c.*, 
                                    CONCAT(p.nombre, ' ', p.apellidos) as nombre_profesor,
                                    COUNT(e.id_estudiante) as num_estudiantes
                                    FROM cursos c 
                                    LEFT JOIN profesores p ON c.id_profesor = p.id_profesor 
                                    LEFT JOIN estudiantes e ON c.id_curso = e.id_curso AND e.activo = 1
                                    WHERE c.activo = 1 
                                    GROUP BY c.id_curso
                                    ORDER BY c.nombre";
                            $resultado = $conexion->query($sql);
                            
                            if ($resultado->num_rows > 0) {
                                while ($fila = $resultado->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td><span class='badge' style='background: rgba(255,255,255,0.05); color: var(--text-muted);'>" . $fila['id_curso'] . "</span></td>";
                                    echo "<td><strong>" . htmlspecialchars($fila['nombre']) . "</strong>";
                                    if ($fila['descripcion']) {
                                        echo "<br><small style='color: var(--text-muted);'>" . htmlspecialchars(substr($fila['descripcion'], 0, 40)) . (strlen($fila['descripcion']) > 40 ? '...' : '') . "</small>";
                                    }
                                    echo "</td>";
                                    echo "<td><span class='badge' style='border: 1px solid var(--border-glass);'>" . $fila['horas'] . "h</span></td>";
                                    echo "<td>" . ($fila['nombre_profesor'] ? htmlspecialchars($fila['nombre_profesor']) : '<em style="color: var(--text-muted);">Sin asignar</em>') . "</td>";
                                    echo "<td>" . date('d/m/y', strtotime($fila['fecha_inicio'])) . " - " . ($fila['fecha_fin'] ? date('d/m/y', strtotime($fila['fecha_fin'])) : 'N/A') . "</td>";
                                    echo "<td style='text-align: center;'><span class='badge' style='background: var(--primary); font-weight: 700; width: 35px; display: inline-block;'>" . $fila['num_estudiantes'] . "</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align: center; padding: 3rem; color: var(--text-muted);'>No hay cursos registrados</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($accion === 'crear'): ?>
                <h2 class="section-title"><span>➕</span> Configurar Nuevo Curso</h2>

                <form method="POST" onsubmit="return validarFormularioCurso()">
                    <div class="form-group">
                        <label>Nombre del Curso <span class="required">*</span></label>
                        <input type="text" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea id="descripcion" name="descripcion" rows="3" placeholder="Detalles u objetivos del curso..."></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
                        <div class="form-group">
                            <label>Horas Totales <span class="required">*</span></label>
                            <input type="number" id="horas" name="horas" required min="1" value="1000">
                        </div>

                        <div class="form-group">
                            <label>Profesor Responsable</label>
                            <select id="id_profesor" name="id_profesor">
                                <option value="">-- Asignar más tarde --</option>
                                <?php foreach ($profesores as $profesor): ?>
                                    <option value="<?php echo $profesor['id_profesor']; ?>">
                                        <?php echo htmlspecialchars($profesor['nombre'] . ' ' . $profesor['apellidos'] . ' (' . $profesor['especialidad'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div class="form-group">
                            <label>Fecha de Inicio <span class="required">*</span></label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Fecha de Fin Prevista</label>
                            <input type="date" id="fecha_fin" name="fecha_fin">
                        </div>
                    </div>

                    <div style="margin-top: 3rem; display: flex; gap: 1rem;">
                        <button type="submit" name="crear" class="btn btn-primary">
                            <span>💾</span> Registrar Curso
                        </button>
                        <a href="cursos.php" class="btn btn-danger"><span>❌</span> Cancelar</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <footer class="glass-card" style="margin-top: 3rem; padding: 2rem;">
            <p>&copy; <?php echo date('Y'); ?> Centro Educativo Premium | Excelencia en Gestión</p>
        </footer>
    </div>

    <script src="js/scripts.js"></script>
</body>
</html>
<?php 
// Cerrar la conexión a la base de datos
cerrarConexion(); 
?>
