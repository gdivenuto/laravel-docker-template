<?php
if (!isset($_SESSION)) {
	session_start();
}

// Se incluyen las rutas definidas
include_once realpath($_SERVER['DOCUMENT_ROOT']) . '/sgl/administracion/librerias/definiciones.php';

// Se incluye el DireccionadorController
require_once RUTA_LIBRERIAS . "direccionador_controller.php";

// Se direcciona al controlador y se ejecuta la accion respectiva, siempre y cuando este permitido
DireccionadorController::direccionar();
?>