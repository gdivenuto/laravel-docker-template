<?php
/**
 * Clase base para todos los controladores del backend. Posee la funcionalidad base para todos los controladores,
 * los cuales se suponen deben heredar todos de esta clase.
 *
 * @author XXXX
 */

define('ERROR_CONTROLLER_GENERICO', 300);
define('ERROR_SIN_SESION', 301);
define('ERROR_NIVEL_INSUFICIENTE', 302);

abstract class BaseController {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $mensajeOk;			//!< Mensaje de confirmación que debe mostrarse en la vista.
	protected $mensajeError;		//!< Mensaje de error que debe mostrarse en la vista.
	protected $numeroError;			//!< Numero de error que debe mostrarse en la vista.

	protected $accionesPermitidas;	//!< Array de acciones permitidas para el controlador

	protected $nombreModulo;		//!< Nombre del módulo al que pertenece el controlador.
	protected $nivelAccesoMinimo; 	//!< Nivel de acceso mínimo requerido para poder utilizar el controlador.

	protected $baseUrl;				//!< URL base para las redirecciones dentro de la interfaz.

	// Vector con el rango de días, para el cálculo de días en comisión
	protected $vector_rango_de_dias = Array();

	// Parámetros a excluir del criterio de búsqueda
	protected $parametros_excluidos;

	protected $registros_por_pagina;

	// ************************************************************************
	// Definición de Métodos que requieren implementación *********************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct()
	{
		// Inicio de sesion.
		SessionController::get()->iniciarSesion();

		// Inicializo parametros del controlador.
		$this->accionesPermitidas = array();

		// Inicializo parámetros a excluir del criterio de búsqueda
		$this->parametros_excluidos = array();

		// URL Base (utilizado en redirecciones dentro de la interfaz)
		$this->baseUrl = './';

		// Cantidad de registros visualizados por página
		$this->registros_por_pagina = 15;
	}

	/**
	 * Asigna a los atributos mensajeOk y mensajeError cualquier mensaje que pueda estar
	 * esperando de ser mostrado en variables de sesion. Despues de esto, los elimina de la
	 * sesion para que no se vuelvan a mostrar.
	 */
	public function obtenerMensajesInterfazDesdeSesion()
	{
		// Obtengo mensajes de confirmacion o error, si es que hay alguno
		if (SessionController::get()->existe('MENSAJE_OK'))
			$this->mensajeOk = SessionController::get()->obtener('MENSAJE_OK');
		else
			$this->mensajeOk = '';
		if (SessionController::get()->existe('MENSAJE_ERROR')) {
			$this->mensajeError = SessionController::get()->obtener('MENSAJE_ERROR');
			$this->numeroError = SessionController::get()->obtener('NUMERO_ERROR');
		}
		else {
			$this->numeroError = -1;
			$this->mensajeError = '';
		}
		// Una vez que tengo los mensajes capturados, los elimino de la sesion
		SessionController::get()->eliminar('MENSAJE_OK');
		SessionController::get()->eliminarError();
	}

	/**
	 * Valida si la acción que se desea ejecutar es válida. NO VERIFICA EL NIVEL DE ACCESO DE LA ACCION!!!
	 * @param  string $nombreAccion Identificador de la acción a ejecutar.
	 * @return bool         True en caso de ser una acción válida, false en caso contrario.
	 */
	public function validarAccion($nombreAccion)
	{
		// Verifico la exstencia de la accion. NO VERIFICA EL NIVEL DE ACCESO DE LA ACCION!!!
		return array_key_exists($nombreAccion, $this->accionesPermitidas);
	}

	/**
	 * Redirecciona, utilizando una respuesta http 302 (Location) a otro controlador y acción.
	 * @param  string $controlador Nombre del controlador. Si no se especifica, asume 'home'.
	 * @param  string $accion      Acción del controlador destino. Si no se especifica, asume 'view'.
	 * @param  mixed $extraParams  Parametros adicionales de la redireccion. Puede ser un string (url) o un array con la forma $extraParams['key']='value'
	 */
	public function redireccionar($controlador = '', $accion = '', $extraParams = '')
	{
		$controlador = ($controlador == '') ? 'home' : $controlador;
		$accion = ($accion == '') ? 'view' : $accion;

		if (is_array($extraParams)) {
			$aux = array();
			foreach ($extraParams as $key => $value)
				$aux[] = sprintf('%s=%s', $key, $value);
			$params = join('&', $aux);
		} else
			$params = $extraParams;

		if ($params != '' )
			$params = '&'.$params;

		header('Location: '.$this->baseUrl.'index.php?c='.$controlador.'&a='.$accion.$params);
		exit(); // Termino la ejecución del script
	}

