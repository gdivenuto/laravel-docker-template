<?php
if (!isset($_SESSION)) {
	session_start();
}

// Incluye la libreria (y configuracion) de envio por mail mediante PHPList
require_once $_SERVER['DOCUMENT_ROOT'] . "/sgl/librerias/PhpListRESTApiClient.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/sgl/config/mail_config.php";

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "notificaciones_listas.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "notificaciones_listas/grilla.php";
require_once RUTA_VISTAS . "notificaciones_listas/edicion.php";
require_once RUTA_VISTAS . "notificaciones_listas/edicion_suscriptores_lista.php";

class notificaciones_listas_controller extends ControllerBase {

	public function __construct() {
		parent::__construct();

		$this->campo_orden_por_defecto = 'id';

		// Se crea una instancia del modelo
		$this->modelo = new notificacionesListasModel();

		// Se crea una instancia de la Vista
		$this->vista_grilla = new VistaNotificacionesListasGrilla();
		$this->vista_edicion = new VistaNotificacionesListasEdicion();
		$this->vista_edicion_suscriptores_lista = new VistaNotificacionesListasEdicionSuscriptores();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	public function listar($mensaje = '', $tipo_mensaje = '') {

		$filtro = Array();

		// SI SE RECIBE UN MENSAJE DEL RESULTADO DE UNA OPERACION REALIZADA
		if (LibreriaGeneral::recoge('mensaje')) {
			$mensaje = LibreriaGeneral::recoge('mensaje');
			$tipo_mensaje = LibreriaGeneral::recoge('tipo_mensaje');
		}
		if (empty($mensaje) && empty($tipo_mensaje)) {
			$mensaje = (isset($_SESSION['mensaje'])) ? $_SESSION['mensaje'] : '';
			$tipo_mensaje = (isset($_SESSION['tipo_mensaje'])) ? $_SESSION['tipo_mensaje'] : '';
		}

		// Filtro por Nombre ó Descripción
		$filtro['f_nombre_descripcion'] = LibreriaGeneral::recoge('f_nombre_descripcion');

		// se establece el campo por el cual ordenar
		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ($campo_orden != '') {
			$filtro['campo_orden'] = $campo_orden;
		} else {
			//por defecto
			$filtro['campo_orden'] = $this->campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}

		// DIRECCION PARA LA PAGINACION (PRIMERO, ANTERIOR, SGTE., ULTIMO)
		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');

		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			// Si es la primera vez que carga la pagina o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') ? 'desc' : 'asc';
		}

		// Cantidad de registros a mostrar
		$filtro['rango'] = $this->rango_paginacion;

		// SE ESTABLECE EL FILTRO EN EL MODELO
		$this->modelo->setFiltro($filtro);

		// Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		$filtro['pagina'] = LibreriaGeneral::recoge('pagina');

		// SI NO SE RECIBIÓ LA PÁGINA
		if (!$filtro['pagina']) {
			// SE ESTABLECE LA ÚLTIMA
			$filtro['pagina'] = ($filtro['nro_paginas'] > 0) ? $filtro['nro_paginas'] : 1;

			// SI LA CANTIDAD ES MENOR AL RANGO DE PAGINA
			if ($filtro['cantidad'] < $filtro['rango']) {
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			} else {
				// SE MUESTRAN LOS ÚLTIMOS (VALOR DEL RANGO) REGISTROS
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
			}

		} else {
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1; // para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; // para la pagina posterior

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['f_notificaciones_listas'] = $filtro;

		// Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		// Se obtienen las Listas de la DB
		$listado = $this->modelo->listar();

		$this->vista_grilla->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
	}

	public function editar() {
		// Se recibe el id para su edición
		$id = LibreriaGeneral::recoge('id', 0);

		// Se busca el registro en la base de datos
		$datos = $this->modelo->obtenerRegistro($id);

		// Si ya existe
		if ($datos['id']) {
			$datos['pagina'] = LibreriaGeneral::recoge('pagina');
			// Se obtienen los suscriptores de la lista respectiva
			$datos['suscriptores'] = $this->obtenerSuscriptoresDeLista($datos['id']);
		} else {
			// En caso de editarse un NUEVO registro
			$datos = null;
		}

		$this->vista_edicion->mostrar($datos);
	}

	public function seguirEditando($id_lista, $mensaje, $tipo_mensaje) {
		// Se busca el registro en la base de datos
		$datos = $this->modelo->obtenerRegistro($id_lista);

		// Se obtienen los suscriptores de la lista respectiva
		$datos['suscriptores'] = $this->obtenerSuscriptoresDeLista($datos['id']);

		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se ingresa una Lista de distribución,  en la API y en la DB
	 */
	public function insertar() {
		$datos = $_REQUEST; // Se recibe la info

		$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

		try {
			if ($api->login()) {
				// Se agrega la lista mediante la API
				$resultado_list_add = $api->listAdd(
					$datos['name'],
					$datos['description'],
					0, // listorder
					'', // [el prefijo del asunto]
					'', // rssfeed
					0// active, en cero para setearla como Privada a la Lista
				);

				// Si se agregó con la API
				if (isset($resultado_list_add) && $resultado_list_add->id != '') {
					// Se toma el id recién generado de la API
					$datos['id'] = $resultado_list_add->id;
					// Se ingresa la lista a la DB del SGL
					if ($this->modelo->insertar($datos)) {
						$this->listar("la Lista de distribuci&oacute;n nro. " . $datos['id'] . " se ingres&oacute; con &eacute;xito.", 1);
					} else {
						$this->listar("Error al ingresar la Lista de distribuci&oacute;n nro. " . $datos['id'], 2);
					}
				} else {
					$this->listar("Error al ingresar la Lista de distribuci&oacute;n (desde la API).", 2);
				}
			} else {
				$this->listar("Error para conectarse a la API de PHPList.", 2);
			}
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Se modifica una Lista de distribución, en la API
	 */
	public function modificar() {
		$datos = $_REQUEST; // Se recibe la info

		$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

		try {
			if ($api->login()) {
				// Se modifica la lista mediante la API
				$resultado_list_update = $api->listUpdate(
					$datos['id'],
					$datos['name'],
					$datos['description'],
					0, // listorder
					'', // [el prefijo del asunto]
					'', // rssfeed
					$datos['active']// active
				);

				// Si se modificó con la API
				if (isset($resultado_list_update) && $resultado_list_update->id != '') {
					// Se modifica la lista en la DB del SGL
					if ($this->modelo->modificar($datos)) {
						$this->listar("la Lista de distribuci&oacute;n nro. " . $datos['id'] . " se modific&oacute; con &eacute;xito.", 1);
					} else {
						$this->listar("Error al modificar la Lista de distribuci&oacute;n nro. " . $datos['id'] . " (desde la API).", 2);
					}
				} else {
					$this->listar("Error al modificar la Lista de distribuci&oacute;n nro. " . $datos['id'] . " (desde la API).", 2);
				}
			} else {
				$this->listar("Error para conectarse a la API de PHPList.", 2);
			}
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Se elimina una Lista de distribución determinada
	 */
	public function eliminar() {
		$id_lista = LibreriaGeneral::recoge('id');

		$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

		try {
			if ($api->login()) {
				// Se elimina con la API
				$resultado_list_delete = $api->listDelete($id_lista);

				// Si se pudo eliminar con la API
				if (isset($resultado_list_delete)) {
					// Se elimina de la DB del SGL
					if ($this->modelo->eliminar($id_lista)) {
						$this->listar("La Lista de distribuci&oacute;n nro. " . $id_lista . " se ha dado de baja con &eacute;xito.", 1);
					} else {
						$this->listar("Error al eliminar la Lista de distribuci&oacute;n nro. " . $id_lista, 2);
					}
				} else {
					$this->listar("No se pudo eliminar la Lista de distribuci&oacute;n nro. " . $id_lista, 2);
				}
			} else {
				$this->listar("Error para conectarse a la API de PHPList.", 2);
			}
		} catch (Exception $e) {
			return false;
		}
	}

	/**
	 * Se obtienen los Suscriptores de una Lista determinada
	 * @param  integer $id_lista 				Identificador de la Lista
	 * @return array   $suscriptores_lista      Conjunto de Suscriptores
	 */
	private function obtenerSuscriptoresDeLista($id_lista) {

		$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

		try {
			if ($api->login()) {
				$suscriptores_lista = $api->subscribersGetByList($id_lista);
			} else {
				$this->listar("Error para conectarse a la API de PHPList.", 2);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $suscriptores_lista;
	}

	/**
	 * Se obtienen TODOS los Suscriptores ACTIVOS
	 * @return array   $suscriptores      Conjunto de Suscriptores
	 */
	private function obtenerTodosLosSuscriptores() {

		$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

		try {
			if ($api->login()) {

				// 31/01/2022 XXXX
				// Se obtiene la cantidad de TODOS los suscriptores del sistema.
				$cant_total_suscriptores = $api->subscribersCount();

				$suscriptores = $api->subscribersGet('email', '', $cant_total_suscriptores);
			} else {
				$this->listar("Error para conectarse a la API de PHPList.", 2);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}

		return $suscriptores;
	}

	/**
	 * Se editan los Suscriptores de una Lista determinada
	 * ex buscarSuscriptores
	 */
	public function editarSuscriptoresLista() {

		// Se recibe el id de la Lista
		$id_lista = LibreriaGeneral::recoge('id_lista', 0);

		$criterio_a_buscar = LibreriaGeneral::recoge('criterio_a_buscar');

		// Se busca el registro en la base de datos
		$datos = $this->modelo->obtenerRegistro($id_lista);

		$datos['pagina'] = LibreriaGeneral::recoge('pagina', 0);

		// Se obtienen los suscriptores de la lista respectiva
		$suscriptores_asignados = $this->obtenerSuscriptoresDeLista($id_lista);

		$cantidad_suscriptores_asignados = (isset($suscriptores_asignados)) ? count($suscriptores_asignados) : 0;
		for ($i = 0; $i < $cantidad_suscriptores_asignados; $i++) {
			// Se toman los Ids de los suscriptores que están asignados a la Lista
			$id_asignados[] = &$suscriptores_asignados[$i]->userid;
		}

		// Se obtienen TODOS los suscriptores
		$suscriptores = $this->obtenerTodosLosSuscriptores();

		// Si se recibió un criterio para filtrar
		if ($criterio_a_buscar != '') {

			$cantidad = (isset($suscriptores)) ? count($suscriptores) : 0;
			// Por cada Suscriptor
			for ($i = 0; $i < $cantidad; $i++) {
				// Se busca
				$pos = strpos($suscriptores[$i]->email, $criterio_a_buscar);
				if ($pos === false) {
					// nada
				} else {
					$filtrados[] = $suscriptores[$i];
				}
			}
			// Si se han filtrado suscriptores, se asignan, sino el listado completo
			$suscriptores = (count($filtrados) > 0) ? $filtrados : null;
		}

		$this->vista_edicion_suscriptores_lista->mostrar($datos, $id_asignados, $suscriptores, $criterio_a_buscar);
	}

	/**
	 * Se asigna 1 o muchos Suscriptores a una lista determinada
	 */
	public function asignarSuscriptores() {
		// Se recibe la info
		$datos = $_REQUEST;

		$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

		try {
			if ($api->login()) {
				// Primero se debe verificar si el suscriptor se encuentra asignado,
				// porque al estar ya asignado, falla el método de la API y no retorna un valor false para su evaluación,
				// retorna un mensaje (un string)
				// Para ello se obtienen los IDs de los suscriptores asignados.
				// Si no se encuentra, se asigna, sino nada.

				// Se obtienen los suscriptores de la lista respectiva
				$suscriptores_asignados = $this->obtenerSuscriptoresDeLista($datos['id']);
				// Se obtiene la cantidad
				$cantidad_suscriptores_asignados = (isset($suscriptores_asignados)) ? count($suscriptores_asignados) : 0;
				for ($i = 0; $i < $cantidad_suscriptores_asignados; $i++) {
					// Se toman los Ids de los suscriptores que están asignados a la Lista
					$id_asignados[] = &$suscriptores_asignados[$i]->userid;
				}

				// Cantidad de suscriptores recibidos para asignar a la lista
				$cantidad_a_asignar = (isset($datos['suscriptor'])) ? count($datos['suscriptor']) : 0;

				// Por cada suscriptor recibido
				for ($i = 0; $i < $cantidad_a_asignar; $i++) {
					// Se verifica si ya no se encuentra asignado a la lista
					if (isset($id_asignados) && in_array($datos['suscriptor'][$i], $id_asignados)) {
						//
					} else {
						// De no estarlo, se asigna a la lista
						$resultado_add_suscriptor = $api->listSubscriberAdd($datos['id'], $datos['suscriptor'][$i]);
					}
				}

				// Se vuelve a la edición con el mensaje respectivo
				$this->seguirEditando($datos['id'], "Se asignaron los suscriptos a la Lista con &eacute;xito.", 1);
			} else {
				$this->seguirEditando($datos['id'], "Error para conectarse a la API de PHPList.", 2);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * Se crea y asigna un nuevo Suscriptor
	 */
	public function asignarNuevoSuscriptor() {
		// Se recibe el id de la Lista
		$id_lista = LibreriaGeneral::recoge('id_lista', 0);
		// Se recibe el nuevo email
		$email = LibreriaGeneral::recoge('email');

		$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

		try {
			if ($api->login()) {
				// Si se recibió el mail de un NUEVO suscriptor
				if ($email != '') {
					// Se agrega el nuevo Suscriptor, mediante la API
					$resultado = $api->subscriberAdd(
						$email, // el email recibido
						1, // confirmed
						1, // htmlemail
						'', // rssfrequency
						'', // password
						0// disabled
					);

					// Si se agregó con éxito
					if (isset($resultado) && $resultado->id != '') {
						// Se lo asigna a la lista
						$resultado_add_suscriptor = $api->listSubscriberAdd($id_lista, $resultado->id);
						// Se vuelve a la edición con el mensaje respectivo
						$this->seguirEditando($id_lista, "Se cre&oacute; y asign&oacute; el nuevo suscriptor a la Lista con &eacute;xito.", 1);
					} else {
						$this->seguirEditando($id_lista, "Error al intentar agregar un nuevo suscriptor.", 2);
					}
				} else {
					$this->seguirEditando($id_lista, "No se ha recibido el email para agregar el nuevo suscriptor.", 2);
				}
			} else {
				$this->seguirEditando($id_lista, "Error para conectarse a la API de PHPList.", 2);
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	/**
	 * Se elimina un Suscriptor de una Lista determinada
	 */
	public function eliminarSuscriptorDeLista() {

		$id_lista = LibreriaGeneral::recoge('id_lista');
		$id_suscriptor = LibreriaGeneral::recoge('id_suscriptor');

		$api = new PhpListRESTApiClient(LISTA_CORREO_API_URL, LISTA_CORREO_API_USER, LISTA_CORREO_API_PASSWORD);

		try {
			if ($api->login()) {
				// Se elimina el suscriptor de la lista, mediante la API
				$resultado = $api->listSubscriberDelete($id_lista, $id_suscriptor);
				if ($resultado) {
					$this->seguirEditando($id_lista, "Se ha eliminado el suscriptor de la lista con &eacute;xito.", 1);
				} else {
					$this->seguirEditando($id_lista, "Error al intentar eliminar el suscriptor de la Lista.", 2);
				}
			} else {
				$this->seguirEditando($id_lista, "Error para conectarse a la API de PHPList.", 2);
			}
		} catch (Exception $e) {
			return false;
		}
	}
}
?>
