<?php
/**
 * Este script se utiliza para actualizar la fecha y hora de último acceso, en la session vigente.
 * Devuelve en formato json una respuesta:
 * SI, en caso de haberse actualizado
 * NO, en caso de haber caducado la session
 *
 * Para ser utilizado cada vez que se refresca un contenido con Ajax, para mantener la actividad en el SGL.
 *
 * 2018-08-24 XXXX, XXXX
**/

// Verificamos si la sesión está activa
function estaSesionActiva() {
    // Si la API de Servidor que utiliza PHP NO es CLI (la función la devuelve en minúscula)
    if ( php_sapi_name() !== 'cli' ) {
        // Si la versión de PHP es mayor o igual a la 5.4.0
        if ( version_compare(phpversion(), '5.4.0', '>=') )
            // Si la sessión existe y está habilitada
            return (session_status() === PHP_SESSION_ACTIVE) ? true : false;
        else
            // Si existe el ID de la sesión
            return (session_id() === '') ? false : true;
    }
    return false;
}

// Si no está activa
if ( estaSesionActiva() === false )
    // Se inicia
	session_start();

// Si la sesión sigue activa
if ( isset($_SESSION["usuario"]) ) {
    // Se actualiza la fecha y hora de último acceso
    $_SESSION["ultimoAcceso"] = strtotime(date("Y-n-j H:i:s"));
    echo "{'actualizado':'SI'}";
} else
    echo "{'actualizado':'NO'}";
?>