	/**
	 * Obtiene de la sesion actual, el usuario validado (o null si no hay sesion iniciada).
	 * @return Usuario Usuario actual validado, o null si no hay sesion iniciada.
	 */
	public function obtenerUsuarioActual()
	{
		if (SessionController::get()->existe('USUARIO'))
			return SessionController::get()->obtenerSerializado('USUARIO', new Usuario());
		else
			return null;
	}

	/**
	 * Valida si las credenciales de usuario actuales son válidas.
	 * @return bool True si las credenciales actuales son válidas, false en caso contrario (también es false si no hay credenciales registradas).
	 */
	public function usuarioValido()
	{
		if (SessionController::get()->existe('USUARIO'))
		{
			$usuario = SessionController::get()->obtenerSerializado('USUARIO', new Usuario());
			return (NG::seguridad()->validarUsuario($usuario) !== null);
		}
		else
			return false;
	}

	/**
	 * Valida el nivel de un usuario determinado con respecto a lo requerido para el controlador (nombreModulo + nivelAccesoMinimo).
	 * En caso de fallo, redirecciona al home con el mensaje correspondiente.
	 * @param  boolean $redireccionHome Si es TRUE, redirecciona al usuario al home del sitio.
	 * @param  string $nombreAccion Nombre de la acción para la cual se desea verificar el nivel de acceso del usuario
	 */
	public function verificarNivelAccesoUsuario($nombreAccion, $redireccionHome = true)
	{
		if ($this->usuarioValido())	{
			$usuario = $this->obtenerUsuarioActual();
			if (!NG::seguridad()->validarPermisosUsuario($usuario, $this->nombreModulo, $this->accionesPermitidas[$nombreAccion])) {
				SessionController::get()->guardarError('Nivel de acceso insuficiente para realizar la operaci&oacute;n solicitada.', ERROR_NIVEL_INSUFICIENTE);
				if ($redireccionHome)
					$this->redireccionar('home', 'view');
			}
			else {
				// Viva Peron! Tenes acceso...
			}
		}
		else {
			SessionController::get()->guardarError('Debe iniciar sesi&oacute;n para poder realizar la operaci&oacute;n solicitada.', ERROR_SIN_SESION);
			if ($redireccionHome)
				$this->redireccionar('home', 'view');
		}
	}

	/**
	 * Genera los parámeros iniciales de una vista con valores por defecto.
	 * @return array Array asosciativo con los parámetos de una vista, al cual se debe agregar el resto de la información que se desee pasar como parámetro a la vista.
	 */
	public function generarParametrosVista()
	{
		$parametrosVista = array();

		$parametrosVista['titulo_app'] = NG::configuracion()->obtenerTituloAplicacion();
		$parametrosVista['titulo'] = "Sin t&iacute;tulo";
		$parametrosVista['subtitulo'] = "Sin subt&iacute;tulo";
		$parametrosVista['texto'] = "Sin texto";
		$parametrosVista['usuario'] = $this->obtenerUsuarioActual();
		$parametrosVista['usuario_es_administrador'] = NG::seguridad()->validarPermisosUsuario($this->obtenerUsuarioActual(), $this->nombreModulo, NIVEL_ACCESO_ADMINISTRADOR);
		// *** agregados por XXXX el 13/03/2017 ***
		$parametrosVista['usuario_es_operador'] = NG::seguridad()->validarPermisosUsuario($this->obtenerUsuarioActual(), $this->nombreModulo, NIVEL_ACCESO_OPERADOR);
		$parametrosVista['usuario_es_invitado'] = NG::seguridad()->validarPermisosUsuario($this->obtenerUsuarioActual(), $this->nombreModulo, NIVEL_ACCESO_CONCEJAL);
		// ***
		$parametrosVista['autor_app'] = NG::configuracion()->obtenerAutorAplicacion();

		$parametrosVista['tipo_cabecera'] = VISTA_CABECERA_VACIA;

		// Obtengo mensajes de confirmacion o error, si es que hay alguno
		$this->obtenerMensajesInterfazDesdeSesion();
		$parametrosVista['mensaje_ok'] = $this->mensajeOk;
		$parametrosVista['mensaje_error'] = $this->mensajeError;
		$parametrosVista['numero_error'] = $this->numeroError;

		return $parametrosVista;
	}


