<?php
// Incluir archivos necesarios
require_once 'includes/conexion.php';
require_once 'includes/auth.php';

// Solo admin puede acceder
requiereAdmin();


$accion = isset($_GET['accion']) ? $_GET['accion'] : 'listar';
$mensaje = '';
$tipoMensaje = '';

// CREAR PROFESOR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $nombre = limpiarDatos($_POST['nombre']);
    $apellidos = limpiarDatos($_POST['apellidos']);
    $email = limpiarDatos($_POST['email']);
    $telefono = limpiarDatos($_POST['telefono']);
    $especialidad = limpiarDatos($_POST['especialidad']);
    $fecha_alta = limpiarDatos($_POST['fecha_alta']);

    $sql = "INSERT INTO profesores (nombre, apellidos, email, telefono, especialidad, fecha_alta) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssss", $nombre, $apellidos, $email, $telefono, $especialidad, $fecha_alta);
    
    if ($stmt->execute()) {
        $mensaje = "Profesor creado exitosamente";
        $tipoMensaje = "success";
        $accion = 'listar';
    } else {
        $mensaje = "Error al crear profesor: " . $conexion->error;
        $tipoMensaje = "error";
    }
}

// LISTAR PROFESORES (se maneja más abajo en el HTML)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profesores - Centro Educativo</title>
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
                <li><a href="profesores.php" class="active">👨‍🏫 Profesores</a></li>
                <li><a href="cursos.php">📚 Cursos</a></li>
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
                    <h2 class="glow-text" style="font-size: 2.2rem; margin-bottom: 0;"><span>👨‍🏫</span> Directorio del Claustro</h2>
                    <a href="?accion=crear" class="btn btn-primary" style="background: var(--c-secondary); border: none; font-weight: 700;">➕ ALTA DOCENTE</a>
                </div>
                
                <div style="margin-bottom: 2.5rem;">
                    <input type="text" id="busqueda" placeholder="🔍 Buscar docente o especialidad..." 
                           onkeyup="filtrarTabla('busqueda', 'tablaProfesores')"
                           style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 1.25rem 2rem; border-radius: 18px;">
                </div>

                <div class="table-container" style="border: 1px solid var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(10px);">
                    <table id="tablaProfesores">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Profesor</th>
                                <th>Email</th>
                                <th>Especialidad</th>
                                <th>Cursos</th>
                                <th>Fecha Alta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT p.*, COUNT(c.id_curso) as num_cursos 
                                    FROM profesores p 
                                    LEFT JOIN cursos c ON p.id_profesor = c.id_profesor AND c.activo = 1
                                    WHERE p.activo = 1 
                                    GROUP BY p.id_profesor
                                    ORDER BY p.apellidos, p.nombre";
                            $resultado = $conexion->query($sql);
                            
                            if ($resultado && $resultado->num_rows > 0) {
                                while ($fila = $resultado->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td><span class='badge' style='background: rgba(255,255,255,0.05); color: var(--text-muted);'>" . $fila['id_profesor'] . "</span></td>";
                                    echo "<td><strong>" . htmlspecialchars($fila['nombre'] . " " . $fila['apellidos']) . "</strong><br><small style='color: var(--text-muted);'>" . ($fila['telefono'] ? htmlspecialchars($fila['telefono']) : '-') . "</small></td>";
                                    echo "<td>" . htmlspecialchars($fila['email']) . "</td>";
                                    echo "<td><span class='badge' style='background: rgba(6, 182, 212, 0.1); color: var(--c-secondary); border: 1px solid rgba(6, 182, 212, 0.2);'>" . htmlspecialchars($fila['especialidad']) . "</span></td>";
                                    echo "<td><span class='badge' style='background: var(--c-primary); box-shadow: 0 0 10px rgba(139, 92, 246, 0.2);'>" . $fila['num_cursos'] . "</span></td>";
                                    echo "<td>" . date('d/m/Y', strtotime($fila['fecha_alta'])) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align: center; padding: 3rem; color: var(--text-muted);'>No hay profesores registrados</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($accion === 'crear'): ?>
                <h2 class="section-title"><span>➕</span> Alta de Nuevo Profesor</h2>

                <form method="POST" onsubmit="return validarFormularioProfesor()">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div class="form-group">
                            <label>Nombre <span class="required">*</span></label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label>Apellidos <span class="required">*</span></label>
                            <input type="text" id="apellidos" name="apellidos" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required placeholder="elena.garcia@centro.edu">
                        </div>

                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" placeholder="600 111 222">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div class="form-group">
                            <label>Especialidad <span class="required">*</span></label>
                            <input type="text" id="especialidad" name="especialidad" required>
                        </div>

                        <div class="form-group">
                            <label>Fecha de Alta <span class="required">*</span></label>
                            <input type="date" id="fecha_alta" name="fecha_alta" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div style="margin-top: 3rem; display: flex; gap: 1rem;">
                        <button type="submit" name="crear" class="btn btn-primary">
                            <span>💾</span> Registrar Profesor
                        </button>
                        <a href="profesores.php" class="btn btn-danger"><span>❌</span> Cancelar</a>
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
<?php cerrarConexion(); ?>
