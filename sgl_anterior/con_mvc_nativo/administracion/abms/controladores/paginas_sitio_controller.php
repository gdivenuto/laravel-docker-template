<?php
if (!isset($_SESSION))
	session_start();

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "paginas_sitio.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "paginas_sitio/edicion.php";

class paginas_sitio_controller extends ControllerBase {
	
	public function __construct() {
		
		parent::__construct();

		$this->campo_orden_por_defecto = 'id';

		// Se crea una instancia del modelo
		$this->modelo = new paginasSitioModel();

		// Se crea una instancia de la Vista
		$this->vista_edicion = new VistaPaginasSitioEdicion();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}

	public function editar($mensaje = '', $tipo_mensaje = '') {

		// Se obtienen las Categorías
		$categorias = $this->modelo->obtenerCategorias();
		
		$this->vista_edicion->mostrar($categorias, $mensaje, $tipo_mensaje);
	}

    public function guardar() {

		$datos = $_REQUEST;
		
		if ( $this->modelo->guardar($datos) ) {
			$this->editar("La configuraci&oacute;n de las p&aacute;ginas del sitio web se guard&oacute; con &eacute;xito.", 1);
		} else {
			$this->editar("Error al guardar la configuraci&oacute;n de las p&aacute;ginas del sitio web", 2);
		}
    }
    
}