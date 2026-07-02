<?php
if (!isset($_SESSION))
	session_start();

require_once RUTA_MODELOS . "Remitente.php";
require_once RUTA_MODELOS . "Provincia.php";

require_once RUTA_VISTAS . "remitente/grilla.php";
require_once RUTA_VISTAS . "remitente/edicion.php";
//require_once RUTA_VISTAS . "remitente/informe_pdf.php";

class RemitenteController extends ControladorBase {
	
	private $modelo_provincia;

	public function __construct() {
		parent::__construct();

		$this->campo_orden_por_defecto = 'id';

		$this->modelo = new RemitenteModel();
		$this->modelo_provincia = new ProvinciaModel();

		$this->vista_grilla = new VistaRemitenteGrilla();
		$this->vista_edicion = new VistaRemitenteEdicion();
		//$this->vista_pdf = new VistaRemitenteInformePDF();
	}

	public function guardarRegistroOriginal($original) {

		$_SESSION['id_original'] = $original['id'];
		$_SESSION['provincia_id_original'] = $original['provincia_id'];
		$_SESSION['apellido_original'] = $original['apellido'];
		$_SESSION['nombre_original'] = $original['nombre'];
		$_SESSION['dni_original'] = $original['dni'];
		$_SESSION['localidad_original'] = $original['localidad'];
		$_SESSION['codigo_postal_original'] = $original['codigo_postal'];
		$_SESSION['direccion_calle_original'] = $original['direccion_calle'];
		$_SESSION['direccion_numero_original'] = $original['direccion_numero'];
		$_SESSION['direccion_piso_original'] = $original['direccion_piso'];
		$_SESSION['direccion_departamento_original'] = $original['direccion_departamento'];
		$_SESSION['tel_fijo_cod_area_original'] = $original['tel_fijo_cod_area'];
		$_SESSION['tel_fijo_numero_original'] = $original['tel_fijo_numero'];
		$_SESSION['movil_cod_area_original'] = $original['movil_cod_area'];
		$_SESSION['movil_numero_original'] = $original['movil_numero'];
		$_SESSION['mail_original'] = $original['mail'];
		$_SESSION['fecha_alta_original'] = $original['fecha_alta'];
		$_SESSION['observaciones_original'] = $original['observaciones'];
		$_SESSION['habilitado_original'] = $original['habilitado'];
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
			$filtro['inicio'] = 0;
			$filtro['pagina'] = 1;
		} elseif ($filtro['valor_buscado'] == '') {
			$filtro['inicio'] = ($filtro['pagina'] - 1) * $filtro['rango'];
		}

		$filtro['pagina_ant'] = $filtro['pagina'] - 1;
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;

		$this->modelo->setFiltro($filtro);

		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		$this->modelo->setFiltro($filtro);

		$datos['info'] = $this->modelo->listar();

		$this->vista_grilla->mostrar($datos, $mensaje, $tipo_mensaje, $filtro);
	}

	/**
	 * Se edita un registro de un Id determinado
	 */
	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = ''): void {
		
		if ($datos_formulario === null) {
			$codigo = LibreriaGeneral::recoge('id', 0);

			$datos = $this->modelo->obtenerRegistro($codigo);

			if ($datos['id']) {
				$this->guardarRegistroOriginal($datos);

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);
			} else {
				$datos = null;
			}
		} else {
			$datos_formulario['fecha_alta'] = ($datos_formulario['fecha_alta'] != '') ? $this->modelo->formatearFechaMySQL($datos_formulario['fecha_alta']) : '';

			$datos = $datos_formulario;
		}

		$datos['provincias'] = $this->modelo_provincia->listar();
		
		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}

	/**
	 * Se ingresa un registro determinado
	 */
	public function insertar() {
		
		$datos = $_REQUEST;

		if ($this->modelo->existe($datos)) {
			$this->listar($this->mensaje_registro_existente, 2, $datos['pagina']);
		} elseif ($this->modelo->insertar($datos)) {
			$this->listar($this->mensaje_ingreso_ok, 1, $datos['pagina']);
		} else {
			$this->listar($this->mensaje_ingreso_error, 2, $datos['pagina']);
		}
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
		
		$id = LibreriaGeneral::recoge('id', 0);
		$pagina = LibreriaGeneral::recoge('pagina');

		if ($this->modelo->eliminar($id)) {
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

	/**
	 *  Se obtienen los datos en base a su nombre
	 */
	public function obtenerInfo() {
		$nombre = LibreriaGeneral::recoge('nombre');

		$info = $this->modelo->obtenerInfo($nombre);

		header('Content-type: application/json; charset=utf-8');
		echo json_encode($info);
	}

	/**
	 * Se genera el listado en formato PDF
	 */
	public function generarResumenCtaPDF() {

		$filtro['valor_buscado'] = LibreriaGeneral::recoge('valor_buscado');

		$listado = $this->modelo->obtenerListadoParaPDF($filtro);

		$this->vista_pdf->mostrar($listado);
	}
}
?>
