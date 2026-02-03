<?php
require_once 'includes/conexion.php';
require_once 'includes/auth.php';

// Solo estudiantes pueden acceder
if (!esEstudiante()) {
    header('Location: index.php');
    exit();
}

$id_curso = $_SESSION['estudiante_id_curso'] ?? null;

if (!$id_curso) {
    header('Location: estudiante_panel.php');
    exit();
}

// Obtener nombre del curso
$res_curso = $conexion->query("SELECT nombre FROM cursos WHERE id_curso = $id_curso");
$nombre_curso = $res_curso->fetch_assoc()['nombre'];

// Obtener compañeros
$sql = "SELECT nombre, apellidos FROM estudiantes WHERE id_curso = ? AND activo = 1 ORDER BY apellidos ASC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$resultado = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Compañeros - Centro Educativo</title>
    <link rel="stylesheet" href="css/estilos.css?v=5">
</head>
<body class="animate-fade">

    <header class="main-header">
        <div class="brand">
            <div style="font-size: 2rem;">🏫</div>
            <h1>Centro Educativo</h1>
        </div>
        <nav>
            <ul>
                <li><a href="estudiante_panel.php">🏠 Mi Panel</a></li>
                <li><a href="estudiante_companeros.php" class="active">👥 Compañeros</a></li>
                <li><a href="profesores.php">👨‍🏫 Profesores</a></li>
                <li><a href="logout.php" style="color: #fca5a5;">🚪 Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container animate-fade">
        <div class="top-header" style="margin: 2rem 0; text-align: left; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Mis Compañeros</h2>
                <p style="color: var(--text-muted);"><?php echo htmlspecialchars($nombre_curso); ?></p>
            </div>
            <a href="estudiante_panel.php" class="btn btn-secondary">← Volver</a>
        </div>

        <div class="glass-card" style="padding: 2rem;">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows > 0): ?>
                            <?php while ($fila = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td style="font-weight: 600;"><?php echo htmlspecialchars($fila['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($fila['apellidos']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" style="text-align: center; padding: 3rem; color: var(--text-muted);">No se encontraron compañeros en este grupo.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="glass-card" style="margin-top: 3rem; padding: 2rem;">
            <p>&copy; <?php echo date('Y'); ?> Centro Educativo | Compañeros de Clase</p>
        </footer>
    </div>

</body>
</html>
<?php cerrarConexion(); ?>
