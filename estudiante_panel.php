<?php
require_once 'includes/conexion.php';
require_once 'includes/auth.php';

// Solo estudiantes pueden acceder
if (!esEstudiante()) {
    header('Location: index.php');
    exit();
}

$id_curso = $_SESSION['estudiante_id_curso'] ?? null;
$nombre_estudiante = getNombreUsuario();

// Obtener información del curso si tiene uno asignado
$curso_info = null;
if ($id_curso) {
    $stmt = $conexion->prepare("SELECT c.*, p.nombre as prof_nombre, p.apellidos as prof_ape 
                               FROM cursos c 
                               LEFT JOIN profesores p ON c.id_profesor = p.id_profesor 
                               WHERE c.id_curso = ?");
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $curso_info = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel - Centro Educativo</title>
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
                <li><a href="estudiante_panel.php" class="active">🏠 Mi Panel</a></li>
                <li><a href="profesores.php">👨‍🏫 Profesores</a></li>
                <li><a href="cursos.php">📚 Cursos</a></li>
                <li><a href="logout.php" style="color: #fca5a5;">🚪 Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container animate-fade">
        <div class="top-header" style="margin: 2rem 0 3rem 0; text-align: center;">
            <h2 style="font-size: 3rem; background: linear-gradient(90deg, #fff, var(--c-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">¡Hola, <?php echo htmlspecialchars($nombre_estudiante); ?>!</h2>
            <p style="color: var(--text-muted); font-size: 1.15rem;">Bienvenido a tu área personal del centro.</p>
        </div>

        <div class="bento-grid">
            <?php if ($curso_info): ?>
                <!-- Card de mi curso -->
                <div class="glass-card bento-item-large" style="padding: 3rem; border-left: 5px solid var(--c-secondary);">
                    <span class="stat-label">Mi Curso Actual</span>
                    <div class="stat-value" style="font-size: 2.5rem; margin: 1rem 0;"><?php echo htmlspecialchars($curso_info['nombre']); ?></div>
                    <p style="color: var(--text-muted); margin-bottom: 2rem;"><?php echo htmlspecialchars($curso_info['descripcion']); ?></p>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <span class="badge" style="background: var(--c-secondary);"><?php echo $curso_info['horas']; ?> Horas</span>
                        <span style="color: var(--text-muted);">Tutor: <?php echo htmlspecialchars($curso_info['prof_nombre'] . " " . $curso_info['prof_ape']); ?></span>
                    </div>
                </div>

                <!-- Card de compañeros -->
                <div class="glass-card bento-item-tall" style="padding: 2rem; display: flex; flex-direction: column; background: linear-gradient(180deg, transparent, rgba(6, 182, 212, 0.08));">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                    <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Mi Clase</h3>
                    <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem;">Consulta quién más está en tu grupo.</p>
                    <div style="margin-top: auto;">
                        <a href="estudiante_companeros.php" class="btn btn-primary" style="background: var(--c-secondary); width: 100%; text-align: center;">VER COMPAÑEROS</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="glass-card bento-item-large" style="padding: 3rem; text-align: center;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">⏳</div>
                    <h3>Pendiente de Asignación</h3>
                    <p style="color: var(--text-muted);">Tu cuenta de estudiante ha sido creada, pero aún no has sido asignado a ningún curso. Por favor, contacta con administración.</p>
                </div>
            <?php endif; ?>

            <!-- Card de Profesores Global -->
            <div class="glass-card bento-item-tall" style="padding: 2rem; display: flex; flex-direction: column; border-bottom: 4px solid var(--c-primary);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">👨‍🏫</div>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Profesorado</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem;">Conoce a todos los docentes del centro educativo.</p>
                <div style="margin-top: auto;">
                    <a href="profesores.php" style="color: var(--c-primary); text-decoration: none; font-weight: 700;">Explorar profesorado →</a>
                </div>
            </div>
        </div>

        <footer class="glass-card" style="margin-top: 3rem; padding: 2rem;">
            <p>&copy; <?php echo date('Y'); ?> Centro Educativo | Portal del Estudiante</p>
        </footer>
    </div>

    <script src="js/scripts.js"></script>
</body>
</html>
<?php cerrarConexion(); ?>
