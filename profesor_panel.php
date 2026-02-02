<?php
/**
 * ============================================================
 * CENTRO EDUCATIVO - PANEL DEL PROFESOR
 * Proyecto IAW - CFGS ASIR
 * ============================================================
 * 
 * Vista exclusiva para profesores (rol limitado)
 * Solo pueden ver informacion, no modificar
 */

require_once 'includes/conexion.php';
require_once 'includes/auth.php';

// Requiere autenticacion
requiereLogin();

// Si es admin, puede acceder a todo
// Si es profesor, se queda aqui

// Obtener estadísticas globales para el dashboard
$totalEstudiantes = $conexion->query("SELECT COUNT(*) as total FROM estudiantes WHERE activo = 1")->fetch_assoc()['total'];
$totalCursos = $conexion->query("SELECT COUNT(*) as total FROM cursos WHERE activo = 1")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Docente - Centro Educativo</title>
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
                <li><a href="profesor_panel.php" class="active">🏠 Mi Panel</a></li>
                <li><a href="profesor_estudiantes.php">👨‍🎓 Estudiantes</a></li>
                <li><a href="profesor_cursos.php">📚 Mis Cursos</a></li>
                <?php if (esAdmin()): ?>
                    <li><a href="index.php" style="color: var(--primary-light);">⚙️ Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php" style="color: #fca5a5;">🚪 Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">

    <div class="container animate-fade">
        <div class="top-header" style="margin: 2rem 0 3rem 0;">
            <h2 class="glow-text" style="font-size: 3rem; margin-bottom: 0.5rem;">Portal Docente</h2>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Bienvenido a tu estación de trabajo, <?php echo htmlspecialchars(getNombreUsuario()); ?>.</p>
        </div>

        <div class="bento-grid">
            <!-- Featured Card -->
            <div class="glass-card bento-item-large" style="padding: 3rem; border-left: 4px solid var(--c-secondary);">
                <span class="stat-label">Alumnado Global</span>
                <div class="stat-value"><?php echo $totalEstudiantes; ?></div>
                <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 1.1rem;">Consulta el expediente y progreso de todos los estudiantes matriculados.</p>
                <a href="profesor_estudiantes.php" class="btn btn-primary" style="background: var(--c-secondary); border: none;">Directorio Estudiantil</a>
            </div>

            <!-- Profile Info Card -->
            <div class="glass-card bento-item-tall" style="padding: 2.5rem; display: flex; flex-direction: column; background: linear-gradient(135deg, rgba(6, 182, 212, 0.05), transparent);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🧑‍🏫</div>
                <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars(getNombreUsuario()); ?></h3>
                <span class="badge" style="background: var(--c-primary); color: #fff; align-self: flex-start; margin-bottom: 1.5rem;"><?php echo strtoupper(getRolUsuario()); ?></span>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-top: auto;">Acceso restringido a modo consulta académica.</p>
            </div>

            <!-- Stats Mini Card -->
            <div class="glass-card" style="padding: 2rem; border-right: 4px solid var(--c-accent);">
                <span class="stat-label">Cursos Activos</span>
                <div class="stat-value" style="font-size: 3rem;"><?php echo $totalCursos; ?></div>
            </div>

            <!-- Wide Utility Card -->
            <div class="glass-card bento-item-wide" style="padding: 2.5rem; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(90deg, rgba(236, 72, 153, 0.08), transparent);">
                <div>
                    <h3 style="font-size: 1.2rem; margin-bottom: 0.25rem;">Calendario Académico</h3>
                    <p style="color: var(--text-muted); font-size: 0.85rem;">No hay eventos próximos registrados para hoy.</p>
                </div>
                <a href="profesor_cursos.php" class="btn btn-primary" style="background: var(--c-accent); border: none;">Ver Mis Grupos</a>
            </div>
        </div>

            <h2 class="section-title" style="margin-top: 5rem;"><span>🔔</span> Últimas Incorporaciones</h2>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Email</th>
                            <th>Curso Asignado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT e.nombre, e.apellidos, e.email, c.nombre as curso, e.fecha_matricula 
                                FROM estudiantes e 
                                LEFT JOIN cursos c ON e.id_curso = c.id_curso 
                                WHERE e.activo = 1 
                                ORDER BY e.fecha_matricula DESC 
                                LIMIT 5";
                        $resultado = $conexion->query($sql);
                        
                        while ($fila = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><strong>" . htmlspecialchars($fila['nombre'] . ' ' . $fila['apellidos']) . "</strong></td>";
                            echo "<td>" . htmlspecialchars($fila['email']) . "</td>";
                            echo "<td><span class='badge' style='background: rgba(255,255,255,0.05); color: var(--text-muted);'>" . htmlspecialchars($fila['curso'] ?? 'Por asignar') . "</span></td>";
                            echo "<td>" . date('d/m/Y', strtotime($fila['fecha_matricula'])) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <footer class="glass-card" style="margin-top: 3rem; padding: 2rem;">
            <p>&copy; <?php echo date('Y'); ?> Centro Educativo Premium | Portal Docente</p>
        </footer>
    </div>

    <script src="js/scripts.js"></script>
</body>
</html>
<?php cerrarConexion(); ?>
