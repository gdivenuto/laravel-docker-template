<?php

/**
 * Capa de acceso a datos (persistencia) específica de la gestion de Revisiones de Expediente Electrónico Pendiente.
 */
class DBRevExpedienteElecPend extends DBBaseClass {

	protected $t_expe_rev_expediente_elec_pend = 'expe_rev_expediente_elec_pend'; //!< Identificador de tabla 'expe_rev_expediente_elec_pend'
	protected $t_expe_expedientes_elec_pend = 'expe_expedientes_elec_pend'; //!< Identificador de tabla 'expe_expedientes_elec_pend'
	protected $t_admin_usuarios = 'admin_usuarios'; //!< Identificador de tabla 'admin_usuarios'

	/**
	 * DBRevExpedienteElecPend: Obtiene un array de filas correspondientes a la clase RevExpedienteElecPend en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-11-16 12:41:32
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_revision
	 * @param  integer id_usuario
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string observaciones	 
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerRevsExpedienteElecPend(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_revision = null,
		$pid_usuario = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
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
			T.`id_revision`,
			T.`id_usuario`,
			T.`estado`,
			T.`fecha_hora_entrada`,
			T.`fecha_hora_salida`,
			T.`observaciones`,
			EEP.`detalle` as ro_detalle,
			EEP.`documento` as ro_documento,
			EEP.`embebido` as ro_embebido,
			EEP.`observaciones` as ro_observaciones_ee,
			EEP.`id_usuario` as ro_id_usuario_solicitante,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario,
			USR.`u_mail` as ro_mail_usuario,
			USR_S.`codigo_usuario` as ro_codigo_usuario_solicitante,
			USR_S.`nombre_usuario` as ro_nombre_usuario_solicitante,
			USR_S.`u_mail` as ro_mail_usuario_solicitante

		FROM 
			`{$this->t_expe_rev_expediente_elec_pend}` as T
		INNER JOIN `{$this->t_expe_expedientes_elec_pend}` as EEP ON
			(
				T.`anio` = EEP.`anio` AND
				T.`tipo` = EEP.`tipo` AND
				T.`numero` = EEP.`numero` AND
				T.`cuerpo` = EEP.`cuerpo` AND
				T.`alcance` = EEP.`alcance` AND
				T.`orden` = EEP.`orden`
			)
		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(T.`id_usuario` = USR.`id_usuario`)
		LEFT JOIN `{$this->t_admin_usuarios}` as USR_S ON
			(EEP.`id_usuario` = USR_S.`id_usuario`)				
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.orden', IGUAL_A, $porden);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_revision', IGUAL_A, $pid_revision);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;
		
		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBRevExpedienteElecPend: Obtiene la cantidad de filas correspondientes de la clase RevExpedienteElecPend en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-11-16 12:41:32
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_revision
	 * @param  integer id_usuario
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string observaciones	 
	 * @return int
	 */
	public function obtenerRevsExpedienteElecPendCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_revision = null,
		$pid_usuario = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pobservaciones = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT 
			count(*) as cantidad
		FROM 
			`{$this->t_expe_rev_expediente_elec_pend}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.orden', IGUAL_A, $porden);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_revision', IGUAL_A, $pid_revision);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBRevExpedienteElecPend: Guarda una instancia de la clase RevExpedienteElecPend en la base de datos.
	 * GenerateClass 0.97.7 beta @ 2022-11-16 12:41:32
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_revision
	 * @param  integer id_usuario
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string observaciones	 
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarRevExpedienteElecPend(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_revision = null,
		$pid_usuario = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pobservaciones = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_rev_expediente_elec_pend;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'orden', $porden);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_revision', $pid_revision);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'estado', $pestado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_hora_entrada', $pfecha_hora_entrada);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_hora_salida', $pfecha_hora_salida);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones', $pobservaciones);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;		
	}

	/**
	 * DBRevExpedienteElecPend: Elimina un conjunto de RevsExpedienteElecPend en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-11-16 12:41:32
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_revision
	 * @param  integer id_usuario
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string observaciones	 
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarRevsExpedienteElecPend(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_revision = null,
		$pid_usuario = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pobservaciones = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($porden) && is_null($pid_revision) && is_null($pid_usuario) && is_null($pestado) && is_null($pfecha_hora_entrada) && is_null($pfecha_hora_salida) && is_null($pobservaciones))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarRevsExpedienteElecPend: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_rev_expediente_elec_pend;
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'orden', IGUAL_A, $porden);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_revision', IGUAL_A, $pid_revision);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones', IGUAL_A, $pobservaciones);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

/**
	 * Obtiene el siguiente número de id revision para un documento de un expediente electronico pendiente.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @param  [type] $porden   [description]
	 * @return [type]           [description]
	 */
	public function obtenerRevExpedienteElecPendIdSiguiente(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance,
		$porden) 
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT 
			IFNULL(MAX(id_revision)+1, 1) as max_id_revision
		FROM 
			`{$this->t_expe_rev_expediente_elec_pend}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.orden', IGUAL_A, $porden);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['max_id_revision'];
	}

	/**
	 * Ejecuta la misma lógica que obtenerRevsExpedienteElecPend, con la diferencia que
	 * al filtrar por usuario, incluye en la respuesta aquellas revisiones que pertenecen 
	 * al usuario solicitante o al usuario revisor (utilizando el operador OR). Con esto 
	 * se determina quienes tienen permiso a operar sobre dichas revisiones pendientes 
	 * (el solicitante o el revisor).
	 * @param  [type]     $panio               [description]
	 * @param  [type]     $ptipo               [description]
	 * @param  [type]     $pnumero             [description]
	 * @param  [type]     $pcuerpo             [description]
	 * @param  [type]     $palcance            [description]
	 * @param  [type]     $porden              [description]
	 * @param  [type]     $pid_revision        [description]
	 * @param  [type]     $pid_usuario         [description]
	 * @param  [type]     $pestado             [description]
	 * @param  [type]     $pfecha_hora_entrada [description]
	 * @param  [type]     $pfecha_hora_salida  [description]
	 * @param  [type]     $pobservaciones      [description]
	 * @param  array|null $pOrdenColumnas      [description]
	 * @param  [type]     $pLimiteCantidad     [description]
	 * @param  [type]     $pLimiteOffset       [description]
	 * @return [type]                          [description]
	 */
	public function obtenerRevsExpedienteElecPendUsuario(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_revision = null,
		$pid_usuario = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
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
			T.`id_revision`,
			T.`id_usuario`,
			T.`estado`,
			T.`fecha_hora_entrada`,
			T.`fecha_hora_salida`,
			T.`observaciones`,
			EEP.`detalle` as ro_detalle,
			EEP.`documento` as ro_documento,
			EEP.`embebido` as ro_embebido,
			EEP.`observaciones` as ro_observaciones_ee,
			EEP.`id_usuario` as ro_id_usuario_solicitante,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario,
			USR.`u_mail` as ro_mail_usuario,
			USR_S.`codigo_usuario` as ro_codigo_usuario_solicitante,
			USR_S.`nombre_usuario` as ro_nombre_usuario_solicitante,
			USR_S.`u_mail` as ro_mail_usuario_solicitante

		FROM 
			`{$this->t_expe_rev_expediente_elec_pend}` as T
		INNER JOIN `{$this->t_expe_expedientes_elec_pend}` as EEP ON
			(
				T.`anio` = EEP.`anio` AND
				T.`tipo` = EEP.`tipo` AND
				T.`numero` = EEP.`numero` AND
				T.`cuerpo` = EEP.`cuerpo` AND
				T.`alcance` = EEP.`alcance` AND
				T.`orden` = EEP.`orden`
			)
		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(T.`id_usuario` = USR.`id_usuario`)
		LEFT JOIN `{$this->t_admin_usuarios}` as USR_S ON
			(EEP.`id_usuario` = USR_S.`id_usuario`)				
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.orden', IGUAL_A, $porden);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_revision', IGUAL_A, $pid_revision);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// Que el usuario revisor cumpla con la condición
		$subCrit = new ListaCriteriosQuery();

		if ( !is_null($pid_usuario) && is_array($pid_usuario)) 
			$subCrit->agregarCriterioMultiple(P_INT, 'T.id_usuario', $pid_usuario);
		else
			$subCrit->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		if ( !is_null($pid_usuario) && is_array($pid_usuario)) 
			$subCrit->agregarCriterioMultiple(P_INT, 'EEP.id_usuario', $pid_usuario);
		else
			$subCrit->agregarCriterioSimple(P_INT, 'EEP.id_usuario', IGUAL_A, $pid_usuario);

		$builder->criteriosWhere->agregarSubCriterio(CRITERIO_OR, $subCrit);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;
		
		//Logger::get()->Log("builder", $builder->logQuery());

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * Ejecuta la misma lógica que obtenerRevsExpedienteElecPendCantidad, con la diferencia que
	 * al filtrar por usuario, incluye en la respuesta aquellas revisiones que pertenecen 
	 * al usuario solicitante o al usuario revisor (utilizando el operador OR). Con esto 
	 * se determina quienes tienen permiso a operar sobre dichas revisiones pendientes 
	 * (el solicitante o el revisor).
	 * @param  [type]     $panio               [description]
	 * @param  [type]     $ptipo               [description]
	 * @param  [type]     $pnumero             [description]
	 * @param  [type]     $pcuerpo             [description]
	 * @param  [type]     $palcance            [description]
	 * @param  [type]     $porden              [description]
	 * @param  [type]     $pid_revision        [description]
	 * @param  [type]     $pid_usuario         [description]
	 * @param  [type]     $pestado             [description]
	 * @param  [type]     $pfecha_hora_entrada [description]
	 * @param  [type]     $pfecha_hora_salida  [description]
	 * @param  [type]     $pobservaciones      [description]
	 * @param  array|null $pOrdenColumnas      [description]
	 * @param  [type]     $pLimiteCantidad     [description]
	 * @param  [type]     $pLimiteOffset       [description]
	 * @return [type]                          [description]
	 */
	public function obtenerRevsExpedienteElecPendUsuarioCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_revision = null,
		$pid_usuario = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
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
			count(*) as cantidad
		FROM 
			`{$this->t_expe_rev_expediente_elec_pend}` as T
		INNER JOIN `{$this->t_expe_expedientes_elec_pend}` as EEP ON
			(
				T.`anio` = EEP.`anio` AND
				T.`tipo` = EEP.`tipo` AND
				T.`numero` = EEP.`numero` AND
				T.`cuerpo` = EEP.`cuerpo` AND
				T.`alcance` = EEP.`alcance` AND
				T.`orden` = EEP.`orden`
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
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_revision', IGUAL_A, $pid_revision);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// Que el usuario revisor O el solicitiante cumplan con la condición
		$subCrit = new ListaCriteriosQuery();
		$subCrit->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$subCrit->agregarCriterioSimple(P_INT, 'EEP.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarSubCriterio(CRITERIO_OR, $subCrit);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}	

}
?>