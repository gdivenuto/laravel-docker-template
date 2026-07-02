<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "contenidos_web.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "contenidos_web/edicion.php";
require_once RUTA_VISTAS . "contenidos_web/formato_pdf.php";

class contenidos_web_controller extends ControllerBase {
	
	public function __construct() {
		
		parent::__construct();

		$this->campo_orden_por_defecto = 'id';

		// Se crea una instancia del modelo
		$this->modelo = new contenidosWebModel();

		// Se crea una instancia de la Vista
		$this->vista_edicion = new VistaContenidosWebEdicion();
		$this->vista_formato_pdf = new VistaFormatoPdf();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	/**
	 * [editar description]
	 * @param  [type] $datos_formulario [description]
	 * @param  string $mensaje          [description]
	 * @param  string $tipo_mensaje     [description]
	 * @return [type]                   [description]
	 */
	public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '') {

		// Si NO se viene del formulario de edición por un error
		if ($datos_formulario === null) {

			$id = LibreriaGeneral::recoge('id', 0);

			$datos = $this->modelo->obtenerRegistro($id);
		} else {
			// Si se vuelve de la edición
			$datos = $datos_formulario;
		}

		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}

	/**
	 * [insertar description]
	 * @return [type] [description]
	 */
    public function insertar() {

		$datos = $_REQUEST;
		
		if ( $this->modelo->insertar($datos) ) {
			
			// Se obtiene el Id recién registrado
			$datos['id'] = $this->modelo->obtenerUltimoId();

			$this->editar($datos, "El contenido se ingres&oacute; con &eacute;xito.", 1);
		} else {
			$this->editar($datos, "Error al ingresar el contenido.", 2);
		}
	}

	/**
	 * [modificar description]
	 * @return [type] [description]
	 */
    public function modificar() {

		$datos = $_REQUEST;
		
		if ( $this->modelo->modificar($datos) ) {
			$this->editar($datos, "El contenido se modific&oacute; con &eacute;xito.", 1);
		} else {
			$this->editar($datos, "Error al modificar el contenido.", 2);
		}
    }

	/**
	 * Se genera un PDF sólo para pruebas
	 */
	public function generarPdf()
	{
		$id = LibreriaGeneral::recoge('id', 0);

		$datos = $this->modelo->obtenerRegistro($id);
		
		$this->vista_formato_pdf->mostrar($datos);
	}
}