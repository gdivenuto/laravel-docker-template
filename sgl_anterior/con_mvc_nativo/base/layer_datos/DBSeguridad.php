<?php
/**
 * Capa de acceso a datos (persistencia) específica de la gestion de Seguridad.
 */
class DBSeguridad extends DBBaseClass {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $t_admin_usuarios           = 'admin_usuarios'; //!< Identificador de tabla 'admin_usuarios'
	protected $t_admin_modulos            = 'admin_modulos'; //!< Identificador de tabla 'admin_modulos'
	protected $t_admin_usuarios_x_modulo  = 'admin_usuarios_x_modulo'; //!< Identificador de tabla 'admin_usuarios_x_modulo'
	protected $t_admin_perfiles_x_usuario = 'admin_perfiles'; //!< Identificador de tabla 'admin_perfiles'
	protected $t_admin_sistemas			  = 'admin_sistemas'; //!< Identificador de tabla 'admin_sistemas'
	protected $t_pers_personal            = 'pers_personal'; //!< Identificador de tabla 'pers_personal'
	protected $t_pers_cargos              = 'pers_cargos'; //!< Identificador de tabla 'pers_cargos'
	protected $t_pers_codcargos           = 'pers_codcargos'; //!< Identificador de tabla 'pers_codcargos'

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
	// Usuarios
	// ************************************************************************
	/**
	 * DBSeguridad: Obtiene un array de filas correspondientes a la clase Usuario en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:44
	 * @param  integer|array (PK) id_usuario
	 * @param  string codigo_usuario
	 * @param  string nombre_usuario
	 * @param  string iniciales_usuario
	 * @param  string password_usuario
	 * @param  string habilitado_usuario
	 * @param  string observaciones_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerUsuarios(
		// Parametros
		$pid_usuario = null,
		$pcodigo_usuario = null,
		$pnombre_usuario = null,
		$piniciales_usuario = null,
		$ppassword_usuario = null,
		$phabilitado_usuario = null,
		$pconfirma_giros = null,
		$pobservaciones_usuario = null,
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
			T.`id_usuario`,
			T.`codigo_usuario`,
			T.`nombre_usuario`,
			T.`iniciales_usuario`,
			T.`password_usuario`,
			T.`habilitado_usuario`,
			T.`confirma_giros`,
			T.`observaciones_usuario`,
			T.`u_legajo`,
			T.`u_mail`
		FROM
			`{$this->t_admin_usuarios}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		if ( !is_null($pid_usuario) && is_array($pid_usuario))
			$builder->criteriosWhere->agregarCriterioMultiple(P_INT, 'T.id_usuario', $pid_usuario);
		else
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.codigo_usuario', IGUAL_A, $pcodigo_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.nombre_usuario', IGUAL_A, $pnombre_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciales_usuario', IGUAL_A, $piniciales_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.password_usuario', IGUAL_A, $ppassword_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_usuario', IGUAL_A, $phabilitado_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.confirma_giros', IGUAL_A, $pconfirma_giros);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_usuario', IGUAL_A, $pobservaciones_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBSeguridad: Obtiene la cantidad de filas correspondientes de la clase Usuario en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:44
	 * @param  integer|array (PK) id_usuario
	 * @param  string codigo_usuario
	 * @param  string nombre_usuario
	 * @param  string iniciales_usuario
	 * @param  string password_usuario
	 * @param  string habilitado_usuario
	 * @param  string observaciones_usuario
	 * @return int
	 */
	public function obtenerUsuariosCantidad(
		// Parametros
		$pid_usuario = null,
		$pcodigo_usuario = null,
		$pnombre_usuario = null,
		$piniciales_usuario = null,
		$ppassword_usuario = null,
		$phabilitado_usuario = null,
		$pconfirma_giros = null,
		$pobservaciones_usuario = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_admin_usuarios}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		if ( !is_null($pid_usuario) && is_array($pid_usuario))
			$builder->criteriosWhere->agregarCriterioMultiple(P_INT, 'T.id_usuario', $pid_usuario);
		else
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.codigo_usuario', IGUAL_A, $pcodigo_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.nombre_usuario', IGUAL_A, $pnombre_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciales_usuario', IGUAL_A, $piniciales_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.password_usuario', IGUAL_A, $ppassword_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_usuario', IGUAL_A, $phabilitado_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.confirma_giros', IGUAL_A, $pconfirma_giros);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_usuario', IGUAL_A, $pobservaciones_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBSeguridad: Guarda una instancia de la clase Usuario en la base de datos.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:44
	 * @param  integer (PK) id_usuario
	 * @param  string codigo_usuario
	 * @param  string nombre_usuario
	 * @param  string iniciales_usuario
	 * @param  string password_usuario
	 * @param  string habilitado_usuario
	 * @param  string observaciones_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarUsuario(
		// Parametros
		$pid_usuario = null,
		$pcodigo_usuario = null,
		$pnombre_usuario = null,
		$piniciales_usuario = null,
		$ppassword_usuario = null,
		$phabilitado_usuario = null,
		$pobservaciones_usuario = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_admin_usuarios;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeoAutoincremental(P_INT, 'id_usuario', $pid_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'codigo_usuario', $pcodigo_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'nombre_usuario', $pnombre_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'iniciales_usuario', $piniciales_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'password_usuario', $ppassword_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'habilitado_usuario', $phabilitado_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_usuario', $pobservaciones_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBSeguridad: Elimina un conjunto de Usuarios en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:44
	 * @param  integer (PK) id_usuario
	 * @param  string codigo_usuario
	 * @param  string nombre_usuario
	 * @param  string iniciales_usuario
	 * @param  string password_usuario
	 * @param  string habilitado_usuario
	 * @param  string observaciones_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarUsuarios(
		// Parametros
		$pid_usuario = null,
		$pcodigo_usuario = null,
		$pnombre_usuario = null,
		$piniciales_usuario = null,
		$ppassword_usuario = null,
		$phabilitado_usuario = null,
		$pobservaciones_usuario = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($pid_usuario) && is_null($pcodigo_usuario) && is_null($pnombre_usuario) && is_null($piniciales_usuario) && is_null($ppassword_usuario) && is_null($phabilitado_usuario) && is_null($pobservaciones_usuario))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarUsuarios: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_admin_usuarios;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'codigo_usuario', IGUAL_A, $pcodigo_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'nombre_usuario', IGUAL_A, $pnombre_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'iniciales_usuario', IGUAL_A, $piniciales_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'password_usuario', IGUAL_A, $ppassword_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'habilitado_usuario', IGUAL_A, $phabilitado_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones_usuario', IGUAL_A, $pobservaciones_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}


	/**
	 * 2018/11/26 XXXX
	 * Se consulta a qué sistemas tiene acceso y el perfil para cada uno
	 * @param  [integer] $pid_usuario Id del usuario
	 */
	public function obtenerAccesosUsuario($pid_usuario = null) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			T.`id_sistema`,
			T.`id_usuario`,
			T.`perfil`
		FROM
			`{$this->t_admin_perfiles_x_usuario}` as T
		WHERE T.`id_usuario` = '$pid_usuario'
		AND T.`id_sistema` IN ( SELECT `id_sistema`
								FROM `{$this->t_admin_sistemas}`
								WHERE `habilitado_sistema` = 1
						  	  )
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * 2018/11/27 XXXX
	 * Se obtiene el perfil del usuario según el sistema que haya elegido
	 * @param integer $pid_sistema
	 * @param integer $pid_usuario
	 * @return integer Id del perfil de usuario
	 */
	public function obtenerPerfilSegunSistema($pid_sistema, $pid_usuario)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			T.`perfil`
		FROM
			`{$this->t_admin_perfiles_x_usuario}` as T
		WHERE T.`id_sistema` = '$pid_sistema'
		AND T.`id_usuario` = '$pid_usuario'
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * 2018/11/27 XXXX
	 * Se obtiene el nombre de un sistema determinado por su Id
	 * @param  [integer] $pid_sistema [description]
	 * @return [string]              [description]
	 */
	public function obtenerNombreSistema($pid_sistema)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			T.`nombre_sistema`
		FROM
			`{$this->t_admin_sistemas}` as T
		WHERE T.`id_sistema` = '$pid_sistema'
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	// ************************************************************************
	// Usuarios por Modulos
	// ************************************************************************
	/**
	 * DBSeguridad: Obtiene un array de filas correspondientes a la clase UsuarioModulo en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-21 11:25:02
	 * @param  integer (PK) id_usuario
	 * @param  string (PK) id_modulo
	 * @param  integer nivel
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerUsuarioModulos(
		// Parametros
		$pid_usuario = null,
		$pid_modulo = null,
		$pnivel = null,
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
			T.`id_usuario`,
			T.`id_modulo`,
			T.`nivel`
		FROM
			`{$this->t_admin_usuarios_x_modulo}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.id_modulo', IGUAL_A, $pid_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.nivel', IGUAL_A, $pnivel);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBSeguridad: Obtiene la cantidad de filas correspondientes de la clase UsuarioModulo en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-21 11:25:02
	 * @param  integer (PK) id_usuario
	 * @param  string (PK) id_modulo
	 * @param  integer nivel
	 * @return int
	 */
	public function obtenerUsuarioModulosCantidad(
		// Parametros
		$pid_usuario = null,
		$pid_modulo = null,
		$pnivel = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_admin_usuarios_x_modulo}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.id_modulo', IGUAL_A, $pid_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.nivel', IGUAL_A, $pnivel);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBSeguridad: Guarda una instancia de la clase UsuarioModulo en la base de datos.
	 * GenerateClass 0.97.5 beta @ 2016-09-21 11:25:02
	 * @param  integer (PK) id_usuario
	 * @param  string (PK) id_modulo
	 * @param  integer nivel
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarUsuarioModulo(
		// Parametros
		$pid_usuario = null,
		$pid_modulo = null,
		$pnivel = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_admin_usuarios_x_modulo;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'id_modulo', $pid_modulo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'nivel', $pnivel);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBSeguridad: Elimina un conjunto de UsuarioModulos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-21 11:25:02
	 * @param  integer (PK) id_usuario
	 * @param  string (PK) id_modulo
	 * @param  integer nivel
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarUsuarioModulos(
		// Parametros
		$pid_usuario = null,
		$pid_modulo = null,
		$pnivel = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($pid_usuario) && is_null($pid_modulo) && is_null($pnivel))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarUsuarioModulos: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_admin_usuarios_x_modulo;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'id_modulo', IGUAL_A, $pid_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'nivel', IGUAL_A, $pnivel);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Modulos
	// ************************************************************************

	/**
	 * DBSeguridad: Obtiene un array de filas correspondientes a la clase Modulo en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:47
	 * @param  string (PK) id_modulo
	 * @param  string nombre_modulo
	 * @param  string descripcion_modulo
	 * @param  bool activo
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerModulos(
		// Parametros
		$pid_modulo = null,
		$pnombre_modulo = null,
		$pdescripcion_modulo = null,
		$pactivo = null,
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
			T.`id_modulo`,
			T.`nombre_modulo`,
			T.`descripcion_modulo`,
			T.`activo`
		FROM
			`{$this->t_admin_modulos}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.id_modulo', IGUAL_A, $pid_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.nombre_modulo', IGUAL_A, $pnombre_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.descripcion_modulo', IGUAL_A, $pdescripcion_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.activo', IGUAL_A, $this->boolToInt($pactivo));

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBSeguridad: Obtiene la cantidad de filas correspondientes de la clase Modulo en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:47
	 * @param  string (PK) id_modulo
	 * @param  string nombre_modulo
	 * @param  string descripcion_modulo
	 * @param  bool activo
	 * @return int
	 */
	public function obtenerModulosCantidad(
		// Parametros
		$pid_modulo = null,
		$pnombre_modulo = null,
		$pdescripcion_modulo = null,
		$pactivo = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_admin_modulos}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.id_modulo', IGUAL_A, $pid_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.nombre_modulo', IGUAL_A, $pnombre_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.descripcion_modulo', IGUAL_A, $pdescripcion_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.activo', IGUAL_A, $this->boolToInt($pactivo));

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBSeguridad: Guarda una instancia de la clase Modulo en la base de datos.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:47
	 * @param  string (PK) id_modulo
	 * @param  string nombre_modulo
	 * @param  string descripcion_modulo
	 * @param  bool activo
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarModulo(
		// Parametros
		$pid_modulo = null,
		$pnombre_modulo = null,
		$pdescripcion_modulo = null,
		$pactivo = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_admin_modulos;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'id_modulo', $pid_modulo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'nombre_modulo', $pnombre_modulo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'descripcion_modulo', $pdescripcion_modulo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'activo', $this->boolToInt($pactivo));

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBSeguridad: Elimina un conjunto de Modulos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:47
	 * @param  string (PK) id_modulo
	 * @param  string nombre_modulo
	 * @param  string descripcion_modulo
	 * @param  bool activo
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarModulos(
		// Parametros
		$pid_modulo = null,
		$pnombre_modulo = null,
		$pdescripcion_modulo = null,
		$pactivo = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($pid_modulo) && is_null($pnombre_modulo) && is_null($pdescripcion_modulo) && is_null($pactivo))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarModulos: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_admin_modulos;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'id_modulo', IGUAL_A, $pid_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'nombre_modulo', IGUAL_A, $pnombre_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'descripcion_modulo', IGUAL_A, $pdescripcion_modulo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'activo', IGUAL_A, $this->boolToInt($pactivo));

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Usuarios Firmantes
	// ************************************************************************

	/**
	 * [obtenerUsuariosFirmantes description]
	 * @param  [type]     $pid_usuario            [description]
	 * @param  [type]     $pcodigo_usuario        [description]
	 * @param  [type]     $pnombre_usuario        [description]
	 * @param  [type]     $piniciales_usuario     [description]
	 * @param  [type]     $ppassword_usuario      [description]
	 * @param  [type]     $phabilitado_usuario    [description]
	 * @param  [type]     $pobservaciones_usuario [description]
	 * @param  [type]     $pu_legajo              [description]
	 * @param  array|null $pOrdenColumnas         [description]
	 * @param  [type]     $pLimiteCantidad        [description]
	 * @param  [type]     $pLimiteOffset          [description]
	 * @return [type]                             [description]
	 */
	public function obtenerUsuariosFirmantes(
		// Parametros
		$pid_usuario = null,
		$pcodigo_usuario = null,
		$pnombre_usuario = null,
		$piniciales_usuario = null,
		$ppassword_usuario = null,
		$phabilitado_usuario = null,
		$pconfirma_giros = null,
		$pobservaciones_usuario = null,
		$pu_legajo = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null
	) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			T.`id_usuario`,
			T.`codigo_usuario`,
			T.`nombre_usuario`,
			T.`iniciales_usuario`,
			T.`password_usuario`,
			T.`habilitado_usuario`,
			T.`confirma_giros`,
			T.`observaciones_usuario`,
			T.`u_legajo`,
			T.`u_mail`
		FROM
			`{$this->t_admin_usuarios}` as T
		INNER JOIN hcd.pers_personal AS P ON
			(T.`u_legajo` = P.`p_legajo`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		if ( !is_null($pid_usuario) && is_array($pid_usuario))
			$builder->criteriosWhere->agregarCriterioMultiple(P_INT, 'T.id_usuario', $pid_usuario);
		else
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		if ( !is_null($pu_legajo) && is_array($pu_legajo))
			$builder->criteriosWhere->agregarCriterioMultiple(P_INT, 'T.u_legajo', $pu_legajo);
		else
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.u_legajo', IGUAL_A, $pu_legajo);

		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.codigo_usuario', IGUAL_A, $pcodigo_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.nombre_usuario', IGUAL_A, $pnombre_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciales_usuario', IGUAL_A, $piniciales_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.password_usuario', IGUAL_A, $ppassword_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_usuario', IGUAL_A, $phabilitado_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.confirma_giros', IGUAL_A, $pconfirma_giros);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_usuario', IGUAL_A, $pobservaciones_usuario);

		// Configuro un criterio constante
