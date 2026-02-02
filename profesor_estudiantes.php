<?php
/**
 * ============================================================
 * CENTRO EDUCATIVO - VISTA DE ESTUDIANTES (PROFESOR)
 * Proyecto IAW - CFGS ASIR
 * ============================================================
 * 
 * Vista de solo lectura de estudiantes para profesores
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
    <title>Estudiantes - Centro Educativo</title>
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
                <li><a href="profesor_estudiantes.php" class="active">👨‍🎓 Estudiantes</a></li>
                <li><a href="profesor_cursos.php">📚 Mis Cursos</a></li>
                <?php if (esAdmin()): ?>
                    <li><a href="index.php" style="color: var(--primary-light);">⚙️ Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php" style="color: #fca5a5;">🚪 Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">

        <div class="content glass-card">
            <h2 class="section-title"><span>👨‍🎓</span> Listado de Estudiantes</h2>

            <div style="margin-bottom: 2rem;">
                <input type="text" id="busqueda" placeholder="🔍 Buscar por nombre, email o curso..." 
                       onkeyup="filtrarTabla('busqueda', 'tablaEstudiantes')">
            </div>

            <div class="table-container">
                    <table id="tablaEstudiantes">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Estudiante</th>
                                <th>Contacto</th>
                                <th>Curso Actual</th>
                                <th>Fecha Matriculación</th>
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
                                    echo "<td><strong>" . htmlspecialchars($fila['nombre'] . ' ' . $fila['apellidos']) . "</strong></td>";
                                    echo "<td>" . htmlspecialchars($fila['email']) . "<br><small style='color: var(--text-muted);'>" . ($fila['telefono'] ? htmlspecialchars($fila['telefono']) : '-') . "</small></td>";
                                    echo "<td><span class='badge'>" . ($fila['nombre_curso'] ? htmlspecialchars($fila['nombre_curso']) : 'Por asignar') . "</span></td>";
                                    echo "<td>" . date('d/m/Y', strtotime($fila['fecha_matricula'])) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align: center; padding: 3rem; color: var(--text-muted);'>No hay estudiantes registrados</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            <div class="alert alert-info" style="margin-top: 3rem; border-radius: 12px; border-left: 4px solid var(--primary-light);">
                <p><strong>💡 Nota informativa:</strong> Como profesor, tu acceso es de solo lectura. Si detectas alguna incongruencia en los datos del alumnado, por favor contacta con Secretaría o Administración.</p>
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
