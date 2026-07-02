<?php
if (!isset($_SESSION))
	session_start();

include_once realpath($_SERVER['DOCUMENT_ROOT']) . '/sgl/defensoria/librerias/definiciones.php';

require_once RUTA_LIBRERIAS . "DireccionadorController.php";

DireccionadorController::direccionar();
?>