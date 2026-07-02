<?php
/**
 * Este script se utiliza para controlar el estado de la sesión, nada más.
 * Devuelve en formato json una respuesta:
 * SI, en caso de estar activa
 * NO, en caso de estar caducada
 *
 * Para ser utilizado cada X tiempo en el index.php de cada sistema
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

// Segundos que tardará en cerrarse la sesión, sin actividad del usuario.
$tiempo_maximo_espera = 1440; // 24 minutos (24 x 60 segundos)
//$tiempo_maximo_espera = 300; // 5 minutos (5 x 60 segundos) DE PRUEBA

// Se recibe la fecha y hora del último acceso al sistema
$ultimo_acceso = $_SESSION["ultimoAcceso"];
// Se guarda la fecha y hora actual
$ahora = strtotime(date("Y-n-j H:i:s"));

// Se calcula el tiempo que transcurrió desde el último acceso
$tiempo_transcurrido = ($ahora - $ultimo_acceso);

// Si el tiempo transcurrido supera el tiempo máximo de espera permitido
// retorna 'SI'
// sino retorna 'NO'
echo ($tiempo_transcurrido > $tiempo_maximo_espera) ? "{'sesion_caducada':'SI'}" : "{'sesion_caducada':'NO'}";
?>