	/**
	 * Toma como parametro un array asociativo y copia los elementos cuya clave coincida con los atributos
	 * públicos de la instancia destino. Se utiliza principalmente para evitar todo el mapeo de campos
	 * de los formularios de edición a variables o atributos de una instancia contcreta. Permite además
	 * ignorar un conjunto de parámetos, y setear un prefijo para preseleccionar los parámetos a copiar.
	 * @param  array $arrayOrigen      Array asociativo de origen.
	 * @param  mixed $instanciaDestino Instancia de destino, la cual recibirá los valores.
	 * @param  array $arrayIgnorar     Array de strings, los cuales serán parámetos ignorados.
	 * @param  string $prefijoParametro Prefijo para preseleccion de parámetos del array origen.
	 */
	public function copyArrayParamToInstance($arrayOrigen, $instanciaDestino, $arrayIgnorar = null, $prefijoParametro = "f_")
	{
		$claseInstanciaDestino = get_class($instanciaDestino);

		// Si no hay ignorados, debo crear el array vacio
		if (is_null($arrayIgnorar))
			$arrayIgnorar = array();

		// recorro todos los parametros
		foreach ($arrayOrigen as $key => $value) {
			// Verifico si tengo que ignorar el parametro
			if (!in_array($key, $arrayIgnorar))
			{
				// verifico el prefijo
				if (substr($key, 0, strlen($prefijoParametro)) == $prefijoParametro)
				{
					// quito el prefijo
					$nombrePropiedad = substr($key, strlen($prefijoParametro));
					// Si existe la propiedad en la instancia, la seteo
					if (property_exists($claseInstanciaDestino, $nombrePropiedad))
						$instanciaDestino->{$nombrePropiedad} = $value;
				}
			}
		}
	}

	/**
	 * Toma un parametro enviado al controlador y ejecuta un filtro de saneo del mismo.
	 * @param  mixed $parametro Parametro a sanear.
	 * @return mixed            Parametro saneado.
	 */
	public function sanearParametro($parametro) {
		return Validator::get()->sanear($parametro);
	}

	/**
	 * Toma un arreglo con todos los parametros enviados al controlador y ejecuta un filtro
	 * de saneo de los mismos. Por defecto, omite el parametro 'request_files'.
	 * @param  array $requestParamList Array de parametros a sanear.
	 * @param  array $arrayExclusion Array de strings con los identificadores de los parametros a omitir al momento de ejecutar el saneo.
	 * @return array                 Arreglo de parametros saneado.
	 */
	public function sanearConjuntoParametros($requestParamList, $arrayExclusion = null) {
		// si el array es nulo, lo creo y omito 'request_files'
		if (is_null($arrayExclusion))
			$arrayExclusion = array('request_files');
		else
			// si no es nulo, omito 'request_files' si es que ya no esta omitido
			if (!in_array('request_files', $arrayExclusion))
				$arrayExclusion[] = 'request_files';

		return Validator::get()->sanearConjunto($requestParamList, $arrayExclusion);
	}

	/**
	 * Devuelve la fecha x años para atrás (negativo) o adelante (positivo)
	 * en el primer día de dicho año (01 de Enero)
	 * @param  integer $cantidad Cantidad de años a restar|sumar a la fecha actual
	 * @return string            Fecha con años = año actual + $cantidad
	 */
	public function obtenerFechaAniosAtrasConDiaInicial($cantidad) {
		return (date("Y")+$cantidad).'-01-01';
	}

	/**
	 * Devuelve la fecha x años para atrás (negativo) o adelante (positivo)
	 * @param  integer $cantidad Cantidad de años a restar|sumar a la fecha actual
	 * @return string            Fecha con años = año actual + $cantidad
	 */
	public function getFechaOffsetAnios($cantidad)
	{
		$x_anios_atras = mktime(0, 0, 0, date("m"), date("d"), date("Y") + $cantidad);

		return date('Y-m-d', $x_anios_atras);
	}

