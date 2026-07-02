<?php
/**
 * Clase de controlador de Marca de Comisiones (opción Tareas del menú principal).
 *
 * @author XXXX, XXXX
 */
class BEMarcacomisionController extends BaseController
{
	// ************************************************************************
	// Definición de Atributos  ***********************************************
	// ************************************************************************
	private $expediente_a_marcar = Array();
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	private $defaultOffsetAnios = -10;// Cantidad de años por defecto con los que se setean los filtros de fecha Desde

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
		$this->accionesPermitidas['mostrar'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['save'] = NIVEL_ACCESO_OPERADOR;
		$this->accionesPermitidas['limpiarmarcas'] = NIVEL_ACCESO_OPERADOR;
	}

	/**
	 * Se inicializan los parámetros del criterio de búsqueda, con valores por defecto
	 *
	 * @return array Conjunto de parámetros con sus valores por defecto
	 */
	private function setearValoresPorDefectoParaVista()
	{
		$resultado = array();
		$resultado['f_fecha_desde']   = $this->obtenerFechaAniosAtrasConDiaInicial($this->defaultOffsetAnios);//getFechaOffsetAnios
		$resultado['f_fecha_hasta']   = date("Y-m-d");
		$resultado['f_fecha_listado'] = date("Y-m-d");// Utilizada para calcular los días en comisión
		$resultado['f_comision']      = 0;

		return $resultado;
	}

	/**
	 * Verifica que los parámetros posean un valor, sino se les asigna un valor por defecto
	 *
	 * @param  array $requestParams Parámetros a verificar
	 * @return array                Parámetros verificados
	 */
	private function obtenerParametros($requestParams)
	{
		$resultado = array();

		// la fecha Desde, por defecto es 10 años menor que la fecha actual
		$resultado['f_fecha_desde'] = (isset($requestParams['f_fecha_desde'])) ? $requestParams['f_fecha_desde'] : $this->obtenerFechaAniosAtrasConDiaInicial($this->defaultOffsetAnios);//getFechaOffsetAnios
		// la fecha Hasta, por defecto es la fecha actual
		$resultado['f_fecha_hasta'] = (isset($requestParams['f_fecha_hasta'])) ? $requestParams['f_fecha_hasta'] : date("Y-m-d");
		// la fecha del Listado, por defecto es la fecha actual
		$resultado['f_fecha_listado'] = (isset($requestParams['f_fecha_listado'])) ? $requestParams['f_fecha_listado'] : date("Y-m-d");

		// Se recibe una Comisión
		$resultado['f_comision'] = (isset($requestParams['f_comision'])) ? $requestParams['f_comision'] : null;

		return $resultado;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams)
	{
		// Antes que nada verifico el nivel de acceso
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista (HEREDADO)
		$paramVista = $this->generarParametrosVista();

		// Se sanean los parámetros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// La búsqueda se inicializa con valores por defecto
		$parametros_marca_comision = $this->setearValoresPorDefectoParaVista();

		// Se recibe (o no) un parámetro para verificar si se pulsó el botón Restablecer
		$f_restablecer = (isset($requestParams['f_restablecer'])) ? $requestParams['f_restablecer'] : '';

		// Si se desea restablecer el criterio de búsqueda
		if ($f_restablecer != '') {
			try {
				// Se limpian los parámetros en la sesión
				SessionController::get()->eliminar('parametros_marca_comision');
			}
			catch (Exception $ex) {
				// Si falla, vuelve al home y elimina la busqueda en sesión
				SessionController::get()->eliminar('parametros_marca_comision');
				SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
				$this->redireccionar('home', 'view');
			}
		}
		else // si NO se desea restablecer
		{
			// Si por lo menos se recibe la fecha desde y la fecha hasta
			if (isset($requestParams['f_fecha_desde']) && $requestParams['f_fecha_desde'] != '' &&
				isset($requestParams['f_fecha_hasta']) && $requestParams['f_fecha_hasta'] != '' )
			{
				try	{
					// Se limpian los parámetros en la sesión
					SessionController::get()->eliminar('parametros_marca_comision');

					// Se obtienen los parámetros recibidos, verificando que posean un valor
					$parametros_marca_comision = $this->obtenerParametros($requestParams);
				}
				catch (Exception $ex) {
					// Si falla, vuelve al home y elimina la busqueda en sesión
					SessionController::get()->eliminar('parametros_marca_comision');
					SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
					$this->redireccionar('home', 'view');
				}
			}
			else // Si NO se recibieron las fechas desde y hasta
			{
				try {
					// Si existen los parámetros en la sesión
					if (SessionController::get()->existe('parametros_marca_comision')) {
						// Se obtienen desde la sesión
						$parametros_marca_comision = SessionController::get()->obtener('parametros_marca_comision');
					}
				} catch (Exception $ex) {
					// Si falla, vuelve al home y elimina la busqueda en sesión
					SessionController::get()->eliminar('parametros_marca_comision');
					SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
					$this->redireccionar('home', 'view');
				}
			}
		}

		// 25/02/2021 XXXX, se ordenan por Descripción
		// Se obtienen todas las Comisiones
		$paramVista['listado_comisiones'] = NG::expedientesParam()->obtenerLugares(
			'C', null, null, null, null, null, null, null, null, '1', null, array('descripcion_grp'), null, null);

		// Se setea el conjunto de parámetros para la vista
		// Si hubo un error, nunca se llega a ejecutar este código porque se sale por los "catch".
		$paramVista['parametros_marca_comision'] = $parametros_marca_comision;

		// Se guardan en sesion (siempre... en un caso es redundante)
		SessionController::get()->guardar('parametros_marca_comision', $parametros_marca_comision);

		// Instancio la vista y la muestro
		$vista = new BEMarcaComisionView($paramVista);
		$vista->vistaListadoMarcaComision();
	}

