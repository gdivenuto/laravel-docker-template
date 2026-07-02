<?php 
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Si caducó la sesión
if( !isset($_SESSION['usuario']) )
{
	// Se eliminana todas las variables de sesión
    session_unset();
    // Se destruye la sesión
    session_destroy();
    
    // Se redirecciona al Login
    header ("Location: ../index.php?sesion_caducada=true");
    exit();
}
?>