// HereDoc Start -----------------------------------------------------------------------------------
		$criterio_cargo_activo = <<<SQL
			P.`p_legajo` IN (
				SELECT 	C.`c_legajo`
				FROM 	hcd.`pers_cargos` AS C
				WHERE
						C.`c_fecha_alta` <= CURDATE()
					AND (
							C.`c_fecha_alta` = (SELECT MAX(`c_fecha_alta` ) FROM `{$this->t_pers_cargos}` WHERE `c_legajo` = C.`c_legajo`)
						AND (C.`c_fecha_baja` IS NULL OR C.`c_fecha_baja` > CURDATE())
					)
				AND (C.`c_fecha_baja` >= CURDATE() OR C.`c_fecha_baja` IS NULL )
				AND C.`c_nomenclador` IN (SELECT `cc_nomenclador` FROM `{$this->t_pers_codcargos}`)
				AND (C.`c_fecha_baja` IS NULL OR C.`c_fecha_baja` > CURDATE())
			)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		$builder->criteriosWhere->agregarCriterioConstante($criterio_cargo_activo);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * [obtenerUsuariosFirmantesCantidad description]
	 * @param  [type] $pid_usuario            [description]
	 * @param  [type] $pcodigo_usuario        [description]
	 * @param  [type] $pnombre_usuario        [description]
	 * @param  [type] $piniciales_usuario     [description]
	 * @param  [type] $ppassword_usuario      [description]
	 * @param  [type] $phabilitado_usuario    [description]
	 * @param  [type] $pobservaciones_usuario [description]
	 * @param  [type] $pu_legajo              [description]
	 * @return [type]                         [description]
	 */
	public function obtenerUsuariosFirmantesCantidad(
		// Parametros
		$pid_usuario = null,
		$pcodigo_usuario = null,
		$pnombre_usuario = null,
		$piniciales_usuario = null,
		$ppassword_usuario = null,
		$phabilitado_usuario = null,
		$pconfirma_giros = null,
		$pobservaciones_usuario = null,
		$pu_legajo = null
	) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_admin_usuarios}` as T
		INNER JOIN hcd.pers_personal AS P ON
			(T.`u_legajo` = P.`p_legajo`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		if ( !is_null($pid_usuario) && is_array($pid_usuario))
			$builder->criteriosWhere->agregarCriterioMultiple(P_INT, 'T.id_usuario', $pid_usuario);
		else
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		if ( !is_null($pu_legajo) && is_array($pu_legajo))
			$builder->criteriosWhere->agregarCriterioMultiple(P_INT, 'T.u_legajo', $pu_legajo);
		else
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.u_legajo', IGUAL_A, $pu_legajo);

		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.codigo_usuario', IGUAL_A, $pcodigo_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.nombre_usuario', IGUAL_A, $pnombre_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciales_usuario', IGUAL_A, $piniciales_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.password_usuario', IGUAL_A, $ppassword_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_usuario', IGUAL_A, $phabilitado_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.confirma_giros', IGUAL_A, $pconfirma_giros);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_usuario', IGUAL_A, $pobservaciones_usuario);

		// Configuro un criterio constante
// HereDoc Start -----------------------------------------------------------------------------------
		$criterio_cargo_activo = <<<SQL
			P.`p_legajo` IN (
				SELECT 	C.`c_legajo`
				FROM 	hcd.`pers_cargos` AS C
				WHERE
						C.`c_fecha_alta` <= CURDATE()
					AND (
							C.`c_fecha_alta` = (SELECT MAX(`c_fecha_alta` ) FROM `{$this->t_pers_cargos}` WHERE `c_legajo` = C.`c_legajo`)
						AND (C.`c_fecha_baja` IS NULL OR C.`c_fecha_baja` > CURDATE())
					)
				AND (C.`c_fecha_baja` >= CURDATE() OR C.`c_fecha_baja` IS NULL )
				AND C.`c_nomenclador` IN (SELECT `cc_nomenclador` FROM `{$this->t_pers_codcargos}`)
				AND (C.`c_fecha_baja` IS NULL OR C.`c_fecha_baja` > CURDATE())
			)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		$builder->criteriosWhere->agregarCriterioConstante($criterio_cargo_activo);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	// ************************************************************************
	// E-Mails Notificables
	// ************************************************************************

	/**
	 * Devuelve una lista de direcciones de correo notificables del sistema
	 * a partir de la unión de resultados de la tabla de usuarios y personal.
	 * @return [type] [description]
	 */
	public function obtenerEMailsNotificables()
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			T.mail,
			MIN(T.nombre_completo) AS nombre_completo
		FROM (
			SELECT
				`u_mail` AS mail,
			    UPPER(nombre_usuario) AS nombre_completo
			FROM `{$this->t_admin_usuarios}`
			WHERE
				`u_mail` IS NOT NULL
			AND `habilitado_usuario` = 1

			UNION SELECT
					`p_mail` AS mail,
				    CONCAT(`p_apellido`, ', ', `p_nombre`) AS nombre_completo
				FROM `{$this->t_pers_personal}`
				WHERE
					`p_mail` IS NOT NULL
				AND `p_legajo` IN (
					SELECT 	C.`c_legajo`
					FROM 	hcd.`pers_cargos` AS C
					WHERE
							C.`c_fecha_alta` <= CURDATE()
						AND (
								C.`c_fecha_alta` = (SELECT MAX(`c_fecha_alta`) FROM `{$this->t_pers_cargos}` WHERE `c_legajo` = C.`c_legajo`)
							AND (C.`c_fecha_baja` IS NULL OR C.`c_fecha_baja` > CURDATE())
						)
					AND (C.`c_fecha_baja` >= CURDATE() OR C.`c_fecha_baja` IS NULL )
					AND C.`c_nomenclador` IN (SELECT `cc_nomenclador` FROM `{$this->t_pers_codcargos}`)
					AND (C.`c_fecha_baja` IS NULL OR C.`c_fecha_baja` > CURDATE())
				)
		) AS T
		GROUP BY 1
		ORDER BY 2
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	// ************************************************************************
	// Usuarios habilitados para Giros
	// ************************************************************************

	/**
	 * [obtenerUsuariosHabilitadosParaGiros description]
	 * @param  [type]     $pid_usuario            [description]
	 * @param  [type]     $pcodigo_usuario        [description]
	 * @param  [type]     $pnombre_usuario        [description]
	 * @param  [type]     $piniciales_usuario     [description]
	 * @param  [type]     $ppassword_usuario      [description]
	 * @param  [type]     $phabilitado_usuario    [description]
	 * @param  [type]     $pobservaciones_usuario [description]
	 * @param  [type]     $pu_legajo              [description]
	 * @param  array|null $pOrdenColumnas         [description]
	 * @param  [type]     $pLimiteCantidad        [description]
	 * @param  [type]     $pLimiteOffset          [description]
	 * @return [type]                             [description]
	 */
	public function obtenerUsuariosHabilitadosParaGiros(
		// Parametros
		$pid_usuario = null,
		$pcodigo_usuario = null,
		$pnombre_usuario = null,
		$piniciales_usuario = null,
		$ppassword_usuario = null,
		$phabilitado_usuario = null,
		$pconfirma_giros = null,
		$pobservaciones_usuario = null,
		$pu_legajo = null,
		// Control de consulta
		array $pOrdenColumnas = null,
		$pLimiteCantidad = null,
		$pLimiteOffset = null
	) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			T.`id_usuario`,
			T.`codigo_usuario`,
			T.`nombre_usuario`,
			T.`iniciales_usuario`,
			T.`password_usuario`,
			T.`habilitado_usuario`,
			T.`confirma_giros`,
			T.`observaciones_usuario`,
			T.`u_legajo`,
			T.`u_mail`
		FROM
			`{$this->t_admin_usuarios}` as T
		INNER JOIN hcd.pers_personal AS P ON
			(T.`u_legajo` = P.`p_legajo`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		if ( !is_null($pid_usuario) && is_array($pid_usuario))
			$builder->criteriosWhere->agregarCriterioMultiple(P_INT, 'T.id_usuario', $pid_usuario);
		else
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		if ( !is_null($pu_legajo) && is_array($pu_legajo))
			$builder->criteriosWhere->agregarCriterioMultiple(P_INT, 'T.u_legajo', $pu_legajo);
		else
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.u_legajo', IGUAL_A, $pu_legajo);

		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.codigo_usuario', IGUAL_A, $pcodigo_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.nombre_usuario', IGUAL_A, $pnombre_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciales_usuario', IGUAL_A, $piniciales_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.password_usuario', IGUAL_A, $ppassword_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_usuario', IGUAL_A, $phabilitado_usuario);
		//$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.confirma_giros', IGUAL_A, $pconfirma_giros);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_usuario', IGUAL_A, $pobservaciones_usuario);

		// Configuro un criterio constante
// HereDoc Start -----------------------------------------------------------------------------------
		$criterio_confirma_giros = <<<SQL
			(
				P.`p_legajo` IN (
					SELECT 	C.`c_legajo`
					FROM 	hcd.`pers_cargos` AS C
					WHERE
							C.`c_fecha_alta` <= CURDATE()
						AND (
								C.`c_fecha_alta` = (SELECT MAX(`c_fecha_alta` )
													FROM `{$this->t_pers_cargos}`
													WHERE `c_legajo` = C.`c_legajo`
												   )
							AND (C.`c_fecha_baja` IS NULL OR C.`c_fecha_baja` > CURDATE())
						)
					AND (C.`c_fecha_baja` >= CURDATE() OR C.`c_fecha_baja` IS NULL )
					AND C.`c_nomenclador` = '00659909'
					AND (C.`c_fecha_baja` IS NULL OR C.`c_fecha_baja` > CURDATE())
				)
				OR T.confirma_giros = 1
			)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		$builder->criteriosWhere->agregarCriterioConstante($criterio_confirma_giros);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * [obtenerUsuariosHabilitadosParaGirosCantidad description]
	 * @param  [type] $pid_usuario            [description]
	 * @param  [type] $pcodigo_usuario        [description]
	 * @param  [type] $pnombre_usuario        [description]
	 * @param  [type] $piniciales_usuario     [description]
	 * @param  [type] $ppassword_usuario      [description]
	 * @param  [type] $phabilitado_usuario    [description]
	 * @param  [type] $pobservaciones_usuario [description]
	 * @param  [type] $pu_legajo              [description]
	 * @return [type]                         [description]
	 */
	public function obtenerUsuariosHabilitadosParaGirosCantidad(
		// Parametros
		$pid_usuario = null,
		$pcodigo_usuario = null,
		$pnombre_usuario = null,
		$piniciales_usuario = null,
		$ppassword_usuario = null,
		$phabilitado_usuario = null,
		$pconfirma_giros = null,
		$pobservaciones_usuario = null,
		$pu_legajo = null
	) {
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_admin_usuarios}` as T
		INNER JOIN hcd.pers_personal AS P ON
			(T.`u_legajo` = P.`p_legajo`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		if ( !is_null($pid_usuario) && is_array($pid_usuario))
			$builder->criteriosWhere->agregarCriterioMultiple(P_INT, 'T.id_usuario', $pid_usuario);
		else
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		if ( !is_null($pu_legajo) && is_array($pu_legajo))
			$builder->criteriosWhere->agregarCriterioMultiple(P_INT, 'T.u_legajo', $pu_legajo);
		else
			$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.u_legajo', IGUAL_A, $pu_legajo);

		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.codigo_usuario', IGUAL_A, $pcodigo_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.nombre_usuario', IGUAL_A, $pnombre_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.iniciales_usuario', IGUAL_A, $piniciales_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.password_usuario', IGUAL_A, $ppassword_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_usuario', IGUAL_A, $phabilitado_usuario);
		//$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.confirma_giros', IGUAL_A, $pconfirma_giros);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_usuario', IGUAL_A, $pobservaciones_usuario);

		// Configuro un criterio constante
// HereDoc Start -----------------------------------------------------------------------------------
		$criterio_confirma_giros = <<<SQL
			(
				P.`p_legajo` IN (
					SELECT 	C.`c_legajo`
					FROM 	hcd.`pers_cargos` AS C
					WHERE
							C.`c_fecha_alta` <= CURDATE()
						AND (
								C.`c_fecha_alta` = (SELECT MAX(`c_fecha_alta` )
													FROM `{$this->t_pers_cargos}`
													WHERE `c_legajo` = C.`c_legajo`
												   )
							AND (C.`c_fecha_baja` IS NULL OR C.`c_fecha_baja` > CURDATE())
						)
					AND (C.`c_fecha_baja` >= CURDATE() OR C.`c_fecha_baja` IS NULL )
					AND C.`c_nomenclador` = '00659909'
					AND (C.`c_fecha_baja` IS NULL OR C.`c_fecha_baja` > CURDATE())
				)
				OR T.confirma_giros = 1
			)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		$builder->criteriosWhere->agregarCriterioConstante($criterio_confirma_giros);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

}
?>
