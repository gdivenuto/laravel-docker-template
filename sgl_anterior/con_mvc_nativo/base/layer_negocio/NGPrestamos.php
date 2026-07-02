<?php
/**
 * Capa de negocio de Préstamos.
 */

class NGPrestamos extends NGBaseClass {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Obtiene una coleccion de préstamos en base a diferentes criterios de selección.
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud_desde Fecha de solicitud del expediente; funciona como filtro para obtener conjunto con $pFecha_solicitud_hasta.
	 * @param string $pFecha_solicitud_hasta Fecha de solicitud del expediente; funciona como filtro para obtener conjunto con $pFecha_solicitud_desde.
	 * @param string $pSolicitante_tipo Tipo del solicitante.
	 * @param string $pSolicitante_codigo Código del solicitante.
	 * @param array $pEstados Arreglo de estados para filtro. Conjunto de estados a devolver. Si se indica null o un array vacío, no filtra por estado.
	 * @param array $pOrdenColumnas Array de nombres de atributos para ordernar la colección resultado.
	 * @param string $pOrdenSentido Criterio de ordenamiento de la colección resultado (ORDEN_ASCENDENTE, ORDEN_DESCENDENTE).
	 * @param integer $pLimiteCantidad Limite de cantidad de resultados a devolver (utilizado normalmente para paginación).
	 * @param integer $pLimiteOffset Offset de resultados a devolver (utilizado normalmente para paginación).
	 * @return array Colección de instancias de los préstamos seleccionados.
	 * @throws RuntimeException
	 */
	public function obtenerPrestamos(
		// Parametros
		$panio = null, $ptipo = null, $pnumero = null,
		$pcuerpo = null, $palcance = null, $pdigito = null,
		$pcuerpoalcance = null, $panexoalcance = null, $pcuerpoanexoalcance = null,
		$panexo = null, $pcuerpoanexo = null,
		$pfecha_solicitud_desde = null, $pfecha_solicitud_hasta = null,
		$psolicitante_tipo = null, $psolicitante_codigo = null,
		array $pestados = null,
		// Control de consulta
		$pInstanciasCompletas = false,
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null)
	{
		DB::prestamos()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::prestamos()->obtenerPrestamos(
				$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pdigito,
				$pcuerpoalcance, $panexoalcance, $pcuerpoanexoalcance, $panexo,	$pcuerpoanexo,
				$pfecha_solicitud_desde, $pfecha_solicitud_hasta,
				$psolicitante_tipo, $psolicitante_codigo,
				$pestados,
				$pOrdenColumnas,
				$pLimiteCantidad,
				$pLimiteOffset);
		} catch (Exception $e) {
			DB::prestamos()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerPrestamos: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Prestamo
		$resultado = $this->arrayResultToInstance($filas, 'Prestamo');

		DB::prestamos()->desconectar();

		if ($pInstanciasCompletas)
			foreach ($resultado as $p)
				$p->ro_estados_siguientes = $this->obtenerEstadosSiguientes($p);

		return $resultado;
	}

