<?php
/**
 * Clase de controlador de Gestion de Plantillas
 *
 * @author XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_PLANTILLAS');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_PLANTILLAS');

class BEPlantillasController extends BaseController
{
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct()
	{
		// Llamada al constructor del padre
		parent::__construct();

		// Seteo de ruta base de la interfaz
		$this->baseUrl = URL_KRAKEN_HTML_BACKEND;

		// Nombre del módulo al que corresponde el controlador
		$this->nombreModulo = 'EXPEDIENTES';

		// Determino las acciones válidas y su nivel de acceso mínimo requerido
		$this->accionesPermitidas['getform'] = NIVEL_ACCESO_CONCEJAL;//NIVEL_ACCESO_OPERADOR;
		// 2023-06-07: Se modificó el nivel de acceso para Concejales, para que puedan utilizar las Plantillas
	}

	/**
	 * Esta acción del controlador permite obtener el contenido del fomulario de edicion dinámico de una plantilla.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function getform($requestParams)
	{
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = [];

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {

			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['data'] = null;

		} else {

			// Saneo parametros
			$requestParams = $this->sanearConjuntoParametros($requestParams);

			// Parametros de filtro
			$f_plantilla = Validator::get()->obtenerDefault($requestParams['f_plantilla'], '');
			$f_plantilla = Validator::get()->validar($f_plantilla, '/^[a-z0-9\-_]{1,100}$/i', false, 'Identificador de Plantilla');

			try {
				$f_plantilla = sprintf('%s%s.xml', PATH_SGL_DOC_PLANTILLAS, $f_plantilla);
				$templator = new Templator($f_plantilla);

				$resultado['estado'] = 'OK';
				$resultado['mensaje'] = 'Formulario de plantilla obtenido con éxito.';
				$resultado['data'] = [];

				$resultado['data']['plantilla'] = $templator->plantilla_config->plantilla;
				$resultado['data']['formulario'] = $templator->generarFormularioHTML([
		            'form_generar_tag' => false,
		            'form_con_submit' => false,
		            'form_con_cabecera' => false
		        ]);

			} catch (Exception $ex) {
				$resultado['estado'] = 'ERROR';
				$resultado['mensaje'] = $ex->getMessage();
				$resultado['data'] = null;
			}
		}
		echo json_encode($resultado);
	}

}
?>
