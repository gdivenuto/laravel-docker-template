<?php

/**
 * Capa de acceso a datos (persistencia) específica de la gestion de tablas de Auditorias.
 */
class DBAuditorias extends DBBaseClass {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $t_expe_auditoria = 'expe_auditoria'; //!< // Identificador de tabla 'expe_auditoria'

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
	// Auditoria de Expedientes
	// ************************************************************************

	/**
	 * DBAuditoria: Obtiene un array de filas correspondientes a la clase Auditoria en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2018-03-15 09:49:48
	 * @param  integer (PK) id_log
	 * @param  string fecha_hora_log
	 * @param  integer id_usuario
	 * @param  string operacion
	 * @param  string tabla
	 * @param  integer anio_log
	 * @param  string tipo_log
	 * @param  float numero_log
	 * @param  string digito_log
	 * @param  integer cuerpo_log
	 * @param  integer alcance_log
	 * @param  integer cuerpoalcance_log
	 * @param  integer anexoalcance_log
	 * @param  integer cuerpoanexoalcance_log
	 * @param  integer anexo_log
	 * @param  integer cuerpoanexo_log
	 * @param  string fecha_log
	 * @param  float orden_log
	 * @param  string netusername
	 * @param  string netpcname
	 * @param  string observaciones_log
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerAuditorias(
		// Parametros
		$pid_log = null,
		$pfecha_hora_log = null,
		$pid_usuario = null,
		$poperacion = null,
		$ptabla = null,
		$panio_log = null,
		$ptipo_log = null,
		$pnumero_log = null,
		$pdigito_log = null,
		$pcuerpo_log = null,
		$palcance_log = null,
		$pcuerpoalcance_log = null,
		$panexoalcance_log = null,
		$pcuerpoanexoalcance_log = null,
		$panexo_log = null,
		$pcuerpoanexo_log = null,
		$pfecha_log = null,
		$porden_log = null,
		$pnetusername = null,
		$pnetpcname = null,
		$pobservaciones_log = null,
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
			T.`id_log`,
			T.`fecha_hora_log`,
			T.`id_usuario`,
			T.`operacion`,
			T.`tabla`,
			T.`anio_log`,
			T.`tipo_log`,
			T.`numero_log`,
			T.`digito_log`,
			T.`cuerpo_log`,
			T.`alcance_log`,
			T.`cuerpoalcance_log`,
			T.`anexoalcance_log`,
			T.`cuerpoanexoalcance_log`,
			T.`anexo_log`,
			T.`cuerpoanexo_log`,
			T.`fecha_log`,
			T.`orden_log`,
			T.`netusername`,
			T.`netpcname`,
			T.`observaciones_log`
		FROM
			`{$this->t_expe_auditoria}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_log', IGUAL_A, $pid_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_log', IGUAL_A, $pfecha_hora_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.operacion', IGUAL_A, $poperacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tabla', IGUAL_A, $ptabla);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio_log', IGUAL_A, $panio_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo_log', IGUAL_A, $ptipo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero_log', IGUAL_A, $pnumero_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.digito_log', IGUAL_A, $pdigito_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo_log', IGUAL_A, $pcuerpo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance_log', IGUAL_A, $palcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoalcance_log', IGUAL_A, $pcuerpoalcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexoalcance_log', IGUAL_A, $panexoalcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexoalcance_log', IGUAL_A, $pcuerpoanexoalcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexo_log', IGUAL_A, $panexo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexo_log', IGUAL_A, $pcuerpoanexo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_log', IGUAL_A, $pfecha_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_log', IGUAL_A, $porden_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.netusername', IGUAL_A, $pnetusername);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.netpcname', IGUAL_A, $pnetpcname);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_log', IGUAL_A, $pobservaciones_log);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBAuditoria: Obtiene la cantidad de filas correspondientes de la clase Auditoria en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2018-03-15 09:49:48
	 * @param  integer (PK) id_log
	 * @param  string fecha_hora_log
	 * @param  integer id_usuario
	 * @param  string operacion
	 * @param  string tabla
	 * @param  integer anio_log
	 * @param  string tipo_log
	 * @param  float numero_log
	 * @param  string digito_log
	 * @param  integer cuerpo_log
	 * @param  integer alcance_log
	 * @param  integer cuerpoalcance_log
	 * @param  integer anexoalcance_log
	 * @param  integer cuerpoanexoalcance_log
	 * @param  integer anexo_log
	 * @param  integer cuerpoanexo_log
	 * @param  string fecha_log
	 * @param  float orden_log
	 * @param  string netusername
	 * @param  string netpcname
	 * @param  string observaciones_log
	 * @return int
	 */
	public function obtenerAuditoriasCantidad(
		// Parametros
		$pid_log = null,
		$pfecha_hora_log = null,
		$pid_usuario = null,
		$poperacion = null,
		$ptabla = null,
		$panio_log = null,
		$ptipo_log = null,
		$pnumero_log = null,
		$pdigito_log = null,
		$pcuerpo_log = null,
		$palcance_log = null,
		$pcuerpoalcance_log = null,
		$panexoalcance_log = null,
		$pcuerpoanexoalcance_log = null,
		$panexo_log = null,
		$pcuerpoanexo_log = null,
		$pfecha_log = null,
		$porden_log = null,
		$pnetusername = null,
		$pnetpcname = null,
		$pobservaciones_log = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_auditoria}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_log', IGUAL_A, $pid_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_log', IGUAL_A, $pfecha_hora_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.operacion', IGUAL_A, $poperacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tabla', IGUAL_A, $ptabla);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio_log', IGUAL_A, $panio_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo_log', IGUAL_A, $ptipo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero_log', IGUAL_A, $pnumero_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.digito_log', IGUAL_A, $pdigito_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo_log', IGUAL_A, $pcuerpo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance_log', IGUAL_A, $palcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoalcance_log', IGUAL_A, $pcuerpoalcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexoalcance_log', IGUAL_A, $panexoalcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexoalcance_log', IGUAL_A, $pcuerpoanexoalcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexo_log', IGUAL_A, $panexo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexo_log', IGUAL_A, $pcuerpoanexo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_log', IGUAL_A, $pfecha_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_log', IGUAL_A, $porden_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.netusername', IGUAL_A, $pnetusername);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.netpcname', IGUAL_A, $pnetpcname);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_log', IGUAL_A, $pobservaciones_log);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBAuditoria: Guarda una instancia de la clase Auditoria en la base de datos.
	 * GenerateClass 0.97.7 beta @ 2018-03-15 09:49:48
	 * @param  integer (PK) id_log
	 * @param  string fecha_hora_log
	 * @param  integer id_usuario
	 * @param  string operacion
	 * @param  string tabla
	 * @param  integer anio_log
	 * @param  string tipo_log
	 * @param  float numero_log
	 * @param  string digito_log
	 * @param  integer cuerpo_log
	 * @param  integer alcance_log
	 * @param  integer cuerpoalcance_log
	 * @param  integer anexoalcance_log
	 * @param  integer cuerpoanexoalcance_log
	 * @param  integer anexo_log
	 * @param  integer cuerpoanexo_log
	 * @param  string fecha_log
	 * @param  float orden_log
	 * @param  string netusername
	 * @param  string netpcname
	 * @param  string observaciones_log
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarAuditoria(
		// Parametros
		$pid_log = null,
		$pfecha_hora_log = '',
		$pid_usuario = null,
		$poperacion = null,
		$ptabla = null,
		$panio_log = null,
		$ptipo_log = null,
		$pnumero_log = null,
		$pdigito_log = null,
		$pcuerpo_log = null,
		$palcance_log = null,
		$pcuerpoalcance_log = null,
		$panexoalcance_log = null,
		$pcuerpoanexoalcance_log = null,
		$panexo_log = null,
		$pcuerpoanexo_log = null,
		$pfecha_log = null,
		$porden_log = null,
		$pnetusername = null,
		$pnetpcname = null,
		$pobservaciones_log = null) {
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_auditoria;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeoAutoincremental(P_INT, 'id_log', $pid_log);
		// 07/01/2022 XXXX
		// Se comenta el mapeo con fecha_hora_log, no es necesario, siendo por defecto timestamp
		//$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_hora_log', $pfecha_hora_log);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'operacion', $poperacion);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tabla', $ptabla);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio_log', $panio_log);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo_log', $ptipo_log);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero_log', $pnumero_log);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'digito_log', $pdigito_log);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo_log', $pcuerpo_log);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance_log', $palcance_log);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoalcance_log', $pcuerpoalcance_log);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anexoalcance_log', $panexoalcance_log);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoanexoalcance_log', $pcuerpoanexoalcance_log);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anexo_log', $panexo_log);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoanexo_log', $pcuerpoanexo_log);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_log', $pfecha_log);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'orden_log', $porden_log);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'netusername', $pnetusername);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'netpcname', $pnetpcname);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_log', $pobservaciones_log);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBAuditoria: Elimina un conjunto de Auditorias en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2018-03-15 09:49:48
	 * @param  integer (PK) id_log
	 * @param  string fecha_hora_log
	 * @param  integer id_usuario
	 * @param  string operacion
	 * @param  string tabla
	 * @param  integer anio_log
	 * @param  string tipo_log
	 * @param  float numero_log
	 * @param  string digito_log
	 * @param  integer cuerpo_log
	 * @param  integer alcance_log
	 * @param  integer cuerpoalcance_log
	 * @param  integer anexoalcance_log
	 * @param  integer cuerpoanexoalcance_log
	 * @param  integer anexo_log
	 * @param  integer cuerpoanexo_log
	 * @param  string fecha_log
	 * @param  float orden_log
	 * @param  string netusername
	 * @param  string netpcname
	 * @param  string observaciones_log
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarAuditorias(
		// Parametros
		$pid_log = null,
		$pfecha_hora_log = null,
		$pid_usuario = null,
		$poperacion = null,
		$ptabla = null,
		$panio_log = null,
		$ptipo_log = null,
		$pnumero_log = null,
		$pdigito_log = null,
		$pcuerpo_log = null,
		$palcance_log = null,
		$pcuerpoalcance_log = null,
		$panexoalcance_log = null,
		$pcuerpoanexoalcance_log = null,
		$panexo_log = null,
		$pcuerpoanexo_log = null,
		$pfecha_log = null,
		$porden_log = null,
		$pnetusername = null,
		$pnetpcname = null,
		$pobservaciones_log = null) {
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($pid_log) && is_null($pfecha_hora_log) && is_null($pid_usuario) && is_null($poperacion) && is_null($ptabla) && is_null($panio_log) && is_null($ptipo_log) && is_null($pnumero_log) && is_null($pdigito_log) && is_null($pcuerpo_log) && is_null($palcance_log) && is_null($pcuerpoalcance_log) && is_null($panexoalcance_log) && is_null($pcuerpoanexoalcance_log) && is_null($panexo_log) && is_null($pcuerpoanexo_log) && is_null($pfecha_log) && is_null($porden_log) && is_null($pnetusername) && is_null($pnetpcname) && is_null($pobservaciones_log)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarAuditorias: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_auditoria;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_log', IGUAL_A, $pid_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_hora_log', IGUAL_A, $pfecha_hora_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'operacion', IGUAL_A, $poperacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tabla', IGUAL_A, $ptabla);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio_log', IGUAL_A, $panio_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo_log', IGUAL_A, $ptipo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero_log', IGUAL_A, $pnumero_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'digito_log', IGUAL_A, $pdigito_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo_log', IGUAL_A, $pcuerpo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance_log', IGUAL_A, $palcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpoalcance_log', IGUAL_A, $pcuerpoalcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anexoalcance_log', IGUAL_A, $panexoalcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpoanexoalcance_log', IGUAL_A, $pcuerpoanexoalcance_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anexo_log', IGUAL_A, $panexo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpoanexo_log', IGUAL_A, $pcuerpoanexo_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_log', IGUAL_A, $pfecha_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'orden_log', IGUAL_A, $porden_log);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'netusername', IGUAL_A, $pnetusername);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'netpcname', IGUAL_A, $pnetpcname);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones_log', IGUAL_A, $pobservaciones_log);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}
}
