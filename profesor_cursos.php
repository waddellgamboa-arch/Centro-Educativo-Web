<?php
/**
 * ============================================================
 * CENTRO EDUCATIVO - VISTA DE CURSOS (PROFESOR)
 * Proyecto IAW - CFGS ASIR
 * ============================================================
 * 
 * Vista de solo lectura de cursos para profesores
 */

require_once 'includes/conexion.php';
require_once 'includes/auth.php';

requiereLogin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos - Centro Educativo</title>
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
                <li><a href="profesor_panel.php">🏠 Mi Panel</a></li>
                <li><a href="profesor_estudiantes.php">👨‍🎓 Estudiantes</a></li>
                <li><a href="profesor_cursos.php" class="active">📚 Mis Cursos</a></li>
                <?php if (esAdmin()): ?>
                    <li><a href="index.php" style="color: var(--primary-light);">⚙️ Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php" style="color: #fca5a5;">🚪 Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">

        <div class="content glass-card">
            <h2 class="section-title"><span>📚</span> Oferta Académica Disponible</h2>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Curso</th>
                            <th>Horas</th>
                            <th>Profesor</th>
                            <th>Fecha Inicio</th>
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
                        
                        while ($fila = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td><span class='badge' style='background: rgba(255,255,255,0.05); color: var(--text-muted);'>" . $fila['id_curso'] . "</span></td>";
                            echo "<td><strong>" . htmlspecialchars($fila['nombre']) . "</strong></td>";
                            echo "<td><span class='badge' style='border: 1px solid var(--border-glass);'>" . $fila['horas'] . "h</span></td>";
                            echo "<td>" . htmlspecialchars($fila['nombre_profesor'] ?? 'Sin asignar') . "</td>";
                            echo "<td>" . date('d/m/Y', strtotime($fila['fecha_inicio'])) . "</td>";
                            echo "<td style='text-align: center;'><span class='badge' style='background: var(--primary); font-weight: 700; width: 35px; display: inline-block;'>" . $fila['num_estudiantes'] . "</span></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info" style="margin-top: 3rem; border-radius: 12px; border-left: 4px solid var(--primary-light);">
                <p><strong>💡 Nota informativa:</strong> Como profesor, tu acceso es de solo lectura. Para cualquier cambio en la asignación de grupos o modificación de horarios, por favor contacta con Jefatura de Estudios.</p>
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
