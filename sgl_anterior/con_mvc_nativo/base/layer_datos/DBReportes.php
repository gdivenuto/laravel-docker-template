<?php
/**
 * Capa de acceso a datos (persistencia) específica de la gestion de Reportes.
 */
class DBReportes extends DBBaseClass {
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

	protected $t_admin_usuarios = 'admin_usuarios'; //!< // Identificador de tabla 'admin_usuarios'

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
	 * DBReportes: Obtiene un array de filas correspondientes a la clase Expediente en base a diferentes criterios de selección, asociados a la búsqueda avanzada de expedientes.
	 * @param  array|string     $pfecha_entrada_expe   [description]
	 * @param  array|string     $pfecha_promulga       [description]
	 * @param  array|string     $pfecha_sancion        [description]
	 * @param  integer     $pid_codcategoria      [description]
	 * @param  string     $piniciador_tipo       [description]
	 * @param  string     $piniciador_codigo     [description]
	 * @param  string     $pcaratula             [description]
	 * @param  integer     $pid_codtema           [description]
	 * @param  string     $pautor_tipo           [description]
	 * @param  string     $pautor_codigo         [description]
	 * @param  bool     $ptratamiento_comision [description]
	 * @param  string     $pcomision_codigo      [description]
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerExpedientesAvanzado(
		// Parametros
		$pfecha_entrada_expe = null,
		$pfecha_promulga = null,
		$pfecha_sancion = null,
		$pid_codcategoria = null,
		$piniciador_tipo = null,
		$piniciador_codigo = null,
		$pcaratula = null,
		$pid_codtema = null,
		$pautor_tipo = null,
		$pautor_codigo = null,
		$ptratamiento_comision = null, /* boolean */
		$pcomision_codigo = null,
		$pid_codestado = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
		    E.`anio`,
			E.`tipo`,
			E.`numero`,
			E.`cuerpo`,
			E.`alcance`,
			E.`iniciador_tipo`,
			E.`iniciador_codigo`,
			E.`iniciador_bloque_tipo`,
			E.`iniciador_bloque_codigo`,
			E.`agregado_anio`,
			E.`agregado_tipo`,
			E.`agregado_numero`,
			E.`agregado_cuerpo`,
			E.`agregado_alcance`,
			E.`id_codcategoria`,
			E.`fecha_entrada_expe`,
			E.`caratula`,
			E.`observaciones_expe`,
			E.`marca_comision`,
			E.`id_usuario`,
			CAT.`descripcion_categoria` as ro_descripcion_categoria,
			LUG_A.`descripcion_grp` as ro_iniciador_descripcion_grp,
			LUG_B.`descripcion_grp` as ro_iniciador_bloque_descripcion_grp,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario
		FROM
			`{$this->t_expe_expedientes}` as E
		INNER JOIN
			(	-- Esta subconsulta obtiene las claves agrupadas del criterio complejo de busqueda,
				-- las cuales mejoran el tiempo de respuesta del agrupamiento al ser solo columnas pertenecientes
				-- a la clave primaria.
				SELECT DISTINCT
					EX.`anio`,
					EX.`tipo`,
					EX.`numero`,
					EX.`cuerpo`,
					EX.`alcance`
				FROM
					`{$this->t_expe_expedientes}` as EX

				-- Caratula o Descripcion de proyecto
				LEFT JOIN `{$this->t_expe_proyectos}` as PROY ON
					(EX.`anio` = PROY.`anio` AND EX.`tipo` = PROY.`tipo` AND EX.`numero` = PROY.`numero` AND EX.`cuerpo` = PROY.`cuerpo` AND EX.`alcance` = PROY.`alcance`)

				-- Temas
				LEFT JOIN `{$this->t_expe_temas}` as TEMAS ON
					(EX.`anio` = TEMAS.`anio` AND EX.`tipo` = TEMAS.`tipo` AND EX.`numero` = TEMAS.`numero` AND EX.`cuerpo` = TEMAS.`cuerpo` AND EX.`alcance` = TEMAS.`alcance`)

				-- Autores
				LEFT JOIN `{$this->t_expe_autores}` as AUT ON
					(EX.`anio` = AUT.`anio` AND EX.`tipo` = AUT.`tipo` AND EX.`numero` = AUT.`numero` AND EX.`cuerpo` = AUT.`cuerpo` AND EX.`alcance` = AUT.`alcance`)

				-- Ultimos estados de cada expediente
				LEFT JOIN
					(
						SELECT
							CAB_E.anio, CAB_E.tipo, CAB_E.numero, CAB_E.cuerpo, CAB_E.alcance,
							CAB_E.id_codestado,
							-- CAB_E.fecha_estado,
							-- CAB_E.orden_estado,
							COD_EST.tratamiento_comision
						FROM `{$this->t_expe_estados}` CAB_E -- cabecera
						INNER JOIN (
							SELECT anio, tipo, numero, cuerpo, alcance, MAX(CONCAT(fecha_estado, LPAD(orden_estado, 3, '0'))) AS fecha_orden_estado
							FROM `{$this->t_expe_estados}`
							GROUP BY 1, 2, 3, 4, 5) AS UEAUX -- Ultimo estado de cada expediente
						ON 	(	CAB_E.anio = UEAUX.anio
							AND CAB_E.tipo = UEAUX.tipo
							AND CAB_E.numero = UEAUX.numero
							AND CAB_E.cuerpo = UEAUX.cuerpo
							AND CAB_E.alcance = UEAUX.alcance
							AND CONCAT(CAB_E.fecha_estado, LPAD(CAB_E.orden_estado, 3, '0')) = UEAUX.fecha_orden_estado)
						INNER JOIN `{$this->t_expe_codestados}` AS COD_EST  -- Join con Codificadora de estados para determinar tratamiento en comision
						ON (CAB_E.`id_codestado` = COD_EST.`id_codestado`)
					) as EST ON
					(EX.`anio` = EST.`anio` AND EX.`tipo` = EST.`tipo` AND EX.`numero` = EST.`numero` AND EX.`cuerpo` = EST.`cuerpo` AND EX.`alcance` = EST.`alcance`)

				-- Giros
				LEFT JOIN `{$this->t_expe_giros}` as GIROS ON
					(EX.`anio` = GIROS.`anio` AND EX.`tipo` = GIROS.`tipo` AND EX.`numero` = GIROS.`numero` AND EX.`cuerpo` = GIROS.`cuerpo` AND EX.`alcance` = GIROS.`alcance`)

				-- Sanciones
				LEFT JOIN `{$this->t_expe_sanciones}` as SANC ON
					(EX.`anio` = SANC.`anio` AND EX.`tipo` = SANC.`tipo` AND EX.`numero` = SANC.`numero` AND EX.`cuerpo` = SANC.`cuerpo` AND EX.`alcance` = SANC.`alcance`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE

		// Si el parametro es una fecha, busca una coincidencia exacta.
		// Si es un array (de dos elementos), busca elem_1 <= fecha_entrada_expe <= elem_2.
		if (is_array($pfecha_entrada_expe)) {
			$pfecha_entrada_expe_desde = (count($pfecha_entrada_expe) > 0) ? $pfecha_entrada_expe[0] : null; // asigno el primer elemento, o null
			$pfecha_entrada_expe_hasta = (count($pfecha_entrada_expe) > 1) ? $pfecha_entrada_expe[1] : null; // asigno el segundo elemento, o null
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MAYOR_IGUAL_A, $pfecha_entrada_expe_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MENOR_IGUAL_A, $pfecha_entrada_expe_hasta);
		} else {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', IGUAL_A, $pfecha_entrada_expe);
		}

		// Si el parametro es una fecha, busca una coincidencia exacta.
		// Si es un array (de dos elementos), busca elem_1 <= pfecha_promulga <= elem_2.
		if (is_array($pfecha_promulga)) {
			$pfecha_promulga_desde = (count($pfecha_promulga) > 0) ? $pfecha_promulga[0] : null; // asigno el primer elemento, o null
			$pfecha_promulga_hasta = (count($pfecha_promulga) > 1) ? $pfecha_promulga[1] : null; // asigno el segundo elemento, o null
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_promulga', MAYOR_IGUAL_A, $pfecha_promulga_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_promulga', MENOR_IGUAL_A, $pfecha_promulga_hasta);
		} else {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_promulga', IGUAL_A, $pfecha_promulga);
		}

		// Si el parametro es una fecha, busca una coincidencia exacta.
		// Si es un array (de dos elementos), busca elem_1 <= pfecha_sancion <= elem_2.
		if (is_array($pfecha_sancion)) {
			$pfecha_sancion_desde = (count($pfecha_sancion) > 0) ? $pfecha_sancion[0] : null; // asigno el primer elemento, o null
			$pfecha_sancion_hasta = (count($pfecha_sancion) > 1) ? $pfecha_sancion[1] : null; // asigno el segundo elemento, o null
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_sancion', MAYOR_IGUAL_A, $pfecha_sancion_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_sancion', MENOR_IGUAL_A, $pfecha_sancion_hasta);
			$builder->criteriosWhere->agregarCriterioConstante('SANC.`fecha_promulga` IS NULL');
		} else if (!is_null($pfecha_sancion)) {
			// se agrega un filtro extra en el else porque este criterio trabaja en conjunto con un criterio constante
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_sancion', IGUAL_A, $pfecha_sancion);
			$builder->criteriosWhere->agregarCriterioConstante('SANC.`fecha_promulga` IS NULL');
		}

		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EX.id_codcategoria', IGUAL_A, $pid_codcategoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.iniciador_tipo', IGUAL_A, $piniciador_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.iniciador_codigo', IGUAL_A, $piniciador_codigo);

