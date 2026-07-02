<?php
if (!isset($_SESSION))
	session_start();

require_once RUTA_MODELOS . "TipoProceso.php";

require_once RUTA_VISTAS . "tipo_proceso/grilla.php";
require_once RUTA_VISTAS . "tipo_proceso/edicion.php";

class Tipo_ProcesoController extends ControladorBase {

	public function __construct() {
		parent::__construct();

		$this->campo_orden_por_defecto = 'nombre';

		$this->modelo = new TipoProcesoModel();

		$this->vista_grilla = new VistaTipoProcesoGrilla();
		$this->vista_edicion = new VistaTipoProcesoEdicion();
	}

	public function guardarRegistroOriginal($original) {
		$_SESSION['id_original'] = $original['id'];
		$_SESSION['nombre_original'] = $original['nombre'];
		$_SESSION['habilitado_original'] = $original['habilitado'];
	}

	public function listar($mensaje = '', $tipo_mensaje = '', $p_pagina = ''): void {
		
		$filtro = Array();

		$filtro['valor_buscado'] = LibreriaGeneral::recoge('valor_buscado');

		$filtro['pagina'] = ($p_pagina == '') ? LibreriaGeneral::recoge('pagina', 1) : $p_pagina;

		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ($campo_orden != '') {
			$filtro['campo_orden'] = $campo_orden;
		} else {
			$filtro['campo_orden'] = $this->campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}

		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');

		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') ? 'desc' : 'asc';
		}

		$filtro['rango'] = $this->rango_paginacion;

		if ($filtro['pagina'] == '') {
			$filtro['inicio'] = 0; //por lo tanto se inicia en el primer registro
			$filtro['pagina'] = 1; //con la primer pagina
		} else {
			// si no se busca
			if ($filtro['valor_buscado'] == '') {
				$filtro['inicio'] = ($filtro['pagina'] - 1) * $filtro['rango'];
			}
		}
		$filtro['pagina_ant'] = $filtro['pagina'] - 1; //para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1; //para la pagina posterior

		$this->modelo->setFiltro($filtro);

		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		$this->modelo->setFiltro($filtro);

		$listado = $this->modelo->listar();

		$this->vista_grilla->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
	}

	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = ''): void {
		
		if ($datos_formulario === null) {
			$id = LibreriaGeneral::recoge('id', 0);

			$datos = $this->modelo->obtenerRegistro($id);

			if ($datos['id']) {
				$this->guardarRegistroOriginal($datos);

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);
			} else {
				$datos = null;
			}
		} else {
			$datos = $datos_formulario;
		}
		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se ingresa un registro determinado
	 */
	public function insertar() {
		parent::insertarBase();
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