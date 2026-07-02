<?php
/**
 * Clase base para todos las vistas del frontend.
 * Cada vista dispone de 'acciones' (patrón strategy), que representan las diferentes facetas
 * de la interfase según la interacción del usuario o las redirecciones del controlador que las
 * invoque.
 * 
 * @author Kaleb
 */
abstract class BaseView {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	public $baseTemplatePath; 	//!< Ruta base donde se encuentran los templates
	public $baseUrl;			//!< URL base para la interfaz.
	
	public $dataTituloApp;		//!< Titulo de la aplicacion
	public $dataTitulo;			//!< Titulo de la vista
	public $dataSubtitulo;		//!< Subtitulo de la vista
	public $dataTexto;			//!< Texto introductorio de la vista
	public $dataUsuario;		//!< Instancia del usuario actual.
	public $dataMensajeOk;		//!< Mensaje de confirmación que debe mostrarse en la vista.
	public $dataMensajeError;	//!< Mensaje de error que debe mostrarse en la vista.

	public $dataAutorApp;		//!< Autor de la aplicacion

	public $dataTipoCabecera;   //!< De que tipo será la cabecera de cada vista (VISTA_CABECERA_VACIA | VISTA_CABECERA_ALERT | VISTA_CABECERA_MODAL).

	public $data;				//!< Array asociativo con toda la información necesaria para renderizar la vista.

	public $accionVista;		//!< Acción de la vista (BaseViewAction) que se ejecutara.
	
	// ************************************************************************
	// Definición de Métodos que requieren implementación *********************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	
	/**
	 * Constructor de clase.
	 * @param array $viewData Array asociativo con toda la información necesaria para renderizar la vista.
	 */
	public function __construct($viewData)
	{
		// Analizo el array asociativo de información para la vista. Aquellos parametros relevantes
		// los paso a los atributos. El resto queda en el array para que la vista los utilice (en realidad
		// las BaseViewAction de la vista)
		$this->dataTituloApp = (array_key_exists('titulo_app', $viewData)) ? $viewData['titulo_app'] : "Sin nombre";
		$this->dataTitulo = (array_key_exists('titulo', $viewData)) ? $viewData['titulo'] : "Sin t&iacute;tulo";
		$this->dataSubtitulo = (array_key_exists('subtitulo', $viewData)) ? $viewData['subtitulo'] : "Sin subt&iacute;tulo";
		$this->dataTexto = (array_key_exists('texto', $viewData)) ? $viewData['texto'] : "Sin texto";
		$this->dataUsuario = (array_key_exists('usuario', $viewData)) ? $viewData['usuario'] : null;
		$this->dataMensajeOk = (array_key_exists('mensaje_ok', $viewData)) ? $viewData['mensaje_ok'] : '';
		$this->dataMensajeError = (array_key_exists('mensaje_error', $viewData)) ? $viewData['mensaje_error'] : '';
		$this->dataNumeroError = (array_key_exists('numero_error', $viewData)) ? $viewData['numero_error'] : '';
		$this->dataAutorApp = (array_key_exists('autor_app', $viewData)) ? $viewData['autor_app'] : "Sin autor";

		$this->dataTipoCabecera = (array_key_exists('tipo_cabecera', $viewData)) ? $viewData['tipo_cabecera'] : VISTA_CABECERA_VACIA;

		$this->data = $viewData;

		$this->accionVista = null; // No asigno una accion para la vista.

		$this->baseTemplatePath = './templates/';
		$this->baseUrl = './';
	}

	/**
	 * Vista por defecto para cualquier vista.
	 */
	public function vistaDefault()
	{
		// Asigno la accion
		$this->accionVista = new BaseViewAction($this);

		// Muestro la plantilla
		$this->accionVista->entregar('base.php');
	}
}
?>