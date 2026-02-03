<?php
/**
 * ============================================================
 * CENTRO EDUCATIVO - PAGINA DE LOGIN
 * Proyecto IAW - CFGS ASIR
 * ============================================================
 */

require_once 'includes/conexion.php';
require_once 'includes/auth.php';

// Si ya esta logueado, redirigir
if (estaAutenticado()) {
    if (esAdmin()) {
        header('Location: index.php');
    } elseif (esProfesor()) {
        header('Location: profesor_panel.php');
    } else {
        header('Location: estudiante_panel.php');
    }
    exit();
}

$error = '';

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = limpiarDatos($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Por favor, complete todos los campos';
    } else {
        $usuario = autenticarUsuario($conexion, $username, $password);
        
        if ($usuario) {
            iniciarSesionUsuario($usuario);
            
            // Redirigir segun rol
            if ($usuario['rol'] === 'admin') {
                header('Location: index.php');
            } elseif ($usuario['rol'] === 'profesor') {
                header('Location: profesor_panel.php');
            } else {
                header('Location: estudiante_panel.php');
            }
            exit();
        } else {
            $error = 'Usuario o contrasena incorrectos';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Seguro - Centro Educativo</title>
    <link rel="stylesheet" href="css/estilos.css?v=5?v=5">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: #1e1b4b; /* Solid base or keep gradient from estilos.css?v=5 */
            margin: 0;
        }

        .login-hero {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(30,27,75,0.8), rgba(67,56,202,0.4)), url('img/bg_login.png');
            background-size: cover;
            background-position: center;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 3rem;
            background: rgba(30, 27, 75, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .login-title {
            font-size: 2.2rem;
            color: #fff;
            margin-bottom: 0.5rem;
            font-family: 'Outfit';
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 1rem;
        }

        .login-form input {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            margin-bottom: 1.5rem;
        }

        .login-form label {
            color: #fff;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: var(--c-primary);
            filter: brightness(1.1);
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.4);
        }

        .login-footer {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .login-footer a {
            color: var(--c-primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition-fast);
        }

        .login-footer a:hover {
            color: var(--c-accent);
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-hero">
        <div class="login-card animate-fade">
            <div class="login-header">
                <div class="login-logo">🏫</div>
                <h1 class="login-title">Centro Educativo</h1>
                <p class="login-subtitle">Gestión Académica de Alto Nivel</p>
            </div>
            
            <?php if ($error): ?>
                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem; font-size: 0.9rem; text-align: center;">
                    ⚠️ <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
                
                <button type="submit" class="btn-login">
                    ACCEDER AL SISTEMA
                </button>
            </form>

            <div class="login-footer">
                ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php cerrarConexion(); ?>
