<?php
if (!isset($_SESSION))
	session_start();

require_once RUTA_MODELOS . "banca25.php";

require_once RUTA_VISTAS . "banca25/grilla.php";
require_once RUTA_VISTAS . "banca25/edicion.php";
require_once RUTA_VISTAS . "banca25/pdf_solicitudes.php";
require_once RUTA_VISTAS . "banca25/documentos.php";

class banca25_controller extends ControllerBase {

	protected $vista_listado_pdf;
	protected $vista_documentos;

	public function __construct() {

		parent::__construct();

		$this->rango_paginacion = 12;

		$this->campo_orden_por_defecto = 'id';

		$this->modelo = new banca25Model();

		$this->vista_grilla = new VistaBanca25Grilla();
		$this->vista_edicion = new VistaBanca25Edicion();
		$this->vista_listado_pdf = new VistaBanca25SolicitudesPDF();
		$this->vista_documentos = new VistaBanca25Documentos();
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
		$_SESSION['f_banca25'] = (LibreriaGeneral::recoge('limpiar') == 'si') 
			? '' : $_SESSION['f_banca25'];

		$f_fecha_desde = LibreriaGeneral::recoge('f_fecha_desde');
		$filtro['f_fecha_desde'] = ( isset($f_fecha_desde) && $this->esFechaValida($f_fecha_desde) ) 
			? $this->modelo->formatearFechaMySQL($f_fecha_desde) : '';
		
		$f_fecha_hasta = LibreriaGeneral::recoge('f_fecha_hasta');
		$filtro['f_fecha_hasta'] = ( isset($f_fecha_hasta) && $this->esFechaValida($f_fecha_hasta) ) 
			? $this->modelo->formatearFechaMySQL($f_fecha_hasta) : '';
		
		$filtro['f_texto'] = LibreriaGeneral::recoge('f_texto', (isset($_SESSION['f_banca25']['f_texto']) 
			? $_SESSION['f_banca25']['f_texto'] : ''));

		$filtro['f_tipo'] = LibreriaGeneral::recoge('f_tipo', 0);

		$_SESSION['f_banca25'] = $filtro;

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
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') 
				? 'desc' : 'asc';
		}

		$filtro['rango'] = $this->rango_paginacion;

		$this->modelo->setFiltro($filtro);

		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

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

		$this->modelo->setFiltro($filtro);

		$datos = $this->modelo->listar();
		
		$this->vista_grilla->mostrar($datos, $mensaje, $tipo_mensaje, $filtro);
	}

	public function editar($mensaje = '', $tipo_mensaje = '') {

		$id = LibreriaGeneral::recoge('id', 0);

		$datos = $this->modelo->obtenerRegistro($id);

		if ($datos['id']) {
			
			$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

			$datos = $this->retirarBarraInvertida($datos);
		} else {
			$datos['id'] = $id;
		}

		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}

    /**
     * Se guarda la fecha de Sesion y la clave del Exped/Nota
     */
    public function guardar() {

        $datos = $_REQUEST; 

        if ($this->modelo->guardar($datos))
            $this->listar("Los datos se registraron correctamente", 1, $datos['pagina']);
        else
            $this->listar("Error al registrar los datos.", 2, $datos['pagina']);
    }

	public function borrar($mensaje = '', $tipo_mensaje = '') {

		$solicitud_id = LibreriaGeneral::recoge('solicitud_id', 0);
		$pagina = LibreriaGeneral::recoge('pagina', 1);

		if ($this->modelo->borrar($solicitud_id))
            $this->listar("Los datos se han borrado correctamente", 1, $pagina);
        else
            $this->listar("Error al borrar los datos.", 2, $pagina);
	}

	public function generarInforme() {

		$f_fecha_desde = LibreriaGeneral::recoge('f_fecha_desde');
		$filtro['f_fecha_desde'] = isset($f_fecha_desde) ? $f_fecha_desde : '';
		
		$f_fecha_hasta = LibreriaGeneral::recoge('f_fecha_hasta');
		$filtro['f_fecha_hasta'] = isset($f_fecha_hasta) ? $f_fecha_hasta : '';
		
		$filtro['f_texto'] = LibreriaGeneral::recoge('f_texto', (isset($_SESSION['f_banca25']['f_texto']) 
			? $_SESSION['f_banca25']['f_texto'] : ''));

		$filtro['f_tipo'] = LibreriaGeneral::recoge('f_tipo', 0);

		$filtro['rango'] = 0;// a propósito para que retorne todos los registros
		
		$this->modelo->setFiltro($filtro);
		
		$listado = $this->modelo->listar(true);
		
		$this->vista_listado_pdf->mostrar($listado, $filtro);
	}

	public function mostrarDocumentos() {

		$dni = LibreriaGeneral::recoge('dni', 0);
		
		$this->vista_documentos->mostrar($dni);
	}
}
?>