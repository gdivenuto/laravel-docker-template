<?php

/**
 * Capa de acceso a datos (persistencia) específica de la gestion de Expedientes.
 */
class DBExpedientes extends DBBaseClass {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************

	protected $t_expe_antecedentes = 'expe_antecedentes'; //!< // Identificador de tabla 'expe_antecedentes'
	protected $t_expe_autores = 'expe_autores'; //!< // Identificador de tabla 'expe_autores'
	protected $t_expe_estados = 'expe_estados'; //!< // Identificador de tabla 'expe_estados'
	protected $t_expe_expedientes = 'expe_expedientes'; //!< // Identificador de tabla 'expe_expedientes'
	protected $t_expe_giros = 'expe_giros'; //!< // Identificador de tabla 'expe_giros'
	protected $t_expe_proyectos = 'expe_proyectos'; //!< // Identificador de tabla 'expe_proyectos'
	protected $t_expe_sanciones = 'expe_sanciones'; //!< // Identificador de tabla 'expe_sanciones'
	protected $t_expe_temas = 'expe_temas'; //!< // Identificador de tabla 'expe_temas'
	protected $t_expe_codcategoria = 'expe_codcategoria'; //!< // Identificador de tabla 'expe_codcategoria'
	protected $t_expe_lugares = 'expe_lugares'; //!< // Identificador de tabla 'expe_lugares'
	protected $t_expe_codtemas = 'expe_codtemas'; //!< // Identificador de tabla 'expe_codtemas'
	protected $t_expe_codestados = 'expe_codestados'; //!< // Identificador de tabla 'expe_codestados'
	protected $t_expe_codproyectos = 'expe_codproyectos'; //!< // Identificador de tabla 'expe_codproyectos'
	protected $t_expe_informes = 'expe_informes'; //!< // Identificador de tabla 'expe_informes'
	protected $t_expe_en_participacion = 'expe_en_participacion'; //!< // Identificador de tabla 'expe_en_participacion'
	protected $t_expe_participaciones = 'expe_participaciones'; //!< // Identificador de tabla 'expe_participaciones'

	protected $t_admin_usuarios = 'admin_usuarios'; //!< // Identificador de tabla 'admin_usuarios'

