<?php
/**
 * Capa de negocio del circuito de prestamos.
 * Contiene toda la lógica necesaria para, dado un determinado prestamo,
 * saber cual es su posición en el circuito, y "avanzarlo".
 *
 * @author XXXX, XXXX
 * @version 1.1
 * @since 2015.08.06
 *
 */

// Configuracion de rutas
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/path_config.php');

// Layer de persistencia
require_once(PATH_SGL_LAYER_DATOS_PRESTAMOS.'db_prestamos.php');

// Layer de negocio
require_once(PATH_SGL_LAYER_NEGOCIO_PRESTAMOS.'ng_helper.php');
require_once(PATH_SGL_LAYER_NEGOCIO_PRESTAMOS.'RegExValidator.php');

// Clases del modelo
require_once(PATH_SGL_LAYER_MODELO_PRESTAMOS.'Prestamo.php');
require_once(PATH_SGL_LAYER_MODELO_PRESTAMOS.'SolicitudExpedienteExterno.php');

/**
 *
 * @author XXXX
 *
 */
class ng_prestamos {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	// Referencia a la capa de datos
	private $db;

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct()
	{
		$this->db = new db_prestamos();
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
	public function ObtenerPrestamos($pAnio, $pTipo, $pNumero, $pCuerpo = null, $pAlcance  = null, $pDigito = null,
			$pCuerpoalcance = null, $pAnexoalcance = null, $pCuerpoanexoalcance = null, $pAnexo = null, $pCuerpoanexo = null,
			$pFecha_solicitud_desde = null, $pFecha_solicitud_hasta = null,
			$pSolicitante_tipo = null, $pSolicitante_codigo = null,
			array $pEstados = null,
			array $pOrdenColumnas = null, $pOrdenSentido = ORDEN_ASCENDENTE,
			$pLimiteCantidad = null, $pLimiteOffset = null)
	{
		$this->db->conectar();

		// Traigo la información básica de los expedientes (la cabecera)
		$resultado_prestamos = $this->db->ObtenerPrestamos($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance , $pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo,	$pCuerpoanexo,
				$pFecha_solicitud_desde, $pFecha_solicitud_hasta,
				$pSolicitante_tipo, $pSolicitante_codigo,
				$pEstados,
				$pOrdenColumnas, $pOrdenSentido,
				$pLimiteCantidad, $pLimiteOffset);

		// Transformo el array de resultados en un array de instancias de expediente
		$prestamos = ng_helper::arrayResultToInstance($resultado_prestamos, 'Prestamo');

		$this->db->desconectar();

		return $prestamos;
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
	public function ObtenerPrestamosCantidadResultados($pAnio, $pTipo, $pNumero, $pCuerpo = null, $pAlcance  = null, $pDigito = null,
			$pCuerpoalcance = null, $pAnexoalcance = null, $pCuerpoanexoalcance = null, $pAnexo = null,	$pCuerpoanexo = null,
			$pFecha_solicitud_desde = null, $pFecha_solicitud_hasta = null,
			$pSolicitante_tipo = null, $pSolicitante_codigo = null,
			array $pEstados = null)
	{
		$this->db->conectar();

		// Obtengo la cantidad de resultados
		$cantidad = $this->db->ObtenerPrestamosCantidadResultados($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance , $pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo,	$pCuerpoanexo,
				$pFecha_solicitud_desde, $pFecha_solicitud_hasta,
				$pSolicitante_tipo, $pSolicitante_codigo,
				$pEstados);

		$this->db->desconectar();

		return $cantidad;
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
	public function ObtenerPrestamosPorSolicitante($pSolicitante_tipo = null, $pSolicitante_codigo = null,
			array $pEstados = null,
			array $pOrdenColumnas = null, $pOrdenSentido = ORDEN_ASCENDENTE,
			$pLimiteCantidad = null, $pLimiteOffset = null)
	{
		return $this->ObtenerPrestamos(null, null, null, null, null, null, null, null, null, null, null, // Sin filtro por clave
				null, null,									// Sin filtro por fechas
				$pSolicitante_tipo, $pSolicitante_codigo, 	// Filtro por solicitante tipo y codigo
				$pEstados, 									// Filtro por estado
				$pOrdenColumnas, $pOrdenSentido,  			// Seleccion de orden
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
	public function ObtenerPrestamo($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance, $pDigito,
			$pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
			$pFecha_solicitud)
	{
		$prestamos = $this->ObtenerPrestamos(
				$pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance, $pDigito,
				$pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
				$pFecha_solicitud, $pFecha_solicitud); // Repito la fecha de solicitud para obtener exactamante un prestamo

		if (count($prestamos) == 1)
			return $prestamos[0]; // devuelvo el mismo préstamo, pero desde la capa de datos.
		else if (count($prestamos) == 0)
			return null; // no encuentro el prestamo
		else
			//Si encuentro el prestamo que acabo de guardar mas de una vez, algo ha sucedido... lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de nb_prestamos.ObtenerPrestamo: Se ha encontrado mas de un préstamo para una búsqueda de resultado único.");
	}

	/**
	 * Recarga la información de un préstamo.
	 * @param Prestamo $prestamo Prestamo del cual se desea recargar su información.
	 * @throws RuntimeException
	 * @return Prestamo Instancia del prestamo buscado, con sus datos actualizados. Si no se encuentra, devuelve NULL.
	 */
	public function RecargarPrestamo(Prestamo $prestamo)
	{
		return $this->ObtenerPrestamo(
				$prestamo->anio, $prestamo->tipo, $prestamo->numero, $prestamo->cuerpo, $prestamo->alcance,
				$prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance, $prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
				$prestamo->fecha_solicitud);
	}

	public function ValidarPrestamoExistentePorSolicitante(Prestamo $prestamo)
	{
		// Ahora verifico que no exista un préstamo abierto del mismo solicitante
		$prestamos_abiertos = $this->ObtenerPrestamos(
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
				throw new Exception("ERROR: ya existe una solicitud abierta para este expediente y solicitante.");
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
	 * Guarda un préstamo, persistiendo su estado. Previo a guardar el préstamo, ejecuta la validación del mismo.
	 * @param Prestamo $prestamo Instancia de préstamo a ser persistida.
	 * @param bool $recargar Si es TRUE, una vez guardado el préstamo, lo vuelve a leer desde el origen de datos (esto permite "refrescar" campos calculados).
	 * @return Prestamo
	 * @throws RunTimeException
	 */
	public function GuardarPrestamo(Prestamo $prestamo, $recargar = FALSE)
	{
		// Antes que nada, valido el préstamo (si falla, lanza una excepción).
		$prestamo = $this->ValidarPrestamo($prestamo);

		// Luego valido que no exista otro prestamo con el mismo solicitante
		$prestamo = $this->ValidarPrestamoExistentePorSolicitante($prestamo);

		$this->db->conectar();

		// Si llegue hasta aqui, es que puedo guardar.
		$this->db->GuardarPrestamo(
				$prestamo->anio, $prestamo->tipo, $prestamo->numero, $prestamo->cuerpo, $prestamo->alcance,
				$prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance, $prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
				$prestamo->fecha_solicitud,	$prestamo->fecha_prestado, $prestamo->fecha_devuelto, $prestamo->fecha_anulado,
				$prestamo->solicitante_tipo, $prestamo->solicitante_codigo, $prestamo->libro_numero, $prestamo->libro_folio,
				$prestamo->estado,
				$prestamo->observaciones_prestamo, $prestamo->id_usuario);

		$this->db->desconectar();

		// Se registra en la auditoría el ingreso del préstamo.
		$this->RegistrarLogParaPrestamo($prestamo, "GUARDAR");

		// Si se pudo guardar y el programador lo desea, hago un "refresh" del dato guardado.
		if ($recargar) {
			$prestamo_recargado = $this->RecargarPrestamo($prestamo);

			if ($prestamo_recargado != null)
				return $prestamo_recargado; // devuelvo el mismo préstamo.
			else
				//Si no encuentro el prestamo que acabo de guardar, algo ha sucedido... lanzo una excepcion
				throw new RuntimeException("Falló la ejecución de ng_prestamos.GuardarPrestamo: No se encuentra el préstamo que se acaba de guardar.");
		}
		else
			return $prestamo;
	}

	/**
	 * A partir de un préstamo, este método devuelve una coleccion de estados siguientes posibles.
	 * Si no hubieran estados siguientes, la coleccion queda vacía.
	 * @param Prestamo $prestamo
	 * @return array Arreglo de estados siguientes posibles.
	 * @throws InvalidArgumentException
	 */
	public function ObtenerEstadosSiguientes(Prestamo $prestamo)
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
					$solicitudes_ee = $this->ObtenerSolicitudesExpedientesExternosCantidadResultados($prestamo->anio, $prestamo->tipo, $prestamo->numero,
							$prestamo->cuerpo, $prestamo->alcance, $prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance,
							$prestamo->cuerpoanexoalcance, $prestamo->anexo, $prestamo->cuerpoanexo,
							null, null, array(SolicitudExpedienteExterno::E_INGRESADO_EE));

					$flag_verificar_expediente_externo = $solicitudes_ee > 0;
				}

				// Segunda condicion: no puedo prestar un expediente si ya lo tengo prestado.
				$prestamos_anteriores = $this->ObtenerPrestamosCantidadResultados(
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
	 * A partir de un préstamo, este método devuelve una coleccion de estados siguientes posibles.
	 * Si no hubieran estados siguientes, la coleccion queda vacía.
	 * @param Prestamo $prestamo
	 * @return array Arreglo de estados siguientes posibles.
	 * @throws InvalidArgumentException
	 */
	public function ObtenerEstadosSiguientesPrestamo(Prestamo $prestamo)
	{
		return $this->ObtenerEstadosSiguientes($prestamo);
	}

	/**
	 * Toma un préstamo y lo "mueve" en el circuito de préstamos, modificando su estado al estado siguiente.
	 * @param Prestamo $prestamo Prestamo a avanzar en el circuito.
	 * @param string $estadoSiguiente Estado al que se desea mover el préstamo.
	 * @param string $fechaEstado Fecha en la cual se desea mover el préstamo al nuevo estado. Si es null, la fecha utilizada es "getdate()". La máscara de la fecha debe ser 'Y-m-d H:i:s'.
	 * @return Prestamo Intancia del préstamo modificada.array
	 * @throws InvalidArgumentException
	 */
	public function CambiarEstado(Prestamo $prestamo, $estadoSiguiente, $fechaEstado = null)
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
		$fechaActual = $prestamo->ObtenerFechaActualAsDateTime();
		if (($fechaActual != null) && ($fechaActual > $fecha))
			throw new InvalidArgumentException("La nueva fecha no puede ser anterior a la fecha del ultimo estado. Fecha actual: ".$fechaActual->format("Y-m-d H:i:s").", nueva fecha: ".$fecha->format("Y-m-d H:i:s"));

		// Obtengo los estados siguientes posibles
		$estadosPosibles = $this->ObtenerEstadosSiguientes($prestamo);
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
				$prestamo->Set_fecha_solicitud_FromDateTime($fecha);
				// En E_SOLICITADO, los datos del solicitante son obligatorios
				if (empty($prestamo->solicitante_codigo) || empty($prestamo->solicitante_tipo))
					throw new InvalidArgumentException("El código y tipo de solicitante son obligatorios para el estado E_SOLICITADO.");
				break;

			case Prestamo::E_PRESTADO : // Prestado desde el HCD
				$prestamo->Set_fecha_prestado_FromDateTime($fecha);
				// En E_PRESTADO, libro_numero y libro_folio son obligatorios
				if (empty($prestamo->libro_numero) || empty($prestamo->libro_folio))
					throw new InvalidArgumentException("El número y folio del libro son obligatorios para el estado E_PRESTADO.");
				break;

			case Prestamo::E_DEVUELTO : // Devuelto al HCD
				$prestamo->Set_fecha_devuelto_FromDateTime($fecha);
				break;

			case Prestamo::E_ANULADO : // Prestamo anulado
				$prestamo->Set_fecha_anulado_FromDateTime($fecha);
				break;

			default:
				throw new InvalidArgumentException("El nuevo estado es inválido. Estado: ".$estadoSiguiente);
		}
		$prestamo->estado = $estadoSiguiente;

		return $prestamo;
	}

	/**
	 * Toma un préstamo y lo "mueve" en el circuito de préstamos, modificando su estado al estado siguiente.
	 * @param Prestamo $prestamo Prestamo a avanzar en el circuito.
	 * @param string $estadoSiguiente Estado al que se desea mover el préstamo.
	 * @param string $fechaEstado Fecha en la cual se desea mover el préstamo al nuevo estado. Si es null, la fecha utilizada es "getdate()". La máscara de la fecha debe ser 'Y-m-d H:i:s'.
	 * @return Prestamo Intancia del préstamo modificada.array
	 * @throws InvalidArgumentException
	 */
	public function CambiarEstadoPrestamo(Prestamo $prestamo, $estadoSiguiente, $fechaEstado = null)
	{
		return $this->CambiarEstado($prestamo, $estadoSiguiente, $fechaEstado);
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
	public function RequiereSolicitudExpedienteExterno(Prestamo $prestamo)
	{
		$resultado = false;

		if ( ($prestamo->tipo == 'D' || $prestamo->tipo == 'O') && !$this->EnEstadoFinalPrestamo($prestamo) ) {
			// Verifico si existe al menos una solicitud en estado E_SOLICITADO_HCD, E_SOLICITADO_EE o E_INGRESADO_EE
			$cantidad_solicitudes = $this->ObtenerSolicitudesExpedientesExternosCantidadResultados($prestamo->anio, $prestamo->tipo, $prestamo->numero,
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
	public function ExistePrestamoPrestadoParaSolicitud(SolicitudExpedienteExterno $solicitud)
	{
		// Se verifica igualmente si el tipo es del D.E. u Otro
		if ($solicitud->tipo != 'D' && $solicitud->tipo != 'O' )
			throw new Exception('Error: las solicitudes solo deberían ser de expedientes externos (D, O) y se ha encontrado una solicitud de expediente interno ('.$solicitud->tipo.')');

		$cantidad_prestamos = $this->ObtenerPrestamosCantidadResultados(
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
	public function ExistePrestamoPendienteParaSolicitud(SolicitudExpedienteExterno $solicitud)
	{
		// Se verifica igualmente si el tipo es del D.E. o de Otro Ente Externo
		if ($solicitud->tipo != 'D' && $solicitud->tipo != 'O' )
			throw new Exception('Error: las solicitudes solo deberían ser de expedientes externos (D, O) y se ha encontrado una solicitud de expediente interno ('.$solicitud->tipo.')');

		$cantidad_prestamos = $this->ObtenerPrestamosCantidadResultados(
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
	public function ExisteExpedienteHCD($anio, $tipo, $numero, $cuerpo, $alcance)
	{
		return $this->db->ExisteExpedienteHCD($anio, $tipo, $numero, $cuerpo, $alcance);
	}

	/**
	 * Crea una nueva instancia de SolicitudExpedienteExterno a partir de una instancia de Prestamo.
	 * La fecha y hora inicial de la solicitud se obtienen a partir de la fecha y hora del sistema.
	 * @param Prestamo $prestamo Préstamo desde el cual se va a generar la instancia.
	 * @return SolicitudExpedienteExterno Nueva instancia de SolicitudExpedienteExterno generada a partir del Prestamo.
	 */
	public function ObtenerInstanciaSolicitudExpedienteExterno(Prestamo $prestamo)
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
		$solicitud->Set_fecha_solicitud_hcd_FromDateTime(new DateTime());
		$solicitud->fecha_solicitud_ee = null;
		$solicitud->fecha_ingresado_ee = null;
		$solicitud->fecha_devuelto_ee = null;
		$solicitud->fecha_anulado_ee = null;
		$solicitud->id_usuario = $prestamo->id_usuario;
		$solicitud->observaciones = "Solicitud generada automáticamente a partir del préstamo del expediente ".$prestamo->ToStringDescription();

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
	public function GenerarNuevaSolicitudExpedienteExterno(SolicitudExpedienteExterno $solicitud_devuelta)
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
		$solicitud->Set_fecha_solicitud_hcd_FromDateTime(new DateTime());
		$solicitud->fecha_solicitud_ee = null;
		$solicitud->fecha_ingresado_ee = null;
		$solicitud->fecha_devuelto_ee = null;
		$solicitud->fecha_anulado_ee = null;
		$solicitud->id_usuario = $solicitud_devuelta->id_usuario;
		$solicitud->observaciones = "Solicitud generada automáticamente a partir de la devolución al Ente Externo del expediente ".$solicitud_devuelta->ToStringDescription();

		return $solicitud;
	}

	/**
	 * Determina si un préstamo se encuentra en su estado final.
	 * @param Prestamo $prestamo
	 * @return bool True en caso de estar en un estado final. False en caso contrario.
	 * @throws InvalidArgumentException
	 */
	public function EnEstadoFinalPrestamo(Prestamo $prestamo)
	{
		return ($prestamo->estado == Prestamo::E_DEVUELTO) || ($prestamo->estado == Prestamo::E_ANULADO);
	}

	/**
	 * Valida un préstamo, verificando el valor de sus atributos. En caso de error, devuelve una excepción.
	 * @param Prestamo $prestamo Préstamo a validar.
	 * @throws Exception
	 * @return Prestamo La misma instancia validada.
	 */
	public function ValidarPrestamo(Prestamo $prestamo)
	{
		// Validación de expresiones regulares (sin nulos permitidos)
		$prestamo->anio = RegExValidator::Validar($prestamo->anio, RegExValidator::PATRON_NUMEROS, false, 'Año del expediente');
		$prestamo->tipo = RegExValidator::Validar($prestamo->tipo, RegExValidator::PATRON_TIPO_EXPEDIENTE, false, 'Tipo del expediente');
		$prestamo->numero = RegExValidator::Validar($prestamo->numero, RegExValidator::PATRON_NUMEROS, false, 'Número del expediente');
		$prestamo->cuerpo = RegExValidator::Validar($prestamo->cuerpo, RegExValidator::PATRON_NUMEROS, false, 'Cuerpo del expediente');
		$prestamo->alcance = RegExValidator::Validar($prestamo->alcance, RegExValidator::PATRON_NUMEROS, false, 'Alcance del expediente');
		$prestamo->digito = RegExValidator::Validar($prestamo->digito, RegExValidator::PATRON_NUMEROS, false, 'Dígito del expediente');
		$prestamo->cuerpoalcance = RegExValidator::Validar($prestamo->cuerpoalcance, RegExValidator::PATRON_NUMEROS, false, 'Cuerpo del alcance del expediente');
		$prestamo->anexoalcance = RegExValidator::Validar($prestamo->anexoalcance, RegExValidator::PATRON_NUMEROS, false, 'Anexo del alcance del expediente');
		$prestamo->cuerpoanexoalcance = RegExValidator::Validar($prestamo->cuerpoanexoalcance, RegExValidator::PATRON_NUMEROS, false, 'Cuerpo del anexo del alcance del expediente');
		$prestamo->anexo = RegExValidator::Validar($prestamo->anexo, RegExValidator::PATRON_NUMEROS, false, 'Anexo del expediente');
		$prestamo->cuerpoanexo = RegExValidator::Validar($prestamo->cuerpoanexo, RegExValidator::PATRON_NUMEROS, false, 'Cuerpo del anexo del expediente');

		// Esta es la unica validacion de fecha que se mantiene como extra, porque tambien verifica que el atributo no sea nulo.
		$prestamo->fecha_solicitud = RegExValidator::Validar($prestamo->fecha_solicitud, RegExValidator::PATRON_FECHA_HORA, false, 'Fecha de solicitud del expediente');

		$prestamo->solicitante_tipo = RegExValidator::Validar($prestamo->solicitante_tipo, RegExValidator::PATRON_LETRAS_NO_VACIO, false, 'Tipo de solicitante del expediente');
		$prestamo->solicitante_codigo = RegExValidator::Validar($prestamo->solicitante_codigo, RegExValidator::PATRON_CODIGO_ALFANUMERICO_NO_VACIO, false, 'Código de solicitante del expediente');

		$prestamo->estado = RegExValidator::Validar($prestamo->estado, RegExValidator::PATRON_ESTADO_PRESTAMO_EXPEDIENTE, false, 'Estado del préstamo del expediente');

		$prestamo->id_usuario = RegExValidator::Validar($prestamo->id_usuario, RegExValidator::PATRON_NUMEROS, false, 'Identificador de usuario');

		// Validación de expresiones regulares (con nulos permitidos)
// Esas validaciones se anulan porque resultan redundantes. Ver validaciones de fechas mas adelante.
// 		$prestamo->fecha_prestado = RegExValidator::Validar($prestamo->fecha_prestado, RegExValidator::PATRON_FECHA_HORA, true, 'Fecha de préstamo del expediente');
// 		$prestamo->fecha_devuelto = RegExValidator::Validar($prestamo->fecha_devuelto, RegExValidator::PATRON_FECHA_HORA, true, 'Fecha de devolución del expediente');
// 		$prestamo->fecha_anulado = RegExValidator::Validar($prestamo->fecha_anulado, RegExValidator::PATRON_FECHA_HORA, true, 'Fecha de anulación del préstamo del expediente');

		$prestamo->libro_numero = RegExValidator::Validar($prestamo->libro_numero, RegExValidator::PATRON_CODIGO_ALFANUMERICO_NO_VACIO, true, 'Libro-Número del expediente');
		$prestamo->libro_folio = RegExValidator::Validar($prestamo->libro_folio, RegExValidator::PATRON_CODIGO_ALFANUMERICO_NO_VACIO, true, 'Libro-Folio del expediente');
		$prestamo->observaciones_prestamo = RegExValidator::Validar($prestamo->observaciones_prestamo, RegExValidator::PATRON_SEGURO_SIGNOS, true, 'Observaciones del préstamo del expediente');

		// Validación de valor de atributos
		// XXXX 26/07/2017 SE QUITÓ LA SIGUIENTE VALIDACIÓN: ($prestamo->anio < 1983) ||
		if (($prestamo->anio > date("Y")))
			throw new Exception("Año expediente inválido.");

		// Obtengo las fechas usando metodos especiales de la clase Prestamo, los cuales
		// si fallan (por tener basura en el atributo fecha), me lanzan una excepción.
		$dummy = $prestamo->Get_fecha_solicitud_AsDateTime();
		$dummy = $prestamo->Get_fecha_prestado_AsDateTime();
		$dummy = $prestamo->Get_fecha_devuelto_AsDateTime();
		$dummy = $prestamo->Get_fecha_anulado_AsDateTime();

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
	 * @param string $pOrdenSentido Criterio de ordenamiento de la colección resultado (ORDEN_ASCENDENTE, ORDEN_DESCENDENTE).
	 * @param integer $pLimiteCantidad Limite de cantidad de resultados a devolver (utilizado normalmente para paginación).
	 * @param integer $pLimiteOffset Offset de resultados a devolver (utilizado normalmente para paginación).
	 * @return array Array asociativo de los préstamos seleccionados.
	 * @throws RuntimeException
	 */
	public function ObtenerSolicitudesExpedientesExternos($pAnio, $pTipo, $pNumero, $pCuerpo = null, $pAlcance  = null, $pDigito = null,
			$pCuerpoalcance = null, $pAnexoalcance = null, $pCuerpoanexoalcance = null, $pAnexo = null, $pCuerpoanexo = null,
			$pFecha_solicitud_hcd_desde = null, $pFecha_solicitud_hcd_hasta = null,
			array $pEstados = null,
			array $pOrdenColumnas = null, $pOrdenSentido = ORDEN_ASCENDENTE,
			$pLimiteCantidad = null, $pLimiteOffset = null)
	{
		$this->db->conectar();

		// Traigo la información básica de los expedientes (la cabecera)
		$resultado_solicitudes = $this->db->ObtenerSolicitudesExpedientesExternos($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance , $pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo,	$pCuerpoanexo,
				$pFecha_solicitud_hcd_desde, $pFecha_solicitud_hcd_hasta,
				$pEstados,
				$pOrdenColumnas, $pOrdenSentido,
				$pLimiteCantidad, $pLimiteOffset);

		// Transformo el array de resultados en un array de instancias de expediente
		$solicitudes = ng_helper::arrayResultToInstance($resultado_solicitudes, 'SolicitudExpedienteExterno');

		$this->db->desconectar();

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
	 * @return Prestamo Devuelve una instancia de Prestamo, o NULL si no se encuentra.
	 * @throws RuntimeException
	 */
	public function ObtenerSolicitudExpedienteExterno($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance, $pDigito,
			$pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
			$pFecha_solicitud_hcd)
	{
		$solicitudes_ee = $this->ObtenerSolicitudesExpedientesExternos(
				$pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance, $pDigito,
				$pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
				$pFecha_solicitud_hcd, $pFecha_solicitud_hcd); // Repito la fecha de solicitud para obtener exactamante un prestamo

		if (count($solicitudes_ee) == 1)
		{
			return $solicitudes_ee[0]; // devuelvo la misma solicitud, pero desde la capa de datos.
		}
		else if (count($solicitudes_ee) == 0)
		{
			return null; // no encuentro la solicitud
		}
		else
		{
			//Si encuentro la solicitud que acabo de guardar mas de una vez, algo ha sucedido... lanzo una excepcion
			throw new RuntimeException("Falló la ejecución de nb_prestamos.ObtenerSolicitudExpedientesExternos: Se ha encontrado mas de una solicitud para una búsqueda de resultado único.");
		}

	}

	/**
	 * Recarga la información de un préstamo.
	 * @param SolicitudExpedienteExterno $solicitud_ee Solicitud de expediente externo de la cual se desea recargar su información.
	 * @throws RuntimeException
	 * @return Prestamo Instancia de la solicitud de expediente externo buscada, con sus datos actualizados. Si no se encuentra, devuelve NULL.
	 */
	public function RecargarSolicitudExpedienteExterno(SolicitudExpedienteExterno $solicitud_ee)
	{
		return $this->ObtenerSolicitudExpedienteExterno(
				$solicitud_ee->anio, $solicitud_ee->tipo, $solicitud_ee->numero, $solicitud_ee->cuerpo, $solicitud_ee->alcance,
				$solicitud_ee->digito, $solicitud_ee->cuerpoalcance, $solicitud_ee->anexoalcance, $solicitud_ee->cuerpoanexoalcance, $solicitud_ee->anexo, $solicitud_ee->cuerpoanexo,
				$solicitud_ee->fecha_solicitud_hcd);
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
	public function ObtenerSolicitudesExpedientesExternosCantidadResultados($pAnio, $pTipo, $pNumero, $pCuerpo = null, $pAlcance  = null, $pDigito = null,
			$pCuerpoalcance = null, $pAnexoalcance = null, $pCuerpoanexoalcance = null, $pAnexo = null,	$pCuerpoanexo = null,
			$pFecha_solicitud_hcd_desde = null, $pFecha_solicitud_hcd_hasta = null,
			array $pEstados = null)
	{
		$this->db->conectar();

		// Obtengo la cantidad de resultados
		$cantidad = $this->db->ObtenerSolicitudesExpedientesExternosCantidadResultados($pAnio, $pTipo, $pNumero, $pCuerpo, $pAlcance , $pDigito, $pCuerpoalcance, $pAnexoalcance, $pCuerpoanexoalcance, $pAnexo, $pCuerpoanexo,
				$pFecha_solicitud_hcd_desde, $pFecha_solicitud_hcd_hasta,
				$pEstados);

		$this->db->desconectar();

		return $cantidad;
	}

	/**
	 * Valida una solicitud de expediente externo, verificando el valor de sus atributos. En caso de error, devuelve una excepción.
	 * @param SolicitudExpedienteExterno $solicitud_ee Solicitud de expediente externo a validar.
	 * @throws Exception
	 * @return SolicitudExpedienteExterno La misma instancia validada.
	 */
	public function ValidarSolicitudExpedienteExterno(SolicitudExpedienteExterno $solicitud_ee)
	{
		// Validación de expresiones regulares (sin nulos permitidos)
		$solicitud_ee->anio = RegExValidator::Validar($solicitud_ee->anio, RegExValidator::PATRON_NUMEROS, false, 'Año del expediente externo');
		$solicitud_ee->tipo = RegExValidator::Validar($solicitud_ee->tipo, RegExValidator::PATRON_TIPO_EXPEDIENTE, false, 'Tipo del expediente externo');
		$solicitud_ee->numero = RegExValidator::Validar($solicitud_ee->numero, RegExValidator::PATRON_NUMEROS, false, 'Número del expediente externo');
		$solicitud_ee->cuerpo = RegExValidator::Validar($solicitud_ee->cuerpo, RegExValidator::PATRON_NUMEROS, false, 'Cuerpo del expediente externo');
		$solicitud_ee->alcance = RegExValidator::Validar($solicitud_ee->alcance, RegExValidator::PATRON_NUMEROS, false, 'Alcance del expediente externo');
		$solicitud_ee->digito = RegExValidator::Validar($solicitud_ee->digito, RegExValidator::PATRON_NUMEROS, false, 'Dígito del expediente externo');
		$solicitud_ee->cuerpoalcance = RegExValidator::Validar($solicitud_ee->cuerpoalcance, RegExValidator::PATRON_NUMEROS, false, 'Cuerpo del alcance del expediente externo');
		$solicitud_ee->anexoalcance = RegExValidator::Validar($solicitud_ee->anexoalcance, RegExValidator::PATRON_NUMEROS, false, 'Anexo del alcance del expediente externo');
		$solicitud_ee->cuerpoanexoalcance = RegExValidator::Validar($solicitud_ee->cuerpoanexoalcance, RegExValidator::PATRON_NUMEROS, false, 'Cuerpo del anexo del alcance del expediente externo');
		$solicitud_ee->anexo = RegExValidator::Validar($solicitud_ee->anexo, RegExValidator::PATRON_NUMEROS, false, 'Anexo del expediente externo');
		$solicitud_ee->cuerpoanexo = RegExValidator::Validar($solicitud_ee->cuerpoanexo, RegExValidator::PATRON_NUMEROS, false, 'Cuerpo del anexo del expediente externo');

		// Esta es la unica validacion de fecha que se mantiene como extra, porque tambien verifica que el atributo no sea nulo.
		$solicitud_ee->fecha_solicitud_hcd = RegExValidator::Validar($solicitud_ee->fecha_solicitud_hcd, RegExValidator::PATRON_FECHA_HORA, false, 'Fecha de solicitud al HCD del expediente externo');

		$solicitud_ee->estado = RegExValidator::Validar($solicitud_ee->estado, RegExValidator::PATRON_ESTADO_SOLICITUD_EXPEDIENTE_EXTERNO, false, 'Estado de la solicitud del expediente externo');

		$solicitud_ee->id_usuario = RegExValidator::Validar($solicitud_ee->id_usuario, RegExValidator::PATRON_NUMEROS, false, 'Identificador de usuario');

		$solicitud_ee->observaciones = RegExValidator::Validar($solicitud_ee->observaciones, RegExValidator::PATRON_SEGURO_SIGNOS, true, 'Observaciones del préstamo del expediente externo');

		// Validación de valor de atributos
		if (($solicitud_ee->anio < 1900) || ($solicitud_ee->anio > date("Y")))
			throw new Exception("Año expediente externo inválido. Rango válido: 1900 - ".date("Y"));

		// Obtengo las fechas usando metodos especiales de la clase Prestamo, los cuales
		// si fallan (por tener basura en el atributo fecha), me lanzan una excepción.
		$dummy = $solicitud_ee->Get_fecha_solicitud_hcd_AsDateTime();
		$dummy = $solicitud_ee->Get_fecha_solicitud_ee_AsDateTime();
		$dummy = $solicitud_ee->Get_fecha_ingresado_ee_AsDateTime();
		$dummy = $solicitud_ee->Get_fecha_devuelto_ee_AsDateTime();
		$dummy = $solicitud_ee->Get_fecha_anulado_ee_AsDateTime();

		// Devuelvo el prestamo
		return $solicitud_ee;
	}

	/**
	 * Guarda una solicitud de expediente externo, persistiendo su estado. Previo a guardar la solicitud de expediente externo, ejecuta la validación de la misma.
	 * @param SolicitudExpedienteExterno $solicitud_ee Instancia de solicitud de expediente externo a ser persistida.
	 * @param bool $recargar Si es TRUE, una vez guardada la solicitud de expediente externo, la vuelve a leer desde el origen de datos (esto permite "refrescar" campos calculados).
	 * @return SolicitudExpedienteExterno
	 * @throws RunTimeException
	 */
	public function GuardarSolicitudExpedienteExterno(SolicitudExpedienteExterno $solicitud_ee, $recargar = FALSE)
	{
		// Antes que nada, valido la solicitud de expediente externo (si falla, lanza una excepción).
		$solicitud_ee = $this->ValidarSolicitudExpedienteExterno($solicitud_ee);

		$this->db->conectar();

		$this->db->GuardarSolicitudExpedienteExterno(
				$solicitud_ee->anio, $solicitud_ee->tipo, $solicitud_ee->numero, $solicitud_ee->cuerpo, $solicitud_ee->alcance,
				$solicitud_ee->digito, $solicitud_ee->cuerpoalcance, $solicitud_ee->anexoalcance, $solicitud_ee->cuerpoanexoalcance, $solicitud_ee->anexo, $solicitud_ee->cuerpoanexo,
				$solicitud_ee->fecha_solicitud_hcd,	$solicitud_ee->fecha_solicitud_ee,	$solicitud_ee->fecha_ingresado_ee, $solicitud_ee->fecha_devuelto_ee, $solicitud_ee->fecha_anulado_ee,
				$solicitud_ee->estado,
				$solicitud_ee->observaciones, $solicitud_ee->id_usuario);

		$this->db->desconectar();

		// Se registra en la auditoría el ingreso de la Solicitud.
		$this->RegistrarLogParaSolicitudEE($solicitud_ee, "ALTA");

		// Si se pudo guardar y el programador lo desea, hago un "refresh" del dato guardado.
		if ($recargar)
		{
			$solicitud_ee_recargada = $this->RecargarSolicitudExpedienteExterno($solicitud_ee);
			if ($solicitud_ee_recargada != null)
			{
				return $solicitud_ee_recargada; // devuelvo el mismo préstamo.
			}
			else
			{
				//Si no encuentro la solicitud de expediente externo que acabo de guardar, algo ha sucedido... lanzo una excepcion
				throw new RuntimeException("Falló la ejecución de ng_prestamos.GuardarSolicitudExpedienteExterno: No se encuentra la solicitud de expediente externo que se acaba de guardar.");
			}
		}
		else
			return $solicitud_ee;
	}

	/**
	 * A partir de una solicitud de expediente externo, este método devuelve una coleccion de estados
	 * siguientes posibles.
	 * Si no hubieran estados siguientes, la coleccion queda vacía.
	 * @param SolicitudExpedienteExterno $solicitud_ee
	 * @return array Arreglo de estados siguientes posibles.
	 * @throws InvalidArgumentException
	 */
	public function ObtenerEstadosSiguientesExpedienteExterno(SolicitudExpedienteExterno $solicitud_ee)
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
				$devoluciones_pendientes = $this->ObtenerPrestamosCantidadResultados(
						$solicitud_ee->anio, $solicitud_ee->tipo, $solicitud_ee->numero, $solicitud_ee->cuerpo, $solicitud_ee->alcance,
						$solicitud_ee->digito, $solicitud_ee->cuerpoalcance, $solicitud_ee->anexoalcance, $solicitud_ee->cuerpoanexoalcance, $solicitud_ee->anexo, $solicitud_ee->cuerpoanexo,
						null, null, null, null, array(Prestamo::E_PRESTADO));

				if ($devoluciones_pendientes == 0)
				{
					$estados_siguientes[] = SolicitudExpedienteExterno::E_DEVUELTO_EE; // Se permite devolver el expediente
				}
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
	 * Toma una solicitud externa y la "mueve" en el circuito de solicitudes de expedientes externos, modificando su
	 * estado al estado siguiente.
	 * @param SolicitudExpedienteExterno $solicitud_ee Solicitud externa a avanzar en el circuito.
	 * @param string $estadoSiguiente Estado al que se desea mover la solicitud externa.
	 * @param string $fechaEstado Fecha en la cual se desea mover la solicitud externa al nuevo estado. Si es null, la fecha utilizada es "getdate()". La máscara de la fecha debe ser 'Y-m-d H:i:s'.
	 * @return SolicitudExpedienteExterno Intancia de la solicitud externa modificada.
	 * @throws InvalidArgumentException
	 */
	public function CambiarEstadoExpedienteExterno(SolicitudExpedienteExterno $solicitud_ee, $estadoSiguiente, $fechaEstado = null)
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
		$fechaActual = $solicitud_ee->ObtenerFechaActualAsDateTime();
		if (($fechaActual != null) && ($fechaActual > $fecha))
			throw new InvalidArgumentException("La nueva fecha no puede ser anterior a la fecha del ultimo estado. Fecha actual: ".$fechaActual->format("Y-m-d H:i:s").", nueva fecha: ".$fecha->format("Y-m-d H:i:s"));

		// Obtengo los estados siguientes posibles
		$estadosPosibles = $this->ObtenerEstadosSiguientesExpedienteExterno($solicitud_ee);
		// Si el estado que se me indico como parametro no existe entre los posibles siguientes, lanzo una excepcion
		$estadoValido = false;
		foreach ($estadosPosibles as $estado) {
			if ($estado == $estadoSiguiente)
				$estadoValido = true;
		}
		if (!$estadoValido)
			throw new InvalidArgumentException("El nuevo estado no es válido como posible estado siguiente. Estado actual: ".$solicitud_ee->estado.", nuevo estado: ".$estadoSiguiente);

		// Si llegue hasta aqui es que todo esta bien. Procedo a actualizar el estado y la fecha.
		// (Por las dudas, verifico que el estadoSiguiente sea valido)
		switch ($estadoSiguiente)
		{
			case SolicitudExpedienteExterno::E_SOLICITADO_HCD : // Solicitado al HCD
				$solicitud_ee->Set_fecha_solicitud_hcd_FromDateTime($fecha);
				break;

			case SolicitudExpedienteExterno::E_SOLICITADO_EE : // Solicitado al Ente externo
				$solicitud_ee->Set_fecha_solicitud_ee_FromDateTime($fecha);
				break;

			case SolicitudExpedienteExterno::E_INGRESADO_EE : // Ingresado desde el ente externo
				$solicitud_ee->Set_fecha_ingresado_ee_FromDateTime($fecha);
				break;

			case SolicitudExpedienteExterno::E_DEVUELTO_EE : // Devuelto al ente externo
				$solicitud_ee->Set_fecha_devuelto_ee_FromDateTime($fecha);
				break;

			case SolicitudExpedienteExterno::E_ANULADO_EE : // Solicitud externa anulada
				$solicitud_ee->Set_fecha_anulado_ee_FromDateTime($fecha);
				break;

			default:
				throw new InvalidArgumentException("El nuevo estado es inválido. Estado: ".$estadoSiguiente);
		}
		$solicitud_ee->estado = $estadoSiguiente;

		return $solicitud_ee;
	}

	/**
	 * Se registra en la auditoría el movimiento realizado en un préstamo.
	 * @param Prestamo $prestamo
	 * @param string $operacion
	 */
	public function RegistrarLogParaPrestamo(Prestamo $prestamo, $operacion)
	{
		// Se obtiene una instancia del modelo de Auditoría de Expedientes
		$modelo = new auditoriaExpedientesModel();

		$datos_log = Array();
		$datos_log['operacion_log']     = $operacion;
		$datos_log['tabla_log']         = 'expe_prestamos';
		$datos_log['anio_log']          = $prestamo->anio;
		$datos_log['tipo_log']          = $prestamo->tipo;
		$datos_log['numero_log']        = $prestamo->numero;
		$datos_log['cuerpo_log']        = $prestamo->cuerpo;
		$datos_log['alcance_log']       = $prestamo->alcance;
		$datos_log['fecha_log']         = "null";
		$datos_log['orden_log']         = "null";
		$datos_log['observaciones_log'] = "Se guarda un préstamo para el expediente ".$prestamo->anio.'-'.$prestamo->tipo.'-'.$prestamo->numero;

		// Se registra en auditoria el movimiento
		$modelo->registrarMovimiento($datos_log);
	}

	/**
	 * Se registra en la auditoría el ingreso de una Solicitud.
	 * @param SolicitudExpedienteExterno $solicitud_ee
	 * @param string $operacion
	 */
	public function RegistrarLogParaSolicitudEE(SolicitudExpedienteExterno $solicitud_ee, $operacion)
	{
		// Se obtiene una instancia del modelo de Auditoría de Expedientes
		$modelo = new auditoriaExpedientesModel();

		$datos_log = Array();
		$datos_log['operacion_log']     = $operacion;
		$datos_log['tabla_log']         = 'expe_expedientes_externos';
		$datos_log['anio_log']          = $solicitud_ee->anio;
		$datos_log['tipo_log']          = $solicitud_ee->tipo;
		$datos_log['numero_log']        = $solicitud_ee->numero;
		$datos_log['cuerpo_log']        = $solicitud_ee->cuerpo;
		$datos_log['alcance_log']       = $solicitud_ee->alcance;
		$datos_log['fecha_log']         = "null";
		$datos_log['orden_log']         = "null";
		$datos_log['observaciones_log'] = "Se guarda una solicitud para el expediente externo ".$solicitud_ee->anio.'-'.$solicitud_ee->tipo.'-'.$solicitud_ee->numero;

		// Se registra en auditoria el movimiento
		$modelo->registrarMovimiento($datos_log);
	}

	/**
	 * Elimina un Préstamo (de forma lógica)
	 * @param Prestamo $prestamo instancia de Prestamo a ser eliminada
	 */
	public function EliminarPrestamo(Prestamo $prestamo)
	{
		$this->db->conectar();

		$this->db->EliminarPrestamo(
				$prestamo->anio, $prestamo->tipo, $prestamo->numero, $prestamo->cuerpo, $prestamo->alcance,
				$prestamo->digito, $prestamo->cuerpoalcance, $prestamo->anexoalcance, $prestamo->cuerpoanexoalcance,
				$prestamo->anexo, $prestamo->cuerpoanexo, $prestamo->fecha_solicitud);

		$this->db->desconectar();
	}

	/**
	 * Elimina una Solicitud (de forma lógica)
	 * @param SolicitudExpedienteExterno $solicitud_ee instancia de SolicitudExpedienteExterno a ser eliminada
	 */
	public function EliminarSolicitud(SolicitudExpedienteExterno $solicitud_ee)
	{
		$this->db->conectar();

		$this->db->EliminarSolicitud(
				$solicitud_ee->anio, $solicitud_ee->tipo, $solicitud_ee->numero, $solicitud_ee->cuerpo, $solicitud_ee->alcance,
				$solicitud_ee->digito, $solicitud_ee->cuerpoalcance, $solicitud_ee->anexoalcance, $solicitud_ee->cuerpoanexoalcance,
				$solicitud_ee->anexo, $solicitud_ee->cuerpoanexo, $solicitud_ee->fecha_solicitud_hcd);

		$this->db->desconectar();
	}
}
?>
