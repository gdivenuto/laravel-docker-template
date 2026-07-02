<?php 
if ( !isset($_SESSION) )
	session_start();

// Si caducó la sesion
if ( !isset($_SESSION['usuario']) ) {
    session_unset();
    session_destroy();
    // Se vuelve al Login
    header("Location: ../../html/backend/index.php");
    exit();
}
?>
