<?php
require_once 'includes/conexion.php';
require_once 'includes/auth.php';

// Solo admin puede acceder
requiereAdmin();

// Obtener estadísticas
$totalEstudiantes = $conexion->query("SELECT COUNT(*) as total FROM estudiantes WHERE activo = 1")->fetch_assoc()['total'];
$totalProfesores = $conexion->query("SELECT COUNT(*) as total FROM profesores WHERE activo = 1")->fetch_assoc()['total'];
$totalCursos = $conexion->query("SELECT COUNT(*) as total FROM cursos WHERE activo = 1")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Centro Educativo</title>
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
                <li><a href="index.php" class="active">📊 Resumen</a></li>
                <li><a href="estudiantes.php">👨‍🎓 Estudiantes</a></li>
                <li><a href="profesores.php">👨‍🏫 Profesores</a></li>
                <li><a href="cursos.php">📚 Cursos</a></li>
                <li><a href="logout.php" style="color: #fca5a5;">🚪 Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">

    <div class="container animate-fade">
        <div class="top-header" style="margin: 2rem 0 3rem 0; text-align: center;">
            <h2 style="font-size: 3rem; margin-bottom: 0.5rem; letter-spacing: -0.02em; background: linear-gradient(90deg, #fff, var(--c-primary), var(--c-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Panel de Administración</h2>
            <p style="color: var(--text-muted); font-size: 1.15rem;">Bienvenido, <?php echo htmlspecialchars(getNombreUsuario()); ?>. Aquí tienes un resumen del centro.</p>
        </div>

        <div class="bento-grid">
            <!-- Large Stats Card -->
            <div class="glass-card bento-item-large" style="padding: 3rem; border-left: 5px solid var(--c-primary); display: flex; flex-direction: column; justify-content: center;">
                <span class="stat-label">Estudiantes</span>
                <div class="stat-value"><?php echo $totalEstudiantes; ?></div>
                <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 1.1rem;">Estudiantes matriculados actualmente.</p>
                <div style="display: flex; gap: 1rem;">
                    <a href="estudiantes.php" class="btn btn-primary" style="background: var(--c-primary); border: none; padding: 1rem 2rem; font-weight: 700; border-radius: 12px;">VER TODOS</a>
                    <a href="estudiantes.php?accion=crear" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); color: #fff; padding: 1rem 1.5rem; border-radius: 12px;">➕ Nuevo</a>
                </div>
            </div>

            <!-- Tall Stats Card: Professors -->
            <div class="glass-card bento-item-tall" style="padding: 2rem; display: flex; flex-direction: column; border-bottom: 4px solid var(--c-secondary); background: linear-gradient(180deg, transparent, rgba(6, 182, 212, 0.08));">
                <span class="stat-label">Profesores</span>
                <div class="stat-value" style="font-size: 3.5rem;"><?php echo $totalProfesores; ?></div>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem;">Docentes activos en el centro.</p>
                <div style="margin-top: auto;">
                    <a href="profesores.php" style="color: var(--c-secondary); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
                        Ver profesores <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Tall Stats Card: Courses -->
            <div class="glass-card bento-item-tall" style="padding: 2rem; display: flex; flex-direction: column; border-bottom: 4px solid var(--c-accent); background: linear-gradient(180deg, transparent, rgba(244, 114, 182, 0.08));">
                <span class="stat-label">Cursos</span>
                <div class="stat-value" style="font-size: 3.5rem;"><?php echo $totalCursos; ?></div>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.5rem;">Cursos disponibles actualmente.</p>
                <div style="margin-top: auto;">
                    <a href="cursos.php" style="color: var(--c-accent); text-decoration: none; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
                        Ver cursos <span>→</span>
                    </a>
                </div>
            </div>
        </div>


        <footer class="glass-card" style="margin-top: 3rem; padding: 2rem;">
            <p>&copy; <?php echo date('Y'); ?> Centro Educativo Premium | Desarrollado con Excelencia Técnica</p>
        </footer>
    </div>

    <script src="js/scripts.js"></script>
</body>
</html>
<?php cerrarConexion(); ?>
