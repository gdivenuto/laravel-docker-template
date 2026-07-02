<?php
if (!isset($_SESSION))
	session_start();

require_once RUTA_MODELOS . "Resolucion.php";
require_once RUTA_MODELOS . "Remitente.php";

require_once RUTA_VISTAS . "resolucion/grilla.php";
require_once RUTA_VISTAS . "resolucion/edicion.php";

class ResolucionController extends ControladorBase {
	
	private $modelo_remitente;

	public function __construct() {
		parent::__construct();

		$this->campo_orden_por_defecto = 'numero';

		$this->modelo = new ResolucionModel();
		$this->modelo_remitente = new RemitenteModel();

		$this->vista_grilla = new VistaResolucionGrilla();
		$this->vista_edicion = new VistaResolucionEdicion();
	}

	public function guardarRegistroOriginal($original) {

		$_SESSION['numero_original'] = $original['numero'];
		$_SESSION['remitente_id_original'] = $original['remitente_id'];
		$_SESSION['fecha_original'] = $original['fecha'];
		$_SESSION['texto_original'] = $original['texto'];
	}

	/**
	 * Se prepara y muestra la grilla
	 * @param  string $mensaje      [description]
	 * @param  string $tipo_mensaje [description]
	 * @param  string $p_pagina     [description]
	 * @return [type]               [description]
	 */
	public function listar($mensaje = '', $tipo_mensaje = '', $p_pagina = ''): void {
		
		$filtro = Array();

		$filtro['f_numero'] = LibreriaGeneral::recoge('f_numero');

		$filtro['f_remitente'] = LibreriaGeneral::recoge('f_remitente', 0);
		
		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		$filtro['f_fecha'] = ( isset($f_fecha) && $this->esFechaValida($f_fecha) ) 
			? $this->modelo->formatearFechaMySQL($f_fecha) : '';
		
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
			$filtro['inicio'] = 0;
			$filtro['pagina'] = 1;
		} else {
			$filtro['inicio'] = ($filtro['pagina'] - 1) * $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1;
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;

		$this->modelo->setFiltro($filtro);

		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		$this->modelo->setFiltro($filtro);

		$datos['info'] = $this->modelo->listar();
		$datos['remitentes'] = $this->modelo_remitente->listarHabilitados();
		
		$this->vista_grilla->mostrar($datos, $mensaje, $tipo_mensaje, $filtro);
	}

	/**
	 * Se edita un registro de un Id determinado
	 */
	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = ''): void {
		
		if ($datos_formulario === null) {
			$numero = LibreriaGeneral::recoge('numero', 0);

			$datos = $this->modelo->obtenerRegistro($numero);

			if ($datos['numero']) {
				$this->guardarRegistroOriginal($datos);

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);
			} else {
				$datos = null;
			}
		} else {
			$datos_formulario['fecha'] = ($datos_formulario['fecha'] != '') 
				? $this->modelo->formatearFechaMySQL($datos_formulario['fecha']) : '';

			$datos = $datos_formulario;
		}

		$datos['remitentes'] = $this->modelo_remitente->listarHabilitados();
		
		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se ingresa un registro determinado
	 */
	public function insertar() {
		
		$datos = $_REQUEST;

		if ($this->modelo->insertar($datos))
			$this->listar($this->mensaje_ingreso_ok, 1, $datos['pagina']);
		else
			$this->listar($this->mensaje_ingreso_error, 2, $datos['pagina']);
	}

	/**
	 * Se modifica un registro determinado
	 */
	public function modificar() {
		
		$datos = $_REQUEST;

		if ($this->modelo->noLoModificoOtroUsuario()) {
			if ($this->modelo->modificar($datos)) {
				$this->listar($this->mensaje_modificacion_ok, 1, $datos['pagina']);
			} else {
				$this->editar($datos, $this->mensaje_modificacion_error, 2);
			}
		} else {
			$this->editar($datos, $this->mensaje_modificacion_previa, 2);
		}
	}

	/**
	 * Se elimina un registro determinado
	 */
	public function eliminar() {
		
		$numero = LibreriaGeneral::recoge('numero', 0);
		$pagina = LibreriaGeneral::recoge('pagina');

		if ($this->modelo->eliminar($numero)) {
			$this->listar($this->mensaje_eliminacion_ok, 1, $pagina);
		} else {
			$this->listar($this->mensaje_eliminacion_error, 2, $pagina);
		}
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado() {
		parent::modificarEstadoBase();
	}
}
?>
