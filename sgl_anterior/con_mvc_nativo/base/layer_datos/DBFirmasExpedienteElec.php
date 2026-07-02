<?php

/**
 * Capa de acceso a datos (persistencia) específica de la gestion de Firmas de Expediente Electrónico.
 */
class DBFirmasExpedienteElec extends DBBaseClass {

	protected $t_expe_firmas_expediente_elec = 'expe_firmas_expediente_elec'; //!< Identificador de tabla 'expe_firmas_expediente_elec'
	protected $t_expe_expedientes_elec = 'expe_expedientes_elec'; //!< Identificador de tabla 'expe_expedientes_elec'
	protected $t_admin_usuarios = 'admin_usuarios'; //!< Identificador de tabla 'admin_usuarios'

	/**
	 * DBFirmasExpedienteElec: Obtiene un array de filas correspondientes a la clase FirmaExpedienteElec en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-28 12:37:51
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_firma
	 * @param  integer id_usuario
	 * @param  integer id_usuario_solicitante
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string usuario_cargo
	 * @param  string usuario_dependencia
	 * @param  string observaciones
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerFirmasExpedienteElec(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_firma = null,
		$pid_usuario = null,
		$pid_usuario_solicitante = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pusuario_cargo = null,
		$pusuario_dependencia = null,
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
			T.`id_firma`,
			T.`id_usuario`,
			T.`id_usuario_solicitante`,
			T.`estado`,
			T.`fecha_hora_entrada`,
			T.`fecha_hora_salida`,
			T.`usuario_cargo`,
			T.`usuario_dependencia`,
			T.`observaciones`,
			EE.`detalle` as ro_detalle,
			EE.`documento` as ro_documento,
			EE.`embebido` as ro_embebido,
			EE.`observaciones` as ro_observaciones_ee,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario,
			USR.`u_mail` as ro_mail_usuario,
			USR_S.`codigo_usuario` as ro_codigo_usuario_solicitante,
			USR_S.`nombre_usuario` as ro_nombre_usuario_solicitante,
			USR_S.`u_mail` as ro_mail_usuario_solicitante

		FROM
			`{$this->t_expe_firmas_expediente_elec}` as T
		INNER JOIN `{$this->t_expe_expedientes_elec}` as EE ON
			(
				T.`anio` = EE.`anio` AND
				T.`tipo` = EE.`tipo` AND
				T.`numero` = EE.`numero` AND
				T.`cuerpo` = EE.`cuerpo` AND
				T.`alcance` = EE.`alcance` AND
				T.`orden` = EE.`orden`
			)
		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(T.`id_usuario` = USR.`id_usuario`)
		LEFT JOIN `{$this->t_admin_usuarios}` as USR_S ON
			(T.`id_usuario_solicitante` = USR_S.`id_usuario`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.orden', IGUAL_A, $porden);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_firma', IGUAL_A, $pid_firma);

		// 2023-05-09 XXXX
		// ----- Que el usuario firmante O el solicitante cumplan con la condición
		$subCrit = new ListaCriteriosQuery();

		// El usuario firmante puede ser un array de id's de usuarios
		if ( !is_null($pid_usuario) && is_array($pid_usuario))
			$subCrit->agregarCriterioMultiple(P_INT, 'T.id_usuario', $pid_usuario);
		else
			$subCrit->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// El usuario solicitante puede ser un array de id's de usuarios
		if ( !is_null($pid_usuario_solicitante) && is_array($pid_usuario_solicitante))
			$subCrit->agregarCriterioMultiple(P_INT, 'T.id_usuario_solicitante', $pid_usuario_solicitante);
		else
			$subCrit->agregarCriterioSimple(P_INT, 'T.id_usuario_solicitante', IGUAL_A, $pid_usuario_solicitante);

		$builder->criteriosWhere->agregarSubCriterio(CRITERIO_OR, $subCrit);
		// ----------------------------
		//$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		//$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario_solicitante', IGUAL_A, $pid_usuario_solicitante);

		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.usuario_cargo', IGUAL_A, $pusuario_cargo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.usuario_dependencia', IGUAL_A, $pusuario_dependencia);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		//Logger::get()->Log("query", [$pid_usuario, $pestado, $builder->getQuery()]);

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBFirmasExpedienteElec: Obtiene la cantidad de filas correspondientes de la clase FirmaExpedienteElec en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-28 12:37:51
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_firma
	 * @param  integer id_usuario
	 * @param  integer id_usuario_solicitante
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string usuario_cargo
	 * @param  string usuario_dependencia
	 * @param  string observaciones
	 * @return int
	 */
	public function obtenerFirmasExpedienteElecCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_firma = null,
		$pid_usuario = null,
		$pid_usuario_solicitante = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pusuario_cargo = null,
		$pusuario_dependencia = null,
		$pobservaciones = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_firmas_expediente_elec}` as T
		INNER JOIN `{$this->t_expe_expedientes_elec}` as EE ON
			(
				T.`anio` = EE.`anio` AND
				T.`tipo` = EE.`tipo` AND
				T.`numero` = EE.`numero` AND
				T.`cuerpo` = EE.`cuerpo` AND
				T.`alcance` = EE.`alcance` AND
				T.`orden` = EE.`orden`
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
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_firma', IGUAL_A, $pid_firma);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario_solicitante', IGUAL_A, $pid_usuario_solicitante);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.usuario_cargo', IGUAL_A, $pusuario_cargo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.usuario_dependencia', IGUAL_A, $pusuario_dependencia);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBFirmasExpedienteElec: Guarda una instancia de la clase FirmaExpedienteElec en la base de datos.
	 * GenerateClass 0.97.7 beta @ 2022-09-28 12:37:51
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_firma
	 * @param  integer id_usuario
	 * @param  integer id_usuario_solicitante
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string usuario_cargo
	 * @param  string usuario_dependencia
	 * @param  string observaciones
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarFirmaExpedienteElec(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_firma = null,
		$pid_usuario = null,
		$pid_usuario_solicitante = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pusuario_cargo = null,
		$pusuario_dependencia = null,
		$pobservaciones = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_firmas_expediente_elec;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'orden', $porden);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_firma', $pid_firma);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario_solicitante', $pid_usuario_solicitante);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'estado', $pestado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_hora_entrada', $pfecha_hora_entrada);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_hora_salida', $pfecha_hora_salida);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'usuario_cargo', $pusuario_cargo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'usuario_dependencia', $pusuario_dependencia);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones', $pobservaciones);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBFirmasExpedienteElec: Elimina un conjunto de FirmasExpedienteElec en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-09-28 12:37:51
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden
	 * @param  integer (PK) id_firma
	 * @param  integer id_usuario
	 * @param  integer id_usuario_solicitante
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  string usuario_cargo
	 * @param  string usuario_dependencia
	 * @param  string observaciones
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarFirmasExpedienteElec(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden = null,
		$pid_firma = null,
		$pid_usuario = null,
		$pid_usuario_solicitante = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pusuario_cargo = null,
		$pusuario_dependencia = null,
		$pobservaciones = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($porden) && is_null($pid_firma) && is_null($pid_usuario) && is_null($pid_usuario_solicitante) && is_null($pestado) && is_null($pfecha_hora_entrada) && is_null($pfecha_hora_salida) && is_null($pusuario_cargo) && is_null($pusuario_dependencia) && is_null($pobservaciones))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarFirmasExpedienteElec: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_firmas_expediente_elec;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'orden', IGUAL_A, $porden);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_firma', IGUAL_A, $pid_firma);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario_solicitante', IGUAL_A, $pid_usuario_solicitante);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'usuario_cargo', IGUAL_A, $pusuario_cargo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'usuario_dependencia', IGUAL_A, $pusuario_dependencia);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones', IGUAL_A, $pobservaciones);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * Obtiene el siguiente número de id firma para un documento de un expediente electronico.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @param  [type] $porden   [description]
	 * @return [type]           [description]
	 */
	public function obtenerFirmaExpedienteElecIdSiguiente(
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
			IFNULL(MAX(id_firma)+1, 1) as max_id_firma
		FROM
			`{$this->t_expe_firmas_expediente_elec}` as T
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

		return $resultado[0]['max_id_firma'];
	}
}
?>
