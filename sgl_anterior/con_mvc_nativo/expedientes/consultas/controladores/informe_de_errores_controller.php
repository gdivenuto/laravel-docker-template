<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class informe_de_errores_controller extends ControllerBase
{
	public function listar()
	{
		$_SESSION['mensaje'] = "Ha surgido un error. Comun&iacute;quese con el Departamento de Inform&aacute;tica";
		$_SESSION['tipo_mensaje'] = 2;
		
		// Se redirecciona al listado principal de Expedientes
		header("Location: ../index.php");
	}
}
?>