	/**
	 * Obtiene la cantidad de resultados para una consulta de préstamos en base a diferentes criterios de selección. En esencia
	 * ejecuta la misma consulta que ObtenerPrestamos, pero en vez de devolver el conjunto de datos, devuelve la cantidad de resultados.
	 * Se utiliza en paginación de resultados.
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud_desde Fecha de solicitud del expediente; funciona como filtro para obtener conjunto con $pFecha_solicitud_hasta.
	 * @param string $pFecha_solicitud_hasta Fecha de solicitud del expediente; funciona como filtro para obtener conjunto con $pFecha_solicitud_desde.
	 * @param string $pSolicitante_tipo Tipo del solicitante.
	 * @param string $pSolicitante_codigo Código del solicitante.
	 * @param array $pEstados Arreglo de estados para filtro. Conjunto de estados a devolver. Si se indica null o un array vacío, no filtra por estado.
	 * @return integer Cantidad de resultados para una determinada consulta de préstamos.
	 * @throws RuntimeException
	 */
	public function obtenerPrestamosCantidad(
		// Parametros
		$panio = null, $ptipo = null, $pnumero = null,
		$pcuerpo = null, $palcance  = null, $pdigito = null,
		$pcuerpoalcance = null, $panexoalcance = null, $pcuerpoanexoalcance = null, $panexo = null,	$pcuerpoanexo = null,
		$pfecha_solicitud_desde = null, $pfecha_solicitud_hasta = null,
		$psolicitante_tipo = null, $psolicitante_codigo = null,
		array $pestados = null)
	{
		DB::prestamos()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::prestamos()->obtenerPrestamosCantidad(
				$panio, $ptipo, $pnumero, $pcuerpo, $palcance ,
				$pdigito, $pcuerpoalcance, $panexoalcance, $pcuerpoanexoalcance, $panexo, $pcuerpoanexo,
				$pfecha_solicitud_desde, $pfecha_solicitud_hasta,
				$psolicitante_tipo, $psolicitante_codigo,
				$pestados);
		} catch (Exception $e) {
			DB::prestamos()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerPrestamosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::prestamos()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * Obtiene una coleccion de préstamos en base al estado, tipo y codigo de solicitante.
	 * @param string $pSolicitante_tipo Tipo del solicitante.
	 * @param string $pSolicitante_codigo Código del solicitante.
	 * @param array $pEstados Arreglo de estados para filtro. Conjunto de estados a devolver. Si se indica null o un array vacío, no filtra por estado.
	 * @param array $pOrdenColumnas Array de nombres de atributos para ordernar la colección resultado.
	 * @param string $pOrdenSentido Criterio de ordenamiento de la colección resultado (ORDEN_ASCENDENTE, ORDEN_DESCENDENTE).
	 * @param integer $pLimiteCantidad Limite de cantidad de resultados a devolver (utilizado normalmente para paginación).
	 * @param integer $pLimiteOffset Offset de resultados a devolver (utilizado normalmente para paginación).
	 * @return array Colección de instancias de los préstamos seleccionados.
	 * @throws RuntimeException
	 */
	public function obtenerPrestamosPorSolicitante(
			// Parametros
			$psolicitante_tipo = null,
			$psolicitante_codigo = null,
			array $pestados = null,
			// Control de consulta
			$pInstanciasCompletas = false,
			array $pOrdenColumnas = null,
			$pLimiteCantidad = null,
			$pLimiteOffset = null)
	{
		return $this->obtenerPrestamos(null, null, null, null, null, null, null, null, null, null, null, // Sin filtro por clave
				null, null,									// Sin filtro por fechas
				$psolicitante_tipo, $psolicitante_codigo, 	// filtro por solicitante tipo y codigo
				$pestados, 									// Filtro por estado
				$pInstanciasCompletas,						// Instancias completas
				$pOrdenColumnas, 				  			// Seleccion de orden
				$pLimiteCantidad, $pLimiteOffset); 			// Seleccion de limite
	}

	/**
	 * Obtiene un préstamo.
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud Fecha de solicitud del expediente.
	 * @return Prestamo Devuelve una instancia de Prestamo, o NULL si no se encuentra.
	 * @throws RuntimeException
	 */
	public function obtenerPrestamo($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pdigito,
			$pcuerpoalcance, $panexoalcance, $pcuerpoanexoalcance, $panexo, $pcuerpoanexo,
			$pfecha_solicitud, $pInstanciasCompletas = false)
	{
		$prestamos = $this->obtenerPrestamos(
				$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pdigito,
				$pcuerpoalcance, $panexoalcance, $pcuerpoanexoalcance, $panexo, $pcuerpoanexo,
				$pfecha_solicitud, $pfecha_solicitud,
				null, null, null, // Cualquier solicitante (tipo y codigo) y estado
				$pInstanciasCompletas); // Repito la fecha de solicitud para obtener exactamante un prestamo

		if (count($prestamos) == 1)
			return $prestamos[0]; // devuelvo el mismo préstamo, pero desde la capa de datos.
		else if (count($prestamos) == 0)
			return null; // no encuentro el prestamo
		else
			//Si encuentro el prestamo que acabo de guardar mas de una vez, algo ha sucedido... lanzo una excepcion
			throw new RuntimeException(sprintf("Error en %s.obtenerPrestamo: Se ha encontrado m&aacute;s de un préstamo para una búsqueda de resultado único.", get_class($this)));
	}

	/**
	 * Recarga la información de un préstamo.
	 * @param Prestamo $prestamo Prestamo del cual se desea recargar su información.
	 * @throws RuntimeException
	 * @return Prestamo Instancia del prestamo buscado, con sus datos actualizados. Si no se encuentra, devuelve NULL.
	 */
	public function recargarPrestamo(Prestamo $prestamo, $pInstanciasCompletas = false)
	{
		return $this->obtenerPrestamo(
				$prestamo->anio, $prestamo->tipo, $prestamo->numero, $prestamo->cuerpo, $prestamo->alcance,
				$prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance, $prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
				$prestamo->fecha_solicitud, $pInstanciasCompletas);
	}

	/**
	 * [ValidarPrestamoExistentePorSolicitante description]
	 * @param Prestamo $prestamo [description]
	 */
	public function validarPrestamoExistentePorSolicitante(Prestamo $prestamo)
	{
		// Ahora verifico que no exista un préstamo abierto del mismo solicitante
		$prestamos_abiertos = $this->obtenerPrestamos(
				$prestamo->anio, $prestamo->tipo, $prestamo->numero, $prestamo->cuerpo, $prestamo->alcance,
				$prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance, $prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
				null, null, $prestamo->solicitante_tipo, $prestamo->solicitante_codigo,
				array(Prestamo::E_SOLICITADO, Prestamo::E_PRESTADO));

		if (count($prestamos_abiertos) == 1)
		{
			// Encontre un préstamo??? Verifico que no sea el mismo que estoy guardando (para que no se bloquee a si mismo)
			// ATENCION: nótese la negacion de la comparacion en el if
			if ( !( ($prestamo->anio == $prestamos_abiertos[0]->anio) &&
					($prestamo->tipo == $prestamos_abiertos[0]->tipo) &&
					($prestamo->numero == $prestamos_abiertos[0]->numero) &&
					($prestamo->cuerpo == $prestamos_abiertos[0]->cuerpo) &&
					($prestamo->alcance == $prestamos_abiertos[0]->alcance) &&
					($prestamo->digito == $prestamos_abiertos[0]->digito) &&
					($prestamo->cuerpoalcance == $prestamos_abiertos[0]->cuerpoalcance) &&
					($prestamo->anexoalcance == $prestamos_abiertos[0]->anexoalcance) &&
					($prestamo->cuerpoanexoalcance == $prestamos_abiertos[0]->cuerpoanexoalcance) &&
					($prestamo->anexo == $prestamos_abiertos[0]->anexo) &&
					($prestamo->cuerpoanexo == $prestamos_abiertos[0]->cuerpoanexo) &&
					($prestamo->fecha_solicitud == $prestamos_abiertos[0]->fecha_solicitud)))
			{
				// HAY UN PROBLEMA: ya tengo un prestamo existente para el mismo solicitante y expediente
				throw new Exception("Ya existe una solicitud abierta para este Expediente y Solicitante.");
			}
		}
		else if (count($prestamos_abiertos) > 1)
		{
			// HAY UN PROBLEMA GRAVE!!! tengo mas de un prestamo existente para el mismo solicitante y expediente
			throw new Exception("ERROR GRAVE: existe más de un préstamo abierto existente para el mismo solicitante.");
		}

		return $prestamo;
	}

	/**
	 * Valida un préstamo, verificando el valor de sus atributos. En caso de error, devuelve una excepción.
	 * @param Prestamo $prestamo Préstamo a validar.
	 * @throws Exception
	 * @return Prestamo La misma instancia validada.
	 */
	public function validarPrestamo(Prestamo $prestamo)
	{
		// Validación de expresiones regulares (sin nulos permitidos)
		$prestamo->anio = Validator::Validar($prestamo->anio, PATRON_NUMEROS, false, 'Año del expediente');
		$prestamo->tipo = Validator::Validar($prestamo->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo del expediente');
		$prestamo->numero = Validator::Validar($prestamo->numero, PATRON_NUMEROS, false, 'Número del expediente');
		$prestamo->cuerpo = Validator::Validar($prestamo->cuerpo, PATRON_NUMEROS, false, 'Cuerpo del expediente');
		$prestamo->alcance = Validator::Validar($prestamo->alcance, PATRON_NUMEROS, false, 'Alcance del expediente');
		$prestamo->digito = Validator::Validar($prestamo->digito, PATRON_NUMEROS, false, 'Dígito del expediente');
		$prestamo->cuerpoalcance = Validator::Validar($prestamo->cuerpoalcance, PATRON_NUMEROS, false, 'Cuerpo del alcance del expediente');
		$prestamo->anexoalcance = Validator::Validar($prestamo->anexoalcance, PATRON_NUMEROS, false, 'Anexo del alcance del expediente');
		$prestamo->cuerpoanexoalcance = Validator::Validar($prestamo->cuerpoanexoalcance, PATRON_NUMEROS, false, 'Cuerpo del anexo del alcance del expediente');
		$prestamo->anexo = Validator::Validar($prestamo->anexo, PATRON_NUMEROS, false, 'Anexo del expediente');
		$prestamo->cuerpoanexo = Validator::Validar($prestamo->cuerpoanexo, PATRON_NUMEROS, false, 'Cuerpo del anexo del expediente');

		// Esta es la unica validacion de fecha que se mantiene como extra, porque tambien verifica que el atributo no sea nulo.
		$prestamo->fecha_solicitud = Validator::Validar($prestamo->fecha_solicitud, PATRON_FECHA_HORA, false, 'Fecha de solicitud del expediente');

		$prestamo->solicitante_tipo = Validator::Validar($prestamo->solicitante_tipo, PATRON_LETRAS_NO_VACIO, false, 'Tipo de solicitante del expediente');
		$prestamo->solicitante_codigo = Validator::Validar($prestamo->solicitante_codigo, PATRON_CODIGO_ALFANUMERICO_NO_VACIO, false, 'Código de solicitante del expediente');

		$prestamo->estado = Validator::Validar($prestamo->estado, PATRON_ESTADO_PRESTAMO_EXPEDIENTE, false, 'Estado del préstamo del expediente');

		$prestamo->id_usuario = Validator::Validar($prestamo->id_usuario, PATRON_NUMEROS, false, 'Identificador de usuario');

		// Validación de expresiones regulares (con nulos permitidos)
// Esas validaciones se anulan porque resultan redundantes. Ver validaciones de fechas mas adelante.
// 		$prestamo->fecha_prestado = Validator::Validar($prestamo->fecha_prestado, PATRON_FECHA_HORA, true, 'Fecha de préstamo del expediente');
// 		$prestamo->fecha_devuelto = Validator::Validar($prestamo->fecha_devuelto, PATRON_FECHA_HORA, true, 'Fecha de devolución del expediente');
// 		$prestamo->fecha_anulado = Validator::Validar($prestamo->fecha_anulado, PATRON_FECHA_HORA, true, 'Fecha de anulación del préstamo del expediente');

		$prestamo->libro_numero = Validator::Validar($prestamo->libro_numero, PATRON_CODIGO_ALFANUMERICO_NO_VACIO, true, 'Libro-Número del expediente');
		$prestamo->libro_folio = Validator::Validar($prestamo->libro_folio, PATRON_CODIGO_ALFANUMERICO_NO_VACIO, true, 'Libro-Folio del expediente');
		$prestamo->observaciones_prestamo = Validator::Validar($prestamo->observaciones_prestamo, PATRON_SEGURO_SIGNOS, true, 'Observaciones del préstamo del expediente');

		// Validación de valor de atributos
		// XXXX 26/07/2017 SE QUITÓ LA SIGUIENTE VALIDACIÓN: ($prestamo->anio < 1983) ||
		if (($prestamo->anio > date("Y")))
			throw new Exception("Año expediente inválido.");

		// Obtengo las fechas usando metodos especiales de la clase Prestamo, los cuales
		// si fallan (por tener basura en el atributo fecha), me lanzan una excepción.
		$dummy = $prestamo->get_fecha_solicitud_AsDateTime();
		$dummy = $prestamo->get_fecha_prestado_AsDateTime();
		$dummy = $prestamo->get_fecha_devuelto_AsDateTime();
		$dummy = $prestamo->get_fecha_anulado_AsDateTime();

// 		$error_fecha = date_parse_from_format('Y-m-d H:i:s', $prestamo->fecha_solicitud);
// 		if (($error_fecha['error_count'] > 0) || ($error_fecha['warning_count'] > 0))
// 			throw new Exception("Fecha de solicitud del expediente inválida.");

// 		if ($prestamo->fecha_prestado != null)
// 		{
// 			$error_fecha = date_parse_from_format('Y-m-d H:i:s', $prestamo->fecha_prestado);
// 			if (($error_fecha['error_count'] > 0) || ($error_fecha['warning_count'] > 0))
// 				throw new Exception("Fecha de préstamo del expediente inválida.");
// 		}

// 		if ($prestamo->fecha_devuelto != null)
// 		{
// 			$error_fecha = date_parse_from_format('Y-m-d H:i:s', $prestamo->fecha_devuelto);
// 			if (($error_fecha['error_count'] > 0) || ($error_fecha['warning_count'] > 0))
// 				throw new Exception("Fecha de devolución del expediente inválida.");
// 		}

// 		if ($prestamo->fecha_anulado != null)
// 		{
// 			$error_fecha = date_parse_from_format('Y-m-d H:i:s', $prestamo->fecha_anulado);
// 			if (($error_fecha['error_count'] > 0) || ($error_fecha['warning_count'] > 0))
// 				throw new Exception("Fecha de anulación del préstamo del expediente inválida.");
// 		}

		// Devuelvo el prestamo
		return $prestamo;
	}

	/**
	 * Guarda un préstamo, persistiendo su estado. Previo a guardar el préstamo, ejecuta la validación del mismo.
	 * @param Prestamo $prestamo Instancia de préstamo a ser persistida.
	 * @param bool $recargar Si es TRUE, una vez guardado el préstamo, lo vuelve a leer desde el origen de datos (esto permite "refrescar" campos calculados).
	 * @return Prestamo
	 * @throws RunTimeException
	 */
	public function guardarPrestamo(Prestamo $prestamo, $pRecargar = FALSE)
	{
		if (is_null($prestamo))
			throw new Exception(sprintf("Error en %s.guardarPrestamo: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::prestamos()->conectar(false); // AutoCommit: false
		DB::prestamos()->iniciarTransaccion(false); // SoloLectura: false

		// Antes que nada, valido el préstamo (si falla, lanza una excepción).
		$prestamo = $this->validarPrestamo($prestamo);

		// Luego valido que no exista otro prestamo con el mismo solicitante
		$prestamo = $this->validarPrestamoExistentePorSolicitante($prestamo);

		try {
			$audit_operacion = (is_null($this->obtenerPrestamo(
				$prestamo->anio, $prestamo->tipo, $prestamo->numero, $prestamo->cuerpo, $prestamo->alcance,
				$prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance, $prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
				$prestamo->fecha_solicitud))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			// Si llegue hasta aqui, es que puedo guardar.
			DB::prestamos()->guardarPrestamo(
				$prestamo->anio, $prestamo->tipo, $prestamo->numero, $prestamo->cuerpo, $prestamo->alcance,
				$prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance, $prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
				$prestamo->fecha_solicitud,	$prestamo->fecha_prestado, $prestamo->fecha_devuelto, $prestamo->fecha_anulado,
				$prestamo->solicitante_tipo, $prestamo->solicitante_codigo, $prestamo->libro_numero, $prestamo->libro_folio,
				$prestamo->estado,
				$prestamo->observaciones_prestamo,
				$prestamo->id_usuario,
				$prestamo->activo);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$prestamo, null, $audit_operacion, 'hcd.expe_prestamos',  null, null,
				sprintf('Se ha guardado el pr&eacute;stamo %d-%s-%d-%d-%d-%d-%d-%d-%d-%d-%d, fecha solicitud: %s',
					$prestamo->anio, $prestamo->tipo, $prestamo->numero, $prestamo->cuerpo, $prestamo->alcance,
					$prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance, $prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
					$prestamo->fecha_solicitud)
			);

			DB::prestamos()->guardarTransaccion();

		} catch (Exception $e) {
			DB::prestamos()->cancelarTransaccion();
			DB::prestamos()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarPrestamo: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar)
			$resultado = $this->recargarPrestamo($prestamo);
		else
			$resultado = $prestamo;

		DB::prestamos()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarPrestamo: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * Obtiene la cantidad de resultados para una consulta de solicitudes de expedientes externos en base a diferentes criterios de selección.
	 * En esencia ejecuta la misma consulta que ObtenerSolicitudesExpedientesExternos, pero en vez de devolver el conjunto de datos, devuelve
	 * la cantidad de resultados.
	 * Se utiliza en paginación de resultados.
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud_hcd_desde Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_hasta.
	 * @param string $pFecha_solicitud_hcd_hasta Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_desde.
	 * @param array $pEstados Arreglo de estados para filtro. Conjunto de estados a devolver. Si se indica null o un array vacío, no filtra por estado.
	 * @return integer Cantidad de resultados para una determinada consulta de solicitudes de expedientes externos.
	 * @throws RuntimeException
	 */
	public function obtenerSolicitudesExpedientesExternosCantidad($panio, $ptipo, $pnumero, $pcuerpo = null, $palcance  = null, $pdigito = null,
			$pcuerpoalcance = null, $panexoalcance = null, $pcuerpoanexoalcance = null, $panexo = null,	$pcuerpoanexo = null,
			$pfecha_solicitud_hcd_desde = null, $pfecha_solicitud_hcd_hasta = null,
			array $pestados = null)
	{
		DB::prestamos()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::prestamos()->obtenerSolicitudesExpedientesExternosCantidad($panio, $ptipo, $pnumero, $pcuerpo, $palcance , $pdigito, $pcuerpoalcance, $panexoalcance, $pcuerpoanexoalcance, $panexo, $pcuerpoanexo,
				$pfecha_solicitud_hcd_desde, $pfecha_solicitud_hcd_hasta,
				$pestados);
		} catch (Exception $e) {
			DB::prestamos()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerSolicitudesExpedientesExternosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::prestamos()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * A partir de un préstamo, este método devuelve una coleccion de estados siguientes posibles.
	 * Si no hubieran estados siguientes, la coleccion queda vacía.
	 * @param Prestamo $prestamo
	 * @return array Arreglo de estados siguientes posibles.
	 * @throws InvalidArgumentException
	 */
	public function obtenerEstadosSiguientes(Prestamo $prestamo)
	{
		if ($prestamo == null)
			throw new InvalidArgumentException("El préstamo no puede ser nulo.");

		// Verifico a partir del estado donde estoy posicionado, cuales son mis estados siguientes
		$estados_siguientes = array();

		// Primer condicion: respetamos el circuito.
		switch ($prestamo->estado)
		{
			case Prestamo::E_SOLICITADO : // Solicitado al HCD
				// Si el expediente es externo (tipo = "D"), tengo que verificar que exista una
				// solicitud abierta en "expedientes externos" y que el expediente externo
				// este en poder del HCD.
				$flag_verificar_expediente_externo = true; // seteo un flag por defecto en true (si no lo necesito, no afecta al circuito)

				// XXXX 27/07/2017 SE AGREGÓ EL TIPO O = Otro
				if ( $prestamo->tipo == 'D' || $prestamo->tipo == 'O' )
				{
					$solicitudes_ee = $this->obtenerSolicitudesExpedientesExternosCantidad($prestamo->anio, $prestamo->tipo, $prestamo->numero,
							$prestamo->cuerpo, $prestamo->alcance, $prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance,
							$prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
							null, null, array(SolicitudExpedienteExterno::E_INGRESADO_EE));

					$flag_verificar_expediente_externo = $solicitudes_ee > 0;
				}

				// Segunda condicion: no puedo prestar un expediente si ya lo tengo prestado.
				$prestamos_anteriores = $this->obtenerPrestamosCantidad(
					$prestamo->anio, $prestamo->tipo, $prestamo->numero, $prestamo->cuerpo, $prestamo->alcance,
					$prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance, $prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
					null, null, null, null, array(Prestamo::E_PRESTADO));

				// el flag de expediente externo no afecta la condicion si no es requerido (por defecto es "true")
				if (($prestamos_anteriores == 0) && $flag_verificar_expediente_externo)
				{
					$estados_siguientes[] = Prestamo::E_PRESTADO; // Se agrega el estado PRESTADO
				}

				$estados_siguientes[] = Prestamo::E_ANULADO;  // Se agrega el estado ANULADO
				break;

			case Prestamo::E_PRESTADO : // Prestado desde el HCD
				$estados_siguientes[] = Prestamo::E_DEVUELTO; // Se agrega el estado DEVUELTO
				break;

			case Prestamo::E_DEVUELTO : // Devuelto al HCD
				// Si fue devuelto, no hay mas estados posibles.
				break;

			case Prestamo::E_ANULADO : // Prestamo anulado
				// Si fue anulado, no hay mas estados siguientes.
				break;

			default:
				throw new InvalidArgumentException("El estado del préstamo es inválido. Estado: ".$prestamo->estado);
		}

		return $estados_siguientes;
	}

	/**
	 * Toma un préstamo y lo "mueve" en el circuito de préstamos, modificando su estado al estado siguiente.
	 * @param Prestamo $prestamo Prestamo a avanzar en el circuito.
	 * @param string $estadoSiguiente Estado al que se desea mover el préstamo.
	 * @param string $fechaEstado Fecha en la cual se desea mover el préstamo al nuevo estado. Si es null, la fecha utilizada es "getdate()". La máscara de la fecha debe ser 'Y-m-d H:i:s'.
	 * @return Prestamo Intancia del préstamo modificada.array
	 * @throws InvalidArgumentException
	 */
	public function cambiarEstado(Prestamo $prestamo, $estadoSiguiente, $fechaEstado = null)
	{
		// Debo tener una instancia de prestamo valida.
		if ($prestamo == null)
			throw new InvalidArgumentException("El préstamo no puede ser nulo.");

		// Obtengo la fecha por defecto (si es null, la fecha actual)
		if ($fechaEstado == null)
			$fecha = new DateTime();
		else
		{
			// Verifico que la fecha este en el formato correcto
			$error_fecha = date_parse_from_format('Y-m-d H:i:s', $fechaEstado);

			if ( ($error_fecha['error_count'] == 0) && ($error_fecha['warning_count'] == 0) )
				$fecha = DateTime::createFromFormat('Y-m-d H:i:s', $fechaEstado);
			else
				throw new Exception("Fecha de solicitud del expediente inválida.");
		}

		// Determino, del estado actual, que la fecha nueva sea mayor a la actual.
		$fechaActual = $prestamo->obtenerFechaActualAsDateTime();
		if (($fechaActual != null) && ($fechaActual > $fecha))
			throw new InvalidArgumentException("La nueva fecha no puede ser anterior a la fecha del ultimo estado. Fecha actual: ".$fechaActual->format("Y-m-d H:i:s").", nueva fecha: ".$fecha->format("Y-m-d H:i:s"));

		// Obtengo los estados siguientes posibles
		$estadosPosibles = $this->obtenerEstadosSiguientes($prestamo);
		// Si el estado que se me indico como parametro no existe entre los posibles siguientes, lanzo una excepcion
		$estadoValido = false;
		foreach ($estadosPosibles as $estado) {
			if ($estado == $estadoSiguiente)
				$estadoValido = true;
		}
		if (!$estadoValido)
			throw new InvalidArgumentException("El nuevo estado no es válido como posible estado siguiente. Estado actual: ".$prestamo->estado.", nuevo estado: ".$estadoSiguiente);

		// Si llegue hasta aqui es que todo esta bien. Procedo a actualizar el estado y la fecha.
		// (Por las dudas, verifico que el estadoSiguiente sea valido)
		switch ($estadoSiguiente)
		{
			case Prestamo::E_SOLICITADO : // Solicitado al HCD
				$prestamo->set_fecha_solicitud_FromDateTime($fecha);
				// En E_SOLICITADO, los datos del solicitante son obligatorios
				if (Validator::get()->esVacio($prestamo->solicitante_codigo) || Validator::get()->esVacio($prestamo->solicitante_tipo))
					throw new InvalidArgumentException("El código y tipo de solicitante son obligatorios para el estado E_SOLICITADO.");
				break;

			case Prestamo::E_PRESTADO : // Prestado desde el HCD
				$prestamo->set_fecha_prestado_FromDateTime($fecha);
				// En E_PRESTADO, libro_numero y libro_folio son obligatorios
				if (Validator::get()->esVacio($prestamo->libro_numero) || Validator::get()->esVacio($prestamo->libro_folio))
					throw new InvalidArgumentException("El número y folio del libro son obligatorios para el estado E_PRESTADO.");
				break;

			case Prestamo::E_DEVUELTO : // Devuelto al HCD
				$prestamo->set_fecha_devuelto_FromDateTime($fecha);
				break;

			case Prestamo::E_ANULADO : // Prestamo anulado
				$prestamo->set_fecha_anulado_FromDateTime($fecha);
				break;

			default:
				throw new InvalidArgumentException("El nuevo estado es inválido. Estado: ".$estadoSiguiente);
		}
		$prestamo->estado = $estadoSiguiente;

		return $prestamo;
	}

	/**
	 * Determina si un préstamo se encuentra en su estado final.
	 * @param Prestamo $prestamo
	 * @return bool True en caso de estar en un estado final. False en caso contrario.
	 * @throws InvalidArgumentException
	 */
	public function enEstadoFinalPrestamo(Prestamo $prestamo)
	{
		return ($prestamo->estado == Prestamo::E_DEVUELTO) || ($prestamo->estado == Prestamo::E_ANULADO);
	}

	/**
	 * Verifica si el préstamo actual requiere la generación de una solicitud de expediente externo.
	 * Se considera que requiere una solicitud cuando NO existe al menos una en estado:
	 * "solicitada al hcd",
	 * "solicitada al ente externo" o
	 * "ingresada desde el Ente Externo al hcd".
	 * Además, el prestamo debe ser de un expediente externo (tipo = "D" ó tipo = "O"), y debe estar en un
	 * estado QUE NO SEA ESTADO FINAL (porque los estados finales ya no requieren solicitud).
	 * @param Prestamo $prestamo Préstamo del cual se quiere verificar si existe una solicitud válida de expediente externo.
	 * @return boolean True si requiere la generación de una solicitud, False en caso contrario.
	 */
	public function requiereSolicitudExpedienteExterno(Prestamo $prestamo)
	{
		$resultado = false;

		if ( ($prestamo->tipo == 'D' || $prestamo->tipo == 'O') && !$this->enEstadoFinalPrestamo($prestamo) ) {
			// Verifico si existe al menos una solicitud en estado E_SOLICITADO_HCD, E_SOLICITADO_EE o E_INGRESADO_EE
			$cantidad_solicitudes = $this->obtenerSolicitudesExpedientesExternosCantidad($prestamo->anio, $prestamo->tipo, $prestamo->numero,
					$prestamo->cuerpo, $prestamo->alcance, $prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance,
					$prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
					null, null,
					array(SolicitudExpedienteExterno::E_SOLICITADO_HCD,
						  SolicitudExpedienteExterno::E_SOLICITADO_EE,
						  SolicitudExpedienteExterno::E_INGRESADO_EE));

			$resultado = !($cantidad_solicitudes > 0);
		}

		return $resultado;
	}

	/**
	 * Verifica si existe un Préstamo PRESTADO, correspondiente a la Solicitud de Expediente Externo
	 * @param SolicitudExpedienteExterno $solicitud 	Solicitud de la cual se quiere verificar si existe un Préstamo respectivo
	 * @return boolean True si existe el Préstamo correspondiente a la Solicitud, False en caso contrario.
	 */
	public function existePrestamoPrestadoParaSolicitud(SolicitudExpedienteExterno $solicitud)
	{
		// Se verifica igualmente si el tipo es del D.E. u Otro
		if ($solicitud->tipo != 'D' && $solicitud->tipo != 'O' )
			throw new Exception('Error: las solicitudes solo deberían ser de expedientes externos (D, O) y se ha encontrado una solicitud de expediente interno ('.$solicitud->tipo.')');

		$cantidad_prestamos = $this->obtenerPrestamosCantidad(
			$solicitud->anio,
			$solicitud->tipo,
			$solicitud->numero,
			$solicitud->cuerpo,
			$solicitud->alcance,
			$solicitud->digito,
			$solicitud->cuerpoalcance,
			$solicitud->anexoalcance,
			$solicitud->cuerpoanexoalcance,
			$solicitud->anexo,
			$solicitud->cuerpoanexo,
			null, null, null, null, // sin fecha
			array(Prestamo::E_PRESTADO) // Con estado E_DEVUELTO y E_ANULADO se consideran préstamos cerrados
			// Prestamo::E_SOLICITADO,
		);

		// Si existe el Préstamo correspondiente a la Solicitud
		return $cantidad_prestamos > 0;
	}

	/**
	 * 03/08/2017
	 * Verifica si existe el Préstamo correspondiente a la Solicitud de Expediente Externo
	 * @param SolicitudExpedienteExterno $solicitud 	Solicitud de la cual se quiere verificar si existe un Préstamo pedido
	 * @return boolean True si existe el Préstamo correspondiente a la Solicitud, False en caso contrario.
	 */
	public function existePrestamoPendienteParaSolicitud(SolicitudExpedienteExterno $solicitud)
	{
		// Se verifica igualmente si el tipo es del D.E. o de Otro Ente Externo
		if ($solicitud->tipo != 'D' && $solicitud->tipo != 'O' )
			throw new Exception('Error: las solicitudes solo deberían ser de expedientes externos (D, O) y se ha encontrado una solicitud de expediente interno ('.$solicitud->tipo.')');

		$cantidad_prestamos = $this->obtenerPrestamosCantidad(
			$solicitud->anio,
			$solicitud->tipo,
			$solicitud->numero,
			$solicitud->cuerpo,
			$solicitud->alcance,
			$solicitud->digito,
			$solicitud->cuerpoalcance,
			$solicitud->anexoalcance,
			$solicitud->cuerpoanexoalcance,
			$solicitud->anexo,
			$solicitud->cuerpoanexo,
			null, null, null, null, // sin fecha
			array(Prestamo::E_SOLICITADO) // Sólo aquellos que estén Solicitados y NO Prestados
		);

		// Si existe el Préstamo correspondiente a la Solicitud
		return $cantidad_prestamos > 0;
	}

	/**
	 * 01/08/2017
	 * Verifica si existe un Expediente del HCD (tipo = E, = N ó = R)
	 * @param [integer] $anio    [description]
	 * @param [string] $tipo    [description]
	 * @param [integer] $numero  [description]
	 * @param [integer] $cuerpo  [description]
	 * @param [integer] $alcance [description]
	 * @return [boolean] True ó False
	 */
	public function existeExpedienteHCD($anio, $tipo, $numero, $cuerpo, $alcance)
	{
		DB::prestamos()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::prestamos()->existeExpedienteHCD($anio, $tipo, $numero, $cuerpo, $alcance);
		} catch (Exception $e) {
			DB::prestamos()->desconectar();
			throw new Exception(sprintf("Error en %s.existeExpedienteHCD: %s", get_class($this), $e->getMessage()));
		}

		DB::prestamos()->desconectar();

		return $resultado;
	}

	/**
	 * Crea una nueva instancia de SolicitudExpedienteExterno a partir de una instancia de Prestamo.
	 * La fecha y hora inicial de la solicitud se obtienen a partir de la fecha y hora del sistema.
	 * @param Prestamo $prestamo Préstamo desde el cual se va a generar la instancia.
	 * @return SolicitudExpedienteExterno Nueva instancia de SolicitudExpedienteExterno generada a partir del Prestamo.
	 */
	public function obtenerInstanciaSolicitudExpedienteExterno(Prestamo $prestamo)
	{
		$solicitud = new SolicitudExpedienteExterno();
		$solicitud->anio = $prestamo->anio;
		$solicitud->tipo = $prestamo->tipo;
		$solicitud->numero = $prestamo->numero;
		$solicitud->cuerpo = $prestamo->cuerpo;
		$solicitud->alcance = $prestamo->alcance;
		$solicitud->digito = $prestamo->digito;
		$solicitud->cuerpoalcance = $prestamo->cuerpoalcance;
		$solicitud->anexoalcance = $prestamo->anexoalcance;
		$solicitud->cuerpoanexoalcance = $prestamo->cuerpoanexoalcance;
		$solicitud->anexo = $prestamo->anexo;
		$solicitud->cuerpoanexo = $prestamo->cuerpoanexo;
		$solicitud->estado = SolicitudExpedienteExterno::E_SOLICITADO_HCD;
		$solicitud->set_fecha_solicitud_hcd_FromDateTime(new DateTime());
		$solicitud->fecha_solicitud_ee = null;
		$solicitud->fecha_ingresado_ee = null;
		$solicitud->fecha_devuelto_ee = null;
		$solicitud->fecha_anulado_ee = null;
		$solicitud->id_usuario = $prestamo->id_usuario;
		$solicitud->observaciones = "Solicitud generada automáticamente a partir del préstamo del expediente ".$prestamo->toStringDescription();

		return $solicitud;
	}

	/**
	 * 03/08/2017
	 * Crea una nueva instancia de SolicitudExpedienteExterno a partir de una instancia de SolicitudExpedienteExterno.
	 * para agregarse luego de haberse devuelto una solicitud anterior.
	 * La fecha y hora inicial de la solicitud se obtienen a partir de la fecha y hora del sistema.
	 * @param SolicitudExpedienteExterno $solicitud_devuelta Solicitud desde el cual se va a generar la instancia.
	 * @return SolicitudExpedienteExterno Nueva instancia de SolicitudExpedienteExterno generada a partir de otra Solicitud.
	 */
	public function generarNuevaSolicitudExpedienteExterno(SolicitudExpedienteExterno $solicitud_devuelta)
	{
		$solicitud = new SolicitudExpedienteExterno();
		$solicitud->anio = $solicitud_devuelta->anio;
		$solicitud->tipo = $solicitud_devuelta->tipo;
		$solicitud->numero = $solicitud_devuelta->numero;
		$solicitud->cuerpo = $solicitud_devuelta->cuerpo;
		$solicitud->alcance = $solicitud_devuelta->alcance;
		$solicitud->digito = $solicitud_devuelta->digito;
		$solicitud->cuerpoalcance = $solicitud_devuelta->cuerpoalcance;
		$solicitud->anexoalcance = $solicitud_devuelta->anexoalcance;
		$solicitud->cuerpoanexoalcance = $solicitud_devuelta->cuerpoanexoalcance;
		$solicitud->anexo = $solicitud_devuelta->anexo;
		$solicitud->cuerpoanexo = $solicitud_devuelta->cuerpoanexo;
		$solicitud->estado = SolicitudExpedienteExterno::E_SOLICITADO_HCD;
		$solicitud->set_fecha_solicitud_hcd_FromDateTime(new DateTime());
		$solicitud->fecha_solicitud_ee = null;
		$solicitud->fecha_ingresado_ee = null;
		$solicitud->fecha_devuelto_ee = null;
		$solicitud->fecha_anulado_ee = null;
		$solicitud->id_usuario = $solicitud_devuelta->id_usuario;
		$solicitud->observaciones = "Solicitud generada automáticamente a partir de la devolución al Ente Externo del expediente ".$solicitud_devuelta->toStringDescription();

		return $solicitud;
	}

	/**
	 * Obtiene una coleccion de solicitudes de expedientes externos en base a diferentes criterios de selección.
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud_hcd_desde Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_hasta.
	 * @param string $pFecha_solicitud_hcd_hasta Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_desde.
	 * @param array $pEstados Arreglo de estados para filtro. Conjunto de estados a devolver. Si se indica null o un array vacío, no filtra por estado.
	 * @param array $pOrdenColumnas Array de nombres de atributos para ordernar la colección resultado.
	 * @param integer $pLimiteCantidad Limite de cantidad de resultados a devolver (utilizado normalmente para paginación).
	 * @param integer $pLimiteOffset Offset de resultados a devolver (utilizado normalmente para paginación).
	 * @return array Array asociativo de las Solicitudes de EE seleccionadas.
	 * @throws RuntimeException
	 */
	public function obtenerSolicitudesExpedientesExternos($panio, $ptipo, $pnumero,
			$pcuerpo = null, $palcance  = null, $pdigito = null,
			$pcuerpoalcance = null, $panexoalcance = null, $pcuerpoanexoalcance = null, $panexo = null, $pcuerpoanexo = null,
			$pfecha_solicitud_hcd_desde = null, $pfecha_solicitud_hcd_hasta = null,
			array $pestados = null,
			// Control de consulta
			$pInstanciasCompletas = false,
			array $pordencolumnas = null,
			$plimitecantidad = null, $plimiteoffset = null)
	{
		DB::prestamos()->conectar();

		try {
			// Traigo la información básica de los expedientes (la cabecera)
			$resultado_solicitudes = DB::prestamos()->obtenerSolicitudesExpedientesExternos($panio, $ptipo, $pnumero,
				$pcuerpo, $palcance , $pdigito, $pcuerpoalcance, $panexoalcance, $pcuerpoanexoalcance, $panexo,	$pcuerpoanexo,
				$pfecha_solicitud_hcd_desde, $pfecha_solicitud_hcd_hasta,
				$pestados,
				$pordencolumnas,
				$plimitecantidad, $plimiteoffset);

			// Transformo el array de resultados en un array de instancias de SolicitudExpedienteExterno
			$solicitudes = $this->arrayResultToInstance($resultado_solicitudes, 'SolicitudExpedienteExterno');

		} catch (Exception $e) {
			DB::prestamos()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerSolicitudesExpedientesExternos: %s", get_class($this), $e->getMessage()));
		}

		DB::prestamos()->desconectar();

		if ($pInstanciasCompletas)
			foreach ($solicitudes as $s)
				$s->ro_estados_siguientes = $this->obtenerEstadosSiguientesExpedienteExterno($s);

		return $solicitudes;
	}

	/**
	 * Obtiene una solicitud de expediente externo.
	 * @param integer $pAnio Año del expediente.
	 * @param string $pTipo Tipo de expediente.
	 * @param integer $pNumero Número del expediente.
	 * @param integer $pCuerpo Cuerpo del expediente.
	 * @param integer $pAlcance Alcance del expediente.
	 * @param string $pDigito Dígito del expediente. Para expedientes externos.
	 * @param integer $pCuerpoalcance Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexoalcance Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexoalcance Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer $pAnexo Anexo del expediente. Para expedientes externos.
	 * @param integer $pCuerpoanexo Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string $pFecha_solicitud_hcd Fecha de solicitud del expediente.
	 * @return SolicitudExpedienteExterno Devuelve una instancia de SolicitudExpedienteExterno, o NULL si no se encuentra.
	 * @throws RuntimeException
	 */
	public function obtenerSolicitudExpedienteExterno($panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pdigito,
			$pcuerpoalcance, $panexoalcance, $pcuerpoanexoalcance, $panexo, $pcuerpoanexo,
			$pfecha_solicitud_hcd)
	{
		$solicitudes_ee = $this->obtenerSolicitudesExpedientesExternos(
				$panio, $ptipo, $pnumero, $pcuerpo, $palcance, $pdigito,
				$pcuerpoalcance, $panexoalcance, $pcuerpoanexoalcance, $panexo, $pcuerpoanexo,
				$pfecha_solicitud_hcd, $pfecha_solicitud_hcd); // Repito la fecha de solicitud para obtener exactamante un prestamo

		if (count($solicitudes_ee) == 1)
			return $solicitudes_ee[0]; // devuelvo la misma solicitud, pero desde la capa de datos.
		else if (count($solicitudes_ee) == 0)
			return null; // no encuentro la solicitud
		else
			//Si encuentro la solicitud que acabo de guardar más de una vez, algo ha sucedido... lanzo una excepcion
			throw new RuntimeException(sprintf("Error en %s.obtenerSolicitudExpedienteExterno: Se ha encontrado más de una solicitud para una búsqueda de resultado único.", get_class($this)));
	}

	/**
	 * Recarga la información de un préstamo.
	 * @param SolicitudExpedienteExterno $solicitud_ee Solicitud de expediente externo de la cual se desea recargar su información.
	 * @throws RuntimeException
	 * @return SolicitudExpedienteExterno Instancia de la solicitud de expediente externo buscada, con sus datos actualizados. Si no se encuentra, devuelve NULL.
	 */
	public function recargarSolicitudExpedienteExterno(SolicitudExpedienteExterno $solicitud_ee)
	{
		return $this->obtenerSolicitudExpedienteExterno(
				$solicitud_ee->anio, $solicitud_ee->tipo, $solicitud_ee->numero, $solicitud_ee->cuerpo, $solicitud_ee->alcance,
				$solicitud_ee->digito, $solicitud_ee->cuerpoalcance, $solicitud_ee->anexoalcance, $solicitud_ee->cuerpoanexoalcance, $solicitud_ee->anexo, $solicitud_ee->cuerpoanexo,
				$solicitud_ee->fecha_solicitud_hcd);
	}

	/**
	 * Valida una solicitud de expediente externo, verificando el valor de sus atributos.
	 * En caso de error, devuelve una excepción.
	 * @param SolicitudExpedienteExterno $solicitud_ee Solicitud de expediente externo a validar.
	 * @throws Exception
	 * @return SolicitudExpedienteExterno La misma instancia validada.
	 */
	public function validarSolicitudExpedienteExterno(SolicitudExpedienteExterno $solicitud_ee)
	{
		// Validación de expresiones regulares (sin nulos permitidos)
		$solicitud_ee->anio = Validator::Validar($solicitud_ee->anio, PATRON_NUMEROS, false, 'Año del expediente externo');
		$solicitud_ee->tipo = Validator::Validar($solicitud_ee->tipo, PATRON_TIPO_EXPEDIENTE, false, 'Tipo del expediente externo');
		$solicitud_ee->numero = Validator::Validar($solicitud_ee->numero, PATRON_NUMEROS, false, 'Número del expediente externo');
		$solicitud_ee->cuerpo = Validator::Validar($solicitud_ee->cuerpo, PATRON_NUMEROS, false, 'Cuerpo del expediente externo');
		$solicitud_ee->alcance = Validator::Validar($solicitud_ee->alcance, PATRON_NUMEROS, false, 'Alcance del expediente externo');
		$solicitud_ee->digito = Validator::Validar($solicitud_ee->digito, PATRON_NUMEROS, false, 'Dígito del expediente externo');
		$solicitud_ee->cuerpoalcance = Validator::Validar($solicitud_ee->cuerpoalcance, PATRON_NUMEROS, false, 'Cuerpo del alcance del expediente externo');
		$solicitud_ee->anexoalcance = Validator::Validar($solicitud_ee->anexoalcance, PATRON_NUMEROS, false, 'Anexo del alcance del expediente externo');
		$solicitud_ee->cuerpoanexoalcance = Validator::Validar($solicitud_ee->cuerpoanexoalcance, PATRON_NUMEROS, false, 'Cuerpo del anexo del alcance del expediente externo');
		$solicitud_ee->anexo = Validator::Validar($solicitud_ee->anexo, PATRON_NUMEROS, false, 'Anexo del expediente externo');
		$solicitud_ee->cuerpoanexo = Validator::Validar($solicitud_ee->cuerpoanexo, PATRON_NUMEROS, false, 'Cuerpo del anexo del expediente externo');

		// Esta es la unica validacion de fecha que se mantiene como extra, porque tambien verifica que el atributo no sea nulo.
		$solicitud_ee->fecha_solicitud_hcd = Validator::Validar($solicitud_ee->fecha_solicitud_hcd, PATRON_FECHA_HORA, false, 'Fecha de solicitud al HCD del expediente externo');

		$solicitud_ee->estado = Validator::Validar($solicitud_ee->estado, PATRON_ESTADO_SOLICITUD_EXPEDIENTE_EXTERNO, false, 'Estado de la solicitud del expediente externo');

		$solicitud_ee->id_usuario = Validator::Validar($solicitud_ee->id_usuario, PATRON_NUMEROS, false, 'Identificador de usuario');

		$solicitud_ee->observaciones = Validator::Validar($solicitud_ee->observaciones, PATRON_SEGURO_SIGNOS, true, 'Observaciones del préstamo del expediente externo');

		// Validación de valor de atributos
		if (($solicitud_ee->anio < 1900) || ($solicitud_ee->anio > date("Y")))
			throw new Exception("Año expediente externo inválido. Rango válido: 1900 - ".date("Y"));

		// Obtengo las fechas usando metodos especiales de la clase SolicitudExpedienteExterno, los cuales
		// si fallan (por tener basura en el atributo fecha), me lanzan una excepción.
		$dummy = $solicitud_ee->get_fecha_solicitud_hcd_AsDateTime();
		$dummy = $solicitud_ee->get_fecha_solicitud_ee_AsDateTime();
		$dummy = $solicitud_ee->get_fecha_ingresado_ee_AsDateTime();
		$dummy = $solicitud_ee->get_fecha_devuelto_ee_AsDateTime();
		$dummy = $solicitud_ee->get_fecha_anulado_ee_AsDateTime();

		// Devuelvo el prestamo
		return $solicitud_ee;
	}

	/**
	 * Guarda una solicitud de expediente externo, persistiendo su estado.
	 * Previo a guardar la solicitud de expediente externo, ejecuta la validación de la misma.
	 * @param SolicitudExpedienteExterno $solicitud_ee Instancia de solicitud de expediente externo a ser persistida.
	 * @param bool $recargar Si es TRUE, una vez guardada la solicitud de expediente externo, la vuelve a leer desde el origen de datos (esto permite "refrescar" campos calculados).
	 * @return SolicitudExpedienteExterno
	 * @throws RunTimeException
	 */
	public function guardarSolicitudExpedienteExterno(SolicitudExpedienteExterno $solicitud_ee, $recargar = FALSE)
	{
		if (is_null($solicitud_ee))
			throw new Exception(sprintf("Error en %s.guardarSolicitudExpedienteExterno: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::prestamos()->conectar(false); // AutoCommit: false
		DB::prestamos()->iniciarTransaccion(false); // SoloLectura: false

		// Antes que nada, valido la solicitud de expediente externo (si falla, lanza una excepción).
		$solicitud_ee = $this->validarSolicitudExpedienteExterno($solicitud_ee);

		try {
			$audit_operacion = (is_null($this->obtenerSolicitudExpedienteExterno(
				$solicitud_ee->anio, $solicitud_ee->tipo, $solicitud_ee->numero,
				$solicitud_ee->cuerpo, $solicitud_ee->alcance,
				$solicitud_ee->digito, $solicitud_ee->cuerpoalcance, $solicitud_ee->anexoalcance,
				$solicitud_ee->cuerpoanexoalcance, $solicitud_ee->anexo, $solicitud_ee->cuerpoanexo,
				$solicitud_ee->fecha_solicitud_hcd))) ? Auditoria::OP_ALTA : Auditoria::OP_MODIFICA;

			DB::prestamos()->guardarSolicitudExpedienteExterno(
				$solicitud_ee->anio, $solicitud_ee->tipo, $solicitud_ee->numero,
				$solicitud_ee->cuerpo, $solicitud_ee->alcance,
				$solicitud_ee->digito, $solicitud_ee->cuerpoalcance, $solicitud_ee->anexoalcance, $solicitud_ee->cuerpoanexoalcance, $solicitud_ee->anexo, $solicitud_ee->cuerpoanexo,
				$solicitud_ee->fecha_solicitud_hcd,	$solicitud_ee->fecha_solicitud_ee,	$solicitud_ee->fecha_ingresado_ee, $solicitud_ee->fecha_devuelto_ee, $solicitud_ee->fecha_anulado_ee,
				$solicitud_ee->estado,
				$solicitud_ee->observaciones,
				$solicitud_ee->id_usuario,
				$solicitud_ee->activo);

			// Auditoria
			NG::auditorias()->auditarComoExpediente(
				$solicitud_ee, null, $audit_operacion, 'hcd.expe_expedientes_externos',  null, null,
				sprintf('Se ha guardado la solicitud al ente externo %d-%s-%d-%d-%d-%d-%d-%d-%d-%d-%d, fecha solicitud al HCD: %s',
					$solicitud_ee->anio, $solicitud_ee->tipo, $solicitud_ee->numero,
					$solicitud_ee->cuerpo, $solicitud_ee->alcance,
					$solicitud_ee->digito, $solicitud_ee->cuerpoalcance, $solicitud_ee->anexoalcance,
					$solicitud_ee->cuerpoanexoalcance, $solicitud_ee->anexo, $solicitud_ee->cuerpoanexo,
					$solicitud_ee->fecha_solicitud_hcd)
			);

			DB::prestamos()->guardarTransaccion();

		} catch (Exception $e) {
			DB::prestamos()->cancelarTransaccion();
			DB::prestamos()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarSolicitudExpedienteExterno: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($recargar)
			$resultado = $this->recargarSolicitudExpedienteExterno($solicitud_ee);
		else
			$resultado = $solicitud_ee;

		DB::prestamos()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarSolicitudExpedienteExterno: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * A partir de una solicitud de expediente externo, este método devuelve una coleccion de estados
	 * siguientes posibles.
	 * Si no hubieran estados siguientes, la coleccion queda vacía.
	 * @param SolicitudExpedienteExterno $solicitud_ee
	 * @return array Arreglo de estados siguientes posibles.
	 * @throws InvalidArgumentException
	 */
	public function obtenerEstadosSiguientesExpedienteExterno(SolicitudExpedienteExterno $solicitud_ee)
	{
		if ($solicitud_ee == null)
			throw new InvalidArgumentException("La solicitud de expediente externo no puede ser nula.");

		if ( $solicitud_ee->tipo != 'D' && $solicitud_ee->tipo != 'O' )
			throw new InvalidArgumentException("El tipo de expediente de la solicitud de expediente externo, debe ser 'D' u 'O'.");

		// Verifico a partir del estado donde estoy posicionado, cuales son mis estados siguientes
		$estados_siguientes = array();

		// Primer condicion: respetamos el circuito.
		switch ($solicitud_ee->estado)
		{
			case SolicitudExpedienteExterno::E_SOLICITADO_HCD : // Solicitado al HCD
				$estados_siguientes[] = SolicitudExpedienteExterno::E_SOLICITADO_EE;
				$estados_siguientes[] = SolicitudExpedienteExterno::E_ANULADO_EE;
				break;

			case SolicitudExpedienteExterno::E_SOLICITADO_EE : //Solicitado al Ente Externo
				$estados_siguientes[] = SolicitudExpedienteExterno::E_INGRESADO_EE;
				$estados_siguientes[] = SolicitudExpedienteExterno::E_ANULADO_EE;
				break;

			case SolicitudExpedienteExterno::E_INGRESADO_EE : // Ingresado desde el Ente Externo
				// Segunda condicion: no puedo devolver un expediente externo si lo tengo prestado.
				$devoluciones_pendientes = $this->obtenerPrestamosCantidad(
						$solicitud_ee->anio, $solicitud_ee->tipo, $solicitud_ee->numero, $solicitud_ee->cuerpo, $solicitud_ee->alcance,
						$solicitud_ee->digito, $solicitud_ee->cuerpoalcance, $solicitud_ee->anexoalcance, $solicitud_ee->cuerpoanexoalcance, $solicitud_ee->anexo, $solicitud_ee->cuerpoanexo,
						null, null, null, null, array(Prestamo::E_PRESTADO));

				if ($devoluciones_pendientes == 0)
					// Se permite devolver el expediente
					$estados_siguientes[] = SolicitudExpedienteExterno::E_DEVUELTO_EE;

				break;

			case SolicitudExpedienteExterno::E_DEVUELTO_EE : // Devuelto al Ente Externo
				// Si fue devuelta, no hay mas estados posibles.
				break;

			case SolicitudExpedienteExterno::E_ANULADO_EE : // Solicitud externa anulada
				// Si fue anulada, no hay mas estados siguientes.
				break;

			default:
				throw new InvalidArgumentException("El estado de la solicitud de expediente externo es inválida. Estado: ".$solicitud_ee->estado);
		}

		return $estados_siguientes;
	}

	/**
	 * Toma una solicitud externa y la "mueve" en el circuito de solicitudes de expedientes externos,
	 * modificando su estado al estado siguiente.
	 * @param SolicitudExpedienteExterno $solicitud_ee Solicitud externa a avanzar en el circuito.
	 * @param string $estadoSiguiente Estado al que se desea mover la solicitud externa.
	 * @param string $fechaEstado Fecha en la cual se desea mover la solicitud externa al nuevo estado. Si es null, la fecha utilizada es "getdate()". La máscara de la fecha debe ser 'Y-m-d H:i:s'.
	 * @return SolicitudExpedienteExterno Intancia de la solicitud externa modificada.
	 * @throws InvalidArgumentException
	 */
	public function cambiarEstadoExpedienteExterno(SolicitudExpedienteExterno $solicitud_ee, $estadoSiguiente, $fechaEstado = null)
	{
		// Debo tener una instancia de prestamo valida.
		if ($solicitud_ee == null)
			throw new InvalidArgumentException("La solicitud de expediente externo no puede ser nula.");

		if ($solicitud_ee->tipo != 'D' && $solicitud_ee->tipo != 'O')
			throw new InvalidArgumentException("El tipo de expediente de la solicitud de expediente externo, debe ser 'D' u 'O'.");

		// Obtengo la fecha por defecto (si es null, la fecha actual)
		if ($fechaEstado == null)
			$fecha = new DateTime();
		else
		{
			// Verifico que la fecha este en el formato correcto
			$error_fecha = date_parse_from_format('Y-m-d H:i:s', $fechaEstado);

			if ( ($error_fecha['error_count'] == 0) && ($error_fecha['warning_count'] == 0) )
				$fecha = DateTime::createFromFormat('Y-m-d H:i:s', $fechaEstado);
			else
				throw new Exception("Fecha de solicitud del expediente inválida.");
		}

		// Determino, del estado actual, que la fecha nueva sea mayor a la actual.
		$fechaActual = $solicitud_ee->obtenerFechaActualAsDateTime();
		if (($fechaActual != null) && ($fechaActual > $fecha))
			throw new InvalidArgumentException("La nueva fecha no puede ser anterior a la fecha del ultimo estado. Fecha actual: ".$fechaActual->format("Y-m-d H:i:s").", nueva fecha: ".$fecha->format("Y-m-d H:i:s"));

		// Obtengo los estados siguientes posibles
		$estadosPosibles = $this->obtenerEstadosSiguientesExpedienteExterno($solicitud_ee);
		// Si el estado que se me indico como parámetro no existe entre los posibles siguientes, lanzo una excepción
		$estadoValido = false;
		foreach ($estadosPosibles as $estado) {
			if ($estado == $estadoSiguiente)
				$estadoValido = true;
		}
		if (!$estadoValido)
			throw new InvalidArgumentException("El nuevo estado no es válido como posible estado siguiente. Estado actual: ".$solicitud_ee->estado.", nuevo estado: ".$estadoSiguiente);

		// Si llegue hasta aquí es que todo está bien. Procedo a actualizar el estado y la fecha.
		// (Por las dudas, verifico que el estadoSiguiente sea válido)
		switch ($estadoSiguiente)
		{
			case SolicitudExpedienteExterno::E_SOLICITADO_HCD : // Solicitado al HCD
				$solicitud_ee->set_fecha_solicitud_hcd_FromDateTime($fecha);
				break;

			case SolicitudExpedienteExterno::E_SOLICITADO_EE : // Solicitado al Ente externo
				$solicitud_ee->set_fecha_solicitud_ee_FromDateTime($fecha);
				break;

			case SolicitudExpedienteExterno::E_INGRESADO_EE : // Ingresado desde el ente externo
				$solicitud_ee->set_fecha_ingresado_ee_FromDateTime($fecha);
				break;

			case SolicitudExpedienteExterno::E_DEVUELTO_EE : // Devuelto al ente externo
				$solicitud_ee->set_fecha_devuelto_ee_FromDateTime($fecha);
				break;

			case SolicitudExpedienteExterno::E_ANULADO_EE : // Solicitud externa anulada
				$solicitud_ee->set_fecha_anulado_ee_FromDateTime($fecha);
				break;

			default:
				throw new InvalidArgumentException("El nuevo estado es inválido. Estado: ".$estadoSiguiente);
		}
		$solicitud_ee->estado = $estadoSiguiente;

		return $solicitud_ee;
	}

	/**
	 * Elimina un Préstamo (de forma lógica)
	 * @param Prestamo $prestamo instancia de Prestamo a ser eliminada
	 */
	public function eliminarPrestamo(Prestamo $prestamo)
	{
		if (is_null($prestamo))
			throw new Exception(sprintf("Error en %s.eliminarPrestamo: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::prestamos()->conectar(false); // AutoCommit: false
		DB::prestamos()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Se desactiva el Préstamo respectivo
			$prestamo->activo = '0';

			// Se guarda con el campo 'activo' en cero
			// se descarta el elemento recargado porque al NO estar activo las consultas NO lo devuelven
			$resultado = $this->guardarPrestamo($prestamo, false);

			// Auditoria (si es que se eliminó algo "lógicamente", tengo que auditar)
			if ( !is_null($resultado) ) {
				NG::auditorias()->auditarComoExpediente(
					$prestamo, null, Auditoria::OP_BAJA, 'hcd.expe_prestamos', null, null,
					NG::auditorias()->generarMensajeEliminacion($resultado, array(
						$prestamo->anio, $prestamo->tipo, $prestamo->numero, $prestamo->cuerpo, $prestamo->alcance,
						$prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance,
						$prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
						$prestamo->fecha_solicitud))
				);
			}

			DB::prestamos()->guardarTransaccion();

		} catch (Exception $e) {
			DB::prestamos()->cancelarTransaccion();
			DB::prestamos()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarPrestamo: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::prestamos()->desconectar();

		return $prestamo;
	}

	/**
	 * Elimina una Solicitud (de forma lógica)
	 * @param SolicitudExpedienteExterno $solicitud_ee instancia de SolicitudExpedienteExterno a ser eliminada
	 */
	public function eliminarSolicitudExpedienteExterno(SolicitudExpedienteExterno $solicitud_ee)
	{
		if (is_null($solicitud_ee))
			throw new Exception(sprintf("Error en %s.eliminarSolicitud: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::prestamos()->conectar(false); // AutoCommit: false
		DB::prestamos()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Se desactiva la Solicitud respectiva
			$solicitud_ee->activo = '0';

			// Se guarda con el campo 'activo' en cero
			// se descarta el elemento recargado porque al NO estar activo las consultas NO lo devuelven
			$resultado = $this->guardarSolicitudExpedienteExterno($solicitud_ee, false);

			// Auditoria (si es que se eliminó algo "lógicamente", tengo que auditar)
			if ( !is_null($resultado) ) {
				NG::auditorias()->auditarComoExpediente(
					$solicitud_ee, null, Auditoria::OP_BAJA, 'hcd.expe_expedientes_externos', null, null,
					NG::auditorias()->generarMensajeEliminacion($resultado, array(
						$solicitud_ee->anio, $solicitud_ee->tipo, $solicitud_ee->numero,
						$solicitud_ee->cuerpo, $solicitud_ee->alcance,
						$solicitud_ee->digito, $solicitud_ee->cuerpoalcance, $solicitud_ee->anexoalcance,
						$solicitud_ee->cuerpoanexoalcance, $solicitud_ee->anexo, $solicitud_ee->cuerpoanexo,
						$solicitud_ee->fecha_solicitud_hcd))
				);
			}

			DB::prestamos()->guardarTransaccion();

		} catch (Exception $e) {
			DB::prestamos()->cancelarTransaccion();
			DB::prestamos()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarSolicitud: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::prestamos()->desconectar();

		return $solicitud_ee;
	}
}
?>
