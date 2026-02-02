<?php
/**
 * ============================================================
 * CENTRO EDUCATIVO - PAGINA DE REGISTRO
 * Proyecto IAW - CFGS ASIR
 * ============================================================
 */

require_once 'includes/conexion.php';
require_once 'includes/auth.php';

// Si ya esta logueado, redirigir
if (estaAutenticado()) {
    header('Location: index.php');
    exit();
}

$error = '';
$exito = '';

// Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = limpiarDatos($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $nombre_completo = limpiarDatos($_POST['nombre_completo'] ?? '');
    $email = limpiarDatos($_POST['email'] ?? '');
    
    if (empty($username) || empty($password) || empty($nombre_completo) || empty($email)) {
        $error = 'Por favor, complete todos los campos';
    } else {
        // Verificar si el usuario ya existe
        $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $error = 'El nombre de usuario o el email ya están registrados';
        } else {
            // Hashear contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $rol = 'profesor'; // Por defecto todos son profesores
            
            $stmt = $conexion->prepare("INSERT INTO usuarios (username, password, nombre_completo, email, rol) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $password_hash, $nombre_completo, $email, $rol);
            
            if ($stmt->execute()) {
                $exito = 'Cuenta creada con éxito. Ya puedes iniciar sesión.';
            } else {
                $error = 'Error al crear la cuenta. Inténtelo de nuevo.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Centro Educativo</title>
    <link rel="stylesheet" href="css/estilos.css?v=5?v=5">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .register-hero {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(30,27,75,0.8), rgba(67,56,202,0.4));
        }

        .register-card {
            width: 100%;
            max-width: 500px;
            padding: 3rem;
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-bento);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .login-title {
            font-size: 2rem;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .btn-register {
            width: 100%;
            padding: 1rem;
            background: var(--c-primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-s);
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition-base);
            margin-top: 1rem;
        }

        .btn-register:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.4);
        }

        .login-footer {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .login-footer a {
            color: var(--c-primary);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="register-hero">
        <div class="register-card animate-fade">
            <div class="login-header">
                <div class="login-logo">🎓</div>
                <h1 class="login-title">Crear Cuenta</h1>
                <p class="login-subtitle">Únete a nuestra plataforma educativa</p>
            </div>
            
            <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem; text-align: center;">
                    ⚠️ <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($exito): ?>
                <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #6ee7b7; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem; text-align: center;">
                    ✅ <?php echo $exito; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="nombre_completo">Nombre Completo</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" required value="<?php echo htmlspecialchars($_POST['nombre_completo'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email Institucional</label>
                    <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="username">Nombre de Usuario</label>
                    <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn-register">
                    CREAR MI CUENTA
                </button>
            </form>

            <div class="login-footer">
                ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php cerrarConexion(); ?>
