<?php
/**
 * ============================================================
 * CENTRO EDUCATIVO - ARCHIVO DE CONEXIÓN A BASE DE DATOS
 * Proyecto IAW - CFGS ASIR
 * ============================================================
 * 
 * Este archivo gestiona la conexión a la base de datos MySQL.
 * Debe incluirse en cada página que necesite acceder a la BD.
 * 
 * CONFIGURACIÓN:
 * - Para XAMPP local: usar localhost, root, sin contraseña
 * - Para AWS: cambiar los valores según la configuración del servidor
 */

// ============================================================
// CONFIGURACIÓN DE LA BASE DE DATOS
// ============================================================
// Modificar estos valores según el entorno (local/producción)

$servidor = getenv('DB_HOST') ?: "localhost";        
$usuario = getenv('DB_USER') ?: "root";              
$password = getenv('DB_PASS') ?: "";                 
$baseDatos = getenv('DB_NAME') ?: "centro_educativo"; 

// ============================================================
// ESTABLECER CONEXIÓN
// ============================================================
// Creamos la conexión usando MySQLi (MySQL Improved)

$conexion = new mysqli($servidor, $usuario, $password, $baseDatos);

// Verificar si hay errores de conexión
if ($conexion->connect_error) {
    // En producción, registrar el error en un log en lugar de mostrarlo
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer el conjunto de caracteres a UTF-8 para soportar caracteres especiales
$conexion->set_charset("utf8mb4");

// ============================================================
// FUNCIÓN: limpiarDatos
// ============================================================
/**
 * Limpia y sanitiza los datos recibidos de formularios
 * para prevenir inyección SQL y XSS
 * 
 * @param string $dato - El dato a limpiar
 * @return string - El dato sanitizado
 */
function limpiarDatos($dato) {
    global $conexion;
    
    // Eliminar espacios en blanco al inicio y final
    $dato = trim($dato);
    
    // Eliminar barras invertidas
    $dato = stripslashes($dato);
    
    // Convertir caracteres especiales a entidades HTML
    $dato = htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
    
    // Escapar caracteres especiales para MySQL
    $dato = $conexion->real_escape_string($dato);
    
    return $dato;
}

// ============================================================
// FUNCIÓN: cerrarConexion
// ============================================================
/**
 * Cierra la conexión a la base de datos
 * Debe llamarse al final de cada página
 */
function cerrarConexion() {
    global $conexion;
    
    if ($conexion) {
        $conexion->close();
    }
}

// ============================================================
// FUNCIÓN: ejecutarConsulta (función auxiliar opcional)
// ============================================================
/**
 * Ejecuta una consulta SQL y devuelve el resultado
 * 
 * @param string $sql - La consulta SQL a ejecutar
 * @return mysqli_result|bool - El resultado de la consulta
 */
function ejecutarConsulta($sql) {
    global $conexion;
    
    $resultado = $conexion->query($sql);
    
    if (!$resultado) {
        // En producción, registrar el error en un log
        error_log("Error en consulta SQL: " . $conexion->error);
        return false;
    }
    
    return $resultado;
}
?>
