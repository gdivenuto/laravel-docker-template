<?php
if (!isset($_SESSION)) {
	session_start();
}

// Incluye la libreria (y configuracion) de envio por mail mediante PHPList
require_once $_SERVER['DOCUMENT_ROOT'] . "/sgl/librerias/PhpListRESTApiClient.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/sgl/config/mail_config.php";

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "notificaciones_grupos.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "notificaciones_grupos/grilla.php";
require_once RUTA_VISTAS . "notificaciones_grupos/edicion.php";

class notificaciones_grupos_controller extends ControllerBase {

	public function __construct() {
		parent::__construct();

		$this->campo_orden_por_defecto = 'id';

		// Se crea una instancia del modelo
		$this->modelo = new notificacionesGruposModel();

		// Se crea una instancia de la Vista
		$this->vista_grilla = new VistaNotificacionesGruposGrilla();
		$this->vista_edicion = new VistaNotificacionesGruposEdicion();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	public function listar($mensaje = '', $tipo_mensaje = '') {

		$filtro = Array();

		// Filtro por Nombre ó Descripción
		$filtro['f_descripcion'] = LibreriaGeneral::recoge('f_descripcion');

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

		//LibreriaGeneral::registrarLog("filtro", $filtro);

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
		$_SESSION['f_notificaciones_grupos'] = $filtro;

		// Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		// Se obtienen los Grupos
		$listado = $this->modelo->listar();

		// Se obtienen y asignan los Nombres de cada Lista que posea cada Grupo del listado obtenido
		$listado = $this->asignarleNombresDeListas($listado);

		$this->vista_grilla->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
	}

	/**
	 * Se obtienen y asignan los Nombres de cada Lista que posea cada Grupo del listado
	 * @param  array $listado Listado de Grupos
	 * @return array          Listado de Grupos CON los Nombres de cada Lista que posee
	 */
	private function asignarleNombresDeListas($listado) {

		$cantidad = (isset($listado)) ? count($listado) : 0;
		// Por cada Grupo
		for ($i = 0; $i < $cantidad; $i++) {
			// Se separan los IDs de las listas que posee asignadas
			$id_listas = explode(',', $listado[$i]['phplist_ids']);

			$cantidad_id_listas = (isset($id_listas)) ? count($id_listas) : 0;
			// Por cada Lista
			for ($l = 0; $l < $cantidad_id_listas; $l++) {
				// Si NO es la lista nro. 2
				if ($id_listas[$l] != 2) {
					// Se obtiene y asigna el Nombre de dicha lista
					$listado[$i]['nombre_lista'][$l] = $this->modelo->obtenerNombreLista($id_listas[$l]);
				}
			}
			$cantidad_id_listas = 0;
		}

		return $listado;
	}

	public function editar() {
		// Se recibe el id para su edición
		$id = LibreriaGeneral::recoge('id', 0);

		// Se busca la info
		$datos = $this->modelo->obtenerRegistro($id);

		// Se obtienen las Listas
		$listas = $this->modelo->obtenerListas();

		// Si ya existe
		if ($datos['id']) {
			$datos['pagina'] = LibreriaGeneral::recoge('pagina');
		} else {
			// En caso de editarse un NUEVO registro
			$datos = null;
		}

		$this->vista_edicion->mostrar($datos, $listas);
	}

	public function insertar() {

		$datos = $_REQUEST; // Se recibe la info

		// Se agrupan los IDs de las Listas asignadas, separadas por coma, para guardarlos en el campo respectivo
		// Se les antecede el Id de la Lista 2, utilizada para el prefijo [Notificaciones HCD] del asunto de la campaña
		$datos['phplist_ids'] = '2,' . implode(',', $datos['listas_asignadas']);

		// Se ingresa
		if ($this->modelo->insertar($datos)) {
			$this->listar("El Grupo de distribuci&oacute;n  " . $datos['descripcion'] . " se ingres&oacute; con &eacute;xito.", 1);
		} else {
			$this->listar("Error al ingresar el Grupo de distribuci&oacute;n  " . $datos['descripcion'], 2);
		}
	}

	public function modificar() {

		$datos = $_REQUEST; // Se recibe la info

		// Se agrupan los IDs de las listas asignadas, separadas por coma, para guardarlos en el campo respectivo
		// Se les antecede el Id de la Lista 2, utilizada para el prefijo [Notificaciones HCD] del asunto de la campaña
		$datos['phplist_ids'] = '2,' . implode(',', $datos['listas_asignadas']);

		// Se modifica
		if ($this->modelo->modificar($datos)) {
			$this->listar("El Grupo de distribuci&oacute;n  " . $datos['descripcion'] . " se modific&oacute; con &eacute;xito.", 1);
		} else {
			$this->listar("Error al modificar el Grupo de distribuci&oacute;n " . $datos['descripcion'], 2);
		}
	}

	public function eliminar() {
		$id_grupo = LibreriaGeneral::recoge('id', 0);

		// Se elimina
		if ($this->modelo->eliminar($id_grupo)) {
			$this->listar("El Grupo de distribuci&oacute;n nro. " . $id_grupo . " se ha dado de baja con &eacute;xito.", 1);
		} else {
			$this->listar("Error al eliminar el Grupo de distribuci&oacute;n nro. " . $id_grupo, 2);
		}
	}
}
?>