	/**
	 * Devuelve la fecha x meses para atrás (negativo) o adelante (positivo)
	 * @param  integer $cantidad Cantidad de meses a restar|sumar a la fecha actual
	 * @return string            Fecha con meses = mes actual +/- $cantidad
	 */
	public function getFechaOffsetMeses($cantidad)
	{
		$fecha_actual = strtotime(date("Y-m-d"));
		$x_meses_atras = strtotime("$cantidad month", $fecha_actual);

  		return date("Y-m-d", $x_meses_atras);
	}

	/**
	 * Se la convierte al formato dia/mes/anio completo
	 * @param  string $fecha Fecha a formatear
	 * @return string        Fecha formateada
	 */
	public function formatearFecha($fecha)
	{
	    if ($fecha) {
			if ($fecha != '0000-00-00') {
				$fec_partes = explode("-",$fecha);
				return $fec_partes[2].'/'.$fec_partes[1].'/'.$fec_partes[0];
			} else
				return '';
	    } else
			return '';
	}

    /**
     * Devuelve una fecha en formato año_completo-mes-dia para la Capa de Negocio
     * @param string $fecha
     * @return string|boolean
     */
    public function formatearFechaNegocio($fecha)
	{
		if ( $fecha != null )
		{
			$fec_partes = explode("/", $fecha);
			return $fec_partes[2].'-'.$fec_partes[1].'-'.$fec_partes[0];
		}
		else
			return null;
    }

	/**
	 * Se obtiene el texto correspondiente a cada criterio de búsqueda utilizado para generar los reportes.
	 * @param  array  $parametros Conjunto de parámetros recibidos
	 * @return string $texto 	  Criterio utilizado, nombre de cada uno con su valor respectivo, para mostrar en el reporte
	 */
	public function obtenerTextoCriterioBusqueda($parametros)
	{
		$texto = array();

		// Por cada parámetro
		foreach ($parametros as $clave => $valor) {
			// Si NO es un parámetro a excluir
			if (!in_array($clave, $this->parametros_excluidos) ) {
				// Si posee valor
				if (!is_null($valor)) {
					$aux = '';
					// Si el parámetro pertenece al conjunto de etiquetas de un criterio de búsqueda determinado
					$etiqueta = ( array_key_exists($clave, $this->etiquetas) ) ? $this->etiquetas[$clave] : $clave;

					if (is_array($valor)) {
						if (count($valor) > 1)
							$aux = sprintf("<strong>%s:</strong> (%s)", $etiqueta, implode('|', $valor));
						else
							$aux = sprintf("<strong>%s:</strong> %s", $etiqueta, $valor[0]);
					}
					else if (trim($valor) !== '')
						// se arma la línea: etiqueta ya definida con su valor
						$aux = sprintf("<strong>%s:</strong> %s", $etiqueta, $valor);

					$texto[] = $aux;
				}
			}
		}
		return $texto;
	}

    /**
     * Se calcula el número de días que permanece en comisión, salteando los días que se encuentra prestado
     * @param  string  $inicio_rango Fecha de entrada del último Giro
     * @param  string  $fin_rango    Fecha del listado ingresada en el criterio de búsqueda
     * @param  integer $anio
     * @param  string  $tipo
     * @param  integer $numero
     * @param  integer $cuerpo
     * @param  integer $alcance
     * @param  integer $orden_giro   Orden del Giro
     * @return integer $dias       	 Número de días en comisión
     */
    public function calcularDiasEnComision(
    	$inicio_rango,
    	$fin_rango,
    	$anio,
    	$tipo,
    	$numero,
    	$cuerpo,
    	$alcance,
    	$orden_giro)
	{
		// Se obtienen todos los informes del giro
		$informes = NG::expedientes()->obtenerInformes(
			$anio,
			$tipo,
			$numero,
			$cuerpo,
			$alcance,
			$orden_giro);

		// Si NO posee Informes
		if ( is_null($informes) )
			// Se obtiene la diferencia en días entre
			// la fecha de entrada del último giro y la fecha elegida en el listado
			$dias = $this->obtenerDiferenciaFechasEnDias($fin_rango, $inicio_rango);
		else {
			$fechaEntrada = explode("/", $inicio_rango);
			$fechaSalida = explode("/", $fin_rango);

			// Se carga el vector con el rango de fechas
			$this->cargarVectorRangoFechas($fechaEntrada, $fechaSalida, $this->meses($fechaEntrada));

			// Por cada informe
			foreach ($informes as $informe)
				// Se cargan los ceros en el vector de rango de fechas
				$this->cargarCeros($informe->fecha_pedido_informe, $informe->fecha_vuelta_informe);

			// Se suman los días donde NO esté pedido ningún informe
			$dias = $this->sumarDias();
		}

		return $dias;
	}

