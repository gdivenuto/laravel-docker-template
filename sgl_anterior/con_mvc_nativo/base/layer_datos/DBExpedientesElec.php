<?php

/**
 * Capa de acceso a datos (persistencia) específica de la gestion de Expedientes Electrónicos.
 */
class DBExpedientesElec extends DBBaseClass {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************

	protected $t_expe_expedientes_elec = 'expe_expedientes_elec'; //!< Identificador de tabla 'expe_expedientes_elec'
	protected $t_expe_firmas_expediente_elec = 'expe_firmas_expediente_elec'; //!< Identificador de tabla 'expe_firmas_expediente_elec'
	protected $t_admin_usuarios = 'admin_usuarios'; //!< // Identificador de tabla 'admin_usuarios'

	/**
	 * DBExpedientesElec: Obtiene un array de filas correspondientes a la clase ExpedienteElec en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-16 09:33:42
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  string tipo_actuacion
	 * @param  string detalle
	 * @param  string documento
	 * @param  string documento_hash
	 * @param  string texto_original
	 * @param  bool dec1404
	 * @param  bool embebido
	 * @param  bool es_caratula
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string observaciones	 
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerExpedientesElec(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$ptipo_actuacion = null,
		$pdetalle = null,
		$pdocumento = null,
		$pdocumento_hash = null,
		$ptexto_original = null,
		$pdec1404 = null,
		$pembebido = null,
		$pes_caratula = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pobservaciones = null,	
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
			T.`orden`,
			T.`tipo_actuacion`,
			T.`detalle`,
			T.`documento`,
			T.`documento_hash`,
			T.`texto_original`,
			T.`dec1404`,
			T.`embebido`,
			T.`es_caratula`,
			T.`fecha_hora`,
			T.`id_usuario`,
			T.`observaciones`,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario,
			COUNT(F.`id_firma`) as ro_total_firmas,
			COUNT(CASE WHEN F.`estado` = 'firmado' THEN 1 END) as ro_cant_firmados,
			COUNT(CASE WHEN F.`estado` = 'pendiente' THEN 1 END) as ro_cant_pendientes,
			COUNT(CASE WHEN F.`estado` = 'cancelado' THEN 1 END) as ro_cant_cancelados		
		FROM 
			`{$this->t_expe_expedientes_elec}` as T
		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(T.`id_usuario` = USR.`id_usuario`)
		LEFT JOIN `{$this->t_expe_firmas_expediente_elec}` AS F ON
			(
				T.`anio` = F.`anio` AND 
			    T.`tipo` = F.`tipo` AND 
			    T.`numero` = F.`numero` AND 
			    T.`cuerpo` = F.`cuerpo` AND 
			    T.`alcance` = F.`alcance` AND 
			    T.`orden` = F.`orden`
		    )
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.orden', IGUAL_A, $porden);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo_actuacion', IGUAL_A, $ptipo_actuacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.detalle', IGUAL_A, $pdetalle);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.documento', IGUAL_A, $pdocumento);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.documento_hash', IGUAL_A, $pdocumento_hash);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.texto_original', IGUAL_A, $ptexto_original);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.dec1404', IGUAL_A, $this->boolToInt($pdec1404));
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.embebido', IGUAL_A, $this->boolToInt($pembebido));
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.es_caratula', IGUAL_A, $this->boolToInt($pes_caratula));
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora', IGUAL_A, $pfecha_hora);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el GROUP BY
		$builder->criteriosGroupBy = array('T.anio', 'T.tipo', 'T.numero', 'T.cuerpo', 'T.alcance', 'T.orden');

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientesElec: Obtiene la cantidad de filas correspondientes de la clase ExpedienteElec en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-16 09:33:42
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  string tipo_actuacion
	 * @param  string detalle
	 * @param  string documento
	 * @param  string documento_hash
	 * @param  string texto_original
	 * @param  bool dec1404
	 * @param  bool embebido
	 * @param  bool es_caratula
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string observaciones	 
	 * @return int
	 */
	public function obtenerExpedientesElecCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$ptipo_actuacion = null,
		$pdetalle = null,
		$pdocumento = null,
		$pdocumento_hash = null,
		$ptexto_original = null,
		$pdec1404 = null,
		$pembebido = null,
		$pes_caratula = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pobservaciones = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT 
			count(*) as cantidad
		FROM 
			`{$this->t_expe_expedientes_elec}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.orden', IGUAL_A, $porden);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo_actuacion', IGUAL_A, $ptipo_actuacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.detalle', IGUAL_A, $pdetalle);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.documento', IGUAL_A, $pdocumento);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.documento_hash', IGUAL_A, $pdocumento_hash);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.texto_original', IGUAL_A, $ptexto_original);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.dec1404', IGUAL_A, $this->boolToInt($pdec1404));
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.embebido', IGUAL_A, $this->boolToInt($pembebido));
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.es_caratula', IGUAL_A, $this->boolToInt($pes_caratula));
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora', IGUAL_A, $pfecha_hora);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientesElec: Guarda una instancia de la clase ExpedienteElec en la base de datos.
	 * GenerateClass 0.97.7 beta @ 2022-09-16 09:33:42
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  string tipo_actuacion
	 * @param  string detalle
	 * @param  string documento
	 * @param  string documento_hash
	 * @param  string texto_original
	 * @param  bool dec1404
	 * @param  bool embebido
	 * @param  bool es_caratula
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string observaciones	 
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarExpedienteElec(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$ptipo_actuacion = null,
		$pdetalle = null,
		$pdocumento = null,
		$pdocumento_hash = null,
		$ptexto_original = null,
		$pdec1404 = null,
		$pembebido = null,
		$pes_caratula = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pobservaciones = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_expedientes_elec;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'orden', $porden);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo_actuacion', $ptipo_actuacion);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'detalle', $pdetalle);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'documento', $pdocumento);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'documento_hash', $pdocumento_hash);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'texto_original', $ptexto_original);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'dec1404', $this->boolToInt($pdec1404));
		$builder->mapeoCampos->agregarMapeo(P_INT, 'embebido', $this->boolToInt($pembebido));
		$builder->mapeoCampos->agregarMapeo(P_INT, 'es_caratula', $this->boolToInt($pes_caratula));
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_hora', $pfecha_hora);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones', $pobservaciones);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;		
	}

	/**
	 * DBExpedientesElec: Elimina un conjunto de ExpedientesElec en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-16 09:33:42
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  string tipo_actuacion
	 * @param  string detalle
	 * @param  string documento
	 * @param  string documento_hash
	 * @param  string texto_original
	 * @param  bool dec1404
	 * @param  bool embebido
	 * @param  bool es_caratula
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string observaciones	 
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarExpedientesElec(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$ptipo_actuacion = null,
		$pdetalle = null,
		$pdocumento = null,
		$pdocumento_hash = null,
		$ptexto_original = null,
		$pdec1404 = null,
		$pembebido = null,
		$pes_caratula = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pobservaciones = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($porden) && is_null($ptipo_actuacion) && is_null($pdetalle) && is_null($pdocumento) && is_null($pdocumento_hash) && is_null($ptexto_original) && is_null($pdec1404) && is_null($pes_caratula) && is_null($pfecha_hora) && is_null($pid_usuario) && is_null($pobservaciones))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarExpedientesElec: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_expedientes_elec;
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'orden', IGUAL_A, $porden);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo_actuacion', IGUAL_A, $ptipo_actuacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'detalle', IGUAL_A, $pdetalle);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'documento', IGUAL_A, $pdocumento);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'documento_hash', IGUAL_A, $pdocumento_hash);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'texto_original', IGUAL_A, $ptexto_original);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'dec1404', IGUAL_A, $this->boolToInt($pdec1404));
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'embebido', IGUAL_A, $this->boolToInt($pembebido));
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'es_caratula', IGUAL_A, $this->boolToInt($pes_caratula));
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_hora', IGUAL_A, $pfecha_hora);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones', IGUAL_A, $pobservaciones);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * Obtiene el siguiente número de orden para un expediente electronico.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return [type]           [description]
	 */
	public function obtenerExpedienteElecOrdenSiguiente(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) 
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT 
			IFNULL(MAX(orden)+1, 1) as max_orden
		FROM 
			`{$this->t_expe_expedientes_elec}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['max_orden'];
	}
}
?>