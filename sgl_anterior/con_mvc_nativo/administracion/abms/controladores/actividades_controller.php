<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "actividades.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "actividades/grilla.php";
require_once RUTA_VISTAS . "actividades/edicion.php";

class actividades_controller extends ControllerBase {
	public function __construct() {
		parent::__construct();

		$this->campo_orden_por_defecto = 'a_fecha';

		// Se crea una instancia del modelo
		$this->modelo = new actividadesModel();

		// Se crea una instancia de cada Vista
		$this->vista_grilla = new VistaActividadesGrilla();
		$this->vista_edicion = new VistaActividadesEdicion();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	public function guardarRegistroOriginal($original) {
		$_SESSION['a_codigo_original'] = $original['a_codigo'];
		$_SESSION['a_fecha_original'] = $original['a_fecha'];
		$_SESSION['a_hora_original'] = $original['a_hora'];
		$_SESSION['a_titulo_original'] = $original['a_titulo'];
		$_SESSION['a_contenido_original'] = $original['a_contenido'];
		$_SESSION['a_habilitado_original'] = $original['a_habilitado'];
	}

	public function listar($mensaje = '', $tipo_mensaje = '') {
		$filtro = Array();

		// FILTRO POR FECHA
		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		$filtro['f_fecha'] = (isset($f_fecha) && $this->esFechaValida($f_fecha)) ? $this->modelo->formatearFechaMySQL($f_fecha) : '';

		// FILTRO POR TITULO
		$filtro['f_titulo'] = LibreriaGeneral::recoge('f_titulo');

		// FILTRO POR CONTENIDO
		$filtro['f_contenido'] = LibreriaGeneral::recoge('f_contenido');

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
			} else
			// SE MUESTRAN LOS ÚLTIMOS (VALOR DEL RANGO) REGISTROS
			{
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
			}

		} else {
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1; // para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; // para la pagina posterior

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_actividades'] = $filtro;

		// SE ESTABLECE EL FILTRO EN EL MODELO
		$this->modelo->setFiltro($filtro);

		// SE OBTIENE EL LISTADO
		$listado = $this->modelo->listar();

		// se muestra el listado
		$this->vista_grilla->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
	}

	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '') {

		// Si NO se viene del formulario de edición por un error
		if ($datos_formulario === null) {
			// Se recibe el Id para su edición
			$id = LibreriaGeneral::recoge('id', 0);

			// Se busca el registro en la base de datos
			$datos = $this->modelo->obtenerRegistro($id);

			// Si existe
			if ($datos['a_codigo']) {
				// Se audita la consulta del registro
				//$this->modelo->auditarConsultaRegistro($datos);

				// Se guarda el registro en sesion para verificar luego si no ha modificado otro usuario
				$this->guardarRegistroOriginal($datos);

				// Se marca para saber que se encuentra en edición
				//$this->modelo->marcarEnEdicion($datos['a_codigo']);

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);
			} else {
				// En caso de editarse un NUEVO registro
				$datos = null;
			}

		} else {
			// SI SE VIENE DEL FORMULARIO DEBIDO A UN ERROR
			$datos = $datos_formulario;
		}

		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}

	public function insertar() {

		$datos = $_REQUEST;

		// SI NO EXISTE, PARA QUE DOS USUARIOS NO INGRESEN EL MISMO REGISTRO
		if ($this->modelo->existe($datos['a_fecha'], $datos['a_hora'])) {
			$this->listar("Ya se ha ingresado una Actividad con dicha fecha y horario", 2);
		} elseif ($this->modelo->insertar($datos)) {
			$this->listar("Se agreg&oacute; con &eacute;xito la Actividad.", 1);
		} else {
			$this->listar("Error al agregar la Actividad", 2);
		}
	}

	/**
	 * Se modifica un registro determinado
	 */
	public function modificar() {
		parent::modificarBase();
	}

	/**
	 * Se elimina un registro determinado
	 */
	public function eliminar() {
		parent::eliminarBase();
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado() {
		parent::modificarEstadoBase();
	}
}
?>