	/**
	 * Devuelve la diferencia en días entre la fecha de listado y la fecha en comisión
	 * @param string $fecha_listado
	 * @param string $fecha_en_comision
	 * @return integer $dias_diferencia
	 */
	public function obtenerDiferenciaFechasEnDias($fecha_listado, $fecha_en_comision)
	{
		// SE DIVIDE LA FECHA DE FIN DEL RANGO
		$partes_fecha_fin = explode("/", $fecha_listado);
		$anio_fin = $partes_fecha_fin[2];
		$mes_fin  = $partes_fecha_fin[1];
		$dia_fin  = $partes_fecha_fin[0];

		// SE DIVIDE LA FECHA DE INICIO DEL RANGO
		$partes_fecha_inicio = explode("/", $fecha_en_comision);
		$anio_inicio = $partes_fecha_inicio[2];
		$mes_inicio  = $partes_fecha_inicio[1];
		$dia_inicio  = $partes_fecha_inicio[0];

		// SE CALCULA EL TIMESTAMP DE LAS DOS FECHAS
		$timestamp_fin 	  = mktime( 0, 0, 0, $mes_fin, $dia_fin, $anio_fin);
		$timestamp_inicio = mktime(0, 0, 0, $mes_inicio, $dia_inicio, $anio_inicio);

		// SE RESTA A UNA FECHA LA OTRA
		$segundos_diferencia = $timestamp_fin - $timestamp_inicio;

		// SE CONVIERTEN LOS SEGUNDOS EN DIAS
		$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

		// SE OBTIENE EL VALOR ABSOLUTO DE LOS DIAS (SE QUITA UN POSIBLE SIGNO NEGATIVO)
		$dias_diferencia = abs($dias_diferencia);

		// SE QUITAN LOS DECIMALES A LOS DIAS DE DIFERENCIA, EN CASO DE EXISTIR
		$dias_diferencia = floor($dias_diferencia);

		if ( $dias_diferencia < 0 )
			$dias_diferencia = 0;

		return $dias_diferencia;
	}

	/**
	 * Devuelve 29 si es bisiesto, sino 28
	 * @param  integer $anio Año para corroborar si es bisiesto o no
	 * @return integer Ultimo día de Febrero de dicho año
	 */
	public function anioBisiesto($anio)
	{
		// Un año es bisiesto si es divisible entre 4, excepto aquellos divisibles entre 100 pero no entre 400.
		return ( ( $anio%4 == 0 && $anio%100 != 0 ) || $anio%400 == 0 ) ? 29 : 28;
	}

	/**
	 * Devuelve la cantidad de días del mes respectivo a la fecha
	 * @param  string $fecha [description]
	 * @return [type]        [description]
	 */
	public function meses($fecha)
	{
		// Si el mes es Febrero
		if ( $fecha[1] == 2 )
			return $this->anioBisiesto($fecha[2]);
		elseif ( $fecha[1] == 1 || $fecha[1] == 3 || $fecha[1] == 5 || $fecha[1] == 7 || $fecha[1] == 8 || $fecha[1] == 10 || $fecha[1] == 12 )
			return 31;
		else
			return 30;
	}

