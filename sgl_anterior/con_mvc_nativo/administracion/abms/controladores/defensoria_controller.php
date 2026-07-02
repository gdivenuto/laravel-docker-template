<?php
if (!isset($_SESSION))
	session_start();

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "defensoria_dmz.php";
require_once RUTA_MODELOS . "defensoria.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "defensoria/grilla.php";
require_once RUTA_VISTAS . "defensoria/pdf_inscriptos.php";
require_once RUTA_VISTAS . "defensoria/pdf_ficha.php";
require_once RUTA_VISTAS . "defensoria/documentos.php";
require_once RUTA_VISTAS . "defensoria/habilitacion.php";

class defensoria_controller extends ControllerBase {

	protected $modelo_defensoria_dmz;
	protected $modelo_defensoria;

	protected $vista_listado_pdf;
	protected $vista_ficha_pdf;
	protected $vista_documentos;
	protected $vista_habilitacion;

	public function __construct() {

		parent::__construct();

		$this->rango_paginacion = 12;

		$this->campo_orden_por_defecto = 'fecha';

		$this->modelo_defensoria_dmz = new defensoriaDmzModel();
		$this->modelo_defensoria = new defensoriaModel();

		$this->vista_grilla = new VistaInscripcionesGrilla();
		$this->vista_listado_pdf = new VistaInscripcionesPDF();
		$this->vista_ficha_pdf = new VistaInscripcionFichaPDF();
		$this->vista_documentos = new VistaInscripcionesDocumentos();
		$this->vista_habilitacion = new VistaInscripcionesHabilitacion();
	}

	
	/**
	 * Se listan los registros
	 * @param  string $mensaje
	 * @param  string $tipo_mensaje
	 * @return html
	 */
	public function listar($mensaje = '', $tipo_mensaje = '') {

		$filtro = Array();

		// Si se recibe la marca para limpiar, se limpia el filtro en la sesión, sino se mantienen
		$_SESSION['f_defensoria'] = (LibreriaGeneral::recoge('limpiar') == 'si') 
			? '' : $_SESSION['f_defensoria'];

		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		$filtro['f_fecha'] = (isset($f_fecha) && $this->esFechaValida($f_fecha)) 
			? $this->modelo_defensoria_dmz->formatearFechaMySQL($f_fecha) : '';

		$filtro['f_texto'] = LibreriaGeneral::recoge('f_texto', (isset($_SESSION['f_defensoria']['f_texto']) 
			? $_SESSION['f_defensoria']['f_texto'] : ''));

		$filtro['f_habilitados'] = LibreriaGeneral::recoge('f_habilitados', 0);

		$_SESSION['f_defensoria'] = $filtro;

		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ($campo_orden != '') {
			$filtro['campo_orden'] = $campo_orden;
		} else {
			$filtro['campo_orden'] = $this->campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}

		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');

		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') 
				? 'desc' : 'asc';
		}

		$filtro['rango'] = $this->rango_paginacion;

		$this->modelo_defensoria_dmz->setFiltro($filtro);

		$filtro['cantidad'] = $this->modelo_defensoria_dmz->obtenerCantidad();

		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		$filtro['pagina'] = LibreriaGeneral::recoge('pagina');

		if (!$filtro['pagina']) {
			$filtro['pagina'] = ($filtro['nro_paginas'] > 0) ? $filtro['nro_paginas'] : 1;

			if ($filtro['cantidad'] < $filtro['rango'])
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			else
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
		} else
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];

		$filtro['pagina_ant'] = $filtro['pagina'] - 1;
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;

		$this->modelo_defensoria_dmz->setFiltro($filtro);

		$datos = $this->modelo_defensoria_dmz->listar();
		
		$this->vista_grilla->mostrar($datos, $mensaje, $tipo_mensaje, $filtro);
	}

	public function generarInforme() {

		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		$filtro['f_fecha'] = (isset($f_fecha) && $this->esFechaValida($f_fecha)) 
			? $this->modelo_defensoria_dmz->formatearFechaMySQL($f_fecha) : '';

		$filtro['f_texto'] = LibreriaGeneral::recoge('f_texto', (isset($_SESSION['f_defensoria']['f_texto']) 
			? $_SESSION['f_defensoria']['f_texto'] : ''));

		$filtro['f_habilitados'] = LibreriaGeneral::recoge('f_habilitados', 0);

		$filtro['rango'] = 0;// a propósito para que retorne todos los registros
		
		$this->modelo_defensoria_dmz->setFiltro($filtro);
		
		$listado = $this->modelo_defensoria_dmz->listar();
		
		$this->vista_listado_pdf->mostrar($listado);
	}

	public function generarFicha() {

		$id = LibreriaGeneral::recoge('id');

		$registro = $this->modelo_defensoria_dmz->obtenerRegistro($id);

		$this->vista_ficha_pdf->mostrar($registro);
	}

	public function mostrarDocumentos() {

		$dni = LibreriaGeneral::recoge('dni', 0);
		
		$this->vista_documentos->mostrar($dni);
	}

	public function editarHabilitacion() {

		$candidato_id = LibreriaGeneral::recoge('id', 0);
		$pagina = LibreriaGeneral::recoge('pagina');
		$datos = $this->modelo_defensoria->obtenerRegistro($candidato_id);

		if (! isset($datos))
			$datos['candidato_id'] = $candidato_id;
		
		$this->vista_habilitacion->mostrar($datos, $pagina);
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 * del Candidato en la base 'hcd', para evitar la sobreescritura en la sincronización con la DMZ
	 */
	public function modificarEstado() {
		
		$candidato_id = LibreriaGeneral::recoge('candidato_id');
		$habilitado = LibreriaGeneral::recoge('habilitado');
		$motivo = LibreriaGeneral::recoge('motivo');
		$pagina = LibreriaGeneral::recoge('pagina');

		if ($this->modelo_defensoria->modificarEstado($candidato_id, $habilitado, $motivo))
			$this->listar($this->mensaje_modificacion_estado_ok, 1, $pagina);
		else
			$this->listar($this->mensaje_modificacion_estado_error, 1, $pagina);
	}

}
?>