		// Genero un subcriterio OR para los like de la carátula
		$subCrit = new ListaCriteriosQuery();
		$subCrit->agregarCriterioLike(P_TEXT, 'EX.caratula', '%', $pcaratula, '%');
		$subCrit->agregarCriterioLike(P_TEXT, 'PROY.extracto', '%', $pcaratula, '%');
		$builder->criteriosWhere->agregarSubCriterio(CRITERIO_OR, $subCrit);

		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'TEMAS.id_codtema', IGUAL_A, $pid_codtema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'AUT.autor_tipo', IGUAL_A, $pautor_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'AUT.autor_codigo', IGUAL_A, $pautor_codigo);

		// se agrega un filtro extra porque este criterio trabaja en conjunto con un criterio constante
		if (!is_null($pcomision_codigo)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.comision_codigo', IGUAL_A, $pcomision_codigo);
		}

		// Si se recibe un Estado
		if (!is_null($pid_codestado)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.id_codestado', IGUAL_A, $pid_codestado);
		}

		// Si el expediente se trata en una Comisión
		if (!empty($ptratamiento_comision) && !is_null($ptratamiento_comision)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.tratamiento_comision', IGUAL_A, $this->boolToInt($ptratamiento_comision));
		}
		
		// Agrego el pie de la consulta
		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->pie = <<<SQL
		) AS EXPED_FILTRO ON
		    (E.`anio` = EXPED_FILTRO.`anio` AND E.`tipo` = EXPED_FILTRO.`tipo` AND E.`numero` = EXPED_FILTRO.`numero` AND E.`cuerpo` = EXPED_FILTRO.`cuerpo` AND E.`alcance` = EXPED_FILTRO.`alcance`)

		LEFT JOIN `{$this->t_expe_codcategoria}` as CAT ON
			(E.`id_codcategoria` = CAT.`id_codcategoria`)

		LEFT JOIN `{$this->t_expe_lugares}` as LUG_A ON
			(E.`iniciador_tipo` = LUG_A.`tipo_grp` AND E.`iniciador_codigo` = LUG_A.`codigo_grp`)

		LEFT JOIN `{$this->t_expe_lugares}` as LUG_B ON
			(E.`iniciador_bloque_tipo` = LUG_B.`tipo_grp` AND E.`iniciador_bloque_codigo` = LUG_B.`codigo_grp`)

		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(E.`id_usuario` = USR.`id_usuario`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBReportes: Obtiene la cantidad de filas correspondientes a la clase Expediente en base a diferentes criterios de selección, asociados a la búsqueda avanzada de expedientes.
	 * @param  array|string     $pfecha_entrada_expe   [description]
	 * @param  array|string     $pfecha_promulga       [description]
	 * @param  array|string     $pfecha_sancion        [description]
	 * @param  integer     $pid_codcategoria      [description]
	 * @param  string     $piniciador_tipo       [description]
	 * @param  string     $piniciador_codigo     [description]
	 * @param  string     $pcaratula             [description]
	 * @param  integer     $pid_codtema           [description]
	 * @param  string     $pautor_tipo           [description]
	 * @param  string     $pautor_codigo         [description]
	 * @param  bool     $ptratamiento_comision [description]
	 * @param  string     $pcomision_codigo      [description]
	 * @return integer
	 */
	public function obtenerExpedientesAvanzadoCantidad(
		// Parametros
		$pfecha_entrada_expe = null,
		$pfecha_promulga = null,
		$pfecha_sancion = null,
		$pid_codcategoria = null,
		$piniciador_tipo = null,
		$piniciador_codigo = null,
		$pcaratula = null,
		$pid_codtema = null,
		$pautor_tipo = null,
		$pautor_codigo = null,
		$ptratamiento_comision = null, /* boolean */
		$pcomision_codigo = null,
		$pid_codestado = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM (
			SELECT DISTINCT
				EX.`anio`,
				EX.`tipo`,
				EX.`numero`,
				EX.`cuerpo`,
				EX.`alcance`
			FROM
				`{$this->t_expe_expedientes}` as EX

			LEFT JOIN `{$this->t_expe_proyectos}` as PROY ON
				(EX.`anio` = PROY.`anio` AND EX.`tipo` = PROY.`tipo` AND EX.`numero` = PROY.`numero` AND EX.`cuerpo` = PROY.`cuerpo` AND EX.`alcance` = PROY.`alcance`)

			LEFT JOIN `{$this->t_expe_temas}` as TEMAS ON
				(EX.`anio` = TEMAS.`anio` AND EX.`tipo` = TEMAS.`tipo` AND EX.`numero` = TEMAS.`numero` AND EX.`cuerpo` = TEMAS.`cuerpo` AND EX.`alcance` = TEMAS.`alcance`)

			LEFT JOIN `{$this->t_expe_autores}` as AUT ON
				(EX.`anio` = AUT.`anio` AND EX.`tipo` = AUT.`tipo` AND EX.`numero` = AUT.`numero` AND EX.`cuerpo` = AUT.`cuerpo` AND EX.`alcance` = AUT.`alcance`)

			-- Ultimos estados de cada expediente
			LEFT JOIN
				(
					SELECT
						CAB_E.anio, CAB_E.tipo, CAB_E.numero, CAB_E.cuerpo, CAB_E.alcance,
			            CAB_E.id_codestado,
			            COD_EST.tratamiento_comision
					FROM `{$this->t_expe_estados}` CAB_E -- cabecera
					INNER JOIN (
						SELECT anio, tipo, numero, cuerpo, alcance, MAX(CONCAT(fecha_estado, LPAD(orden_estado, 3, '0'))) AS fecha_orden_estado
						FROM `{$this->t_expe_estados}`
						GROUP BY 1, 2, 3, 4, 5) AS UEAUX -- Ultimo estado de cada expediente
					ON 	(	CAB_E.anio = UEAUX.anio
						AND CAB_E.tipo = UEAUX.tipo
						AND CAB_E.numero = UEAUX.numero
						AND CAB_E.cuerpo = UEAUX.cuerpo
						AND CAB_E.alcance = UEAUX.alcance
						AND CONCAT(CAB_E.fecha_estado, LPAD(CAB_E.orden_estado, 3, '0')) = UEAUX.fecha_orden_estado)
					INNER JOIN `{$this->t_expe_codestados}` AS COD_EST  -- Join con Codificadora de estados para determinar tratamiento en comision
			        ON (CAB_E.`id_codestado` = COD_EST.`id_codestado`)
			    ) as EST ON
				(EX.`anio` = EST.`anio` AND EX.`tipo` = EST.`tipo` AND EX.`numero` = EST.`numero` AND EX.`cuerpo` = EST.`cuerpo` AND EX.`alcance` = EST.`alcance`)

			-- Giros
			LEFT JOIN `{$this->t_expe_giros}` as GIROS ON
				(EX.`anio` = GIROS.`anio` AND EX.`tipo` = GIROS.`tipo` AND EX.`numero` = GIROS.`numero` AND EX.`cuerpo` = GIROS.`cuerpo` AND EX.`alcance` = GIROS.`alcance`)

			-- Sanciones
			LEFT JOIN `{$this->t_expe_sanciones}` as SANC ON
				(EX.`anio` = SANC.`anio` AND EX.`tipo` = SANC.`tipo` AND EX.`numero` = SANC.`numero` AND EX.`cuerpo` = SANC.`cuerpo` AND EX.`alcance` = SANC.`alcance`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE

		// Si el parametro es una fecha, busca una coincidencia exacta.
		// Si es un array (de dos elementos), busca elem_1 <= fecha_entrada_expe <= elem_2.
		if (is_array($pfecha_entrada_expe)) {
			$pfecha_entrada_expe_desde = (count($pfecha_entrada_expe) > 0) ? $pfecha_entrada_expe[0] : null; // asigno el primer elemento, o null
			$pfecha_entrada_expe_hasta = (count($pfecha_entrada_expe) > 1) ? $pfecha_entrada_expe[1] : null; // asigno el segundo elemento, o null
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MAYOR_IGUAL_A, $pfecha_entrada_expe_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MENOR_IGUAL_A, $pfecha_entrada_expe_hasta);
		} else {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', IGUAL_A, $pfecha_entrada_expe);
		}

		// Si el parametro es una fecha, busca una coincidencia exacta.
		// Si es un array (de dos elementos), busca elem_1 <= pfecha_promulga <= elem_2.
		if (is_array($pfecha_promulga)) {
			$pfecha_promulga_desde = (count($pfecha_promulga) > 0) ? $pfecha_promulga[0] : null; // asigno el primer elemento, o null
			$pfecha_promulga_hasta = (count($pfecha_promulga) > 1) ? $pfecha_promulga[1] : null; // asigno el segundo elemento, o null
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_promulga', MAYOR_IGUAL_A, $pfecha_promulga_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_promulga', MENOR_IGUAL_A, $pfecha_promulga_hasta);
		} else {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_promulga', IGUAL_A, $pfecha_promulga);
		}

		// Si el parametro es una fecha, busca una coincidencia exacta.
		// Si es un array (de dos elementos), busca elem_1 <= pfecha_sancion <= elem_2.
		if (is_array($pfecha_sancion)) {
			$pfecha_sancion_desde = (count($pfecha_sancion) > 0) ? $pfecha_sancion[0] : null; // asigno el primer elemento, o null
			$pfecha_sancion_hasta = (count($pfecha_sancion) > 1) ? $pfecha_sancion[1] : null; // asigno el segundo elemento, o null
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_sancion', MAYOR_IGUAL_A, $pfecha_sancion_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_sancion', MENOR_IGUAL_A, $pfecha_sancion_hasta);
			$builder->criteriosWhere->agregarCriterioConstante('SANC.`fecha_promulga` IS NULL');
		} else if (!is_null($pfecha_sancion)) {
			// se agrega un filtro extra en el else porque este criterio trabaja en conjunto con un criterio constante
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'SANC.fecha_sancion', IGUAL_A, $pfecha_sancion);
			$builder->criteriosWhere->agregarCriterioConstante('SANC.`fecha_promulga` IS NULL');
		}

		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EX.id_codcategoria', IGUAL_A, $pid_codcategoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.iniciador_tipo', IGUAL_A, $piniciador_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.iniciador_codigo', IGUAL_A, $piniciador_codigo);

		// Genero un subcriterio OR para los like de la carátula
		$subCrit = new ListaCriteriosQuery();
		$subCrit->agregarCriterioLike(P_TEXT, 'EX.caratula', '%', $pcaratula, '%');
		$subCrit->agregarCriterioLike(P_TEXT, 'PROY.extracto', '%', $pcaratula, '%');
		$builder->criteriosWhere->agregarSubCriterio(CRITERIO_OR, $subCrit);

		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'TEMAS.id_codtema', IGUAL_A, $pid_codtema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'AUT.autor_tipo', IGUAL_A, $pautor_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'AUT.autor_codigo', IGUAL_A, $pautor_codigo);
		
		// se agrega un filtro extra porque este criterio trabaja en conjunto con un criterio constante
		if (!is_null($pcomision_codigo)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.comision_codigo', IGUAL_A, $pcomision_codigo);
		}
		
		// Si se recibe un Estado
		if (!is_null($pid_codestado)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.id_codestado', IGUAL_A, $pid_codestado);
		}

		// Si el expediente se trata en una Comisión
		if (!empty($ptratamiento_comision) && !is_null($ptratamiento_comision)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.tratamiento_comision', IGUAL_A, $this->boolToInt($ptratamiento_comision));
		}
		
		$builder->pie = ") AS EXPE_COUNT_RESULT"; // cierro la subconsulta

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBReportes: Obtiene un array de filas correspondientes a la clase Expediente en base a un determinado antecedente.
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
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerExpedientesPorAntecedente(
		// Parametros
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
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
		    E.`anio`,
			E.`tipo`,
			E.`numero`,
			E.`cuerpo`,
			E.`alcance`,
			E.`iniciador_tipo`,
			E.`iniciador_codigo`,
			E.`iniciador_bloque_tipo`,
			E.`iniciador_bloque_codigo`,
			E.`agregado_anio`,
			E.`agregado_tipo`,
			E.`agregado_numero`,
			E.`agregado_cuerpo`,
			E.`agregado_alcance`,
			E.`id_codcategoria`,
			E.`fecha_entrada_expe`,
			E.`caratula`,
			E.`observaciones_expe`,
			E.`marca_comision`,
			E.`id_usuario`,
			CAT.`descripcion_categoria` as ro_descripcion_categoria,
			LUG_A.`descripcion_grp` as ro_iniciador_descripcion_grp,
			LUG_B.`descripcion_grp` as ro_iniciador_bloque_descripcion_grp,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario
		FROM
			`{$this->t_expe_expedientes}` as E
		INNER JOIN
			(	-- Esta subconsulta obtiene las claves agrupadas del criterio complejo de busqueda,
				-- las cuales mejoran el tiempo de respuesta del agrupamiento al ser solo columnas pertenecientes
				-- a la clave primaria.
				SELECT DISTINCT
					A.`anio`,
					A.`tipo`,
					A.`numero`,
					A.`cuerpo`,
					A.`alcance`
				FROM
					`{$this->t_expe_antecedentes}` as A
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.anio_a', IGUAL_A, $panio_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'A.tipo_a', IGUAL_A, $ptipo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'A.numero_a', IGUAL_A, $pnumero_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'A.digito_a', IGUAL_A, $pdigito_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.cuerpo_a', IGUAL_A, $pcuerpo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.alcance_a', IGUAL_A, $palcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.cuerpoalcance_a', IGUAL_A, $pcuerpoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.anexoalcance_a', IGUAL_A, $panexoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.cuerpoanexoalcance_a', IGUAL_A, $pcuerpoanexoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.anexo_a', IGUAL_A, $panexo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.cuerpoanexo_a', IGUAL_A, $pcuerpoanexo_a);

		// Agrego el pie de la consulta
		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->pie = <<<SQL
		) AS ANT ON
		    (E.`anio` = ANT.`anio` AND E.`tipo` = ANT.`tipo` AND E.`numero` = ANT.`numero` AND E.`cuerpo` = ANT.`cuerpo` AND E.`alcance` = ANT.`alcance`)

		LEFT JOIN `{$this->t_expe_codcategoria}` as CAT ON
			(E.`id_codcategoria` = CAT.`id_codcategoria`)

		LEFT JOIN `{$this->t_expe_lugares}` as LUG_A ON
			(E.`iniciador_tipo` = LUG_A.`tipo_grp` AND E.`iniciador_codigo` = LUG_A.`codigo_grp`)

		LEFT JOIN `{$this->t_expe_lugares}` as LUG_B ON
			(E.`iniciador_bloque_tipo` = LUG_B.`tipo_grp` AND E.`iniciador_bloque_codigo` = LUG_B.`codigo_grp`)

		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(E.`id_usuario` = USR.`id_usuario`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBReportes: Obtiene la cantidad de filas correspondientes de la clase Expediente en base a un determinado antecedente.
	 * GenerateClass 0.95.8 beta @ 2016-08-22 11:48:56
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
	 * @return int
	 */
	public function obtenerExpedientesPorAntecedenteCantidad(
		// Parametros
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
		$pcuerpoanexo_a = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
		    count(*) as cantidad
		FROM
			`{$this->t_expe_expedientes}` as E
		INNER JOIN
			(	-- Esta subconsulta obtiene las claves agrupadas del criterio complejo de busqueda,
				-- las cuales mejoran el tiempo de respuesta del agrupamiento al ser solo columnas pertenecientes
				-- a la clave primaria.
				SELECT DISTINCT
					A.`anio`,
					A.`tipo`,
					A.`numero`,
					A.`cuerpo`,
					A.`alcance`
				FROM
					`{$this->t_expe_antecedentes}` as A
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.anio_a', IGUAL_A, $panio_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'A.tipo_a', IGUAL_A, $ptipo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'A.numero_a', IGUAL_A, $pnumero_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'A.digito_a', IGUAL_A, $pdigito_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.cuerpo_a', IGUAL_A, $pcuerpo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.alcance_a', IGUAL_A, $palcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.cuerpoalcance_a', IGUAL_A, $pcuerpoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.anexoalcance_a', IGUAL_A, $panexoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.cuerpoanexoalcance_a', IGUAL_A, $pcuerpoanexoalcance_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.anexo_a', IGUAL_A, $panexo_a);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'A.cuerpoanexo_a', IGUAL_A, $pcuerpoanexo_a);

		// Agrego el pie de la consulta
		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->pie = <<<SQL
		) AS ANT ON
		    (E.`anio` = ANT.`anio` AND E.`tipo` = ANT.`tipo` AND E.`numero` = ANT.`numero` AND E.`cuerpo` = ANT.`cuerpo` AND E.`alcance` = ANT.`alcance`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBReportes: Obtiene un array de filas correspondientes a la clase Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pfecha_comision
	 * @param  string     $pcomision_codigo
	 * @param  integer    $pid_codestado
	 * @param  array 	  $pcomisiones_elegidas_en_modal
	 * @param  bool       $ptratamiento_comision
	 * @param  integer 	  $pvencidos				Sólo vencidos, cantidad de días en Comisión > 120 días
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerExpedientesEnComision(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pfecha_comision = null,
		$pcomision_codigo = null,
		$pid_codestado = null,
		$pcomisiones_elegidas_en_modal = null,
		$ptratamiento_comision = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
		    E.`anio`,
			E.`tipo`,
			E.`numero`,
			E.`cuerpo`,
			E.`alcance`,
			E.`iniciador_tipo`,
			E.`iniciador_codigo`,
			E.`iniciador_bloque_tipo`,
			E.`iniciador_bloque_codigo`,
			E.`agregado_anio`,
			E.`agregado_tipo`,
			E.`agregado_numero`,
			E.`agregado_cuerpo`,
			E.`agregado_alcance`,
			E.`id_codcategoria`,
			E.`fecha_entrada_expe`,
			E.`caratula`,
			E.`observaciones_expe`,
			E.`marca_comision`,
			E.`id_usuario`,
			CAT.`descripcion_categoria` as ro_descripcion_categoria,
			LUG_A.`descripcion_grp` as ro_iniciador_descripcion_grp,
			LUG_B.`descripcion_grp` as ro_iniciador_bloque_descripcion_grp,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario
		FROM
			`{$this->t_expe_expedientes}` as E
		INNER JOIN
			(	-- Esta subconsulta obtiene las claves agrupadas del criterio complejo de busqueda,
				-- las cuales mejoran el tiempo de respuesta del agrupamiento al ser solo columnas pertenecientes
				-- a la clave primaria.
				SELECT DISTINCT
					EX.`anio`,
					EX.`tipo`,
					EX.`numero`,
					EX.`cuerpo`,
					EX.`alcance`
				FROM
					`{$this->t_expe_expedientes}` as EX

				-- Caratula o Descripcion de proyecto
				LEFT JOIN `{$this->t_expe_proyectos}` as PROY ON
					(EX.`anio` = PROY.`anio` AND EX.`tipo` = PROY.`tipo` AND EX.`numero` = PROY.`numero` AND EX.`cuerpo` = PROY.`cuerpo` AND EX.`alcance` = PROY.`alcance`)

				-- Temas
				LEFT JOIN `{$this->t_expe_temas}` as TEMAS ON
					(EX.`anio` = TEMAS.`anio` AND EX.`tipo` = TEMAS.`tipo` AND EX.`numero` = TEMAS.`numero` AND EX.`cuerpo` = TEMAS.`cuerpo` AND EX.`alcance` = TEMAS.`alcance`)

				-- Autores
				LEFT JOIN `{$this->t_expe_autores}` as AUT ON
					(EX.`anio` = AUT.`anio` AND EX.`tipo` = AUT.`tipo` AND EX.`numero` = AUT.`numero` AND EX.`cuerpo` = AUT.`cuerpo` AND EX.`alcance` = AUT.`alcance`)

				-- Ultimos estados de cada expediente
				LEFT JOIN
					(
						SELECT
							CAB_E.anio, CAB_E.tipo, CAB_E.numero, CAB_E.cuerpo, CAB_E.alcance,
							CAB_E.id_codestado,
							-- CAB_E.fecha_estado,
							-- CAB_E.orden_estado,
							COD_EST.tratamiento_comision
						FROM `{$this->t_expe_estados}` CAB_E -- cabecera
						INNER JOIN (
							SELECT anio, tipo, numero, cuerpo, alcance, MAX(CONCAT(fecha_estado, LPAD(orden_estado, 3, '0'))) AS fecha_orden_estado
							FROM `{$this->t_expe_estados}`
							GROUP BY 1, 2, 3, 4, 5) AS UEAUX -- Ultimo estado de cada expediente
						ON 	(	CAB_E.anio = UEAUX.anio
							AND CAB_E.tipo = UEAUX.tipo
							AND CAB_E.numero = UEAUX.numero
							AND CAB_E.cuerpo = UEAUX.cuerpo
							AND CAB_E.alcance = UEAUX.alcance
							AND CONCAT(CAB_E.fecha_estado, LPAD(CAB_E.orden_estado, 3, '0')) = UEAUX.fecha_orden_estado)
						INNER JOIN `{$this->t_expe_codestados}` AS COD_EST  -- Join con Codificadora de estados para determinar tratamiento en comision
						ON (CAB_E.`id_codestado` = COD_EST.`id_codestado`)
					) as EST ON
					(EX.`anio` = EST.`anio` AND EX.`tipo` = EST.`tipo` AND EX.`numero` = EST.`numero` AND EX.`cuerpo` = EST.`cuerpo` AND EX.`alcance` = EST.`alcance`)

				-- Giros
				LEFT JOIN `{$this->t_expe_giros}` as GIROS ON
					(EX.`anio` = GIROS.`anio` AND EX.`tipo` = GIROS.`tipo` AND EX.`numero` = GIROS.`numero` AND EX.`cuerpo` = GIROS.`cuerpo` AND EX.`alcance` = GIROS.`alcance`)

				-- Sanciones
				LEFT JOIN `{$this->t_expe_sanciones}` as SANC ON
					(EX.`anio` = SANC.`anio` AND EX.`tipo` = SANC.`tipo` AND EX.`numero` = SANC.`numero` AND EX.`cuerpo` = SANC.`cuerpo` AND EX.`alcance` = SANC.`alcance`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------

		//*** Configuramos los criterios del WHERE ***

		// Si se reciben las fechas Desde y Hasta, busca por: fecha_desde <= fecha_entrada_expe <= fecha_hasta.
		if (!is_null($pfecha_desde) && !is_null($pfecha_hasta)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MAYOR_IGUAL_A, $pfecha_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MENOR_IGUAL_A, $pfecha_hasta);
		}

		// se agrega un filtro extra porque este criterio trabaja en conjunto con un criterio constante
		if (!is_null($pcomision_codigo)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.comision_codigo', IGUAL_A, $pcomision_codigo);
		}

		// Si se recibe la fecha de Comisión
		if (!is_null($pfecha_comision)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.fecha_entrada_giro', MENOR_IGUAL_A, $pfecha_comision);
		}

		// Si se recibe un Estado
		if (!is_null($pid_codestado)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.id_codestado', IGUAL_A, $pid_codestado);
		}

		// Si se recibe un array de códigos de Comisiones
		if (!is_null($pcomisiones_elegidas_en_modal)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioMultiple(P_TEXT, 'GIROS.comision_codigo', $pcomisiones_elegidas_en_modal);
		}

		// Si el expediente se trata en una Comisión
		if (!empty($ptratamiento_comision) && !is_null($ptratamiento_comision)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.tratamiento_comision', IGUAL_A, $this->boolToInt($ptratamiento_comision));
		}

		// Agrego el pie de la consulta
		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->pie = <<<SQL
		) AS EXPED_FILTRO ON
		    (E.`anio` = EXPED_FILTRO.`anio` AND E.`tipo` = EXPED_FILTRO.`tipo` AND E.`numero` = EXPED_FILTRO.`numero` AND E.`cuerpo` = EXPED_FILTRO.`cuerpo` AND E.`alcance` = EXPED_FILTRO.`alcance`)

		LEFT JOIN `{$this->t_expe_codcategoria}` as CAT ON
			(E.`id_codcategoria` = CAT.`id_codcategoria`)

		LEFT JOIN `{$this->t_expe_lugares}` as LUG_A ON
			(E.`iniciador_tipo` = LUG_A.`tipo_grp` AND E.`iniciador_codigo` = LUG_A.`codigo_grp`)

		LEFT JOIN `{$this->t_expe_lugares}` as LUG_B ON
			(E.`iniciador_bloque_tipo` = LUG_B.`tipo_grp` AND E.`iniciador_bloque_codigo` = LUG_B.`codigo_grp`)

		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(E.`id_usuario` = USR.`id_usuario`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBReportes: Obtiene la cantidad de filas correspondientes a la clase Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pfecha_comision
	 * @param  string     $pcomision_codigo
	 * @param  integer    $pid_codestado
	 * @param  array 	  $pcomisiones_elegidas_en_modal
	 * @param  bool       $ptratamiento_comision
	 * @return integer
	 */
	public function obtenerExpedientesEnComisionCantidad(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pfecha_comision = null,
		$pcomision_codigo = null,
		$pid_codestado = null,
		$pcomisiones_elegidas_en_modal = null,
		$ptratamiento_comision = null/* boolean */) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM (
			SELECT DISTINCT
				EX.`anio`,
				EX.`tipo`,
				EX.`numero`,
				EX.`cuerpo`,
				EX.`alcance`
			FROM
				`{$this->t_expe_expedientes}` as EX

			LEFT JOIN `{$this->t_expe_proyectos}` as PROY ON
				(EX.`anio` = PROY.`anio` AND EX.`tipo` = PROY.`tipo` AND EX.`numero` = PROY.`numero` AND EX.`cuerpo` = PROY.`cuerpo` AND EX.`alcance` = PROY.`alcance`)

			LEFT JOIN `{$this->t_expe_temas}` as TEMAS ON
				(EX.`anio` = TEMAS.`anio` AND EX.`tipo` = TEMAS.`tipo` AND EX.`numero` = TEMAS.`numero` AND EX.`cuerpo` = TEMAS.`cuerpo` AND EX.`alcance` = TEMAS.`alcance`)

			LEFT JOIN `{$this->t_expe_autores}` as AUT ON
				(EX.`anio` = AUT.`anio` AND EX.`tipo` = AUT.`tipo` AND EX.`numero` = AUT.`numero` AND EX.`cuerpo` = AUT.`cuerpo` AND EX.`alcance` = AUT.`alcance`)

			-- Ultimos estados de cada expediente
			LEFT JOIN
				(
					SELECT
						CAB_E.anio, CAB_E.tipo, CAB_E.numero, CAB_E.cuerpo, CAB_E.alcance,
			            CAB_E.id_codestado,
			            COD_EST.tratamiento_comision
					FROM `{$this->t_expe_estados}` CAB_E -- cabecera
					INNER JOIN (
						SELECT anio, tipo, numero, cuerpo, alcance, MAX(CONCAT(fecha_estado, LPAD(orden_estado, 3, '0'))) AS fecha_orden_estado
						FROM `{$this->t_expe_estados}`
						GROUP BY 1, 2, 3, 4, 5) AS UEAUX -- Ultimo estado de cada expediente
					ON 	(	CAB_E.anio = UEAUX.anio
						AND CAB_E.tipo = UEAUX.tipo
						AND CAB_E.numero = UEAUX.numero
						AND CAB_E.cuerpo = UEAUX.cuerpo
						AND CAB_E.alcance = UEAUX.alcance
						AND CONCAT(CAB_E.fecha_estado, LPAD(CAB_E.orden_estado, 3, '0')) = UEAUX.fecha_orden_estado)
					INNER JOIN `{$this->t_expe_codestados}` AS COD_EST  -- Join con Codificadora de estados para determinar tratamiento en comision
			        ON (CAB_E.`id_codestado` = COD_EST.`id_codestado`)
			    ) as EST ON
				(EX.`anio` = EST.`anio` AND EX.`tipo` = EST.`tipo` AND EX.`numero` = EST.`numero` AND EX.`cuerpo` = EST.`cuerpo` AND EX.`alcance` = EST.`alcance`)

			-- Giros
			LEFT JOIN `{$this->t_expe_giros}` as GIROS ON
				(EX.`anio` = GIROS.`anio` AND EX.`tipo` = GIROS.`tipo` AND EX.`numero` = GIROS.`numero` AND EX.`cuerpo` = GIROS.`cuerpo` AND EX.`alcance` = GIROS.`alcance`)

			-- Sanciones
			LEFT JOIN `{$this->t_expe_sanciones}` as SANC ON
				(EX.`anio` = SANC.`anio` AND EX.`tipo` = SANC.`tipo` AND EX.`numero` = SANC.`numero` AND EX.`cuerpo` = SANC.`cuerpo` AND EX.`alcance` = SANC.`alcance`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE

		// Si se reciben las fechas Desde y Hasta, busca por: fecha_desde <= fecha_entrada_expe <= fecha_hasta.
		if (!is_null($pfecha_desde) && !is_null($pfecha_hasta)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MAYOR_IGUAL_A, $pfecha_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MENOR_IGUAL_A, $pfecha_hasta);
		}

		// se agrega un filtro extra porque este criterio trabaja en conjunto con un criterio constante
		if (!is_null($pcomision_codigo)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.comision_codigo', IGUAL_A, $pcomision_codigo);
		}

		// Si se recibe la fecha de Comisión
		if (!is_null($pfecha_comision)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.fecha_entrada_giro', MENOR_IGUAL_A, $pfecha_comision);
		}

		// Si se recibe un Estado
		if (!is_null($pid_codestado)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.id_codestado', IGUAL_A, $pid_codestado);
		}

		// Si se recibe un array de códigos de Comisiones
		if (!is_null($pcomisiones_elegidas_en_modal)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioMultiple(P_TEXT, 'GIROS.comision_codigo', $pcomisiones_elegidas_en_modal);
		}

		// Si el expediente se trata en una Comisión
		if (!empty($ptratamiento_comision) && !is_null($ptratamiento_comision)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.tratamiento_comision', IGUAL_A, $this->boolToInt($ptratamiento_comision));
		}

		$builder->pie = ") AS EXPE_COUNT_RESULT"; // cierro la subconsulta

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBReportes: Obtiene un array de filas correspondientes a la clase Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pcomision_codigo
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerOrdenesDelDia(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pcomision_codigo = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
		    E.`anio`,
			E.`tipo`,
			E.`numero`,
			E.`cuerpo`,
			E.`alcance`,
			E.`iniciador_tipo`,
			E.`iniciador_codigo`,
			E.`iniciador_bloque_tipo`,
			E.`iniciador_bloque_codigo`,
			E.`agregado_anio`,
			E.`agregado_tipo`,
			E.`agregado_numero`,
			E.`agregado_cuerpo`,
			E.`agregado_alcance`,
			E.`id_codcategoria`,
			E.`fecha_entrada_expe`,
			E.`caratula`,
			E.`observaciones_expe`,
			E.`marca_comision`,
			E.`id_usuario`,
			CAT.`descripcion_categoria` as ro_descripcion_categoria,
			LUG_A.`descripcion_grp` as ro_iniciador_descripcion_grp,
			LUG_B.`descripcion_grp` as ro_iniciador_bloque_descripcion_grp,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario
		FROM
			`{$this->t_expe_expedientes}` as E
		INNER JOIN
			(	-- Esta subconsulta obtiene las claves agrupadas del criterio complejo de busqueda,
				-- las cuales mejoran el tiempo de respuesta del agrupamiento al ser solo columnas pertenecientes
				-- a la clave primaria.
				SELECT DISTINCT
					EX.`anio`,
					EX.`tipo`,
					EX.`numero`,
					EX.`cuerpo`,
					EX.`alcance`

				FROM
					`{$this->t_expe_expedientes}` as EX

				-- Caratula o Descripcion de proyecto
				LEFT JOIN `{$this->t_expe_proyectos}` as PROY ON
					(EX.`anio` = PROY.`anio` AND EX.`tipo` = PROY.`tipo` AND EX.`numero` = PROY.`numero` AND EX.`cuerpo` = PROY.`cuerpo` AND EX.`alcance` = PROY.`alcance`)

				-- Temas
				LEFT JOIN `{$this->t_expe_temas}` as TEMAS ON
					(EX.`anio` = TEMAS.`anio` AND EX.`tipo` = TEMAS.`tipo` AND EX.`numero` = TEMAS.`numero` AND EX.`cuerpo` = TEMAS.`cuerpo` AND EX.`alcance` = TEMAS.`alcance`)

				-- Autores
				LEFT JOIN `{$this->t_expe_autores}` as AUT ON
					(EX.`anio` = AUT.`anio` AND EX.`tipo` = AUT.`tipo` AND EX.`numero` = AUT.`numero` AND EX.`cuerpo` = AUT.`cuerpo` AND EX.`alcance` = AUT.`alcance`)

				-- Ultimos estados de cada expediente
				LEFT JOIN
					(
						SELECT
							CAB_E.anio, CAB_E.tipo, CAB_E.numero, CAB_E.cuerpo, CAB_E.alcance,
							CAB_E.id_codestado,
							-- CAB_E.fecha_estado,
							-- CAB_E.orden_estado,
							COD_EST.tratamiento_comision
						FROM `{$this->t_expe_estados}` CAB_E -- cabecera
						INNER JOIN (
							SELECT anio, tipo, numero, cuerpo, alcance, MAX(CONCAT(fecha_estado, LPAD(orden_estado, 3, '0'))) AS fecha_orden_estado
							FROM `{$this->t_expe_estados}`
							GROUP BY 1, 2, 3, 4, 5) AS UEAUX -- Ultimo estado de cada expediente
						ON 	(	CAB_E.anio = UEAUX.anio
							AND CAB_E.tipo = UEAUX.tipo
							AND CAB_E.numero = UEAUX.numero
							AND CAB_E.cuerpo = UEAUX.cuerpo
							AND CAB_E.alcance = UEAUX.alcance
							AND CONCAT(CAB_E.fecha_estado, LPAD(CAB_E.orden_estado, 3, '0')) = UEAUX.fecha_orden_estado)
						INNER JOIN `{$this->t_expe_codestados}` AS COD_EST  -- Join con Codificadora de estados para determinar tratamiento en comision
						ON (CAB_E.`id_codestado` = COD_EST.`id_codestado`)
					) as EST ON
					(EX.`anio` = EST.`anio` AND EX.`tipo` = EST.`tipo` AND EX.`numero` = EST.`numero` AND EX.`cuerpo` = EST.`cuerpo` AND EX.`alcance` = EST.`alcance`)

				-- Giros
				LEFT JOIN `{$this->t_expe_giros}` as GIROS ON
					(EX.`anio` = GIROS.`anio` AND EX.`tipo` = GIROS.`tipo` AND EX.`numero` = GIROS.`numero` AND EX.`cuerpo` = GIROS.`cuerpo` AND EX.`alcance` = GIROS.`alcance`)

				-- Sanciones
				LEFT JOIN `{$this->t_expe_sanciones}` as SANC ON
					(EX.`anio` = SANC.`anio` AND EX.`tipo` = SANC.`tipo` AND EX.`numero` = SANC.`numero` AND EX.`cuerpo` = SANC.`cuerpo` AND EX.`alcance` = SANC.`alcance`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------

		//*** Configuro los criterios del WHERE ***

		// Se buscan sólo los que posean una marca en comisión
		$builder->criteriosWhere->agregarCriterioConstante('EX.marca_comision <> 0');

		// Si se reciben las fechas Desde y Hasta, busca por: fecha_desde <= fecha_entrada_expe <= fecha_hasta.
		if (!is_null($pfecha_desde) && !is_null($pfecha_hasta)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MAYOR_IGUAL_A, $pfecha_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MENOR_IGUAL_A, $pfecha_hasta);
		}
		// se agrega un filtro extra porque este criterio trabaja en conjunto con un criterio constante
		if (!is_null($pcomision_codigo)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.comision_codigo', IGUAL_A, $pcomision_codigo);
		}

		$builder->criteriosWhere->agregarCriterioConstante('EST.tratamiento_comision = 1');

		// Agrego el pie de la consulta
		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->pie = <<<SQL
		) AS EXPED_FILTRO ON
		    (E.`anio` = EXPED_FILTRO.`anio` AND E.`tipo` = EXPED_FILTRO.`tipo` AND E.`numero` = EXPED_FILTRO.`numero` AND E.`cuerpo` = EXPED_FILTRO.`cuerpo` AND E.`alcance` = EXPED_FILTRO.`alcance`)

		LEFT JOIN `{$this->t_expe_codcategoria}` as CAT ON
			(E.`id_codcategoria` = CAT.`id_codcategoria`)

		LEFT JOIN `{$this->t_expe_lugares}` as LUG_A ON
			(E.`iniciador_tipo` = LUG_A.`tipo_grp` AND E.`iniciador_codigo` = LUG_A.`codigo_grp`)

		LEFT JOIN `{$this->t_expe_lugares}` as LUG_B ON
			(E.`iniciador_bloque_tipo` = LUG_B.`tipo_grp` AND E.`iniciador_bloque_codigo` = LUG_B.`codigo_grp`)

		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(E.`id_usuario` = USR.`id_usuario`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBReportes: Obtiene la cantidad de filas correspondientes a la clase Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pcomision_codigo
	 * @return integer
	 */
	public function obtenerOrdenesDelDiaCantidad(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pcomision_codigo = null,
		$ptratamiento_comision = null/* boolean */) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM (
			SELECT DISTINCT
				EX.`anio`,
				EX.`tipo`,
				EX.`numero`,
				EX.`cuerpo`,
				EX.`alcance`
			FROM
				`{$this->t_expe_expedientes}` as EX

			LEFT JOIN `{$this->t_expe_proyectos}` as PROY ON
				(EX.`anio` = PROY.`anio` AND EX.`tipo` = PROY.`tipo` AND EX.`numero` = PROY.`numero` AND EX.`cuerpo` = PROY.`cuerpo` AND EX.`alcance` = PROY.`alcance`)

			LEFT JOIN `{$this->t_expe_temas}` as TEMAS ON
				(EX.`anio` = TEMAS.`anio` AND EX.`tipo` = TEMAS.`tipo` AND EX.`numero` = TEMAS.`numero` AND EX.`cuerpo` = TEMAS.`cuerpo` AND EX.`alcance` = TEMAS.`alcance`)

			LEFT JOIN `{$this->t_expe_autores}` as AUT ON
				(EX.`anio` = AUT.`anio` AND EX.`tipo` = AUT.`tipo` AND EX.`numero` = AUT.`numero` AND EX.`cuerpo` = AUT.`cuerpo` AND EX.`alcance` = AUT.`alcance`)

			-- Ultimos estados de cada expediente
			LEFT JOIN
				(
					SELECT
						CAB_E.anio, CAB_E.tipo, CAB_E.numero, CAB_E.cuerpo, CAB_E.alcance,
			            CAB_E.id_codestado,
			            COD_EST.tratamiento_comision
					FROM `{$this->t_expe_estados}` CAB_E -- cabecera
					INNER JOIN (
						SELECT anio, tipo, numero, cuerpo, alcance, MAX(CONCAT(fecha_estado, LPAD(orden_estado, 3, '0'))) AS fecha_orden_estado
						FROM `{$this->t_expe_estados}`
						GROUP BY 1, 2, 3, 4, 5) AS UEAUX -- Ultimo estado de cada expediente
					ON 	(	CAB_E.anio = UEAUX.anio
						AND CAB_E.tipo = UEAUX.tipo
						AND CAB_E.numero = UEAUX.numero
						AND CAB_E.cuerpo = UEAUX.cuerpo
						AND CAB_E.alcance = UEAUX.alcance
						AND CONCAT(CAB_E.fecha_estado, LPAD(CAB_E.orden_estado, 3, '0')) = UEAUX.fecha_orden_estado)
					INNER JOIN `{$this->t_expe_codestados}` AS COD_EST  -- Join con Codificadora de estados para determinar tratamiento en comision
			        ON (CAB_E.`id_codestado` = COD_EST.`id_codestado`)
			    ) as EST ON
				(EX.`anio` = EST.`anio` AND EX.`tipo` = EST.`tipo` AND EX.`numero` = EST.`numero` AND EX.`cuerpo` = EST.`cuerpo` AND EX.`alcance` = EST.`alcance`)

			-- Giros
			LEFT JOIN `{$this->t_expe_giros}` as GIROS ON
				(EX.`anio` = GIROS.`anio` AND EX.`tipo` = GIROS.`tipo` AND EX.`numero` = GIROS.`numero` AND EX.`cuerpo` = GIROS.`cuerpo` AND EX.`alcance` = GIROS.`alcance`)

			-- Sanciones
			LEFT JOIN `{$this->t_expe_sanciones}` as SANC ON
				(EX.`anio` = SANC.`anio` AND EX.`tipo` = SANC.`tipo` AND EX.`numero` = SANC.`numero` AND EX.`cuerpo` = SANC.`cuerpo` AND EX.`alcance` = SANC.`alcance`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE

		// Se buscan sólo los que posean una marca en comisión
		$builder->criteriosWhere->agregarCriterioConstante('EX.marca_comision <> 0');

		// Si se reciben las fechas Desde y Hasta, busca por: fecha_desde <= fecha_entrada_expe <= fecha_hasta.
		if (!is_null($pfecha_desde) && !is_null($pfecha_hasta)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MAYOR_IGUAL_A, $pfecha_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MENOR_IGUAL_A, $pfecha_hasta);
		}
		// se agrega un filtro extra porque este criterio trabaja en conjunto con un criterio constante
		if (!is_null($pcomision_codigo)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.comision_codigo', IGUAL_A, $pcomision_codigo);
		}

		$builder->criteriosWhere->agregarCriterioConstante('EST.tratamiento_comision = 1');

		$builder->pie = ") AS EXPE_COUNT_RESULT"; // cierro la subconsulta

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * Se obtienen los Informes
	 * @param  [type]     $pfecha_desde     [description]
	 * @param  [type]     $pfecha_hasta     [description]
	 * @param  [type]     $pcomision_codigo [description]
	 * @param  array|null $pOrdenColumnas   [description]
	 * @param  [type]     $pLimiteCantidad  [description]
	 * @param  [type]     $pLimiteOffset    [description]
	 */
	public function obtenerInformes(
		// Parametros
		$pfecha_listado = null,
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pcomision_codigo = null,
		$psolo_vencidos = false,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

		// Para filtrar por Comisión
		$filtro_por_comision = ($pcomision_codigo != '') ? "AND G.`comision_codigo` = '" . $pcomision_codigo . "' -- en una Comisión determinada por su código" : "";

		// Para filtrar solo vencidos
		$filtro_vencidos = ($psolo_vencidos) ? "AND   DATEDIFF('" . $pfecha_listado . "', I.`fecha_pedido_informe`) > " . LIMITE_INFORMES_EXPED_VENCIDOS : '';

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		-- Devuelve los Informes de una Comisión determinada, en un rango de fechas específico
		SELECT
		    I.`anio`,
		    I.`tipo`,
		    I.`numero`,
		    I.`cuerpo`,
		    I.`alcance`,
		    I.`orden_giro`,
		    I.`orden_informe`,
		    I.`fecha_pedido_informe`,
		    I.`fecha_vuelta_informe`,
		    I.`detalle_informe`,
		    I.`observaciones_informe`,
		    I.`id_usuario`,
		    G.`comision_codigo` AS ro_codigo_comision,
		    LugParaComision.`descripcion_grp` AS ro_nombre_comision,
		    G.`fecha_entrada_giro` AS ro_fecha_comision,
		    LugParaIniciador.`descripcion_grp` AS ro_iniciador_descripcion_grp,
		    E.`caratula` AS ro_caratula,
		    E.`fecha_entrada_expe` AS ro_fecha_entrada_expe,
		    DATEDIFF('$pfecha_listado', I.`fecha_pedido_informe`) AS ro_cantidad_dias_del_informe
		FROM `expe_informes` AS I
		-- Giros
		INNER JOIN `expe_giros` G
		ON (I.`anio` = G.`anio` AND
		    I.`tipo` = G.`tipo` AND
		    I.`numero` = G.`numero` AND
		    I.`cuerpo` = G.`cuerpo` AND
		    I.`alcance` = G.`alcance` AND
		    I.`orden_giro` = G.`orden_giro`)
		-- Lugares, para el NOMBRE de la Comisión
		INNER JOIN `expe_lugares` AS LugParaComision
		ON (LugParaComision.`tipo_grp` = G.`comision_tipo` AND LugParaComision.`codigo_grp` = G.`comision_codigo`)
		-- Expedientes
		INNER JOIN `expe_expedientes` AS E
		ON (E.`anio` = I.`anio` AND
		    E.`tipo` = I.`tipo` AND
		    E.`numero` = I.`numero` AND
		    E.`cuerpo` = I.`cuerpo` AND
		    E.`alcance` = I.`alcance`)
		-- Lugares, para el NOMBRE del Iniciador
		INNER JOIN `expe_lugares` AS LugParaIniciador
		ON (LugParaIniciador.`tipo_grp` = E.`iniciador_tipo` AND LugParaIniciador.`codigo_grp` = E.`iniciador_codigo`)
		-- Donde exista la fecha de Entrada en la Comisión
		WHERE G.`fecha_entrada_giro` IS NOT NULL
		-- y sea nula la fecha de Salida de la Comisión
		AND   G.`fecha_salida_giro` IS NULL
		-- y la Fecha de Pedido se encuentre en el rango determinado
		AND   G.`fecha_entrada_giro` BETWEEN '$pfecha_desde' AND '$pfecha_hasta'
		-- y sea nula la fecha de vuelta del informe
		AND   I.`fecha_vuelta_informe` IS NULL
		$filtro_por_comision
		$filtro_vencidos
		-- ORDER BY I.`anio` desc, I.`tipo`, I.`numero` desc, I.`cuerpo`, I.`alcance`, I.`orden_informe`
		ORDER BY I.`anio`, I.`tipo`, I.`numero`, I.`cuerpo`, I.`alcance`, I.`orden_informe`
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		// Agrego el ORDER BY
		//$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBReportes: Obtiene la cantidad de filas correspondientes a la clase Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pcomision_codigo
	 * @return integer
	 */
	public function obtenerInformesCantidad(
		// Parametros
		$pfecha_listado = null,
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pcomision_codigo = null,
		$psolo_vencidos = false) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

		// Para filtrar por Comisión
		$filtro_por_comision = ($pcomision_codigo != '') ? "AND G.`comision_codigo` = '" . $pcomision_codigo . "' -- en una Comisión determinada por su código" : "";

		// Para filtrar solo vencidos
		$filtro_vencidos = ($psolo_vencidos) ? "AND   DATEDIFF('" . $pfecha_listado . "', I.`fecha_pedido_informe`) > " . LIMITE_INFORMES_EXPED_VENCIDOS : '';

		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		-- Devuelve la CANTIDAD de los Informes de una Comisión determinada, en un rango de fechas específico
		SELECT
			count(*) as cantidad
		FROM (
			-- Devuelve los Informes de una Comisión determinada
			SELECT
			    I.`anio`,
			    I.`tipo`,
			    I.`numero`,
			    I.`cuerpo`,
			    I.`alcance`,
			    I.`orden_giro`,
			    I.`orden_informe`,
			    I.`fecha_pedido_informe`,
			    I.`fecha_vuelta_informe`,
			    I.`detalle_informe`,
			    I.`observaciones_informe`,
			    I.`id_usuario`,
			    G.`comision_codigo` AS ro_codigo_comision,
			    LugParaComision.`descripcion_grp` AS ro_nombre_comision,
			    G.`fecha_entrada_giro` AS ro_fecha_comision,
			    LugParaIniciador.`descripcion_grp` AS ro_iniciador_descripcion_grp,
			    E.`caratula` AS ro_caratula,
			    E.`fecha_entrada_expe` AS ro_fecha_entrada_expe,
		    	DATEDIFF('$pfecha_listado', I.`fecha_pedido_informe`) AS ro_cantidad_dias_del_informe
			FROM `expe_informes` AS I
			-- Giros
			INNER JOIN `expe_giros` G
			ON (I.`anio` = G.`anio` AND
			    I.`tipo` = G.`tipo` AND
			    I.`numero` = G.`numero` AND
			    I.`cuerpo` = G.`cuerpo` AND
			    I.`alcance` = G.`alcance` AND
			    I.`orden_giro` = G.`orden_giro`)
			-- Lugares, para el NOMBRE de la Comisión
			INNER JOIN `expe_lugares` AS LugParaComision
			ON (LugParaComision.`tipo_grp` = G.`comision_tipo` AND LugParaComision.`codigo_grp` = G.`comision_codigo`)
			-- Expedientes
			INNER JOIN `expe_expedientes` AS E
			ON (E.`anio` = I.`anio` AND
			    E.`tipo` = I.`tipo` AND
			    E.`numero` = I.`numero` AND
			    E.`cuerpo` = I.`cuerpo` AND
			    E.`alcance` = I.`alcance`)
			-- Lugares, para el NOMBRE del Iniciador
			INNER JOIN `expe_lugares` AS LugParaIniciador
			ON (LugParaIniciador.`tipo_grp` = E.`iniciador_tipo` AND LugParaIniciador.`codigo_grp` = E.`iniciador_codigo`)
			-- Donde exista la fecha de Entrada en la Comisión
			WHERE G.`fecha_entrada_giro` IS NOT NULL
			-- y sea nula la fecha de Salida de la Comisión
			AND   G.`fecha_salida_giro` IS NULL
			-- y la Fecha de Pedido se encuentre en el rango determinado
			AND   G.`fecha_entrada_giro` BETWEEN '$pfecha_desde' AND '$pfecha_hasta'
			-- y sea nula la fecha de vuelta del informe
			AND   I.`fecha_vuelta_informe` IS NULL
			$filtro_por_comision
			$filtro_vencidos
		) AS EXPE_COUNT_RESULT
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * Se obtienen los Expedientes en Préstamo
	 * @param  [type]     $pfecha_desde     [description]
	 * @param  [type]     $pfecha_hasta     [description]
	 * @param  [type]     $pid_codestado [description]
	 * @param  array|null $pOrdenColumnas   [description]
	 * @param  [type]     $pLimiteCantidad  [description]
	 * @param  [type]     $pLimiteOffset    [description]
	 */
	public function obtenerExpedientesEnPrestamo(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pid_codestado = null,
		$pobservaciones_estado = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

		// Para filtrar por Estado
		$filtro_por_estado = "";
		if (!is_null($pid_codestado)) {
			$filtro_por_estado = "AND ( SELECT `id_codestado`
										FROM `expe_estados`
									    WHERE `anio` = E.`anio` AND `tipo` = E.`tipo` AND `numero` = E.`numero` AND `cuerpo` = E.`cuerpo` AND `alcance` = E.`alcance`
									    ORDER BY `anio` DESC, `tipo` DESC, `numero` DESC, `cuerpo` DESC, `alcance` DESC, `fecha_estado` DESC, `orden_estado` DESC
									    LIMIT 1
									  ) IN (" . $pid_codestado . ")";
		}

		// Para filtrar por Observaciones del Estado
		$filtro_por_observaciones_estado = "";
		if (!is_null($pobservaciones_estado)) {
			$filtro_por_observaciones_estado = "AND ( SELECT `observaciones_estado`
													  FROM `expe_estados`
												      WHERE `anio` = E.`anio` AND `tipo` = E.`tipo` AND `numero` = E.`numero` AND `cuerpo` = E.`cuerpo` AND `alcance` = E.`alcance`
												      ORDER BY `anio` DESC, `tipo` DESC, `numero` DESC, `cuerpo` DESC, `alcance` DESC, `fecha_estado` DESC, `orden_estado` DESC
												      LIMIT 1
												    ) LIKE '%" . $pobservaciones_estado . "%'";
		}
// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		-- Devuelve los Expedientes en Préstamo, en un rango de fechas específico
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
			   E.`marca_comision`,
			   (SELECT CCat.`descripcion_categoria` FROM `expe_codcategoria` CCat WHERE CCat.`id_codcategoria` = E.`id_codcategoria`) AS ro_descripcion_categoria,
	   		   (SELECT Ini.`descripcion_grp` FROM `expe_lugares` Ini WHERE Ini.`tipo_grp` = `iniciador_tipo` AND Ini.`codigo_grp` = `iniciador_codigo`) AS ro_iniciador_descripcion_grp
		FROM `expe_expedientes` AS E
		-- donde la Fecha de entrada se encuentre en el rango determinado
		WHERE E.`fecha_entrada_expe` BETWEEN '$pfecha_desde' AND '$pfecha_hasta'
		$filtro_por_estado
		$filtro_por_observaciones_estado
		ORDER BY E.`anio`, E.`tipo`, E.`numero`, E.`cuerpo`, E.`alcance`
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBReportes: Obtiene la cantidad de filas correspondientes a la clase Expediente en base a diferentes criterios de selección
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pid_codestado
	 * @return integer
	 */
	public function obtenerExpedientesEnPrestamoCantidad(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pid_codestado = null,
		$pobservaciones_estado = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

		// Para filtrar por Estado
		$filtro_por_estado = "";
		if (!is_null($pid_codestado)) {
			$filtro_por_estado = "AND ( SELECT `id_codestado` FROM `expe_estados`
									    WHERE `anio` = E.`anio` AND `tipo` = E.`tipo` AND `numero` = E.`numero` AND `cuerpo` = E.`cuerpo` AND `alcance` = E.`alcance`
									    ORDER BY `anio` DESC, `tipo` DESC, `numero` DESC, `cuerpo` DESC, `alcance` DESC, `fecha_estado` DESC, `orden_estado` DESC
									    LIMIT 1
									  ) IN (" . $pid_codestado . ")";
		}

		// Para filtrar por Observaciones del Estado
		$filtro_por_observaciones_estado = "";
		if (!is_null($pobservaciones_estado)) {
			$filtro_por_observaciones_estado = "AND ( SELECT `observaciones_estado`
													  FROM `expe_estados`
												      WHERE `anio` = E.`anio` AND `tipo` = E.`tipo` AND `numero` = E.`numero` AND `cuerpo` = E.`cuerpo` AND `alcance` = E.`alcance`
												      ORDER BY `anio` DESC, `tipo` DESC, `numero` DESC, `cuerpo` DESC, `alcance` DESC, `fecha_estado` DESC, `orden_estado` DESC
												      LIMIT 1
												    ) LIKE '%" . $pobservaciones_estado . "%'";
		}
		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		-- Devuelve la CANTIDAD de los Expedientes en Préstamo, en un rango de fechas específico
		SELECT
			count(*) as cantidad
		FROM (
			-- Devuelve los Expedientes en Préstamo, en un rango de fechas específico
			SELECT E.`anio`,
				   E.`tipo`,
				   E.`numero`,
				   E.`cuerpo`,
				   E.`alcance`,
				   E.`caratula`,
				   E.`fecha_entrada_expe`,
				   E.`id_codcategoria`,
				   E.`iniciador_tipo`,
				   E.`iniciador_codigo`
			FROM `expe_expedientes` AS E
			-- donde la Fecha de entrada (O DE PRESTAMO, VER LUEGO!!!!!!!) se encuentre en el rango determinado
			WHERE E.`fecha_entrada_expe` BETWEEN '$pfecha_desde' AND '$pfecha_hasta'
			$filtro_por_estado
			$filtro_por_observaciones_estado
		) AS EXPE_COUNT_RESULT
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * Se obtienen los Expedientes, en un rango de fechas específico
	 * @param  [type]     $pfecha_desde     [description]
	 * @param  [type]     $pfecha_hasta     [description]
	 */
	public function obtenerExpedientesSoloPorRangoFechas(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		-- Devuelve los Expedientes, en un rango de fechas específico
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
			   (SELECT CCat.`descripcion_categoria` FROM `expe_codcategoria` CCat WHERE CCat.`id_codcategoria` = E.`id_codcategoria`) AS ro_descripcion_categoria,
	   		   (SELECT Ini.`descripcion_grp` FROM `expe_lugares` Ini WHERE Ini.`tipo_grp` = `iniciador_tipo` AND Ini.`codigo_grp` = `iniciador_codigo`) AS ro_iniciador_descripcion_grp
		FROM `expe_expedientes` AS E
		-- donde la Fecha de entrada se encuentre en el rango determinado
		WHERE E.`fecha_entrada_expe` BETWEEN '$pfecha_desde' AND '$pfecha_hasta'
		ORDER BY E.`anio`, E.`tipo`, E.`numero`, E.`cuerpo`, E.`alcance`
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBReportes: Obtiene la cantidad de filas correspondientes a la clase Expediente en base a un rango de fechas específico
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @return integer
	 */
	public function obtenerExpedientesSoloPorRangoFechasCantidad(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		-- Devuelve la CANTIDAD de los Expedientes, en un rango de fechas específico
		SELECT
			count(*) as cantidad
		FROM (
			-- Devuelve los Expedientes, en un rango de fechas específico
			SELECT E.`anio`,
				   E.`tipo`,
				   E.`numero`,
				   E.`cuerpo`,
				   E.`alcance`,
				   E.`caratula`,
				   E.`fecha_entrada_expe`,
				   E.`id_codcategoria`,
				   E.`iniciador_tipo`,
				   E.`iniciador_codigo`
			FROM `expe_expedientes` AS E
			-- donde la Fecha de entrada se encuentre en el rango determinado
			WHERE E.`fecha_entrada_expe` BETWEEN '$pfecha_desde' AND '$pfecha_hasta'
		) AS EXPE_COUNT_RESULT
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBReportes: Obtiene un array de filas correspondientes a la clase Expediente en base a diferentes criterios de selección
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pfecha_comision
	 * @param  string     $pcomision_codigo
	 * @param  integer    $pid_codestado
	 * @param  array 	  $pcomisiones_elegidas_en_modal
	 * @param  bool       $ptratamiento_comision
	 * @param  integer 	  $pvencidos				Sólo vencidos, cantidad de días en Comisión > 120 días
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerSoloExpedientes(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pfecha_comision = null,
		$pcomision_codigo = null,
		$pid_codestado = null,
		$pcomisiones_elegidas_en_modal = null,
		$ptratamiento_comision = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
		    E.`anio`,
			E.`tipo`,
			E.`numero`,
			E.`cuerpo`,
			E.`alcance`,
			E.`iniciador_tipo`,
			E.`iniciador_codigo`,
			E.`iniciador_bloque_tipo`,
			E.`iniciador_bloque_codigo`,
			E.`agregado_anio`,
			E.`agregado_tipo`,
			E.`agregado_numero`,
			E.`agregado_cuerpo`,
			E.`agregado_alcance`,
			E.`id_codcategoria`,
			E.`fecha_entrada_expe`,
			E.`caratula`,
			E.`observaciones_expe`,
			E.`marca_comision`,
			E.`id_usuario`,
			CAT.`descripcion_categoria` as ro_descripcion_categoria,
			LUG_A.`descripcion_grp` as ro_iniciador_descripcion_grp,
			LUG_B.`descripcion_grp` as ro_iniciador_bloque_descripcion_grp,
			USR.`codigo_usuario` as ro_codigo_usuario,
			USR.`nombre_usuario` as ro_nombre_usuario
		FROM
			`{$this->t_expe_expedientes}` as E
		INNER JOIN
			(	-- Esta subconsulta obtiene las claves agrupadas del criterio complejo de busqueda,
				-- las cuales mejoran el tiempo de respuesta del agrupamiento al ser solo columnas pertenecientes
				-- a la clave primaria.
				SELECT DISTINCT
					EX.`anio`,
					EX.`tipo`,
					EX.`numero`,
					EX.`cuerpo`,
					EX.`alcance`
				FROM
					`{$this->t_expe_expedientes}` as EX

				-- Caratula o Descripcion de proyecto
				LEFT JOIN `{$this->t_expe_proyectos}` as PROY ON
					(EX.`anio` = PROY.`anio` AND EX.`tipo` = PROY.`tipo` AND EX.`numero` = PROY.`numero` AND EX.`cuerpo` = PROY.`cuerpo` AND EX.`alcance` = PROY.`alcance`)

				-- Temas
				LEFT JOIN `{$this->t_expe_temas}` as TEMAS ON
					(EX.`anio` = TEMAS.`anio` AND EX.`tipo` = TEMAS.`tipo` AND EX.`numero` = TEMAS.`numero` AND EX.`cuerpo` = TEMAS.`cuerpo` AND EX.`alcance` = TEMAS.`alcance`)

				-- Autores
				LEFT JOIN `{$this->t_expe_autores}` as AUT ON
					(EX.`anio` = AUT.`anio` AND EX.`tipo` = AUT.`tipo` AND EX.`numero` = AUT.`numero` AND EX.`cuerpo` = AUT.`cuerpo` AND EX.`alcance` = AUT.`alcance`)

				-- Ultimos estados de cada expediente
				LEFT JOIN
					(
						SELECT
							CAB_E.anio, CAB_E.tipo, CAB_E.numero, CAB_E.cuerpo, CAB_E.alcance,
							CAB_E.id_codestado,
							-- CAB_E.fecha_estado,
							-- CAB_E.orden_estado,
							COD_EST.tratamiento_comision
						FROM `{$this->t_expe_estados}` CAB_E -- cabecera
						INNER JOIN (
							SELECT anio, tipo, numero, cuerpo, alcance, MAX(CONCAT(fecha_estado, LPAD(orden_estado, 3, '0'))) AS fecha_orden_estado
							FROM `{$this->t_expe_estados}`
							GROUP BY 1, 2, 3, 4, 5) AS UEAUX -- Ultimo estado de cada expediente
						ON 	(	CAB_E.anio = UEAUX.anio
							AND CAB_E.tipo = UEAUX.tipo
							AND CAB_E.numero = UEAUX.numero
							AND CAB_E.cuerpo = UEAUX.cuerpo
							AND CAB_E.alcance = UEAUX.alcance
							AND CONCAT(CAB_E.fecha_estado, LPAD(CAB_E.orden_estado, 3, '0')) = UEAUX.fecha_orden_estado)
						INNER JOIN `{$this->t_expe_codestados}` AS COD_EST  -- Join con Codificadora de estados para determinar tratamiento en comision
						ON (CAB_E.`id_codestado` = COD_EST.`id_codestado`)
					) as EST ON
					(EX.`anio` = EST.`anio` AND EX.`tipo` = EST.`tipo` AND EX.`numero` = EST.`numero` AND EX.`cuerpo` = EST.`cuerpo` AND EX.`alcance` = EST.`alcance`)

				-- Giros
				LEFT JOIN `{$this->t_expe_giros}` as GIROS ON
					(EX.`anio` = GIROS.`anio` AND EX.`tipo` = GIROS.`tipo` AND EX.`numero` = GIROS.`numero` AND EX.`cuerpo` = GIROS.`cuerpo` AND EX.`alcance` = GIROS.`alcance`)

				-- Sanciones
				LEFT JOIN `{$this->t_expe_sanciones}` as SANC ON
					(EX.`anio` = SANC.`anio` AND EX.`tipo` = SANC.`tipo` AND EX.`numero` = SANC.`numero` AND EX.`cuerpo` = SANC.`cuerpo` AND EX.`alcance` = SANC.`alcance`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------

		//*** Configuramos los criterios del WHERE ***

		// Si se reciben las fechas Desde y Hasta, busca por: fecha_desde <= fecha_entrada_expe <= fecha_hasta.
		if (!is_null($pfecha_desde) && !is_null($pfecha_hasta)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MAYOR_IGUAL_A, $pfecha_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MENOR_IGUAL_A, $pfecha_hasta);
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EX.tipo', IGUAL_A, 'E');
		}

		// se agrega un filtro extra porque este criterio trabaja en conjunto con un criterio constante
		if (!is_null($pcomision_codigo)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.comision_codigo', IGUAL_A, $pcomision_codigo);
		}

		// Si se recibe la fecha de Comisión
		if (!is_null($pfecha_comision)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.fecha_entrada_giro', MENOR_IGUAL_A, $pfecha_comision);
		}

		// Si se recibe un Estado
		if (!is_null($pid_codestado)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.id_codestado', IGUAL_A, $pid_codestado);
		}

		// Si se recibe un array de códigos de Comisiones
		if (!is_null($pcomisiones_elegidas_en_modal)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioMultiple(P_TEXT, 'GIROS.comision_codigo', $pcomisiones_elegidas_en_modal);
		}

		// Si el expediente se trata en una Comisión
		if (!empty($ptratamiento_comision) && !is_null($ptratamiento_comision)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.tratamiento_comision', IGUAL_A, $this->boolToInt($ptratamiento_comision));
		}

		// Agrego el pie de la consulta
		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->pie = <<<SQL
		) AS EXPED_FILTRO ON
		    (E.`anio` = EXPED_FILTRO.`anio` AND E.`tipo` = EXPED_FILTRO.`tipo` AND E.`numero` = EXPED_FILTRO.`numero` AND E.`cuerpo` = EXPED_FILTRO.`cuerpo` AND E.`alcance` = EXPED_FILTRO.`alcance`)

		LEFT JOIN `{$this->t_expe_codcategoria}` as CAT ON
			(E.`id_codcategoria` = CAT.`id_codcategoria`)

		LEFT JOIN `{$this->t_expe_lugares}` as LUG_A ON
			(E.`iniciador_tipo` = LUG_A.`tipo_grp` AND E.`iniciador_codigo` = LUG_A.`codigo_grp`)

		LEFT JOIN `{$this->t_expe_lugares}` as LUG_B ON
			(E.`iniciador_bloque_tipo` = LUG_B.`tipo_grp` AND E.`iniciador_bloque_codigo` = LUG_B.`codigo_grp`)

		LEFT JOIN `{$this->t_admin_usuarios}` as USR ON
			(E.`id_usuario` = USR.`id_usuario`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBReportes: Obtiene la cantidad de filas correspondientes a la clase Expediente en base a diferentes criterios de selección,
	 * en una Comisión determinada.
	 * @param  string     $pfecha_desde
	 * @param  string     $pfecha_hasta
	 * @param  string     $pfecha_comision
	 * @param  string     $pcomision_codigo
	 * @param  integer    $pid_codestado
	 * @param  array 	  $pcomisiones_elegidas_en_modal
	 * @param  bool       $ptratamiento_comision
	 * @return integer
	 */
	public function obtenerCantidadSoloExpedientes(
		// Parametros
		$pfecha_desde = null,
		$pfecha_hasta = null,
		$pfecha_comision = null,
		$pcomision_codigo = null,
		$pid_codestado = null,
		$pcomisiones_elegidas_en_modal = null,
		$ptratamiento_comision = null/* boolean */) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

		// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM (
			SELECT DISTINCT
				EX.`anio`,
				EX.`tipo`,
				EX.`numero`,
				EX.`cuerpo`,
				EX.`alcance`
			FROM
				`{$this->t_expe_expedientes}` as EX

			LEFT JOIN `{$this->t_expe_proyectos}` as PROY ON
				(EX.`anio` = PROY.`anio` AND EX.`tipo` = PROY.`tipo` AND EX.`numero` = PROY.`numero` AND EX.`cuerpo` = PROY.`cuerpo` AND EX.`alcance` = PROY.`alcance`)

			LEFT JOIN `{$this->t_expe_temas}` as TEMAS ON
				(EX.`anio` = TEMAS.`anio` AND EX.`tipo` = TEMAS.`tipo` AND EX.`numero` = TEMAS.`numero` AND EX.`cuerpo` = TEMAS.`cuerpo` AND EX.`alcance` = TEMAS.`alcance`)

			LEFT JOIN `{$this->t_expe_autores}` as AUT ON
				(EX.`anio` = AUT.`anio` AND EX.`tipo` = AUT.`tipo` AND EX.`numero` = AUT.`numero` AND EX.`cuerpo` = AUT.`cuerpo` AND EX.`alcance` = AUT.`alcance`)

			-- Ultimos estados de cada expediente
			LEFT JOIN
				(
					SELECT
						CAB_E.anio, CAB_E.tipo, CAB_E.numero, CAB_E.cuerpo, CAB_E.alcance,
			            CAB_E.id_codestado,
			            COD_EST.tratamiento_comision
					FROM `{$this->t_expe_estados}` CAB_E -- cabecera
					INNER JOIN (
						SELECT anio, tipo, numero, cuerpo, alcance, MAX(CONCAT(fecha_estado, LPAD(orden_estado, 3, '0'))) AS fecha_orden_estado
						FROM `{$this->t_expe_estados}`
						GROUP BY 1, 2, 3, 4, 5) AS UEAUX -- Ultimo estado de cada expediente
					ON 	(	CAB_E.anio = UEAUX.anio
						AND CAB_E.tipo = UEAUX.tipo
						AND CAB_E.numero = UEAUX.numero
						AND CAB_E.cuerpo = UEAUX.cuerpo
						AND CAB_E.alcance = UEAUX.alcance
						AND CONCAT(CAB_E.fecha_estado, LPAD(CAB_E.orden_estado, 3, '0')) = UEAUX.fecha_orden_estado)
					INNER JOIN `{$this->t_expe_codestados}` AS COD_EST  -- Join con Codificadora de estados para determinar tratamiento en comision
			        ON (CAB_E.`id_codestado` = COD_EST.`id_codestado`)
			    ) as EST ON
				(EX.`anio` = EST.`anio` AND EX.`tipo` = EST.`tipo` AND EX.`numero` = EST.`numero` AND EX.`cuerpo` = EST.`cuerpo` AND EX.`alcance` = EST.`alcance`)

			-- Giros
			LEFT JOIN `{$this->t_expe_giros}` as GIROS ON
				(EX.`anio` = GIROS.`anio` AND EX.`tipo` = GIROS.`tipo` AND EX.`numero` = GIROS.`numero` AND EX.`cuerpo` = GIROS.`cuerpo` AND EX.`alcance` = GIROS.`alcance`)

			-- Sanciones
			LEFT JOIN `{$this->t_expe_sanciones}` as SANC ON
				(EX.`anio` = SANC.`anio` AND EX.`tipo` = SANC.`tipo` AND EX.`numero` = SANC.`numero` AND EX.`cuerpo` = SANC.`cuerpo` AND EX.`alcance` = SANC.`alcance`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE

		// Si se reciben las fechas Desde y Hasta, busca por: fecha_desde <= fecha_entrada_expe <= fecha_hasta.
		if (!is_null($pfecha_desde) && !is_null($pfecha_hasta)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MAYOR_IGUAL_A, $pfecha_desde);
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'EX.fecha_entrada_expe', MENOR_IGUAL_A, $pfecha_hasta);
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EX.tipo', IGUAL_A, 'E');
		}

		// se agrega un filtro extra porque este criterio trabaja en conjunto con un criterio constante
		if (!is_null($pcomision_codigo)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.comision_codigo', IGUAL_A, $pcomision_codigo);
		}

		// Si se recibe la fecha de Comisión
		if (!is_null($pfecha_comision)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'GIROS.fecha_entrada_giro', MENOR_IGUAL_A, $pfecha_comision);
		}

		// Si se recibe un Estado
		if (!is_null($pid_codestado)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.id_codestado', IGUAL_A, $pid_codestado);
		}

		// Si se recibe un array de códigos de Comisiones
		if (!is_null($pcomisiones_elegidas_en_modal)) {
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_entrada_giro IS NOT NULL');
			$builder->criteriosWhere->agregarCriterioConstante('GIROS.fecha_salida_giro IS NULL');
			$builder->criteriosWhere->agregarCriterioMultiple(P_TEXT, 'GIROS.comision_codigo', $pcomisiones_elegidas_en_modal);
		}

		// Si el expediente se trata en una Comisión
		if (!empty($ptratamiento_comision) && !is_null($ptratamiento_comision)) {
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'EST.tratamiento_comision', IGUAL_A, $this->boolToInt($ptratamiento_comision));
		}

		$builder->pie = ") AS EXPE_COUNT_RESULT"; // cierro la subconsulta

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}
}
?>