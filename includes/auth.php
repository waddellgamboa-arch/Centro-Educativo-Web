<?php
/**
 * ============================================================
 * CENTRO EDUCATIVO - SISTEMA DE AUTENTICACION
 * Proyecto IAW - CFGS ASIR
 * ============================================================
 * 
 * Este archivo gestiona las sesiones y la autenticacion de usuarios.
 * Debe incluirse en todas las paginas protegidas.
 */

// Iniciar sesion si no esta iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica si el usuario esta autenticado
 * @return bool
 */
function estaAutenticado() {
    return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_rol']);
}

/**
 * Verifica si el usuario es administrador
 * @return bool
 */
function esAdmin() {
    return estaAutenticado() && $_SESSION['usuario_rol'] === 'admin';
}

/**
 * Verifica si el usuario es profesor
 * @return bool
 */
function esProfesor() {
    return estaAutenticado() && $_SESSION['usuario_rol'] === 'profesor';
}

/**
 * Redirige al login si no esta autenticado
 */
function requiereLogin() {
    if (!estaAutenticado()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Redirige si no es admin
 */
function requiereAdmin() {
    requiereLogin();
    if (!esAdmin()) {
        header('Location: profesor_panel.php');
        exit();
    }
}

/**
 * Obtiene el nombre del usuario actual
 * @return string
 */
function getNombreUsuario() {
    return $_SESSION['usuario_nombre'] ?? 'Usuario';
}

/**
 * Obtiene el rol del usuario actual
 * @return string
 */
function getRolUsuario() {
    return $_SESSION['usuario_rol'] ?? '';
}

/**
 * Cierra la sesion del usuario
 */
function cerrarSesion() {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Intenta autenticar un usuario
 * @param mysqli $conexion
 * @param string $username
 * @param string $password
 * @return array|false
 */
function autenticarUsuario($conexion, $username, $password) {
    $sql = "SELECT id_usuario, username, password, nombre_completo, rol 
            FROM usuarios 
            WHERE username = ? AND activo = 1";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        // Verificar password (compatible con passwords simples para desarrollo)
        if (password_verify($password, $usuario['password']) || $usuario['password'] === $password) {
            return $usuario;
        }
    }
    
    return false;
}

/**
 * Inicia la sesion del usuario
 * @param array $usuario
 */
function iniciarSesionUsuario($usuario) {
    $_SESSION['usuario_id'] = $usuario['id_usuario'];
    $_SESSION['usuario_nombre'] = $usuario['nombre_completo'];
    $_SESSION['usuario_rol'] = $usuario['rol'];
    $_SESSION['usuario_username'] = $usuario['username'];
}
?>
