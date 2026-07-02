<?php
if (!isset($_SESSION)) {
	session_start();
}

// Incluye la libreria (y configuracion) de envio por mail mediante PHPList
require_once $_SERVER['DOCUMENT_ROOT'] . "/sgl/librerias/PhpListRESTApiClient.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/sgl/config/mail_config.php";

// Incluye la vista que corresponde
require_once RUTA_VISTAS . "notificaciones_suscriptores/grilla.php";
require_once RUTA_VISTAS . "notificaciones_suscriptores/edicion.php";

class notificaciones_suscriptores_controller extends ControllerBase {

	public function __construct() {
		parent::__construct();

		// Se crea una instancia de la Vista
		$this->vista_grilla = new VistaNotificacionesSuscriptoresGrilla();
		$this->vista_edicion = new VistaNotificacionesSuscriptoresEdicion();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	/**
	 * Se obtienen TODOS los Suscriptores ACTIVOS
	 * @return array   $suscriptores      Conjunto de Suscriptores
	 */
	private function obtenerTodosLosSuscriptores() {

		$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

		try {
			if ($api->login()) {
				// Se obtiene la cantidad de TODOS los suscriptores del sistema.
				$cant_total_suscriptores = $api->subscribersCount();
				
				// Se obtienen todos los suscriptores registrados
				$suscriptores = $api->subscribersGet('email', '', $cant_total_suscriptores);
			} else {
				$this->listar("Error para conectarse a la API de PHPList.", 2);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $suscriptores;
	}

	public function listar($mensaje = '', $tipo_mensaje = '') {

		// SI SE RECIBE UN MENSAJE DEL RESULTADO DE UNA OPERACION REALIZADA
		if (LibreriaGeneral::recoge('mensaje')) {
			$mensaje = LibreriaGeneral::recoge('mensaje');
			$tipo_mensaje = LibreriaGeneral::recoge('tipo_mensaje');
		}
		if (empty($mensaje) && empty($tipo_mensaje)) {
			$mensaje = (isset($_SESSION['mensaje'])) ? $_SESSION['mensaje'] : '';
			$tipo_mensaje = (isset($_SESSION['tipo_mensaje'])) ? $_SESSION['tipo_mensaje'] : '';
		}

		$criterio_a_buscar = LibreriaGeneral::recoge('criterio_a_buscar');

		// Se obtienen TODOS los suscriptores
		$suscriptores = $this->obtenerTodosLosSuscriptores();

		// Si se recibió un criterio para filtrar
		if ($criterio_a_buscar != '') {

			$cantidad = (isset($suscriptores)) ? count($suscriptores) : 0;
			// Por cada Suscriptor
			for ($i = 0; $i < $cantidad; $i++) {
				// Se busca
				$pos = strpos(strtolower($suscriptores[$i]->email), strtolower($criterio_a_buscar));
				if ($pos === false) {
					// nada
				} else {
					$filtrados[] = $suscriptores[$i];
				}
			}
			// Si se han filtrado suscriptores, se asignan, sino el listado completo
			$suscriptores = (count($filtrados) > 0) ? $filtrados : null;
		}

		$this->vista_grilla->mostrar($suscriptores, $mensaje, $tipo_mensaje, $criterio_a_buscar);
	}

	/**
	 * Se edita un Suscriptor
	 */
	public function editar() {

		$id_suscriptor = LibreriaGeneral::recoge('id_suscriptor', 0);

		$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

		try {
			if ($api->login()) {
				// Se obtiene la info del Suscriptor si existe o null (al agregar un nuevo suscriptor)
				$info = (isset($id_suscriptor) && $id_suscriptor != 0) ? $api->subscriberGet($id_suscriptor) : null;

				$this->vista_edicion->mostrar($info);
			} else {
				$this->listar("Error para conectarse a la API de PHPList.", 2);
			}
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Se agrega un nuevo Suscriptor
	 */
	public function insertar() {
		// Se recibe la info
		$datos = $_REQUEST;

		try {
			if (isset($datos['email']) && $datos['email'] != '') {

				$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

				if ($api->login()) {
					// Se agrega un nuevo Suscriptor, mediante la API
					$resultado = $api->subscriberAdd(
						$datos['email'], // el email recibido
						1, // confirmed
						1, // htmlemail
						'', // rssfrequency
						'', // password
						0// disabled
					);

					// Si se agregó con la API
					if (isset($resultado) && $resultado->id != '') {
						$this->listar("Se ha ingresado un nuevo suscriptor con &eacute;xito.", 1);
					} else {
						$this->listar("Error al intentar ingresar un suscriptor.", 2);
					}
				} else {
					$this->listar("Error para conectarse a la API de PHPList.", 2);
				}
			} else {
				$this->listar("No se ha recibido el email para agregar el suscriptor.", 2);
			}
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Se modifica la info de un Suscriptor
	 */
	public function modificar() {
		// Se recibe la info
		$datos = $_REQUEST;

		try {
			if (isset($datos['email']) && $datos['email'] != '') {

				$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

				if ($api->login()) {
					// Se obtiene la info del Suscriptor
					$info_suscriptor = $api->subscriberGet($datos['id']);

					// Se modifica el suscriptor, mediante la API
					$resultado = $api->subscriberUpdate(
						$info_suscriptor->id,
						$datos['email'], // el email recibido
						$info_suscriptor->confirmed,
						$info_suscriptor->htmlemail,
						'', // rssfrequency
						'', // password
						$info_suscriptor->disabled
					);

					// Si se modificó con la API
					if (isset($resultado) && $resultado->id != '') {
						$this->listar("Se ha modificado el suscriptor con &eacute;xito.", 1);
					} else {
						$this->listar("Error al intentar modificar el suscriptor.", 2);
					}
				} else {
					$this->listar("Error para conectarse a la API de PHPList.", 2);
				}
			} else {
				$this->listar("No se ha recibido el email para modificar el suscriptor.", 2);
			}
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Se elimina un Suscriptor
	 */
	public function eliminar() {

		$id_suscriptor = LibreriaGeneral::recoge('id_suscriptor', 0);

		try {
			if (isset($id_suscriptor) && $id_suscriptor != 0) {

				$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

				if ($api->login()) {
					// Se elimina el suscriptor de la lista, mediante la API
					$resultado = $api->subscriberDelete($id_suscriptor);
					if ($resultado) {
						$this->listar("Se ha eliminado el suscriptor con &eacute;xito.", 1);
					} else {
						$this->listar("Error al intentar eliminar el suscriptor.", 2);
					}
				} else {
					$this->listar("Error para conectarse a la API de PHPList.", 2);
				}
			} else {
				$this->listar("No se ha recibido el identificador del Suscriptor a eliminar.", 2);
			}
		} catch (Exception $e) {
			return false;
		}
	}
}