<?php
/**
 * Este script se utiliza con require_once en cada script que requiera acceder a variables de Sesión.
 * Se utiliza para evitar la ejecución múltiple de la función session_start();
 * en PHP 5.4+ se puede utilizar session_status();
 * En un futuro, tendrá el saneo de las variables de Sesión y verificación de código malicioso.
 *
 * 2015-08-24 XXXX, XXXX
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
    // La inicia
	session_start();

// Segundos que tardará en cerrarse la sesión, sin actividad del usuario.
$tiempo_maximo_espera = 1440; // 24 minutos (24 x 60 segundos)
//$tiempo_maximo_espera = 300; // 5 minutos (5 x 60 segundos) DE PRUEBA

// Si el usuario está conectado
if ( isset($_SESSION["usuario"]) ) {
    // Se guarda la fecha y hora del último acceso, definido al autenticarse el usuario (autenticar.php)
    $fecha_guardada = $_SESSION["ultimoAcceso"];
    // Se guarda la fecha y hora actual
    $ahora = strtotime(date("Y-n-j H:i:s"));

    // Se calcula el tiempo que transcurrió desde el último acceso
    $tiempo_transcurrido = ($ahora - $fecha_guardada);

    // Si el tiempo transcurrido supera el tiempo máximo de espera permitido
    if ($tiempo_transcurrido > $tiempo_maximo_espera) {
        // Se eliminan todas las variables de sesión
        session_unset();
        // Se destruye la sesión
        session_destroy();
        // Se redirecciona al Login
        header("Location: /sgl/index.php?sesion_caducada=true");
        exit();
    } else
        // Sino se actualiza la fecha y hora de último acceso
        $_SESSION["ultimoAcceso"] = $ahora;
}
