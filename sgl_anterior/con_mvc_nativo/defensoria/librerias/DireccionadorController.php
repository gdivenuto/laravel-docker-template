<?php
if (!isset($_SESSION))
	session_start();

include_once realpath($_SERVER['DOCUMENT_ROOT']) . '/sgl/defensoria/librerias/definiciones.php';

class DireccionadorController {
	static function direccionar() {

		require_once RUTA_LIBRERIAS . 'sesion.php';

		require_once RUTA_LIBRERIAS . "LibreriaGeneral.php";

		require_once RUTA_LIBRERIAS_SGL . "Logger.php";

		require_once RUTA_LIBRERIAS_SGL . "modelo_base_mysqli.php";

		require_once RUTA_LIBRERIAS . "ControladorBase.php";

		require_once RUTA_LIBRERIAS . "VistaBase.php";

		require_once RUTA_LIBRERIAS_SGL . "html2pdf_v4_03/html2pdf.class.php";

		require_once($_SERVER['DOCUMENT_ROOT']."/sgl/administracion/abms/modelos/auditoria_defensoria.php");

		LibreriaGeneral::ObtenerInstancia();

		// Se forma el nombre del Controlador o en su defecto, se utiliza uno por defecto
		$nombre_controlador = (LibreriaGeneral::recoge('controlador') != '') 
			? ucwords(LibreriaGeneral::recoge('controlador') , "_")  . 'Controller' 
			: CONTROLADOR_POR_DEFECTO."Controller";
		
		// Lo mismo sucede con las acciones, si no hay accion, se toma una por defecto
		$nombre_accion = (LibreriaGeneral::recoge('accion') != '') 
			? LibreriaGeneral::recoge('accion') 
			: "listar";

		$ruta_controlador = RUTA_CONTROLADORES . $nombre_controlador . '.php';

		// Si existe la definicion de la clase controladora solicitada
		if (is_file($ruta_controlador)) {
			// Se incluye la definicion de la clase controladora solicitada
			require $ruta_controlador;
		} else {
			die("El controlador " . $nombre_controlador . " no existe.");
		}

		// Si no existe la clase que se busca y su acción
		if (is_callable(array($nombre_controlador, $nombre_accion)) === false) {
			// Se informa al usuario que el controlador y la acción no existen
			trigger_error($nombre_controlador . "->" . $nombre_accion . " no existe", E_USER_NOTICE);
			return false;
		}

		$_SESSION['info_persistente']['controlador'] = str_replace('_controller', '', $nombre_controlador);
		$_SESSION['info_persistente']['accion'] = $nombre_accion;

		$controlador = new $nombre_controlador();
		$controlador->$nombre_accion();
	}
}
?>
