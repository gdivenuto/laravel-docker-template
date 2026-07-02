<?php
/**
 * Clase de controlador.
 *
 * @author Kaleb
 */
class BEVerificarDigitalizacionController extends BaseController
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
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['mostrarcontenidodirectorio'] = NIVEL_ACCESO_OPERADOR;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Instancio la vista y la muestro
		$vista = new BEVerificarDigitalizacionView($paramVista);
		$vista->vistaBuscador();
	}

	public function mostrarcontenidodirectorio($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$resultado['data'] = array();
			$resultado['error'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['numeroError'] = SessionController::get()->obtener('NUMERO_ERROR');
		} else {
			// Se recibe la info del expediente del buscador
			$f_anio   = Validator::get()->obtenerDefault($requestParams['f_anio']);
			$f_numero = Validator::get()->obtenerDefault($requestParams['f_numero']);
			$f_digito = Validator::get()->obtenerDefault($requestParams['f_digito']);

			$resultado = array();

			// Ejecuto la vista
			try	{
				// Se valida el contenido de la info recibida
				$f_anio   = Validator::get()->validar($f_anio, PATRON_NUMEROS, true, 'A&ntilde;o del expediente');
				$f_numero = Validator::get()->validar($f_numero, PATRON_NUMEROS, true, 'N&uacute;mero de expediente');
				//$f_digito = Validator::get()->validar($f_digito, PATRON_NUMEROS, true, 'D&iacute;gito del expediente');

				// La clave del expediente es "obligatoria". Si no me la proveen, simulo un resultado vacío.
				if ( is_null($f_anio) || $f_anio == '' ||
					 is_null($f_numero) || $f_numero == '' ||
					 is_null($f_digito) || $f_digito == '')
				{
					$resultado['data'] = array();  // Es un array vacio!!! No NULL.
				}
				else
				{
					// Nombre codificado del directorio del expediente del ejecutivo: AAAA-NNNNNN-D
					$nombre_codificado = $f_anio."-".substr(1000000+$f_numero, -6)."-".$f_digito;

					// Ruta del directorio del expediente del ejecutivo respectivo
					$directorio_expediente_ejecutivo = PATH_KRAKEN_EXPEDIENTES_DEPTO_EJECUTIVO.$f_anio."/".$nombre_codificado."/";

					$documentos = array();
					// Se verifica la existencia del directorio
					if (is_dir($directorio_expediente_ejecutivo)) {
						// Se escanea el directorio (se obtiene un array con los documentos que contiene)
					    $contenido_directorio = scandir($directorio_expediente_ejecutivo);

					    foreach ($contenido_directorio as $key => $value)
					    	if ( ! ( ($value == '.') || ($value == '..') ) )
					    		$documentos[] = array(
					    			// 16/07/2020 XXXX: se toma la fecha del documento
									'fecha' => date("d/m/Y", filemtime($directorio_expediente_ejecutivo.$value)),
					    			'archivo' => $value,
					    			'url' => URL_KRAKEN_SGL_EXPEDIENTES_EXPE_DE.$f_anio."/".$nombre_codificado."/".$value
					    		);
					}

					$resultado['data'] = $documentos;
				}
			}
			catch (Exception $ex) {
				$resultado['data'] = array();  // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}

		echo JsonHelper::get()->serializar($resultado);
	}
}
?>
