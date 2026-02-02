<?php
/**
 * ============================================================
 * CENTRO EDUCATIVO - GESTIÓN DE ESTUDIANTES
 * Proyecto IAW - CFGS ASIR
 * ============================================================
 * 
 * Este archivo gestiona el CRUD completo de estudiantes:
 * - Listar todos los estudiantes
 * - Crear nuevo estudiante
 * - Editar estudiante existente
 * - Eliminar estudiante (soft delete)
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
// PROCESAR FORMULARIO: CREAR ESTUDIANTE
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    // Obtener y limpiar datos del formulario
    $nombre = limpiarDatos($_POST['nombre']);
    $apellidos = limpiarDatos($_POST['apellidos']);
    $email = limpiarDatos($_POST['email']);
    $telefono = limpiarDatos($_POST['telefono']);
    $fechaNacimiento = !empty($_POST['fecha_nacimiento']) ? limpiarDatos($_POST['fecha_nacimiento']) : null;
    $direccion = limpiarDatos($_POST['direccion']);
    $idCurso = !empty($_POST['id_curso']) ? intval($_POST['id_curso']) : null;
    $fechaMatricula = limpiarDatos($_POST['fecha_matricula']);

    // Preparar consulta SQL con parámetros
    $sql = "INSERT INTO estudiantes (nombre, apellidos, email, telefono, fecha_nacimiento, direccion, id_curso, fecha_matricula) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssss", $nombre, $apellidos, $email, $telefono, $fechaNacimiento, $direccion, $idCurso, $fechaMatricula);
    
    // Ejecutar consulta
    if ($stmt->execute()) {
        $mensaje = "Estudiante creado exitosamente";
        $tipoMensaje = "success";
        $accion = 'listar';
    } else {
        $mensaje = "Error al crear estudiante: " . $conexion->error;
        $tipoMensaje = "error";
    }
    $stmt->close();
}

// ============================================================
// PROCESAR FORMULARIO: EDITAR ESTUDIANTE
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $idEstudiante = intval($_POST['id_estudiante']);
    $nombre = limpiarDatos($_POST['nombre']);
    $apellidos = limpiarDatos($_POST['apellidos']);
    $email = limpiarDatos($_POST['email']);
    $telefono = limpiarDatos($_POST['telefono']);
    $fechaNacimiento = !empty($_POST['fecha_nacimiento']) ? limpiarDatos($_POST['fecha_nacimiento']) : null;
    $direccion = limpiarDatos($_POST['direccion']);
    $idCurso = !empty($_POST['id_curso']) ? intval($_POST['id_curso']) : null;
    $fechaMatricula = limpiarDatos($_POST['fecha_matricula']);

    $sql = "UPDATE estudiantes SET 
            nombre = ?, apellidos = ?, email = ?, telefono = ?, 
            fecha_nacimiento = ?, direccion = ?, id_curso = ?, fecha_matricula = ? 
            WHERE id_estudiante = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssssi", $nombre, $apellidos, $email, $telefono, $fechaNacimiento, $direccion, $idCurso, $fechaMatricula, $idEstudiante);
    
    if ($stmt->execute()) {
        $mensaje = "Estudiante actualizado exitosamente";
        $tipoMensaje = "success";
        $accion = 'listar';
    } else {
        $mensaje = "Error al actualizar estudiante: " . $conexion->error;
        $tipoMensaje = "error";
    }
    $stmt->close();
}

// ============================================================
// PROCESAR ACCIÓN: ELIMINAR ESTUDIANTE (Soft Delete)
// ============================================================
if (isset($_GET['accion']) && $_GET['accion'] === 'eliminar' && isset($_GET['id'])) {
    $idEstudiante = intval($_GET['id']);
    
    // Soft delete: marcar como inactivo en lugar de eliminar
    $sql = "UPDATE estudiantes SET activo = 0 WHERE id_estudiante = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idEstudiante);
    
    if ($stmt->execute()) {
        $mensaje = "Estudiante eliminado exitosamente";
        $tipoMensaje = "success";
    } else {
        $mensaje = "Error al eliminar estudiante: " . $conexion->error;
        $tipoMensaje = "error";
    }
    $stmt->close();
    $accion = 'listar';
}

// ============================================================
// OBTENER DATOS PARA EDITAR
// ============================================================
$estudianteEditar = null;
if ($accion === 'editar' && isset($_GET['id'])) {
    $idEstudiante = intval($_GET['id']);
    $sql = "SELECT * FROM estudiantes WHERE id_estudiante = ? AND activo = 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $idEstudiante);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $estudianteEditar = $resultado->fetch_assoc();
    $stmt->close();
    
    if (!$estudianteEditar) {
        $mensaje = "Estudiante no encontrado";
        $tipoMensaje = "error";
        $accion = 'listar';
    }
}

// ============================================================
// OBTENER LISTA DE CURSOS PARA SELECT
// ============================================================
$cursos = [];
$sqlCursos = "SELECT id_curso, nombre FROM cursos WHERE activo = 1 ORDER BY nombre";
$resultadoCursos = $conexion->query($sqlCursos);
if ($resultadoCursos) {
    while ($fila = $resultadoCursos->fetch_assoc()) {
        $cursos[] = $fila;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiantes - Centro Educativo</title>
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
                <li><a href="estudiantes.php" class="active">👨‍🎓 Estudiantes</a></li>
                <li><a href="profesores.php">👨‍🏫 Profesores</a></li>
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
                    <h2 class="glow-text" style="font-size: 2.2rem; margin-bottom: 0;"><span>👨‍🎓</span> Registro de Estudiantes</h2>
                    <a href="?accion=crear" class="btn btn-primary" style="background: var(--c-primary); border: none; font-weight: 700;">➕ NUEVO REGISTRO</a>
                </div>
                
                <div style="margin-bottom: 2.5rem;">
                    <input type="text" id="busqueda" placeholder="🔍 Filtrar por nombre, contacto o curso..." 
                           onkeyup="filtrarTabla('busqueda', 'tablaEstudiantes')"
                           style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); padding: 1.25rem 2rem; border-radius: 18px;">
                </div>

                <div class="table-container" style="border: 1px solid var(--glass-border); background: var(--glass-bg); backdrop-filter: blur(10px);">
                    <table id="tablaEstudiantes">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estudiante</th>
                                <th>Email</th>
                                <th>Curso</th>
                                <th>Matrícula</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT e.*, c.nombre as nombre_curso 
                                    FROM estudiantes e 
                                    LEFT JOIN cursos c ON e.id_curso = c.id_curso 
                                    WHERE e.activo = 1 
                                    ORDER BY e.apellidos, e.nombre";
                            $resultado = $conexion->query($sql);
                            
                            if ($resultado->num_rows > 0) {
                                while ($fila = $resultado->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td><span class='badge' style='background: rgba(255,255,255,0.05); color: var(--text-muted);'>" . $fila['id_estudiante'] . "</span></td>";
                                    echo "<td><strong>" . htmlspecialchars($fila['nombre'] . " " . $fila['apellidos']) . "</strong><br><small style='color: var(--text-muted);'>" . ($fila['telefono'] ? htmlspecialchars($fila['telefono']) : '-') . "</small></td>";
                                    echo "<td>" . htmlspecialchars($fila['email']) . "</td>";
                                    echo "<td><span class='badge'>" . ($fila['nombre_curso'] ? htmlspecialchars($fila['nombre_curso']) : 'Libre') . "</span></td>";
                                    echo "<td>" . date('d/m/Y', strtotime($fila['fecha_matricula'])) . "</td>";
                                    echo "<td class='acciones'>";
                                    echo "<a href='?accion=editar&id=" . $fila['id_estudiante'] . "' class='btn btn-warning btn-sm' title='Editar'><svg width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'></path><path d='M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z'></path></svg></a>";
                                    echo "<a href='?accion=eliminar&id=" . $fila['id_estudiante'] . "' class='btn btn-danger btn-sm' onclick=\"return confirmarEliminacion('estudiante', '" . htmlspecialchars($fila['nombre'] . " " . $fila['apellidos']) . "')\" title='Eliminar'><svg width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='3 6 5 6 21 6'></polyline><path d='M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2'></path><line x1='10' y1='11' x2='10' y2='17'></line><line x1='14' y1='11' x2='14' y2='17'></line></svg></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' style='text-align: center; padding: 3rem; color: var(--text-muted);'>No hay estudiantes registrados</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($accion === 'crear' || ($accion === 'editar' && $estudianteEditar)): ?>
                <h2 class="section-title"><span><?php echo $accion === 'crear' ? '➕' : '✏️'; ?></span> <?php echo $accion === 'crear' ? 'Nuevo' : 'Editar'; ?> Estudiante</h2>

                <form method="POST" onsubmit="return validarFormularioEstudiante()">
                    <?php if ($accion === 'editar'): ?>
                        <input type="hidden" name="id_estudiante" value="<?php echo $estudianteEditar['id_estudiante']; ?>">
                    <?php endif; ?>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div class="form-group">
                            <label>Nombre <span class="required">*</span></label>
                            <input type="text" id="nombre" name="nombre" required 
                                   value="<?php echo $accion === 'editar' ? htmlspecialchars($estudianteEditar['nombre']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Apellidos <span class="required">*</span></label>
                            <input type="text" id="apellidos" name="apellidos" required
                                   value="<?php echo $accion === 'editar' ? htmlspecialchars($estudianteEditar['apellidos']) : ''; ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                        <div class="form-group">
                            <label>Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" required
                                   value="<?php echo $accion === 'editar' ? htmlspecialchars($estudianteEditar['email']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="tel" id="telefono" name="telefono"
                                   placeholder="600 000 000"
                                   value="<?php echo $accion === 'editar' ? htmlspecialchars($estudianteEditar['telefono'] ?? '') : ''; ?>">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div class="form-group">
                            <label>Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                                   value="<?php echo $accion === 'editar' ? ($estudianteEditar['fecha_nacimiento'] ?? '') : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Fecha de Matrícula <span class="required">*</span></label>
                            <input type="date" id="fecha_matricula" name="fecha_matricula" 
                                   value="<?php echo $accion === 'editar' ? $estudianteEditar['fecha_matricula'] : date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" id="direccion" name="direccion"
                               placeholder="Calle Principal 123, Ciudad"
                               value="<?php echo $accion === 'editar' ? htmlspecialchars($estudianteEditar['direccion'] ?? '') : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Curso Académico</label>
                        <select id="id_curso" name="id_curso">
                            <option value="">-- Sin asignar / Estudio Libre --</option>
                            <?php foreach ($cursos as $curso): ?>
                                <option value="<?php echo $curso['id_curso']; ?>"
                                    <?php echo ($accion === 'editar' && $estudianteEditar['id_curso'] == $curso['id_curso']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($curso['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="margin-top: 3rem; display: flex; gap: 1rem;">
                        <button type="submit" name="<?php echo $accion; ?>" class="btn btn-primary">
                            <span>💾</span> <?php echo $accion === 'crear' ? 'Crear Estudiante' : 'Guardar Cambios'; ?>
                        </button>
                        <a href="estudiantes.php" class="btn btn-danger"><span>❌</span> Cancelar</a>
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