	/**
	 * Se carga un vector de fechas con 1's (unos)
	 * @param  string  $fechaDesde            	Fecha de entrada del último Giro
	 * @param  string  $fechaHasta            	Fecha de Listado
	 * @param  integer $cantidad_dias_del_mes 	Cantidad de días del mes de la $fechaDesde
	 */
	public function cargarVectorRangoFechas($fechaDesde, $fechaHasta, $cantidad_dias_del_mes)
	{
		//  Se concatena el año, mes y día de la fecha Hasta para comparar
		$fecha_hasta = $fechaHasta[2].$fechaHasta[1].$fechaHasta[0];

		$i = 0;// Posición del vector
		while( true ) {
			// PARA COMPLETAR EL DÍA SI NO LO ESTÁ Y ES MENOR A DIEZ
			if ( $fechaDesde[0] < 10 )
				$fechaDesde[0] = substr('0'.$fechaDesde[0], -2);

			// PARA COMPLETAR EL MES SI NO LO ESTÁ Y ES MENOR A DIEZ
			if ( $fechaDesde[1] < 10 )
				$fechaDesde[1] = substr('0'.$fechaDesde[1], -2);

			// SE CONCATENA EL AÑO, MES Y DIA DE LA FECHA DESDE PARA COMPARAR
			$fecha_desde = $fechaDesde[2].$fechaDesde[1].$fechaDesde[0];

			// SI SE LLEGO AL FINAL DEL RANGO, TERMINA DE CARGAR EL VECTOR
			if ( $fecha_desde > $fecha_hasta )
				break;

			// SE INICIALIZA EL VECTOR EN 1 CON CADA DIA DEL RANGO, CON FORMATO DE FECHA yyyy-mm-dd PARA COMPARAR
			$this->vector_rango_de_dias[$i]['fecha'] = $fechaDesde[2]."-".$fechaDesde[1]."-".$fechaDesde[0];
			$this->vector_rango_de_dias[$i]['valor'] = 1;

			// SI NO SE LLEGÓ AL ULTIMO DIA DEL MES (28, 29, 30 o 31)
			if ( $fechaDesde[0] < $cantidad_dias_del_mes)
				$fechaDesde[0]++;// SE INCREMENTA EL DIA
			else { // SI ES EL ULTIMO DIA
				$fechaDesde[0] = 1;// COMIENZA EN EL DIA 1
				$fechaDesde[1]++;// SE PASA AL MES SIGUIENTE

				// Si el mes es mayor a Diciembre, comienza el siguiente año
				if ( $fechaDesde[1] > 12 ) {
					$fechaDesde[2]++;// Se pasa al año siguiente
					$fechaDesde[1] = 1; // Mes 1, Enero
				}

				// Cantidad de días del mes siguiente
				$cantidad_dias_del_mes = $this->meses($fechaDesde);
			}

			$i++;// Siguiente posición
		}
	}

	/**
	 * Se carga con ceros los días del período que está pedido cada informe del giro
	 * @param  string $fecha_pedido Fecha del pedido del informe
	 * @param  string $fecha_vuelta Fecha que se devuelve el informe
	 */
	public function cargarCeros($fecha_pedido, $fecha_vuelta)
	{
		$cantidad = count($this->vector_rango_de_dias);
		$con_informe = false;

		// CASO ESPECIAL: SI LA fecha_pedido ES MENOR A LA FECHA DE ENTRADA DEL GIRO
		if ( $fecha_pedido < $this->vector_rango_de_dias[0]['fecha'] )
			// fecha_pedido = FECHA DE INICIO DEL RANGO
			$fecha_pedido = $this->vector_rango_de_dias[0]['fecha'];

		// Se recorre el vector
		for ( $i=0; $i < $cantidad; $i++ ) {
			// Si concuerda la fecha_pedido con la fecha del vector
			if ( $this->vector_rango_de_dias[$i]['fecha'] == $fecha_pedido ) {
				// Se empieza a cargar con cero
				$this->vector_rango_de_dias[$i]['valor'] = 0;
				$con_informe = true;
			}

			// Si ya se empezó a cargar ceros
			if ( $con_informe ) {
				if ( $fecha_vuelta == null )
					// Se sigue cargando con ceros hasta el final del vector
					$this->vector_rango_de_dias[$i]['valor'] = 0;
				else {
					// Si no se llegó a la fecha de vuelta
					if ( $this->vector_rango_de_dias[$i]['fecha'] != $fecha_vuelta )
						// Se sigue cargando con ceros
						$this->vector_rango_de_dias[$i]['valor'] = 0;
					else {
						// Se carga el último cero porque llegó a la fecha de vuelta
						$this->vector_rango_de_dias[$i]['valor'] = 0;
						// Se establece que pasó el período del informe
						$con_informe = false;
					}
				}
			}
		}
	}

	// Se obtiene la suma de los días donde no esté pedido ningún informe (CON VALOR 1)
	public function sumarDias()
	{
		$suma = 0;
		$cantidad = count($this->vector_rango_de_dias);
		for ( $i=0; $i < $cantidad; $i++ )
			if ( $this->vector_rango_de_dias[$i]['valor'] == 1 )
				$suma++;

		// Se vacía el vector luego de realizar la suma
		$this->vector_rango_de_dias = null;

		return $suma;
	}
}
?>
