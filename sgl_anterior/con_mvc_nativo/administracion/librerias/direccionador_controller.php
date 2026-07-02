<?php
if (!isset($_SESSION)) {
	session_start();
}

// Se incluyen las rutas definidas
include_once realpath($_SERVER['DOCUMENT_ROOT']) . '/sgl/administracion/librerias/definiciones.php';

class DireccionadorController {
	static function direccionar() {

		// SE INCLUYE LO NECESARIO PARA EL SISTEMA
		//****************************************

		// Control de la sesión, de haber caducado su tiempo, se vuelve al Login.
		require_once RUTA_LIBRERIAS . 'sesion.php';

		// CLASE CON METODOS ESTATICOS PARA UTILIZAR EN TODO EL SISTEMA
		require_once RUTA_LIBRERIAS . "LibreriaGeneral.php";

		// Clase que implementa el patrón singleton para centralización de la lógica de logueo de errores e
		// información desde los scripts en PHP.
		require_once RUTA_LIBRERIAS_SGL . "Logger.php";

		// CLASE BASE DE LOS Modelos PARA TRABAJAR CON MySQLi en la DB hcd
		require_once RUTA_LIBRERIAS_SGL . "modelo_base_mysqli.php";

		// CLASE BASE DE LOS Modelos PARA TRABAJAR CON MySQLi en la DB dmz
		require_once RUTA_LIBRERIAS_SGL . "modelo_base_mysqli_dmz.php";

		// CLASE BASE DE LOS Controladores
		require_once RUTA_LIBRERIAS . "controlador_base.php";

		// CLASE BASE DE LAS Vistas
		require_once RUTA_LIBRERIAS . "vista_base.php";

		// CLASE PARA CONVERTIR EL html A pdf
		require_once RUTA_LIBRERIAS_SGL . "html2pdf_v4_03/html2pdf.class.php";

		// SE INCLUYE EL MODELO DE auditoria PARA REGISTRAR EN LA BASE LOS MOVIMIENTOS
		require_once RUTA_MODELOS . "auditoria_administracion.php";

		// SE OBTIENE UNA INSTANCIA DE LibreriaGeneral PARA UTILIZAR METODOS GENERALES EN TODO EL SISTEMA
		LibreriaGeneral::ObtenerInstancia();

		// SE DEFINE UN CONTROLADOR POR DEFECTO PARA CADA AREA
		switch ($_SESSION['perfil1']) {
		case PERFIL_AREA_ACTAS:
			$controlador_por_defecto = 'notificaciones'; // ACTAS
			break;
		case PERFIL_AREA_ADMINISTRACION:
			$controlador_por_defecto = 'notificaciones'; // ADMINISTRACION
			break;
		case PERFIL_AREA_BIBLIOTECA:
			$controlador_por_defecto = 'concejales_historico'; // BIBLIOTECA
			break;
		case PERFIL_AREA_COMISIONES:
			$controlador_por_defecto = 'comisiones_internas'; // COMISIONES 
			break;
		case PERFIL_AREA_INFORMATICA:
			$controlador_por_defecto = 'usuarios'; // INFORMATICA
			break;
		case PERFIL_AREA_MESA_ENTRADAS:
			$controlador_por_defecto = 'participaciones'; // MESA DE ENTRADAS
			break;
		case PERFIL_AREA_MODERNIZACION:
			$controlador_por_defecto = 'compras'; // MODERNIZACIÓN
			break;
		case PERFIL_AREA_PRENSA:
			$controlador_por_defecto = 'actividades'; // PRENSA
			break;
		case PERFIL_AREA_PRESIDENCIA:
			$controlador_por_defecto = 'notificaciones'; // PRESIDENCIA
			break;
		}

		// SE PROCESA LA REDIRECCIÓN
		// *************************

		// Se forma el nombre del Controlador o en su defecto, se utiliza uno por defecto
		$nombre_controlador = (LibreriaGeneral::recoge('controlador') != '') ? LibreriaGeneral::recoge('controlador') . '_controller' : $controlador_por_defecto . "_controller";

		// Lo mismo sucede con las acciones, si no hay accion, se toma una por defecto
		$nombre_accion = (LibreriaGeneral::recoge('accion') != '') ? LibreriaGeneral::recoge('accion') : "listar";

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
			// Se informa al usuario que el controlador no existe
			trigger_error($nombre_controlador . "->" . $nombre_accion . " no existe", E_USER_NOTICE);
			return false;
		}

		$_SESSION['info_persistente']['controlador'] = str_replace('_controller', '', $nombre_controlador);
		$_SESSION['info_persistente']['accion'] = $nombre_accion;

		// Se crea una instancia del controlador
		$controlador = new $nombre_controlador();

		// y se llama a la accion correspondiente
		$controlador->$nombre_accion();
	}
}
?>