	// 2026-03-19 XXXX
	// Se agregan las referencias para la eliminación en cascada
	protected $t_expe_expedientes_elec = 'expe_expedientes_elec';
	protected $t_expe_expedientes_elec_pend = 'expe_expedientes_elec_pend';
	protected $t_expe_rev_expediente_elec_pend = 'expe_rev_expediente_elec_pend';
	protected $t_expe_firmas_expediente_elec = 'expe_firmas_expediente_elec';
	protected $t_expe_firmas_expediente_elec_pend = 'expe_firmas_expediente_elec_pend';

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
	// Antecedentes
	// ************************************************************************

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Antecedente en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:48:56
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) anio_a
	 * @param  string (PK) tipo_a
	 * @param  float (PK) numero_a
	 * @param  string (PK) digito_a
	 * @param  integer (PK) cuerpo_a
	 * @param  integer (PK) alcance_a
	 * @param  integer (PK) cuerpoalcance_a
	 * @param  integer (PK) anexoalcance_a
	 * @param  integer (PK) cuerpoanexoalcance_a
	 * @param  integer (PK) anexo_a
	 * @param  integer (PK) cuerpoanexo_a
	 * @param  string observaciones_antecedentes
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerAntecedentes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$panio_a = null,
		$ptipo_a = null,
		$pnumero_a = null,
		$pdigito_a = null,
		$pcuerpo_a = null,
		$palcance_a = null,
		$pcuerpoalcance_a = null,
		$panexoalcance_a = null,
		$pcuerpoanexoalcance_a = null,
		$panexo_a = null,
		$pcuerpoanexo_a = null,
		$pobservaciones_antecedentes = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null,
		array $pAgrupacionColumnas = null) {
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
			T.`anio_a`,
			T.`tipo_a`,
			T.`numero_a`,
			T.`digito_a`,
			T.`cuerpo_a`,
			T.`alcance_a`,
			T.`cuerpoalcance_a`,
			T.`anexoalcance_a`,
			T.`cuerpoanexoalcance_a`,
			T.`anexo_a`,
			T.`cuerpoanexo_a`,
			T.`observaciones_antecedentes`,
			T.`id_usuario`
		FROM
			`{$this->t_expe_antecedentes}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio_a', IGUAL_A, $panio_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo_a', IGUAL_A, $ptipo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero_a', IGUAL_A, $pnumero_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.digito_a', IGUAL_A, $pdigito_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo_a', IGUAL_A, $pcuerpo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance_a', IGUAL_A, $palcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoalcance_a', IGUAL_A, $pcuerpoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexoalcance_a', IGUAL_A, $panexoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexoalcance_a', IGUAL_A, $pcuerpoanexoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexo_a', IGUAL_A, $panexo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexo_a', IGUAL_A, $pcuerpoanexo_a);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_antecedentes', '%', $pobservaciones_antecedentes, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el GROUP BY
		$builder->criteriosGroupBy = $pAgrupacionColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene la cantidad de filas correspondientes de la clase Antecedente en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:48:56
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) anio_a
	 * @param  string (PK) tipo_a
	 * @param  float (PK) numero_a
	 * @param  string (PK) digito_a
	 * @param  integer (PK) cuerpo_a
	 * @param  integer (PK) alcance_a
	 * @param  integer (PK) cuerpoalcance_a
	 * @param  integer (PK) anexoalcance_a
	 * @param  integer (PK) cuerpoanexoalcance_a
	 * @param  integer (PK) anexo_a
	 * @param  integer (PK) cuerpoanexo_a
	 * @param  string observaciones_antecedentes
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerAntecedentesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$panio_a = null,
		$ptipo_a = null,
		$pnumero_a = null,
		$pdigito_a = null,
		$pcuerpo_a = null,
		$palcance_a = null,
		$pcuerpoalcance_a = null,
		$panexoalcance_a = null,
		$pcuerpoanexoalcance_a = null,
		$panexo_a = null,
		$pcuerpoanexo_a = null,
		$pobservaciones_antecedentes = null,
		$pid_usuario = null,
		$pAgrupacionColumnas = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_antecedentes}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio_a', IGUAL_A, $panio_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo_a', IGUAL_A, $ptipo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero_a', IGUAL_A, $pnumero_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.digito_a', IGUAL_A, $pdigito_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo_a', IGUAL_A, $pcuerpo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance_a', IGUAL_A, $palcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoalcance_a', IGUAL_A, $pcuerpoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexoalcance_a', IGUAL_A, $panexoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexoalcance_a', IGUAL_A, $pcuerpoanexoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anexo_a', IGUAL_A, $panexo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpoanexo_a', IGUAL_A, $pcuerpoanexo_a);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_antecedentes', '%', $pobservaciones_antecedentes, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el GROUP BY
		$builder->criteriosGroupBy = $pAgrupacionColumnas;

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientes: Guarda una instancia de la clase Antecedente en la base de datos.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:48:56
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) anio_a
	 * @param  string (PK) tipo_a
	 * @param  float (PK) numero_a
	 * @param  string (PK) digito_a
	 * @param  integer (PK) cuerpo_a
	 * @param  integer (PK) alcance_a
	 * @param  integer (PK) cuerpoalcance_a
	 * @param  integer (PK) anexoalcance_a
	 * @param  integer (PK) cuerpoanexoalcance_a
	 * @param  integer (PK) anexo_a
	 * @param  integer (PK) cuerpoanexo_a
	 * @param  string observaciones_antecedentes
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarAntecedente(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$panio_a = null,
		$ptipo_a = null,
		$pnumero_a = null,
		$pdigito_a = null,
		$pcuerpo_a = null,
		$palcance_a = null,
		$pcuerpoalcance_a = null,
		$panexoalcance_a = null,
		$pcuerpoanexoalcance_a = null,
		$panexo_a = null,
		$pcuerpoanexo_a = null,
		$pobservaciones_antecedentes = null,
		$pid_usuario = null) {
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_antecedentes;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio_a', $panio_a);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo_a', $ptipo_a);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero_a', $pnumero_a);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'digito_a', $pdigito_a);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo_a', $pcuerpo_a);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance_a', $palcance_a);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoalcance_a', $pcuerpoalcance_a);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anexoalcance_a', $panexoalcance_a);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoanexoalcance_a', $pcuerpoanexoalcance_a);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anexo_a', $panexo_a);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpoanexo_a', $pcuerpoanexo_a);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_antecedentes', $pobservaciones_antecedentes);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Antecedentes en base a diferentes criterios de selección.
	 * GenerateClass 0.97.2 beta @ 2016-08-24 11:57:40
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) anio_a
	 * @param  string (PK) tipo_a
	 * @param  float (PK) numero_a
	 * @param  string (PK) digito_a
	 * @param  integer (PK) cuerpo_a
	 * @param  integer (PK) alcance_a
	 * @param  integer (PK) cuerpoalcance_a
	 * @param  integer (PK) anexoalcance_a
	 * @param  integer (PK) cuerpoanexoalcance_a
	 * @param  integer (PK) anexo_a
	 * @param  integer (PK) cuerpoanexo_a
	 * @param  string observaciones_antecedentes
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarAntecedentes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$panio_a = null,
		$ptipo_a = null,
		$pnumero_a = null,
		$pdigito_a = null,
		$pcuerpo_a = null,
		$palcance_a = null,
		$pcuerpoalcance_a = null,
		$panexoalcance_a = null,
		$pcuerpoanexoalcance_a = null,
		$panexo_a = null,
		$pcuerpoanexo_a = null,
		$pobservaciones_antecedentes = null,
		$pid_usuario = null) {
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($panio_a) && is_null($ptipo_a) && is_null($pnumero_a) && is_null($pdigito_a) && is_null($pcuerpo_a) && is_null($palcance_a) && is_null($pcuerpoalcance_a) && is_null($panexoalcance_a) && is_null($pcuerpoanexoalcance_a) && is_null($panexo_a) && is_null($pcuerpoanexo_a) && is_null($pobservaciones_antecedentes) && is_null($pid_usuario)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarAntecedentes: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_antecedentes;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio_a', IGUAL_A, $panio_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo_a', IGUAL_A, $ptipo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero_a', IGUAL_A, $pnumero_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'digito_a', IGUAL_A, $pdigito_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo_a', IGUAL_A, $pcuerpo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance_a', IGUAL_A, $palcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpoalcance_a', IGUAL_A, $pcuerpoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anexoalcance_a', IGUAL_A, $panexoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpoanexoalcance_a', IGUAL_A, $pcuerpoanexoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anexo_a', IGUAL_A, $panexo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpoanexo_a', IGUAL_A, $pcuerpoanexo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones_antecedentes', IGUAL_A, $pobservaciones_antecedentes);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Autores
	// ************************************************************************

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Autor en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:11
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) autor_tipo
	 * @param  string (PK) autor_codigo
	 * @param  string autor_bloque_tipo
	 * @param  string autor_bloque_codigo
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerAutores(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pautor_tipo = null,
		$pautor_codigo = null,
		$pautor_bloque_tipo = null,
		$pautor_bloque_codigo = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
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
			T.`autor_tipo`,
			T.`autor_codigo`,
			T.`autor_bloque_tipo`,
			T.`autor_bloque_codigo`,
			T.`id_usuario`,
			LUG.`descripcion_grp` as ro_descripcion_grp
		FROM
			`{$this->t_expe_autores}` as T
		LEFT JOIN `{$this->t_expe_lugares}` as LUG ON
			(T.`autor_tipo` = LUG.`tipo_grp` and T.`autor_codigo` = LUG.`codigo_grp`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.autor_tipo', IGUAL_A, $pautor_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.autor_codigo', IGUAL_A, $pautor_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.autor_bloque_tipo', IGUAL_A, $pautor_bloque_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.autor_bloque_codigo', IGUAL_A, $pautor_bloque_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene la cantidad de filas correspondientes de la clase Autor en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:11
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) autor_tipo
	 * @param  string (PK) autor_codigo
	 * @param  string autor_bloque_tipo
	 * @param  string autor_bloque_codigo
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerAutoresCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pautor_tipo = null,
		$pautor_codigo = null,
		$pautor_bloque_tipo = null,
		$pautor_bloque_codigo = null,
		$pid_usuario = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_autores}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.autor_tipo', IGUAL_A, $pautor_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.autor_codigo', IGUAL_A, $pautor_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.autor_bloque_tipo', IGUAL_A, $pautor_bloque_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.autor_bloque_codigo', IGUAL_A, $pautor_bloque_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientes: Guarda una instancia de la clase Autor en la base de datos.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:11
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) autor_tipo
	 * @param  string (PK) autor_codigo
	 * @param  string autor_bloque_tipo
	 * @param  string autor_bloque_codigo
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarAutor(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pautor_tipo = null,
		$pautor_codigo = null,
		$pautor_bloque_tipo = null,
		$pautor_bloque_codigo = null,
		$pid_usuario = null) {
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_autores;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'autor_tipo', $pautor_tipo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'autor_codigo', $pautor_codigo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'autor_bloque_tipo', $pautor_bloque_tipo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'autor_bloque_codigo', $pautor_bloque_codigo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Autores en base a diferentes criterios de selección.
	 * GenerateClass 0.97.2 beta @ 2016-08-24 11:57:40
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) autor_tipo
	 * @param  string (PK) autor_codigo
	 * @param  string autor_bloque_tipo
	 * @param  string autor_bloque_codigo
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarAutores(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pautor_tipo = null,
		$pautor_codigo = null,
		$pautor_bloque_tipo = null,
		$pautor_bloque_codigo = null,
		$pid_usuario = null) {
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($pautor_tipo) && is_null($pautor_codigo) && is_null($pautor_bloque_tipo) && is_null($pautor_bloque_codigo) && is_null($pid_usuario)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarAutores: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_autores;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'autor_tipo', IGUAL_A, $pautor_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'autor_codigo', IGUAL_A, $pautor_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'autor_bloque_tipo', IGUAL_A, $pautor_bloque_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'autor_bloque_codigo', IGUAL_A, $pautor_bloque_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Estados
	// ************************************************************************

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Estado en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:20
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) fecha_estado
	 * @param  float (PK) orden_estado
	 * @param  integer id_codestado
	 * @param  string observaciones_estado
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerEstados(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pfecha_estado = null,
		$porden_estado = null,
		$pid_codestado = null,
		$pobservaciones_estado = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
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
			T.`fecha_estado`,
			T.`orden_estado`,
			T.`id_codestado`,
			T.`observaciones_estado`,
			T.`id_usuario`,
			CD.`nombre_estado` as ro_nombre_estado,
			CD.`tratamiento_comision` as ro_tratamiento_comision
		FROM
			`{$this->t_expe_estados}` as T
		LEFT JOIN `{$this->t_expe_codestados}` as CD ON
			(CD.`id_codestado` = T.`id_codestado`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_estado', IGUAL_A, $pfecha_estado);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_estado', IGUAL_A, $porden_estado);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codestado', IGUAL_A, $pid_codestado);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_estado', '%', $pobservaciones_estado, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene la cantidad de filas correspondientes de la clase Estado en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:20
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) fecha_estado
	 * @param  float (PK) orden_estado
	 * @param  integer id_codestado
	 * @param  string observaciones_estado
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerEstadosCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pfecha_estado = null,
		$porden_estado = null,
		$pid_codestado = null,
		$pobservaciones_estado = null,
		$pid_usuario = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_estados}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_estado', IGUAL_A, $pfecha_estado);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_estado', IGUAL_A, $porden_estado);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codestado', IGUAL_A, $pid_codestado);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_estado', '%', $pobservaciones_estado, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientes: a partir de un determinado clave de expediente, retorna el número de orden del último estado insertado.
	 * En caso de que no existan estados para un determinado año y tipo, devuelve cero.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return int           [description]
	 */
	public function obtenerNumeroUltimoEstado(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance,
		$pfecha_estado) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			max(T.`orden_estado`) as ultimo_numero
		FROM
			`{$this->t_expe_estados}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);

		// 21/03/2018, XXXX
		// al guardar el primer Giro no se conoce la fecha del último estado del expediente
		// sólo la clave del expediente basta para obtener el siguiente número de orden del estado
		if (!is_null($pfecha_estado)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_estado', IGUAL_A, $pfecha_estado);
		}

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = null;

		// Agrego el GROUP BY
		$builder->criteriosGroupBy = null;

		// Agrego el LIMIT
		$builder->limiteCantidad = null;
		$builder->limiteOffset = null;

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return ($resultado[0]['ultimo_numero'] == null) ? 0 : $resultado[0]['ultimo_numero'];
	}

	/**
	 * DBExpedientes: Guarda una instancia de la clase Estado en la base de datos.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:20
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) fecha_estado
	 * @param  float (PK) orden_estado
	 * @param  integer id_codestado
	 * @param  string observaciones_estado
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarEstado(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pfecha_estado = null,
		$porden_estado = null,
		$pid_codestado = null,
		$pobservaciones_estado = null,
		$pid_usuario = null) {
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_estados;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_estado', $pfecha_estado);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'orden_estado', $porden_estado);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_codestado', $pid_codestado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_estado', $pobservaciones_estado);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Estados en base a diferentes criterios de selección.
	 * GenerateClass 0.97.2 beta @ 2016-08-24 11:57:40
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string (PK) fecha_estado
	 * @param  float (PK) orden_estado
	 * @param  integer id_codestado
	 * @param  string observaciones_estado
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarEstados(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pfecha_estado = null,
		$porden_estado = null,
		$pid_codestado = null,
		$pobservaciones_estado = null,
		$pid_usuario = null) {
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($pfecha_estado) && is_null($porden_estado) && is_null($pid_codestado) && is_null($pobservaciones_estado) && is_null($pid_usuario)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarEstados: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_estados;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_estado', IGUAL_A, $pfecha_estado);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'orden_estado', IGUAL_A, $porden_estado);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_codestado', IGUAL_A, $pid_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones_estado', IGUAL_A, $pobservaciones_estado);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Expedientes
	// ************************************************************************

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Expediente en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:27
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string iniciador_tipo
	 * @param  string iniciador_codigo
	 * @param  string iniciador_bloque_tipo
	 * @param  string iniciador_bloque_codigo
	 * @param  integer agregado_anio
	 * @param  string agregado_tipo
	 * @param  float agregado_numero
	 * @param  integer agregado_cuerpo
	 * @param  integer agregado_alcance
	 * @param  integer id_codcategoria
	 * @param  mixed fecha_entrada_expe Si el parametro es una fecha, busca una coincidencia exacta. Si es un array (de dos elementos), busca elem_1 <= fecha_entrada_expe <= elem_2.
	 * @param  string caratula
	 * @param  string observaciones_expe
	 * @param  integer marca_comision
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerExpedientes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$piniciador_tipo = null,
		$piniciador_codigo = null,
		$piniciador_bloque_tipo = null,
		$piniciador_bloque_codigo = null,
		$pagregado_anio = null,
		$pagregado_tipo = null,
		$pagregado_numero = null,
		$pagregado_cuerpo = null,
		$pagregado_alcance = null,
		$pid_codcategoria = null,
		$pfecha_entrada_expe = null,
		$pcaratula = null,
		$pobservaciones_expe = null,
		$pmarca_comision = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
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
			T.`iniciador_tipo`,
			T.`iniciador_codigo`,
			T.`iniciador_bloque_tipo`,
			T.`iniciador_bloque_codigo`,
			T.`agregado_anio`,
			T.`agregado_tipo`,
			T.`agregado_numero`,
			T.`agregado_cuerpo`,
			T.`agregado_alcance`,
			T.`id_codcategoria`,
			T.`fecha_entrada_expe`,
			T.`caratula`,
			T.`observaciones_expe`,
			T.`marca_comision`,
			T.`digi_completa`,
			T.`id_usuario`,
			CAT.`descripcion_categoria` as ro_descripcion_categoria,
			LUG_A.`descripcion_grp` as ro_iniciador_descripcion_grp,
			LUG_B.`descripcion_grp` as ro_iniciador_bloque_descripcion_grp,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario
		FROM
			`{$this->t_expe_expedientes}` as T
		LEFT JOIN `{$this->t_expe_codcategoria}` as CAT ON
			(T.`id_codcategoria` = CAT.`id_codcategoria`)
		LEFT JOIN `{$this->t_expe_lugares}` as LUG_A ON
			(T.`iniciador_tipo` = LUG_A.`tipo_grp` AND T.`iniciador_codigo` = LUG_A.`codigo_grp`)
		LEFT JOIN `{$this->t_expe_lugares}` as LUG_B ON
			(T.`iniciador_bloque_tipo` = LUG_B.`tipo_grp` AND T.`iniciador_bloque_codigo` = LUG_B.`codigo_grp`)
		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(T.`id_usuario` = USR.`id_usuario`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciador_tipo', IGUAL_A, $piniciador_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciador_codigo', IGUAL_A, $piniciador_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciador_bloque_tipo', IGUAL_A, $piniciador_bloque_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciador_bloque_codigo', IGUAL_A, $piniciador_bloque_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.agregado_anio', IGUAL_A, $pagregado_anio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.agregado_tipo', IGUAL_A, $pagregado_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.agregado_numero', IGUAL_A, $pagregado_numero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.agregado_cuerpo', IGUAL_A, $pagregado_cuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.agregado_alcance', IGUAL_A, $pagregado_alcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codcategoria', IGUAL_A, $pid_codcategoria);

		// Si el parametro es una fecha, busca una coincidencia exacta.
		// Si es un array (de dos elementos), busca elem_1 <= fecha_entrada_expe <= elem_2.
		if (is_array($pfecha_entrada_expe)) {
			$pfecha_entrada_expe_desde = (count($pfecha_entrada_expe) > 0) ? $pfecha_entrada_expe[0] : null; // asigno el primer elemento, o null
			$pfecha_entrada_expe_hasta = (count($pfecha_entrada_expe) > 1) ? $pfecha_entrada_expe[1] : null; // asigno el segundo elemento, o null
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_entrada_expe', MAYOR_IGUAL_A, $pfecha_entrada_expe_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_entrada_expe', MENOR_IGUAL_A, $pfecha_entrada_expe_hasta);
		} else {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_entrada_expe', IGUAL_A, $pfecha_entrada_expe);
		}

		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.caratula', IGUAL_A, $pcaratula);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_expe', '%', $pobservaciones_expe, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.marca_comision', IGUAL_A, $pmarca_comision);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Expediente en base a diferentes criterios de selección para una paginación específica.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:27
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string 	pCriterio	Operador lógico con el cual realizar la comparación. Pueden utilizarse las constantes IGUAL_A, DISTINTO_A, MAYOR_A, MAYOR_IGUAL_A, MENOR_A, MENOR_IGUAL_A.
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerExpedientesPagina(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance,
		$pCriterio = IGUAL_A,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		// Para este método en particular, los parametros no pueden ser nulos

		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			CONCAT(T.`fecha_entrada_expe`, T.`anio`, T.`tipo`, LPAD(T.`numero`, 6, '0'), LPAD(T.`cuerpo`, 2, '0'), LPAD(T.`alcance`, 2, '0')) as full_key,
			T.`anio`,
			T.`tipo`,
			T.`numero`,
			T.`cuerpo`,
			T.`alcance`,
			T.`iniciador_tipo`,
			T.`iniciador_codigo`,
			T.`iniciador_bloque_tipo`,
			T.`iniciador_bloque_codigo`,
			T.`agregado_anio`,
			T.`agregado_tipo`,
			T.`agregado_numero`,
			T.`agregado_cuerpo`,
			T.`agregado_alcance`,
			T.`id_codcategoria`,
			T.`fecha_entrada_expe`,
			T.`caratula`,
			T.`observaciones_expe`,
			T.`marca_comision`,
			T.`digi_completa`,
			T.`id_usuario`,
			CAT.`descripcion_categoria` as ro_descripcion_categoria,
			LUG_A.`descripcion_grp` as ro_iniciador_descripcion_grp,
			LUG_B.`descripcion_grp` as ro_iniciador_bloque_descripcion_grp,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario
		FROM
			`{$this->t_expe_expedientes}` as T
		LEFT JOIN `{$this->t_expe_codcategoria}` as CAT ON
			(T.`id_codcategoria` = CAT.`id_codcategoria`)
		LEFT JOIN `{$this->t_expe_lugares}` as LUG_A ON
			(T.`iniciador_tipo` = LUG_A.`tipo_grp` AND T.`iniciador_codigo` = LUG_A.`codigo_grp`)
		LEFT JOIN `{$this->t_expe_lugares}` as LUG_B ON
			(T.`iniciador_bloque_tipo` = LUG_B.`tipo_grp` AND T.`iniciador_bloque_codigo` = LUG_B.`codigo_grp`)
		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(T.`id_usuario` = USR.`id_usuario`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Preparo los parametros
		$pnumero = sprintf('%06d', $pnumero);
		$pcuerpo = sprintf('%02d', $pcuerpo);
		$palcance = sprintf('%02d', $palcance);

		// Configuro los criterios de WHERE
		// HereDoc Start -----------------------------------------------------------------------------------
		$criterioFullKey = <<<SQL
			CONCAT(T.`fecha_entrada_expe`, T.`anio`, T.`tipo`, LPAD(T.`numero`, 6, '0'), LPAD(T.`cuerpo`, 2, '0'), LPAD(T.`alcance`, 2, '0'))
		    {$pCriterio}
			CONCAT(
				(
					SELECT EX.`fecha_entrada_expe`
					FROM `{$this->t_expe_expedientes}` as EX
					WHERE
						EX.`anio` = {$panio}
					AND EX.`tipo` = '{$ptipo}'
					AND EX.`numero` = {$pnumero}
					AND EX.`cuerpo` = {$pcuerpo}
					AND EX.`alcance` = {$palcance}
				),
				'{$panio}{$ptipo}{$pnumero}{$pcuerpo}{$palcance}'
			)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		$builder->criteriosWhere->agregarCriterioConstante($criterioFullKey);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene el último expediente ingresado.
	 * @return array
	 */
	public function obtenerExpedienteUltimo() {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			CONCAT(T.`fecha_entrada_expe`, T.`anio`, T.`tipo`, LPAD(T.`numero`, 6, '0'), LPAD(T.`cuerpo`, 2, '0'), LPAD(T.`alcance`, 2, '0')) as full_key,
			T.`anio`,
			T.`tipo`,
			T.`numero`,
			T.`cuerpo`,
			T.`alcance`,
			T.`iniciador_tipo`,
			T.`iniciador_codigo`,
			T.`iniciador_bloque_tipo`,
			T.`iniciador_bloque_codigo`,
			T.`agregado_anio`,
			T.`agregado_tipo`,
			T.`agregado_numero`,
			T.`agregado_cuerpo`,
			T.`agregado_alcance`,
			T.`id_codcategoria`,
			T.`fecha_entrada_expe`,
			T.`caratula`,
			T.`observaciones_expe`,
			T.`marca_comision`,
			T.`digi_completa`,
			T.`id_usuario`,
			CAT.`descripcion_categoria` as ro_descripcion_categoria,
			LUG_A.`descripcion_grp` as ro_iniciador_descripcion_grp,
			LUG_B.`descripcion_grp` as ro_iniciador_bloque_descripcion_grp,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario
		FROM
			`{$this->t_expe_expedientes}` as T
		LEFT JOIN `{$this->t_expe_codcategoria}` as CAT ON
			(T.`id_codcategoria` = CAT.`id_codcategoria`)
		LEFT JOIN `{$this->t_expe_lugares}` as LUG_A ON
			(T.`iniciador_tipo` = LUG_A.`tipo_grp` AND T.`iniciador_codigo` = LUG_A.`codigo_grp`)
		LEFT JOIN `{$this->t_expe_lugares}` as LUG_B ON
			(T.`iniciador_bloque_tipo` = LUG_B.`tipo_grp` AND T.`iniciador_bloque_codigo` = LUG_B.`codigo_grp`)
		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(T.`id_usuario` = USR.`id_usuario`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = array('full_key desc');

		// Agrego el LIMIT
		$builder->limiteCantidad = 1;
		$builder->limiteOffset = null;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: a partir de un determinado anio y tipo de expediente, retorna el número del último expediente insertado.
	 * En caso de que no existan expedientes para un determinado año y tipo, devuelve un array vacío.
	 * @param  int 		$panio [description]
	 * @param  string 	$ptipo [description]
	 * @return array    El conjunto de números de último expediente insertado para un año/tipo.
	 */
	public function obtenerNumeroUltimoExpediente($panio, $ptipo) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			T.`anio`,
			T.`tipo`,
			max(T.`numero`) as ultimo_numero
		FROM
			`{$this->t_expe_expedientes}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = null;

		// Agrego el GROUP BY
		$builder->criteriosGroupBy = array('T.anio', 'T.tipo');

		// Agrego el LIMIT
		$builder->limiteCantidad = null;
		$builder->limiteOffset = null;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene la cantidad de filas correspondientes de la clase Expediente en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:27
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string iniciador_tipo
	 * @param  string iniciador_codigo
	 * @param  string iniciador_bloque_tipo
	 * @param  string iniciador_bloque_codigo
	 * @param  integer agregado_anio
	 * @param  string agregado_tipo
	 * @param  float agregado_numero
	 * @param  integer agregado_cuerpo
	 * @param  integer agregado_alcance
	 * @param  integer id_codcategoria
	 * @param  mixed fecha_entrada_expe Si el parametro es una fecha, busca una coincidencia exacta. Si es un array (de dos elementos), busca elem_1 <= fecha_entrada_expe <= elem_2.
	 * @param  string caratula
	 * @param  string observaciones_expe
	 * @param  integer marca_comision
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerExpedientesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$piniciador_tipo = null,
		$piniciador_codigo = null,
		$piniciador_bloque_tipo = null,
		$piniciador_bloque_codigo = null,
		$pagregado_anio = null,
		$pagregado_tipo = null,
		$pagregado_numero = null,
		$pagregado_cuerpo = null,
		$pagregado_alcance = null,
		$pid_codcategoria = null,
		$pfecha_entrada_expe = null,
		$pcaratula = null,
		$pobservaciones_expe = null,
		$pmarca_comision = null,
		$pid_usuario = null) {
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

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciador_tipo', IGUAL_A, $piniciador_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciador_codigo', IGUAL_A, $piniciador_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciador_bloque_tipo', IGUAL_A, $piniciador_bloque_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciador_bloque_codigo', IGUAL_A, $piniciador_bloque_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.agregado_anio', IGUAL_A, $pagregado_anio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.agregado_tipo', IGUAL_A, $pagregado_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.agregado_numero', IGUAL_A, $pagregado_numero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.agregado_cuerpo', IGUAL_A, $pagregado_cuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.agregado_alcance', IGUAL_A, $pagregado_alcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codcategoria', IGUAL_A, $pid_codcategoria);

		// Si el parametro es una fecha, busca una coincidencia exacta.
		// Si es un array (de dos elementos), busca elem_1 <= fecha_entrada_expe <= elem_2.
		if (is_array($pfecha_entrada_expe)) {
			$pfecha_entrada_expe_desde = (count($pfecha_entrada_expe) > 0) ? $pfecha_entrada_expe[0] : null; // asigno el primer elemento, o null
			$pfecha_entrada_expe_hasta = (count($pfecha_entrada_expe) > 1) ? $pfecha_entrada_expe[1] : null; // asigno el segundo elemento, o null
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_entrada_expe', MAYOR_IGUAL_A, $pfecha_entrada_expe_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_entrada_expe', MENOR_IGUAL_A, $pfecha_entrada_expe_hasta);
		} else {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_entrada_expe', IGUAL_A, $pfecha_entrada_expe);
		}

		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.caratula', IGUAL_A, $pcaratula);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_expe', '%', $pobservaciones_expe, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.marca_comision', IGUAL_A, $pmarca_comision);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientes: Guarda una instancia de la clase Expediente en la base de datos.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:27
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string iniciador_tipo
	 * @param  string iniciador_codigo
	 * @param  string iniciador_bloque_tipo
	 * @param  string iniciador_bloque_codigo
	 * @param  integer agregado_anio
	 * @param  string agregado_tipo
	 * @param  float agregado_numero
	 * @param  integer agregado_cuerpo
	 * @param  integer agregado_alcance
	 * @param  integer id_codcategoria
	 * @param  string fecha_entrada_expe
	 * @param  string caratula
	 * @param  string observaciones_expe
	 * @param  integer marca_comision
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarExpediente(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$piniciador_tipo = null,
		$piniciador_codigo = null,
		$piniciador_bloque_tipo = null,
		$piniciador_bloque_codigo = null,
		$pagregado_anio = null,
		$pagregado_tipo = null,
		$pagregado_numero = null,
		$pagregado_cuerpo = null,
		$pagregado_alcance = null,
		$pid_codcategoria = null,
		$pfecha_entrada_expe = null,
		$pcaratula = null,
		$pobservaciones_expe = null,
		$pmarca_comision = null,
		$pdigi_completa = null,
		$pid_usuario = null) {
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_expedientes;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'iniciador_tipo', $piniciador_tipo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'iniciador_codigo', $piniciador_codigo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'iniciador_bloque_tipo', $piniciador_bloque_tipo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'iniciador_bloque_codigo', $piniciador_bloque_codigo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'agregado_anio', $pagregado_anio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'agregado_tipo', $pagregado_tipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'agregado_numero', $pagregado_numero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'agregado_cuerpo', $pagregado_cuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'agregado_alcance', $pagregado_alcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_codcategoria', $pid_codcategoria);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_entrada_expe', $pfecha_entrada_expe);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'caratula', $pcaratula);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_expe', $pobservaciones_expe);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'marca_comision', $pmarca_comision);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'digi_completa', $pdigi_completa);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Expedientes en base a diferentes criterios de selección.
	 * GenerateClass 0.97.2 beta @ 2016-08-24 11:57:40
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  string iniciador_tipo
	 * @param  string iniciador_codigo
	 * @param  string iniciador_bloque_tipo
	 * @param  string iniciador_bloque_codigo
	 * @param  integer agregado_anio
	 * @param  string agregado_tipo
	 * @param  float agregado_numero
	 * @param  integer agregado_cuerpo
	 * @param  integer agregado_alcance
	 * @param  integer id_codcategoria
	 * @param  string fecha_entrada_expe
	 * @param  string caratula
	 * @param  string observaciones_expe
	 * @param  integer marca_comision
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarExpedientes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$piniciador_tipo = null,
		$piniciador_codigo = null,
		$piniciador_bloque_tipo = null,
		$piniciador_bloque_codigo = null,
		$pagregado_anio = null,
		$pagregado_tipo = null,
		$pagregado_numero = null,
		$pagregado_cuerpo = null,
		$pagregado_alcance = null,
		$pid_codcategoria = null,
		$pfecha_entrada_expe = null,
		$pcaratula = null,
		$pobservaciones_expe = null,
		$pmarca_comision = null,
		$pid_usuario = null) {
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($piniciador_tipo) && is_null($piniciador_codigo) && is_null($piniciador_bloque_tipo) && is_null($piniciador_bloque_codigo) && is_null($pagregado_anio) && is_null($pagregado_tipo) && is_null($pagregado_numero) && is_null($pagregado_cuerpo) && is_null($pagregado_alcance) && is_null($pid_codcategoria) && is_null($pfecha_entrada_expe) && is_null($pcaratula) && is_null($pobservaciones_expe) && is_null($pmarca_comision) && is_null($pid_usuario)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarExpedientes: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_expedientes;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'iniciador_tipo', IGUAL_A, $piniciador_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'iniciador_codigo', IGUAL_A, $piniciador_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'iniciador_bloque_tipo', IGUAL_A, $piniciador_bloque_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'iniciador_bloque_codigo', IGUAL_A, $piniciador_bloque_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'agregado_anio', IGUAL_A, $pagregado_anio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'agregado_tipo', IGUAL_A, $pagregado_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'agregado_numero', IGUAL_A, $pagregado_numero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'agregado_cuerpo', IGUAL_A, $pagregado_cuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'agregado_alcance', IGUAL_A, $pagregado_alcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_codcategoria', IGUAL_A, $pid_codcategoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_entrada_expe', IGUAL_A, $pfecha_entrada_expe);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'caratula', IGUAL_A, $pcaratula);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones_expe', IGUAL_A, $pobservaciones_expe);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'marca_comision', IGUAL_A, $pmarca_comision);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Giros
	// ************************************************************************

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Giro en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:44
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  string comision_tipo
	 * @param  string comision_codigo
	 * @param  string fecha_entrada_giro
	 * @param  string fecha_salida_giro
	 * @param  string dictamen_giro
	 * @param  string observaciones_giro
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerGiros(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$pcomision_tipo = null,
		$pcomision_codigo = null,
		$pfecha_entrada_giro = null,
		$pfecha_salida_giro = null,
		$pdictamen_giro = null,
		$pobservaciones_giro = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
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
			T.`orden_giro`,
			T.`comision_tipo`,
			T.`comision_codigo`,
			T.`fecha_entrada_giro`,
			T.`fecha_salida_giro`,
			T.`dictamen_giro`,
			T.`observaciones_giro`,
			T.`id_usuario`,
			LUG.`descripcion_grp` AS ro_descripcion_grp
		FROM
			`{$this->t_expe_giros}` as T
		LEFT JOIN `{$this->t_expe_lugares}` as LUG ON
			(LUG.`tipo_grp` = T.`comision_tipo` AND LUG.`codigo_grp` = T.`comision_codigo`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_giro', IGUAL_A, $porden_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.comision_tipo', IGUAL_A, $pcomision_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.comision_codigo', IGUAL_A, $pcomision_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_entrada_giro', IGUAL_A, $pfecha_entrada_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_salida_giro', IGUAL_A, $pfecha_salida_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.dictamen_giro', IGUAL_A, $pdictamen_giro);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_giro', '%', $pobservaciones_giro, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene la cantidad de filas correspondientes de la clase Giro en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:44
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  string comision_tipo
	 * @param  string comision_codigo
	 * @param  string fecha_entrada_giro
	 * @param  string fecha_salida_giro
	 * @param  string dictamen_giro
	 * @param  string observaciones_giro
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerGirosCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$pcomision_tipo = null,
		$pcomision_codigo = null,
		$pfecha_entrada_giro = null,
		$pfecha_salida_giro = null,
		$pdictamen_giro = null,
		$pobservaciones_giro = null,
		$pid_usuario = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_giros}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_giro', IGUAL_A, $porden_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.comision_tipo', IGUAL_A, $pcomision_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.comision_codigo', IGUAL_A, $pcomision_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_entrada_giro', IGUAL_A, $pfecha_entrada_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_salida_giro', IGUAL_A, $pfecha_salida_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.dictamen_giro', IGUAL_A, $pdictamen_giro);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_giro', '%', $pobservaciones_giro, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientes: a partir de un determinado clave de expediente, retorna el número de orden del último giro insertado.
	 * En caso de que no existan giros para un determinado año y tipo, devuelve cero.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return int           [description]
	 */
	public function obtenerNumeroUltimoGiro(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			max(T.`orden_giro`) as ultimo_numero
		FROM
			`{$this->t_expe_giros}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = null;

		// Agrego el GROUP BY
		$builder->criteriosGroupBy = null;

		// Agrego el LIMIT
		$builder->limiteCantidad = null;
		$builder->limiteOffset = null;

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return ($resultado[0]['ultimo_numero'] == null) ? 0 : $resultado[0]['ultimo_numero'];
	}

	/**
	 * DBExpedientes: Guarda una instancia de la clase Giro en la base de datos.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:44
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  string comision_tipo
	 * @param  string comision_codigo
	 * @param  string fecha_entrada_giro
	 * @param  string fecha_salida_giro
	 * @param  string dictamen_giro
	 * @param  string observaciones_giro
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarGiro(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$pcomision_tipo = null,
		$pcomision_codigo = null,
		$pfecha_entrada_giro = null,
		$pfecha_salida_giro = null,
		$pdictamen_giro = null,
		$pobservaciones_giro = null,
		$pid_usuario = null) {
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_giros;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'orden_giro', $porden_giro);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'comision_tipo', $pcomision_tipo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'comision_codigo', $pcomision_codigo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_entrada_giro', $pfecha_entrada_giro);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_salida_giro', $pfecha_salida_giro);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'dictamen_giro', $pdictamen_giro);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_giro', $pobservaciones_giro);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Giros en base a diferentes criterios de selección.
	 * GenerateClass 0.97.2 beta @ 2016-08-24 11:57:40
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  string comision_tipo
	 * @param  string comision_codigo
	 * @param  string fecha_entrada_giro
	 * @param  string fecha_salida_giro
	 * @param  string dictamen_giro
	 * @param  string observaciones_giro
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarGiros(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$pcomision_tipo = null,
		$pcomision_codigo = null,
		$pfecha_entrada_giro = null,
		$pfecha_salida_giro = null,
		$pdictamen_giro = null,
		$pobservaciones_giro = null,
		$pid_usuario = null) {
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($porden_giro) && is_null($pcomision_tipo) && is_null($pcomision_codigo) && is_null($pfecha_entrada_giro) && is_null($pfecha_salida_giro) && is_null($pdictamen_giro) && is_null($pobservaciones_giro) && is_null($pid_usuario)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarGiros: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_giros;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'orden_giro', IGUAL_A, $porden_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'comision_tipo', IGUAL_A, $pcomision_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'comision_codigo', IGUAL_A, $pcomision_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_entrada_giro', IGUAL_A, $pfecha_entrada_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_salida_giro', IGUAL_A, $pfecha_salida_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'dictamen_giro', IGUAL_A, $pdictamen_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones_giro', IGUAL_A, $pobservaciones_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Giro en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:44
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return array
	 */
	public function obtenerUltimoGiro(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		// Control de consulta
		array $pOrdenColumnas = null) {
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
			T.`orden_giro`,
			T.`comision_tipo`,
			T.`comision_codigo`,
			T.`fecha_entrada_giro`,
			T.`fecha_salida_giro`,
			T.`dictamen_giro`,
			T.`observaciones_giro`,
			T.`id_usuario`,
			LUG.`descripcion_grp` AS ro_descripcion_grp
		FROM
			`{$this->t_expe_giros}` as T
		LEFT JOIN `{$this->t_expe_lugares}` as LUG ON
			(LUG.`tipo_grp` = T.`comision_tipo` AND LUG.`codigo_grp` = T.`comision_codigo`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioConstante('T.fecha_entrada_giro IS NOT NULL');
		$builder->criteriosWhere->agregarCriterioConstante('T.fecha_salida_giro IS NULL');

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = 1; // LIMIT 1
		$builder->limiteOffset = null;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * 22/08/2022 XXXX
	 * DBExpedientes: retorna el último Orden del Giro de un expediente determinado.
	 * En caso de que no existan giros para un determinado año y tipo, devuelve cero.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return int           [description]
	 */
	public function obtenerUltimoOrdenGiro(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			max(T.`orden_giro`) as ultimo_numero
		FROM
			`{$this->t_expe_giros}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = null;

		// Agrego el GROUP BY
		$builder->criteriosGroupBy = null;

		// Agrego el LIMIT
		$builder->limiteCantidad = null;
		$builder->limiteOffset = null;

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return ($resultado[0]['ultimo_numero'] == null) ? 0 : $resultado[0]['ultimo_numero'];
	}

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Giro en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:44
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) orden_giro
	 * @return array
	 */
	public function obtenerUltimoInforme(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
			SELECT *
			FROM `{$this->t_expe_informes}`
			WHERE ##CRITERIO_INFORMES##
			AND `orden_informe` = ( SELECT MAX(`orden_informe`)
								    FROM `{$this->t_expe_informes}`
								    WHERE ##CRITERIO_SUBCONSULTA_INFORMES##
								    AND `fecha_vuelta_informe` IS NULL
								  )
			AND `fecha_vuelta_informe` IS NULL
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuramos los criterios de WHERE de INFORMES
		$criteriosWhereInformes = new ListaCriteriosQuery();
		$criteriosWhereInformes->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$criteriosWhereInformes->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$criteriosWhereInformes->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$criteriosWhereInformes->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$criteriosWhereInformes->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$criteriosWhereInformes->agregarCriterioSimple(P_FLOAT, 'orden_giro', IGUAL_A, $porden_giro);
		// Reemplazamos la marca por el criterio respectivo
		$builder->cabecera = str_replace('##CRITERIO_INFORMES##', $criteriosWhereInformes->generarWhere(CRITERIO_AND), $builder->cabecera);

		// Configuramos los criterios de WHERE de la subconsulta de INFORMES
		$criteriosWhereSubconsultaInformes = new ListaCriteriosQuery();
		$criteriosWhereSubconsultaInformes->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$criteriosWhereSubconsultaInformes->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$criteriosWhereSubconsultaInformes->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$criteriosWhereSubconsultaInformes->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$criteriosWhereSubconsultaInformes->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$criteriosWhereSubconsultaInformes->agregarCriterioSimple(P_FLOAT, 'orden_giro', IGUAL_A, $porden_giro);
		// Reemplazamos en la subconsulta la marca por el criterio respectivo
		$builder->cabecera = str_replace('##CRITERIO_SUBCONSULTA_INFORMES##', $criteriosWhereSubconsultaInformes->generarWhere(CRITERIO_AND), $builder->cabecera);

		// Ejecutamos la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	// ************************************************************************
	// Proyectos
	// ************************************************************************

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Proyecto en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:52
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  integer id_codproyecto
	 * @param  string extracto
	 * @param  string observaciones_proyecto
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerProyectos(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pid_codproyecto = null,
		$pextracto = null,
		$pobservaciones_proyecto = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
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
			T.`orden_proyecto`,
			T.`id_codproyecto`,
			T.`extracto`,
			T.`observaciones_proyecto`,
			T.`id_usuario`,
			CP.`descripcion_proyecto` AS ro_descripcion_proyecto,
			S.`numero_promulga` AS ro_numero_promulga,
			S.`fecha_promulga` AS ro_fecha_promulga,
			S.`decreto_promulga` AS ro_decreto_promulga,
			S.`fecha_veto` AS ro_fecha_veto,
			S.`fecha_sancion` AS ro_fecha_sancion,
			S.`numero_sancion` AS ro_numero_sancion
		FROM
			`{$this->t_expe_proyectos}` as T
		LEFT JOIN `{$this->t_expe_codproyectos}` as CP ON
			(CP.`id_codproyecto` = T.`id_codproyecto`)
		LEFT JOIN (
			-- Esta subconsulta obtiene todas las sanciones ACTUALES; es decir, la ultima sanción para cada proyecto.
			SELECT ES.*
			FROM `{$this->t_expe_sanciones}` as ES
			INNER JOIN (
				-- Determino la maxima fecha entre la promulgación y el veto
				SELECT SAUX.`anio`, SAUX.`tipo`, SAUX.`numero`, SAUX.`cuerpo`, SAUX.`alcance`, SAUX.`orden_proyecto`, MAX(GREATEST(IFNULL(SAUX.`fecha_promulga`, '1900-01-01'), IFNULL(SAUX.`fecha_veto`, '1900-01-01'))) as max_fecha_promulga_o_veto
				FROM `{$this->t_expe_sanciones}` as SAUX
				WHERE ##CRITERIO_SANCION##
				GROUP BY SAUX.`anio`, SAUX.`tipo`, SAUX.`numero`, SAUX.`cuerpo`, SAUX.`alcance`, SAUX.`orden_proyecto` )
			AS ES_ACTUAL ON
				(	ES.`anio` = ES_ACTUAL.`anio`
				AND ES.`tipo` = ES_ACTUAL.`tipo`
				AND ES.`numero` = ES_ACTUAL.`numero`
				AND ES.`cuerpo` = ES_ACTUAL.`cuerpo`
				AND ES.`alcance` = ES_ACTUAL.`alcance`
				AND ES.`orden_proyecto` = ES_ACTUAL.`orden_proyecto`
				-- Determino la maxima fecha entre la promulgación y el veto
				AND GREATEST(IFNULL(ES.`fecha_promulga`, '1900-01-01'), IFNULL(ES.`fecha_veto`, '1900-01-01')) = ES_ACTUAL.`max_fecha_promulga_o_veto`)
		) as S ON
			(T.`anio` = S.`anio` AND T.`tipo` = S.`tipo` AND T.`numero` = S.`numero` AND T.`cuerpo` = S.`cuerpo` AND T.`alcance` = S.`alcance` AND T.`orden_proyecto` = S.`orden_proyecto`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE de PROYECTOS
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_proyecto', IGUAL_A, $porden_proyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codproyecto', IGUAL_A, $pid_codproyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.extracto', IGUAL_A, $pextracto);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_proyecto', '%', $pobservaciones_proyecto, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Configuro los criterios de WHERE de SANCIONES
		//TODO: hay que sanear estos parametros
		$criteriosWhereSanciones = new ListaCriteriosQuery();
		$criteriosWhereSanciones->agregarCriterioConstante("SAUX.anio = $panio");
		$criteriosWhereSanciones->agregarCriterioConstante("SAUX.tipo = '$ptipo'");
		$criteriosWhereSanciones->agregarCriterioConstante("SAUX.numero = $pnumero");
		$criteriosWhereSanciones->agregarCriterioConstante("SAUX.cuerpo = $pcuerpo");
		$criteriosWhereSanciones->agregarCriterioConstante("SAUX.alcance = $palcance");

		$builder->cabecera = str_replace('##CRITERIO_SANCION##', $criteriosWhereSanciones->generarWhere(CRITERIO_AND), $builder->cabecera);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene la cantidad de filas correspondientes de la clase Proyecto en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:52
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  integer id_codproyecto
	 * @param  string extracto
	 * @param  string observaciones_proyecto
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerProyectosCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pid_codproyecto = null,
		$pextracto = null,
		$pobservaciones_proyecto = null,
		$pid_usuario = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_proyectos}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_proyecto', IGUAL_A, $porden_proyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codproyecto', IGUAL_A, $pid_codproyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.extracto', IGUAL_A, $pextracto);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_proyecto', '%', $pobservaciones_proyecto, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientes: a partir de un determinado clave de expediente, retorna el número de orden del último proyecto insertado.
	 * En caso de que no existan proyectos para un determinado año y tipo, devuelve cero.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return int           [description]
	 */
	public function obtenerNumeroUltimoProyecto(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			max(T.`orden_proyecto`) as ultimo_numero
		FROM
			`{$this->t_expe_proyectos}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = null;

		// Agrego el GROUP BY
		$builder->criteriosGroupBy = null;

		// Agrego el LIMIT
		$builder->limiteCantidad = null;
		$builder->limiteOffset = null;

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return ($resultado[0]['ultimo_numero'] == null) ? 0 : $resultado[0]['ultimo_numero'];
	}

	/**
	 * DBExpedientes: Guarda una instancia de la clase Proyecto en la base de datos.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:52
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  integer id_codproyecto
	 * @param  string extracto
	 * @param  string observaciones_proyecto
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarProyecto(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pid_codproyecto = null,
		$pextracto = null,
		$pobservaciones_proyecto = null,
		$pid_usuario = null) {
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_proyectos;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'orden_proyecto', $porden_proyecto);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_codproyecto', $pid_codproyecto);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'extracto', $pextracto);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_proyecto', $pobservaciones_proyecto);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Proyectos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.2 beta @ 2016-08-24 11:57:40
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  integer id_codproyecto
	 * @param  string extracto
	 * @param  string observaciones_proyecto
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarProyectos(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pid_codproyecto = null,
		$pextracto = null,
		$pobservaciones_proyecto = null,
		$pid_usuario = null) {
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($porden_proyecto) && is_null($pid_codproyecto) && is_null($pextracto) && is_null($pobservaciones_proyecto) && is_null($pid_usuario)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarProyectos: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_proyectos;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'orden_proyecto', IGUAL_A, $porden_proyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_codproyecto', IGUAL_A, $pid_codproyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'extracto', IGUAL_A, $pextracto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones_proyecto', IGUAL_A, $pobservaciones_proyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// 19/02/2021 XXXX
	// ************************************************************************
	// Participaciones
	// ************************************************************************

	/**
	 * DBExpedientes: Se verifica si el expediente se encuentra habilitado para su participación ciudadana
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return array
	 */
	public function estaHabilitadoParticipacion(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			`anio`,
			`tipo`,
			`numero`,
			`cuerpo`,
			`alcance`
		FROM
			`{$this->t_expe_en_participacion}`
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Participacion en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:52
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) numero_participacion
	 * @param  string texto
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerParticipaciones(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pnumero_participacion = null,
		$ptexto = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			EP.`anio`,
			EP.`tipo`,
			EP.`numero`,
			EP.`cuerpo`,
			EP.`alcance`,
			EP.`numero_participacion`,
			EP.`fecha`,
			EP.`apellidoynombre`,
			EP.`tipodoc`,
			EP.`nrodoc`,
			EP.`domicilio`,
			EP.`localidad`,
			EP.`telefono`,
			EP.`mail`,
			EP.`institucion_nombre`,
			EP.`institucion_domicilio`,
			EP.`texto`
		FROM
			`{$this->t_expe_participaciones}` as EP
		LEFT JOIN `{$this->t_expe_en_participacion}` as EEP ON
			(EP.`anio` = EEP.`anio` AND
			 EP.`tipo` = EEP.`tipo` AND
			 EP.`numero` = EEP.`numero` AND
			 EP.`cuerpo` = EEP.`cuerpo` AND
			 EP.`alcance` = EEP.`alcance`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EP.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EP.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'EP.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EP.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EP.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'EP.numero_participacion', IGUAL_A, $pnumero_participacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EP.texto', IGUAL_A, $ptexto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EP.estado', IGUAL_A, '1'); // Sólo participaciones Aprobadas

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene la cantidad de filas correspondientes de la clase Participacion en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:52
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) numero_participacion
	 * @param  string texto
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerParticipacionesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pnumero_participacion = null,
		$ptexto = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_participaciones}` as EP
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EP.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EP.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'EP.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EP.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EP.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'EP.numero_participacion', IGUAL_A, $pnumero_participacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EP.texto', IGUAL_A, $ptexto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EP.estado', IGUAL_A, '1'); // Sólo participaciones Aprobadas

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientes: a partir de un determinado clave de expediente, retorna el número de orden de la última participacion insertada.
	 * En caso de que no existan participaciones para un determinado año y tipo, devuelve cero.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return int           [description]
	 */
	public function obtenerNumeroUltimaParticipacion(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			max(T.`numero_participacion`) as ultimo_numero
		FROM
			`{$this->t_expe_participaciones}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = null;

		// Agrego el GROUP BY
		$builder->criteriosGroupBy = null;

		// Agrego el LIMIT
		$builder->limiteCantidad = null;
		$builder->limiteOffset = null;

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return ($resultado[0]['ultimo_numero'] == null) ? 0 : $resultado[0]['ultimo_numero'];
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Participaciones en base a diferentes criterios de selección.
	 * GenerateClass 0.97.2 beta @ 2016-08-24 11:57:40
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) numero_participacion
	 * @param  string texto
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarParticipaciones(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pnumero_participacion = null,
		$ptexto = null) {
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($pnumero_participacion) && is_null($ptexto)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarParticipaciones: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_participaciones;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero_participacion', IGUAL_A, $pnumero_participacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'texto', IGUAL_A, $ptexto);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// 2026-03-19 XXXX
	// Eliminaciones de Exped. Electrónico, Revisiones y Firmas
	// Asociados a un Expediente o Nota determinado
	// ************************************************************************

	/**
	 * DBExpedientes: Elimina un conjunto de Firmas Pendientes de un Expediente Elec.
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarFirmasExpedienteElecPend(
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null) {

		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarFirmasExpedienteElecPend: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_firmas_expediente_elec_pend;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);

		//Logger::get()->Log("builder_eliminarFirmasExpedienteElecPend_".date("Ymd_His"), $builder->getQuery());

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Firmas de un Expediente Elec.
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarFirmasExpedienteElec(
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null) {

		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarFirmasExpedienteElec: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

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

		//Logger::get()->Log("builder_eliminarFirmasExpedienteElec_".date("Ymd_His"), $builder->getQuery());

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Revisiones Pendientes de un Expediente Elec.
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarRevExpedienteElecPend(
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null) {

		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarRevExpedienteElecPend: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

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

		//Logger::get()->Log("builder_eliminarRevExpedienteElecPend_".date("Ymd_His"), $builder->getQuery());

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un Expediente Elec. Pendiente
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarExpedientesElecPend(
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null) {

		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarExpedientesElecPend: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_expedientes_elec_pend;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);

		//Logger::get()->Log("builder_eliminarExpedientesElecPend_".date("Ymd_His"), $builder->getQuery());

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un Expediente Elec.
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarExpedientesElec(
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null) {

		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarExpedientesElec: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

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

		//Logger::get()->Log("builder_eliminarExpedientesElec_".date("Ymd_His"), $builder->getQuery());

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Sanciones
	// ************************************************************************

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Sancion en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:59
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  string (PK) fecha_sancion
	 * @param  string numero_sancion
	 * @param  string fecha_promulga
	 * @param  string numero_promulga
	 * @param  string decreto_promulga
	 * @param  string fecha_veto
	 * @param  string decreto_veto
	 * @param  string decreto_presidencia
	 * @param  string fecha_remision_de_comunicacion
	 * @param  string fecha_1er_vto_comunicacion
	 * @param  string fecha_2do_vto_comunicacion
	 * @param  string fecha_rta_comunicacion
	 * @param  string observaciones_sancion
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerSanciones(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pfecha_sancion = null,
		$pnumero_sancion = null,
		$pfecha_promulga = null,
		$pnumero_promulga = null,
		$pdecreto_promulga = null,
		$pfecha_veto = null,
		$pdecreto_veto = null,
		$pdecreto_presidencia = null,
		$pfecha_remision_de_comunicacion = null,
		$pfecha_1er_vto_comunicacion = null,
		$pfecha_2do_vto_comunicacion = null,
		$pfecha_rta_comunicacion = null,
		$pobservaciones_sancion = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
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
			T.`orden_proyecto`,
			T.`fecha_sancion`,
			T.`numero_sancion`,
			T.`fecha_promulga`,
			T.`numero_promulga`,
			T.`decreto_promulga`,
			T.`fecha_veto`,
			T.`decreto_veto`,
			T.`decreto_presidencia`,
			T.`fecha_remision_de_comunicacion`,
			T.`fecha_1er_vto_comunicacion`,
			T.`fecha_2do_vto_comunicacion`,
			T.`fecha_rta_comunicacion`,
			T.`observaciones_sancion`,
			T.`id_usuario`,
			CP.`descripcion_proyecto` AS ro_descripcion_proyecto
		FROM
			`{$this->t_expe_sanciones}` as T
		LEFT JOIN `{$this->t_expe_proyectos}` as P ON
			(P.`anio` = T.`anio` AND P.`tipo` = T.`tipo` AND P.`numero` = T.`numero` AND P.`cuerpo` = T.`cuerpo` AND P.`alcance` = T.`alcance` AND P.`orden_proyecto` = T.`orden_proyecto`)
		LEFT JOIN `{$this->t_expe_codproyectos}` as CP ON
			(CP.`id_codproyecto` = P.`id_codproyecto`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_proyecto', IGUAL_A, $porden_proyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_sancion', IGUAL_A, $pfecha_sancion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.numero_sancion', IGUAL_A, $pnumero_sancion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_promulga', IGUAL_A, $pfecha_promulga);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.numero_promulga', IGUAL_A, $pnumero_promulga);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.decreto_promulga', IGUAL_A, $pdecreto_promulga);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_veto', IGUAL_A, $pfecha_veto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.decreto_veto', IGUAL_A, $pdecreto_veto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.decreto_presidencia', IGUAL_A, $pdecreto_presidencia);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_remision_de_comunicacion', IGUAL_A, $pfecha_remision_de_comunicacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_1er_vto_comunicacion', IGUAL_A, $pfecha_1er_vto_comunicacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_2do_vto_comunicacion', IGUAL_A, $pfecha_2do_vto_comunicacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_rta_comunicacion', IGUAL_A, $pfecha_rta_comunicacion);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_sancion', '%', $pobservaciones_sancion, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene la cantidad de filas correspondientes de la clase Sancion en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:59
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  string (PK) fecha_sancion
	 * @param  string numero_sancion
	 * @param  string fecha_promulga
	 * @param  string numero_promulga
	 * @param  string decreto_promulga
	 * @param  string fecha_veto
	 * @param  string decreto_veto
	 * @param  string decreto_presidencia
	 * @param  string fecha_remision_de_comunicacion
	 * @param  string fecha_1er_vto_comunicacion
	 * @param  string fecha_2do_vto_comunicacion
	 * @param  string fecha_rta_comunicacion
	 * @param  string observaciones_sancion
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerSancionesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pfecha_sancion = null,
		$pnumero_sancion = null,
		$pfecha_promulga = null,
		$pnumero_promulga = null,
		$pdecreto_promulga = null,
		$pfecha_veto = null,
		$pdecreto_veto = null,
		$pdecreto_presidencia = null,
		$pfecha_remision_de_comunicacion = null,
		$pfecha_1er_vto_comunicacion = null,
		$pfecha_2do_vto_comunicacion = null,
		$pfecha_rta_comunicacion = null,
		$pobservaciones_sancion = null,
		$pid_usuario = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_sanciones}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_proyecto', IGUAL_A, $porden_proyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_sancion', IGUAL_A, $pfecha_sancion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.numero_sancion', IGUAL_A, $pnumero_sancion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_promulga', IGUAL_A, $pfecha_promulga);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.numero_promulga', IGUAL_A, $pnumero_promulga);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.decreto_promulga', IGUAL_A, $pdecreto_promulga);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_veto', IGUAL_A, $pfecha_veto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.decreto_veto', IGUAL_A, $pdecreto_veto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.decreto_presidencia', IGUAL_A, $pdecreto_presidencia);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_remision_de_comunicacion', IGUAL_A, $pfecha_remision_de_comunicacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_1er_vto_comunicacion', IGUAL_A, $pfecha_1er_vto_comunicacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_2do_vto_comunicacion', IGUAL_A, $pfecha_2do_vto_comunicacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_rta_comunicacion', IGUAL_A, $pfecha_rta_comunicacion);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_sancion', '%', $pobservaciones_sancion, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientes: Guarda una instancia de la clase Sancion en la base de datos.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:59
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  string (PK) fecha_sancion
	 * @param  string numero_sancion
	 * @param  string fecha_promulga
	 * @param  string numero_promulga
	 * @param  string decreto_promulga
	 * @param  string fecha_veto
	 * @param  string decreto_veto
	 * @param  string decreto_presidencia
	 * @param  string fecha_remision_de_comunicacion
	 * @param  string fecha_1er_vto_comunicacion
	 * @param  string fecha_2do_vto_comunicacion
	 * @param  string fecha_rta_comunicacion
	 * @param  string observaciones_sancion
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarSancion(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pfecha_sancion = null,
		$pnumero_sancion = null,
		$pfecha_promulga = null,
		$pnumero_promulga = null,
		$pdecreto_promulga = null,
		$pfecha_veto = null,
		$pdecreto_veto = null,
		$pdecreto_presidencia = null,
		$pfecha_remision_de_comunicacion = null,
		$pfecha_1er_vto_comunicacion = null,
		$pfecha_2do_vto_comunicacion = null,
		$pfecha_rta_comunicacion = null,
		$pobservaciones_sancion = null,
		$pid_usuario = null) {
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_sanciones;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'orden_proyecto', $porden_proyecto);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_sancion', $pfecha_sancion);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'numero_sancion', $pnumero_sancion);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_promulga', $pfecha_promulga);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'numero_promulga', $pnumero_promulga);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'decreto_promulga', $pdecreto_promulga);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_veto', $pfecha_veto);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'decreto_veto', $pdecreto_veto);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'decreto_presidencia', $pdecreto_presidencia);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_remision_de_comunicacion', $pfecha_remision_de_comunicacion);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_1er_vto_comunicacion', $pfecha_1er_vto_comunicacion);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_2do_vto_comunicacion', $pfecha_2do_vto_comunicacion);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_rta_comunicacion', $pfecha_rta_comunicacion);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_sancion', $pobservaciones_sancion);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Sanciones en base a diferentes criterios de selección.
	 * GenerateClass 0.97.2 beta @ 2016-08-24 11:57:40
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_proyecto
	 * @param  string (PK) fecha_sancion
	 * @param  string numero_sancion
	 * @param  string fecha_promulga
	 * @param  string numero_promulga
	 * @param  string decreto_promulga
	 * @param  string fecha_veto
	 * @param  string decreto_veto
	 * @param  string decreto_presidencia
	 * @param  string fecha_remision_de_comunicacion
	 * @param  string fecha_1er_vto_comunicacion
	 * @param  string fecha_2do_vto_comunicacion
	 * @param  string fecha_rta_comunicacion
	 * @param  string observaciones_sancion
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarSanciones(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_proyecto = null,
		$pfecha_sancion = null,
		$pnumero_sancion = null,
		$pfecha_promulga = null,
		$pnumero_promulga = null,
		$pdecreto_promulga = null,
		$pfecha_veto = null,
		$pdecreto_veto = null,
		$pdecreto_presidencia = null,
		$pfecha_remision_de_comunicacion = null,
		$pfecha_1er_vto_comunicacion = null,
		$pfecha_2do_vto_comunicacion = null,
		$pfecha_rta_comunicacion = null,
		$pobservaciones_sancion = null,
		$pid_usuario = null) {
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($porden_proyecto) && is_null($pfecha_sancion) && is_null($pnumero_sancion) && is_null($pfecha_promulga) && is_null($pnumero_promulga) && is_null($pdecreto_promulga) && is_null($pfecha_veto) && is_null($pdecreto_veto) && is_null($pdecreto_presidencia) && is_null($pfecha_remision_de_comunicacion) && is_null($pfecha_1er_vto_comunicacion) && is_null($pfecha_2do_vto_comunicacion) && is_null($pfecha_rta_comunicacion) && is_null($pobservaciones_sancion) && is_null($pid_usuario)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarSanciones: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_sanciones;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'orden_proyecto', IGUAL_A, $porden_proyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_sancion', IGUAL_A, $pfecha_sancion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'numero_sancion', IGUAL_A, $pnumero_sancion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_promulga', IGUAL_A, $pfecha_promulga);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'numero_promulga', IGUAL_A, $pnumero_promulga);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'decreto_promulga', IGUAL_A, $pdecreto_promulga);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_veto', IGUAL_A, $pfecha_veto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'decreto_veto', IGUAL_A, $pdecreto_veto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'decreto_presidencia', IGUAL_A, $pdecreto_presidencia);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_remision_de_comunicacion', IGUAL_A, $pfecha_remision_de_comunicacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_1er_vto_comunicacion', IGUAL_A, $pfecha_1er_vto_comunicacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_2do_vto_comunicacion', IGUAL_A, $pfecha_2do_vto_comunicacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_rta_comunicacion', IGUAL_A, $pfecha_rta_comunicacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones_sancion', IGUAL_A, $pobservaciones_sancion);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Temas
	// ************************************************************************

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Tema en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:50:09
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_codtema
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerTemas(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_codtema = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
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
			T.`id_codtema`,
			T.`id_usuario`,
			CT.`descripcion_tema` as ro_descripcion_tema
		FROM
			`{$this->t_expe_temas}` as T
		LEFT JOIN `{$this->t_expe_codtemas}` as CT ON
			(CT.`id_codtema` = T.`id_codtema`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codtema', IGUAL_A, $pid_codtema);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene la cantidad de filas correspondientes de la clase Tema en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:50:09
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_codtema
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerTemasCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_codtema = null,
		$pid_usuario = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_temas}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codtema', IGUAL_A, $pid_codtema);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientes: Guarda una instancia de la clase Tema en la base de datos.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:50:09
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_codtema
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarTema(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_codtema = null,
		$pid_usuario = null) {
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_temas;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_codtema', $pid_codtema);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Temas en base a diferentes criterios de selección.
	 * GenerateClass 0.97.2 beta @ 2016-08-24 11:57:40
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_codtema
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarTemas(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_codtema = null,
		$pid_usuario = null) {
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($pid_codtema) && is_null($pid_usuario)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarTemas: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_temas;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_codtema', IGUAL_A, $pid_codtema);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Informes
	// ************************************************************************

	/**
	 * DBExpedientes: Obtiene un array de filas correspondientes a la clase Informe en base a diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:44
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  float (PK) orden_informe
	 * @param  string fecha_pedido_informe
	 * @param  string fecha_vuelta_informe
	 * @param  string detalle_informe
	 * @param  string observaciones_informe
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerInformes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$porden_informe = null,
		$pfecha_pedido_informe = null,
		$pfecha_vuelta_informe = null,
		$pdetalle_informe = null,
		$pobservaciones_informe = null,
		$pid_usuario = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
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
			T.`orden_giro`,
			T.`orden_informe`,
			T.`fecha_pedido_informe`,
			T.`fecha_vuelta_informe`,
			T.`detalle_informe`,
			T.`observaciones_informe`,
			T.`id_usuario`
		FROM
			`{$this->t_expe_informes}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_giro', IGUAL_A, $porden_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_informe', IGUAL_A, $porden_informe);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_pedido_informe', IGUAL_A, $pfecha_pedido_informe);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_vuelta_informe', IGUAL_A, $pfecha_vuelta_informe);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.detalle_informe', '%', $pdetalle_informe, '%');
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_informe', '%', $pobservaciones_informe, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientes: Obtiene la cantidad de filas correspondientes de la clase Informe en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:44
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  float (PK) orden_informe
	 * @param  string fecha_pedido_informe
	 * @param  string fecha_vuelta_informe
	 * @param  string detalle_informe
	 * @param  string observaciones_informe
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerInformesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$porden_informe = null,
		$pfecha_pedido_informe = null,
		$pfecha_vuelta_informe = null,
		$pdetalle_informe = null,
		$pobservaciones_informe = null,
		$pid_usuario = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_informes}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.orden_giro', IGUAL_A, $porden_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.orden_informe', IGUAL_A, $porden_informe);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_pedido_informe', IGUAL_A, $pfecha_pedido_informe);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_vuelta_informe', IGUAL_A, $pfecha_vuelta_informe);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.detalle_informe', '%', $pdetalle_informe, '%');
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.observaciones_informe', '%', $pobservaciones_informe, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

/**
 * DBExpedientes: a partir de un determinado clave de expediente, retorna el número de orden del último giro insertado.
 * En caso de que no existan informes para un determinado año y tipo, devuelve cero.
 * @param  [type] $panio    [description]
 * @param  [type] $ptipo    [description]
 * @param  [type] $pnumero  [description]
 * @param  [type] $pcuerpo  [description]
 * @param  [type] $palcance [description]
 * @return int           [description]
 */
	public function obtenerNumeroUltimoInforme(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			max(T.`orden_informe`) as ultimo_numero
		FROM
			`{$this->t_expe_informes}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = null;

		// Agrego el GROUP BY
		$builder->criteriosGroupBy = null;

		// Agrego el LIMIT
		$builder->limiteCantidad = null;
		$builder->limiteOffset = null;

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return ($resultado[0]['ultimo_numero'] == null) ? 0 : $resultado[0]['ultimo_numero'];
	}

	/**
	 * DBExpedientes: Guarda una instancia de la clase Informe en la base de datos.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:49:44
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  float (PK) orden_informe
	 * @param  string fecha_pedido_informe
	 * @param  string fecha_vuelta_informe
	 * @param  string detalle_informe
	 * @param  string observaciones_informe
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarInforme(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$porden_informe = null,
		$pfecha_pedido_informe = null,
		$pfecha_vuelta_informe = null,
		$pdetalle_informe = null,
		$pobservaciones_informe = null,
		$pid_usuario = null) {
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_informes;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'orden_giro', $porden_giro);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'orden_informe', $porden_informe);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_pedido_informe', $pfecha_pedido_informe);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_vuelta_informe', $pfecha_vuelta_informe);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'detalle_informe', $pdetalle_informe);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_informe', $pobservaciones_informe);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientes: Elimina un conjunto de Informes en base a diferentes criterios de selección.
	 * GenerateClass 0.97.2 beta @ 2016-08-24 11:57:40
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  float (PK) orden_giro
	 * @param  float (PK) orden_informe
	 * @param  string fecha_pedido_informe
	 * @param  string fecha_vuelta_informe
	 * @param  string detalle_informe
	 * @param  string observaciones_informe
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarInformes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$porden_giro = null,
		$porden_informe = null,
		$pfecha_pedido_informe = null,
		$pfecha_vuelta_informe = null,
		$pdetalle_informe = null,
		$pobservaciones_informe = null,
		$pid_usuario = null) {
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($porden_giro) && is_null($porden_informe) && is_null($pfecha_pedido_informe) && is_null($pfecha_vuelta_informe) && is_null($pdetalle_informe) && is_null($pobservaciones_informe) && is_null($pid_usuario)) {
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarInformes: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));
		}

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_informes;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'orden_giro', IGUAL_A, $porden_giro);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'orden_informe', IGUAL_A, $porden_informe);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_pedido_informe', IGUAL_A, $pfecha_pedido_informe);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_vuelta_informe', IGUAL_A, $pfecha_vuelta_informe);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'detalle_informe', IGUAL_A, $pdetalle_informe);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones_informe', IGUAL_A, $pobservaciones_informe);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * Se obtienen los expedientes de una Comisión determinada, en un rango de fechas específico
	 * para definir su "marca" en dicha Comisión
	 * @param  [type] $pfecha_desde     [description]
	 * @param  [type] $pfecha_hasta     [description]
	 * @param  [type] $pcomision_codigo [description]
	 * @return [type]                   [description]
	 */
	public function obtenerExpedientesParaMarcarComision(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pcomision_codigo = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT E.`anio`,
			   E.`tipo`,
			   E.`numero`,
			   E.`cuerpo`,
			   E.`alcance`,
			   E.`caratula`,
			   E.`fecha_entrada_expe`,
			   E.`id_codcategoria`,
			   E.`iniciador_tipo`,
			   E.`iniciador_codigo`,
			   E.`marca_comision`
		FROM `{$this->t_expe_expedientes}` AS E
		WHERE E.fecha_entrada_expe BETWEEN '$pfecha_desde' AND '$pfecha_hasta'
		AND ( SELECT id_codestado FROM `{$this->t_expe_estados}`
			  WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
			  ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
			  LIMIT 1
			) IN ((SELECT id_codestado FROM `expe_codestados` WHERE id_codestado = 3),
				  (SELECT id_codestado FROM `expe_codestados` WHERE id_codestado = 16),
				  (SELECT id_codestado FROM `expe_codestados` WHERE id_codestado = 79)
				 )
		AND ( SELECT comision_codigo FROM `{$this->t_expe_giros}`
			  WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
			  AND fecha_entrada_giro IS NOT NULL
			  AND fecha_salida_giro IS NULL
			  ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC
			  LIMIT 1
			) = '$pcomision_codigo'
		ORDER BY E.anio, E.tipo, E.numero, E.cuerpo, E.alcance
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * Se limpian las Marcas de los expedientes en una Comisión y un período de fechas respectivos
	 * @param  [type] $pfecha_desde     [description]
	 * @param  [type] $pfecha_hasta     [description]
	 * @param  [type] $pcomision_codigo [description]
	 */
	public function limpiarMarcas($pfecha_desde, $pfecha_hasta, $pcomision_codigo) {
		$query = "UPDATE `{$this->t_expe_expedientes}` AS E
				  SET marca_comision = 0
				  WHERE E.fecha_entrada_expe BETWEEN ? AND ?
				  AND ( SELECT id_codestado FROM `{$this->t_expe_estados}`
					    WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
					    ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
					    LIMIT 1
					  ) IN ((SELECT id_codestado FROM `expe_codestados` WHERE id_codestado = 3),
						    (SELECT id_codestado FROM `expe_codestados` WHERE id_codestado = 16),
						    (SELECT id_codestado FROM `expe_codestados` WHERE id_codestado = 79)
						   )
				  AND ( SELECT comision_codigo FROM `{$this->t_expe_giros}`
					    WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
					    AND fecha_entrada_giro IS NOT NULL
					    AND fecha_salida_giro IS NULL
					    ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC
					    LIMIT 1
				 	  ) = ?
				  ORDER BY E.anio, E.tipo, E.numero, E.cuerpo, E.alcance
				 ";

		if ($sentencia = $this->mysqli_conn->prepare($query)) {

			$sentencia->bind_param('sss', $pfecha_desde, $pfecha_hasta, $pcomision_codigo);

			// ejecutar la sentencia
			$sentencia->execute();

			// Verifico errores (debe ser antes de cerrar la sentencia).
			$error_msg = $this->mysqli_conn->error;
			$error_nro = $this->mysqli_conn->errno;

			// cerrar la sentencia
			$sentencia->close();

			// Si hubo errores, los muestro
			if ($error_msg != '') {
				throw new RuntimeException("Falló la ejecución de db_expedientes.limpiarMarcas: ($error_nro) $error_msg");
			}

		} else
		// Lanzo una excepción
		{
			throw new RuntimeException("Falló la ejecución de db_expedientes.limpiarMarcas: (" . $this->mysqli_conn->errno . ")" . $this->mysqli_conn->error);
		}

	}

	/**
	 * DBExpedientes: Guarda una instancia de la clase ExpedienteEnParticipacion en la base de datos.
	 * @param  integer (PK) $panio
	 * @param  string (PK) $ptipo
	 * @param  float (PK) $pnumero
	 * @param  integer (PK) $pcuerpo
	 * @param  integer (PK) $palcance
	 * @param  string $pfecha_inicio
	 * @param  string $pfecha_fin
	 * @param  string $pextracto
	 * @param  integer $pid_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function habilitarExpedienteAParticipar(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pfecha_inicio = null,
		$pfecha_fin = null,
		$pextracto = null,
		$pid_usuario = null) {
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_en_participacion;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_inicio', $pfecha_inicio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_fin', $pfecha_fin);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'extracto', $pextracto);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * Se obtienen los expedientes con estado 90 (en PPC) cuya fecha haya superado los 30 días.
	 * @return [type] [description]
	 */
	public function obtenerExpedientesEnPpcVencidos() {

		$hoy = date("Y-m-d");

		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT E.`anio`,
			   E.`tipo`,
			   E.`numero`,
			   E.`cuerpo`,
			   E.`alcance`,
			   E.`caratula`,
			   E.`fecha_entrada_expe`,
			   Est.`fecha_estado`
		FROM `{$this->t_expe_expedientes}` AS E
		INNER JOIN (
			SELECT anio, tipo, numero, cuerpo, alcance, id_codestado, fecha_estado
			FROM `{$this->t_expe_estados}`
			WHERE id_codestado = 90)
		AS Est ON
			Est.anio = E.anio AND Est.tipo = E.tipo AND Est.numero = E.numero AND Est.cuerpo = E.cuerpo AND Est.alcance = E.alcance
		WHERE E.fecha_entrada_expe BETWEEN '1983-01-01' AND '{$hoy}'
		AND (SELECT DATEDIFF(CURDATE(), Est.`fecha_estado`)) >= 30
		AND ( SELECT id_codestado FROM `{$this->t_expe_estados}`
			  WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
			  -- AND (SELECT DATEDIFF(CURDATE(), fecha_estado)) >= 30
			  ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
			  LIMIT 1
			) IN (SELECT id_codestado FROM `{$this->t_expe_codestados}` WHERE id_codestado = 90)
		ORDER BY E.anio, E.tipo, E.numero, E.cuerpo, E.alcance
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * Verifica si un expediente está Agregado a otro expediente
	 * @param  integer (PK) $panio
	 * @param  string (PK) $ptipo
	 * @param  float (PK) $pnumero
	 * @param  integer (PK) $pcuerpo
	 * @param  integer (PK) $palcance
	 * @return [array]
	 */
	public function estaAgregadoA(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null
	) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			`agregado_anio`, `agregado_tipo`, `agregado_numero`, `agregado_cuerpo`, `agregado_alcance`
		FROM
			`{$this->t_expe_expedientes}`
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioConstante('agregado_anio IS NOT NULL');
		$builder->criteriosWhere->agregarCriterioConstante('agregado_tipo IS NOT NULL');
		$builder->criteriosWhere->agregarCriterioConstante('agregado_numero IS NOT NULL');
		$builder->criteriosWhere->agregarCriterioConstante('agregado_cuerpo IS NOT NULL');
		$builder->criteriosWhere->agregarCriterioConstante('agregado_alcance IS NOT NULL');

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

}
?>
