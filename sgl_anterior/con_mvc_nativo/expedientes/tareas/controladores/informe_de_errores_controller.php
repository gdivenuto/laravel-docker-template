<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class informe_de_errores_controller extends ControllerBase
{
	public function listar()
	{
		$_SESSION['mensaje'] = "El documento supera el tama&ntilde;o m&aacute;ximo permitido! \n Por favor, comun&iacute;quese con el Departamento de Inform&aacute;tica para cargar el documento respectivo.";
		$_SESSION['tipo_mensaje'] = 2;
		
		// Se redirecciona al listado principal de Expedientes
		header("Location: ../index.php");
	}
}
?>