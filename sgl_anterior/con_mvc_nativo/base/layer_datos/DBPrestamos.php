<?php

/**
 * Capa de acceso a datos (persistencia) específica de la gestion de tablas de Préstamos.
 */
class DBPrestamos extends DBBaseClass {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************

	protected $t_expe_prestamos 		   = 'expe_prestamos'; 			  //!< // Identificador de tabla 'expe_prestamos'
	protected $t_expe_lugares 			   = 'expe_lugares'; 			  //!< // Identificador de tabla 'expe_lugares'
	protected $t_expe_expedientes_externos = 'expe_expedientes_externos'; //!< // Identificador de tabla 'expe_expedientes_externos'
	protected $t_expe_expedientes 		   = 'expe_expedientes'; 		  //!< // Identificador de tabla 'expe_expedientes' 	

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct() {
		parent::__construct();
	}

	// ************************************************************************
	// Prestamos
	// ************************************************************************
	
	/**
	 * [obtenerPrestamos description]
	 * @param  [type]     $panio                  [description]
	 * @param  [type]     $ptipo                  [description]
	 * @param  [type]     $pnumero                [description]
	 * @param  [type]     $pcuerpo                [description]
	 * @param  [type]     $palcance               [description]
	 * @param  [type]     $pdigito                [description]
	 * @param  [type]     $pcuerpoalcance         [description]
	 * @param  [type]     $panexoalcance          [description]
	 * @param  [type]     $pcuerpoanexoalcance    [description]
	 * @param  [type]     $panexo                 [description]
	 * @param  [type]     $pcuerpoanexo           [description]
	 * @param  [type]     $pfecha_solicitud_desde [description]
	 * @param  [type]     $pfecha_solicitud_hasta [description]
	 * @param  [type]     $psolicitante_tipo      [description]
	 * @param  [type]     $psolicitante_codigo    [description]
	 * @param  array|null $pestados               [description]
	 * @param  array|null $pOrdenColumnas         [description]
	 * @param  [type]     $pLimiteCantidad        [description]
	 * @param  [type]     $pLimiteOffset          [description]
	 * @return [type]                             [description]
	 */
	public function obtenerPrestamos(
		// Parametros
		$panio = null, $ptipo = null, $pnumero = null, 
		$pcuerpo = null, $palcance  = null, $pdigito = null,
		$pcuerpoalcance = null, $panexoalcance = null, $pcuerpoanexoalcance = null, $panexo = null,	$pcuerpoanexo = null, 
		$pfecha_solicitud_desde = null, $pfecha_solicitud_hasta = null, 
		$psolicitante_tipo = null, $psolicitante_codigo = null,
		array $pestados = null, 
		// Control de consulta
		array $pOrdenColumnas = null, 
		$pLimiteCantidad = null, 
		$pLimiteOffset = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
			SELECT 	
				T.`anio`,
				T.`tipo`,
				T.`numero`,
				T.`cuerpo`,
				T.`alcance`,
				T.`digito`,
				T.`cuerpoalcance`,
				T.`anexoalcance`,
				T.`cuerpoanexoalcance`,
				T.`anexo`,
				T.`cuerpoanexo`,
				T.`fecha_solicitud`,
				T.`fecha_prestado`,
				T.`fecha_devuelto`,
				T.`fecha_anulado`,
				T.`solicitante_tipo`,
				T.`solicitante_codigo`,
				ELUGAR.`descripcion_grp` as ro_solicitante_nombre,
				T.`libro_numero`,
				T.`libro_folio`,
				T.`estado`,
				T.`observaciones_prestamo`,
				T.`id_usuario`
			FROM
				`{$this->t_expe_prestamos}` as T
			LEFT JOIN `{$this->t_expe_lugares}` ELUGAR ON
				(ELUGAR.`tipo_grp` = T.`solicitante_tipo` AND ELUGAR.`codigo_grp` = T.`solicitante_codigo`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		$builder->criteriosWhere->agregarCriterioConstante('T.activo = 1');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.digito', IGUAL_A, $pdigito);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoalcance', IGUAL_A, $pcuerpoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexoalcance', IGUAL_A, $panexoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexoalcance', IGUAL_A, $pcuerpoanexoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexo', IGUAL_A, $panexo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexo', IGUAL_A, $pcuerpoanexo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_solicitud', MAYOR_IGUAL_A, $pfecha_solicitud_desde);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_solicitud', MENOR_IGUAL_A, $pfecha_solicitud_hasta);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.solicitante_tipo', IGUAL_A, $psolicitante_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.solicitante_codigo', IGUAL_A, $psolicitante_codigo);
		$builder->criteriosWhere->agregarCriterioMultiple(P_TEXT, 'T.estado', $pestados);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * [obtenerPrestamosCantidad description]
	 * @param  [type]     $panio                  [description]
	 * @param  [type]     $ptipo                  [description]
	 * @param  [type]     $pnumero                [description]
	 * @param  [type]     $pcuerpo                [description]
	 * @param  [type]     $palcance               [description]
	 * @param  [type]     $pdigito                [description]
	 * @param  [type]     $pcuerpoalcance         [description]
	 * @param  [type]     $panexoalcance          [description]
	 * @param  [type]     $pcuerpoanexoalcance    [description]
	 * @param  [type]     $panexo                 [description]
	 * @param  [type]     $pcuerpoanexo           [description]
	 * @param  [type]     $pfecha_solicitud_desde [description]
	 * @param  [type]     $pfecha_solicitud_hasta [description]
	 * @param  [type]     $psolicitante_tipo      [description]
	 * @param  [type]     $psolicitante_codigo    [description]
	 * @param  array|null $pestados               [description]
	 * @return [type]                             [description]
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
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
			SELECT 	
				count(*) as cantidad
			FROM
				`{$this->t_expe_prestamos}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		$builder->criteriosWhere->agregarCriterioConstante('T.activo = 1');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.digito', IGUAL_A, $pdigito);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoalcance', IGUAL_A, $pcuerpoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexoalcance', IGUAL_A, $panexoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexoalcance', IGUAL_A, $pcuerpoanexoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexo', IGUAL_A, $panexo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexo', IGUAL_A, $pcuerpoanexo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_solicitud', MAYOR_IGUAL_A, $pfecha_solicitud_desde);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_solicitud', MENOR_IGUAL_A, $pfecha_solicitud_hasta);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.solicitante_tipo', IGUAL_A, $psolicitante_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.solicitante_codigo', IGUAL_A, $psolicitante_codigo);
		$builder->criteriosWhere->agregarCriterioMultiple(P_TEXT, 'T.estado', $pestados);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * Obtiene una array de filas correspondientes a solicitudes de expedientes externos en base a diferentes criterios de selección.
	 * @param integer 	$panio 							Año del expediente.
	 * @param string  	$ptipo 							Tipo de expediente.
	 * @param integer 	$pnumero 						Número del expediente.
	 * @param integer 	$pcuerpo 						Cuerpo del expediente.
	 * @param integer 	$palcance 						Alcance del expediente.
	 * @param string  	$pdigito 						Dígito del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoalcance 				Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$panexoalcance 					Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoanexoalcance 			Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$panexo 						Anexo del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoanexo 					Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string  	$pfecha_solicitud_hcd_desde 	Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_hasta.
	 * @param string  	$pfecha_solicitud_hcd_hasta 	Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_desde.
	 * @param array   	$pestados 						Arreglo de estados para filtro. Conjunto de estados a devolver. Si se indica null o un array vacío, no filtra por estado.
	 * @param array   	$pOrdenColumnas 				Array de nombres de atributos para ordernar la colección resultado.
	 * @param integer 	$pLimiteCantidad 				Limite de cantidad de resultados a devolver (utilizado normalmente para paginación).
	 * @param integer 	$pLimiteOffset 					Offset de resultados a devolver (utilizado normalmente para paginación).
	 * @return array 	Array asociativo de las Solicitudes de Expedientes del Ejecutivo seleccionados.
	 * @throws RuntimeException
	 */
	public function obtenerSolicitudesExpedientesExternos(
		// Parametros
		$panio, $ptipo, $pnumero, 
		$pcuerpo = null, $palcance  = null, $pdigito = null,
		$pcuerpoalcance = null, $panexoalcance = null, $pcuerpoanexoalcance = null, $panexo = null,	$pcuerpoanexo = null, 
		$pfecha_solicitud_hcd_desde = null, $pfecha_solicitud_hcd_hasta = null, 
		array $pestados = null, 
		// Control de consulta
		array $pOrdenColumnas = null, 
		$pLimiteCantidad = null, 
		$pLimiteOffset = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
			SELECT 	T.anio,
					T.tipo,
					T.numero,
					T.cuerpo,
					T.alcance,
					T.digito,
					T.cuerpoalcance,
					T.anexoalcance,
					T.cuerpoanexoalcance,
					T.anexo,
					T.cuerpoanexo,
					T.fecha_solicitud_hcd,
					T.fecha_solicitud_ee,
					T.fecha_ingresado_ee,
					T.fecha_devuelto_ee,
					T.fecha_anulado_ee,
					T.estado,
					T.observaciones,
					T.id_usuario
			FROM 
				`{$this->t_expe_expedientes_externos}` as T
			
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		$builder->criteriosWhere->agregarCriterioConstante('T.activo = 1');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.digito', IGUAL_A, $pdigito);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoalcance', IGUAL_A, $pcuerpoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexoalcance', IGUAL_A, $panexoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexoalcance', IGUAL_A, $pcuerpoanexoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexo', IGUAL_A, $panexo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexo', IGUAL_A, $pcuerpoanexo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_solicitud_hcd', MAYOR_IGUAL_A, $pfecha_solicitud_hcd_desde);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_solicitud_hcd', MENOR_IGUAL_A, $pfecha_solicitud_hcd_hasta);
		$builder->criteriosWhere->agregarCriterioMultiple(P_TEXT, 'T.estado', $pestados);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;
		
		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * Obtiene la cantidad de resultados para una consulta de Solicitudes de Expedientes Externos 
	 * en base a diferentes criterios de selección. 
	 * En esencia ejecuta la misma consulta que obtenerSolicitudesExpedientesExternos, 
	 * pero en vez de devolver el conjunto de datos, devuelve la cantidad de resultados.
	 * Se utiliza en paginación de resultados.
	 * @param integer 	$panio 						Año del expediente.
	 * @param string 	$ptipo 						Tipo de expediente.
	 * @param integer 	$pnumero 					Número del expediente.
	 * @param integer 	$pcuerpo 					Cuerpo del expediente.
	 * @param integer 	$palcance 					Alcance del expediente.
	 * @param string 	$pdigito 					Dígito del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoalcance 			Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$panexoalcance 				Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoanexoalcance 		Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$panexo 					Anexo del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoanexo 				Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string 	$pfecha_solicitud_hcd_desde Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_hasta.
	 * @param string 	$pfecha_solicitud_hcd_hasta Fecha de solicitud del expediente desde el HCD; funciona como filtro para obtener conjunto con $pFecha_solicitud_hcd_desde.
	 * @param array 	$pestados 					Arreglo de estados para filtro. Conjunto de estados a devolver. Si se indica null o un array vacío, no filtra por estado.
	 * @return integer  Cantidad de resultados para una determinada consulta de solicitudes de expedientes externos.
	 * @throws RuntimeException
	 */
	public function obtenerSolicitudesExpedientesExternosCantidad(
		// Parametros
		$panio, $ptipo, $pnumero, 
		$pcuerpo = null, $palcance  = null, $pdigito = null,
		$pcuerpoalcance = null, $panexoalcance = null, $pcuerpoanexoalcance = null, $panexo = null,	$pcuerpoanexo = null, 
		$pfecha_solicitud_hcd_desde = null, $pfecha_solicitud_hcd_hasta = null, 
		array $pestados = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
			SELECT 	
				count(*) as cantidad
			FROM
				`{$this->t_expe_expedientes_externos}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		$builder->criteriosWhere->agregarCriterioConstante('T.activo = 1');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.digito', IGUAL_A, $pdigito);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoalcance', IGUAL_A, $pcuerpoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexoalcance', IGUAL_A, $panexoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexoalcance', IGUAL_A, $pcuerpoanexoalcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexo', IGUAL_A, $panexo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexo', IGUAL_A, $pcuerpoanexo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_solicitud_hcd', MAYOR_IGUAL_A, $pfecha_solicitud_hcd_desde);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_solicitud_hcd', MENOR_IGUAL_A, $pfecha_solicitud_hcd_hasta);
		$builder->criteriosWhere->agregarCriterioMultiple(P_TEXT, 'T.estado', $pestados);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * Verifica si existe un Expediente del HCD (tipo = E, = N ó = R)
	 * @param integer 	$panio 		Año del expediente.
	 * @param string 	$ptipo 		Tipo de expediente.
	 * @param integer 	$pnumero 	Número del expediente.
	 * @param integer 	$pcuerpo 	Cuerpo del expediente.
	 * @param integer 	$palcance 	Alcance del expediente.
	 * @return [boolean] 			True ó False
	 */
	public function existeExpedienteHCD($panio, $ptipo, $pnumero, $pcuerpo, $palcance)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
			SELECT 	
				count(*) as cantidad
			FROM
				`{$this->t_expe_expedientes}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return ($resultado[0]['cantidad'] > 0);
	}

	/**
	 * Guarda un Préstamo
	 * @param integer 	$panio 						Año del expediente.
	 * @param string 	$ptipo 						Tipo de expediente.
	 * @param integer 	$pnumero 					Número del expediente.
	 * @param integer 	$pcuerpo 					Cuerpo del expediente.
	 * @param integer 	$palcance 					Alcance del expediente.
	 * @param string 	$pdigito 					Dígito del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoalcance 			Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$panexoalcance 				Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoanexoalcance 		Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$panexo 					Anexo del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoanexo 				Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string 	$pfecha_solicitud 			Fecha de Solicitud del préstamo
	 * @param string 	$pfecha_prestado         	Fecha de Prestado del préstamo
	 * @param string 	$pfecha_devuelto         	Fecha de Devolución del préstamo
	 * @param string    $pfecha_anulado          	Fecha de Anulado del préstamo
	 * @param string    $psolicitante_tipo       	Tipo del solicitante.
	 * @param string    $psolicitante_codigo     	Código del solicitante.
	 * @param integer   $plibro_numero           	Número de registro en libro de préstamos.
	 * @param integer   $plibro_folio            	Folio de registro en libro de préstamos.
	 * @param integer   $pestado                 	Estado del préstamo.
	 * @param string    $pobservaciones_prestamo 	Observaciones del préstamo.
	 * @param integer   $pid_usuario             	Identificador de usuario que realiza la operación
	 * @return [type]                          		[description]
	 */
	public function guardarPrestamo($panio, $ptipo, $pnumero, $pcuerpo, $palcance, 
			$pdigito, $pcuerpoalcance, $panexoalcance, $pcuerpoanexoalcance, $panexo, $pcuerpoanexo, 
			$pfecha_solicitud, $pfecha_prestado, $pfecha_devuelto, $pfecha_anulado,
			$psolicitante_tipo, $psolicitante_codigo, $plibro_numero, $plibro_folio,
			$pestado, $pobservaciones_prestamo, $pid_usuario, $pactivo)
	{
		
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_prestamos;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'digito', $pdigito);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoalcance', $pcuerpoalcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anexoalcance', $panexoalcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoanexoalcance', $pcuerpoanexoalcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anexo', $panexo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoanexo', $pcuerpoanexo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_solicitud', $pfecha_solicitud);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_prestado', $pfecha_prestado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_devuelto', $pfecha_devuelto);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_anulado', $pfecha_anulado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'solicitante_tipo', $psolicitante_tipo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'solicitante_codigo', $psolicitante_codigo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'libro_numero', $plibro_numero);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'libro_folio', $plibro_folio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'estado', $pestado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_prestamo', $pobservaciones_prestamo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'activo', $pactivo);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}


	/**
	 * Guarda una Solicitud de Expediente Externo. 
	 * Si no existe en la base de datos, crea una nueva.
	 * Si ya existe, la actualiza (no se modifican claves primarias).
	 * @param integer 	$panio 					Año del expediente.
	 * @param string 	$ptipo 					Tipo de expediente.
	 * @param integer 	$pnumero 				Número del expediente.
	 * @param integer 	$pcuerpo 				Cuerpo del expediente.
	 * @param integer 	$palcance 				Alcance del expediente.
	 * @param string 	$pdigito 				Dígito del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoalcance 		Cuerpo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$panexoalcance 			Anexo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoanexoalcance 	Cuerpo del anexo del alcance del expediente. Para expedientes externos.
	 * @param integer 	$panexo 				Anexo del expediente. Para expedientes externos.
	 * @param integer 	$pcuerpoanexo 			Cuerpo del anexo del expediente. Para expedientes externos.
	 * @param string  	$pfecha_solicitud_hcd 	Fecha de solicitud del préstamo al HCD.
	 * @param string 	$pfecha_solicitud_ee 	Fecha de solicitud del préstamo al ente externo.
	 * @param string 	$pfecha_ingresado_ee 	Fecha de ingresado del préstamo desde el ente externo.
	 * @param string 	$pfecha_devuelto_ee 	Fecha de devolución del préstamo al ente externo.
	 * @param string 	$pfecha_anulado_ee 		Fecha de anulación del préstamo al ente externo.
	 * @param string 	$pestado 				Estado del préstamo.
	 * @param string 	$pobservaciones 		Observaciones del préstamo.
	 * @param integer 	$pid_usuario 			Identificador de usuario de ultima modificación.
	 * @throws RuntimeException
	 */
	public function guardarSolicitudExpedienteExterno($panio, $ptipo, $pnumero, $pcuerpo, $palcance,
			$pdigito, $pcuerpoalcance, $panexoalcance, $pcuerpoanexoalcance, $panexo, $pcuerpoanexo,
			$pfecha_solicitud_hcd, $pfecha_solicitud_ee, $pfecha_ingresado_ee, $pfecha_devuelto_ee, $pfecha_anulado_ee,
			$pestado, $pobservaciones, $pid_usuario, $pactivo)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_expedientes_externos;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'digito', $pdigito);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoalcance', $pcuerpoalcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anexoalcance', $panexoalcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoanexoalcance', $pcuerpoanexoalcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anexo', $panexo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoanexo', $pcuerpoanexo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_solicitud_hcd', $pfecha_solicitud_hcd);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_solicitud_ee', $pfecha_solicitud_ee);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_ingresado_ee', $pfecha_ingresado_ee);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_devuelto_ee', $pfecha_devuelto_ee);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_anulado_ee', $pfecha_anulado_ee);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'estado', $pestado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones', $pobservaciones);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'activo', $pactivo);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}
}