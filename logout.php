<?php
/**
 * ============================================================
 * CENTRO EDUCATIVO - CIERRE DE SESION
 * Proyecto IAW - CFGS ASIR
 * ============================================================
 */

require_once 'includes/auth.php';

// Cerrar sesion
cerrarSesion();

// Redirigir al login
header('Location: login.php');
exit();
?>