	/**
	 * Se muestran los expedientes de una Comisión en un período de fechas determinado
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function mostrar($requestParams)
	{
		// Antes que nada verifico el nivel de acceso
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista (HEREDADO)
		$paramVista = $this->generarParametrosVista();

		// Se sanean los parámetros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Se limpian los parámetros en la sesión
		SessionController::get()->eliminar('parametros_marca_comision');

		// Se obtienen los parámetros recibidos, verificando que posean un valor
		$parametros = $this->obtenerParametros($requestParams);

		// Verifico aquellos parametros que puedan ser inyectables con SQL
		$parametros['f_comision'] = ($parametros['f_comision']) ? Validator::get()->validar($parametros['f_comision'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo de la Comisi&oacute;n') : null;

		// Consulta de expedientes con el criterio respectivo
		$expedientes = NG::expedientes()->obtenerExpedientesParaMarcarComision(
			// Parametros
	        $parametros['f_fecha_desde'],
			$parametros['f_fecha_hasta'],
			$parametros['f_comision']
		);

		// Por cada expediente
		foreach ($expedientes as $exp) {
			// Se obtiene el último giro del expediente
			$ultimo_giro = NG::expedientes()->obtenerUltimoGiro(
				// Parametros
				$exp->anio,
				$exp->tipo,
				$exp->numero,
				$exp->cuerpo,
				$exp->alcance,
				// Control de consulta
				array('anio desc', 'tipo desc', 'numero desc', 'cuerpo desc', 'alcance desc', 'orden_giro desc'));

			// Si posee por lo menos un Giro
			if ( !is_null($ultimo_giro) ) {
				$fecha_entrada_ultimo_giro = $this->formatearFecha($ultimo_giro->fecha_entrada_giro);
				$fecha_del_listado = $this->formatearFecha($parametros['f_fecha_listado']);

				// Se calcula el número de días en comisión
				$exp->ro_cantidad_dias_en_comision = $this->calcularDiasEnComision(
					$fecha_entrada_ultimo_giro,
					$fecha_del_listado,
					$exp->anio,
					$exp->tipo,
					$exp->numero,
					$exp->cuerpo,
					$exp->alcance,
					$ultimo_giro->orden_giro);
			} else
				$exp->ro_cantidad_dias_en_comision = -1;
		}

		// Se asignan los expedientes obtenidos a la Vista
		$paramVista['expedientes'] = $expedientes;
		// y la cantidad obtenida de expedientes
		$paramVista['recordsTotal'] = count($expedientes);

		// 25/02/2021 XXXX, se ordenan por Descripción
		// Se obtienen todas las Comisiones
		$paramVista['listado_comisiones'] = NG::expedientesParam()->obtenerLugares(
			'C', null, null, null, null, null, null, null, null, '1', null, array('descripcion_grp'), null, null);

		// Se setea el conjunto de parámetros para la vista
		$paramVista['parametros_marca_comision'] = $parametros;

		// Se guardan en sesion (siempre... en un caso es redundante)
		SessionController::get()->guardar('parametros_marca_comision', $parametros);

		// Instancio la vista y la muestro
		$vista = new BEMarcaComisionView($paramVista);
		$vista->vistaListadoMarcaComision();
	}

	/**
	 * Se guardan las Marcas de los expedientes elegidos
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function save($requestParams)
	{
		// Antes que nada verifico el nivel de acceso
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		try {
			// Se obtiene el usuario que realiza la modificación de la marca comisión
			$usuario = $this->obtenerUsuarioActual();

			// Se recorren los expedientes
		    for ( $i = 0; $i < $requestParams['cantidad_expedientes']; $i++ ) {

		    	// Se actualizarán sólo aquellos que hayan cambiado su Marca
				if ( $requestParams['marca_modificada'.$i] === 'true' ) {
					// Se separa la clave del expediente
					$partes_clave_expediente = explode('-', $requestParams['clave_expediente'.$i]);

					$anio    = $partes_clave_expediente[0];
					$tipo    = $partes_clave_expediente[1];
					$numero  = $partes_clave_expediente[2];
					$cuerpo  = $partes_clave_expediente[3];
					$alcance = $partes_clave_expediente[4];

					$marca_comision = $requestParams['i_tipo_marca'.$i];

					$expediente = NG::expedientes()->obtenerExpediente($anio, $tipo, $numero, $cuerpo, $alcance);
					NG::expedientes()->marcarComision($expediente, $marca_comision, $usuario->id_usuario);
				}
		    }

		    // Genero respuesta
			$resultado['estado'] = 'OK';
			$resultado['f_fecha_desde'] = $requestParams['f_fecha_desde'];
            $resultado['f_fecha_hasta'] = $requestParams['f_fecha_hasta'];
            $resultado['f_fecha_listado'] = $requestParams['f_fecha_listado'];
            $resultado['f_comision'] = $requestParams['f_comision'];
			$resultado['mensaje'] = 'Se han guardado las Marcas en la Comisi&oacute;n.';
		}
		catch (Exception $e) {
			$resultado['estado'] = 'ERROR';
			$resultado['mensaje'] = 'No se han guardado las Marcas. Causa: '.$e->getMessage();
			$resultado['data'] = ERROR_CONTROLLER_GENERICO;
		}

		// Para que retorne el resultado de la operación en formato JSON
		header('Content-Type: application/json');
		echo JsonHelper::get()->serializar($resultado);
	}

	/**
	 * Se limpian las Marcas de los expedientes en una Comisión y un período de fechas respectivos
	 * @param  [type] $requestParams [description]
	 * @return [type]                [description]
	 */
	public function limpiarmarcas($requestParams)
	{
		// Antes que nada verifico el nivel de acceso
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista (HEREDADO)
		$paramVista = $this->generarParametrosVista();

		// Se sanean los parámetros (HEREDADO)
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Se obtienen los parámetros recibidos, verificando que posean un valor
		$parametros = $this->obtenerParametros($requestParams);

		// Verifico aquellos parametros que puedan ser inyectables con SQL
		$parametros['f_comision'] = ($parametros['f_comision']) ? Validator::get()->validar($parametros['f_comision'], PATRON_ALFANUM_EXT, true, 'C&oacute;digo de la Comisi&oacute;n') : null;

		// Se limpian las Marcas de los expedientes en una Comisión y un período de fechas respectivos
		NG::expedientes()->limpiarMarcas(
			$parametros['f_fecha_desde'],
			$parametros['f_fecha_hasta'],
			$parametros['f_comision']
		);

		$this->mostrar($requestParams);
	}
}
?>
