<?php
/**
 * Capa de negocio de Seguridad para Kraken.
 *
 * @author XXXX
 *
 */
class NGSeguridad extends NGBaseClass {

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

	// ************************************************************************
	// Usuarios
	// ************************************************************************
	/**
	 * NGSeguridad: Obtiene una coleccion de elementos tipo Usuario en base a diferentes criterios de selección.
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
	 * @return array<Usuario>
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
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBSeguridad()->obtenerUsuarios($pid_usuario, $pcodigo_usuario, $pnombre_usuario, $piniciales_usuario, $ppassword_usuario, $phabilitado_usuario, $pconfirma_giros, $pobservaciones_usuario,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUsuarios: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Usuario
		$resultado = $this->arrayResultToInstance($filas, 'Usuario');

		DB::getInstanceDBSeguridad()->desconectar();

		return $resultado;
	}

	/**
	 * NGSeguridad: Determina la cantidad de elementos tipo Usuario obtenidos en base a diferentes criterios de selección.
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
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBSeguridad()->obtenerUsuariosCantidad($pid_usuario, $pcodigo_usuario, $pnombre_usuario, $piniciales_usuario, $ppassword_usuario, $phabilitado_usuario, $pconfirma_giros, $pobservaciones_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUsuariosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGSeguridad: Obtiene una instancia de tipo Usuario en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:44
	 * @param  integer (PK) id_usuario
	 * @return Usuario Instancia de Usuario buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerUsuario(
		// Parametros
		$pid_usuario)
	{
		if (is_null($pid_usuario))
			throw new Exception(sprintf("Error en %s.obtenerUsuario: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerUsuarios($pid_usuario);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerUsuario: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Guarda una instancia de tipo Usuario. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:44
	 * @param  Usuario $pUsuario 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Usuario               Instancia guardada.
	 */
	public function guardarUsuario(Usuario $pUsuario, $pRecargar = true)
	{
		if (is_null($pUsuario))
			throw new Exception(sprintf("Error en %s.guardarUsuario: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBSeguridad()->conectar(false); // AutoCommit: false
		DB::getInstanceDBSeguridad()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$id = DB::getInstanceDBSeguridad()->guardarUsuario(
				$pUsuario->id_usuario,
				$pUsuario->codigo_usuario,
				$pUsuario->nombre_usuario,
				$pUsuario->iniciales_usuario,
				$pUsuario->password_usuario,
				$pUsuario->habilitado_usuario,
				$pUsuario->observaciones_usuario);

			DB::getInstanceDBSeguridad()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->cancelarTransaccion();
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarUsuario: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$pUsuario->id_usuario = $id; // Actualizo con el valor autogenerado.
			$resultado = $this->obtenerUsuario($pUsuario->id_usuario);
		}
		else
			$resultado = $pUsuario;

		DB::getInstanceDBSeguridad()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarUsuario: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGSeguridad: Elimina un conjunto de Usuarios en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:44
	 * @param  integer (PK) id_usuario
	 * @param  string codigo_usuario
	 * @param  string nombre_usuario
	 * @param  string iniciales_usuario
	 * @param  string password_usuario
	 * @param  string habilitado_usuario
	 * @param  string observaciones_usuario
	 * @return integer Cantidad de entidades afectadas.
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
		DB::getInstanceDBSeguridad()->conectar(false); // AutoCommit: false
		DB::getInstanceDBSeguridad()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBSeguridad()->eliminarUsuarios($pid_usuario, $pcodigo_usuario, $pnombre_usuario, $piniciales_usuario, $ppassword_usuario, $phabilitado_usuario, $pobservaciones_usuario);

			DB::getInstanceDBSeguridad()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->cancelarTransaccion();
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarUsuarios: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return $resultado;
	}

	/**
	 * NGSeguridad: Elimina una instancia de tipo Usuario en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:44
	 * @param  Usuario $pUsuario 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarUsuario(Usuario $pUsuario)
	{
		if (is_null($pUsuario))
			throw new Exception(sprintf("Error en %s.eliminarUsuario: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBSeguridad()->conectar(false); // AutoCommit: false
		DB::getInstanceDBSeguridad()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarUsuarios($pUsuario->id_usuario);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarUsuario: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBSeguridad()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->cancelarTransaccion();
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarUsuario: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return ($resultado == 1);
	}

	/**
	 * Valida las credenciales de un usuario.
	 * @param  string $pusuario  Identificador de usuario
	 * @param  string $ppassword Contraseña
	 * @return Usuario            Usuario validado, o null en caso de no poder validar el usuario.
	 */
	public function validarCredencialesUsuario($pcodigo_usuario, $ppassword_usuario)
	{
		//$resultado = $this->obtenerUsuarios(null, $pcodigo_usuario, null, null, md5(KRAKEN_MAGIC_WORD.$ppassword_usuario), '1');
		$resultado = $this->obtenerUsuarios(null, $pcodigo_usuario, null, null, md5($ppassword_usuario), '1');

		$usuario = null;
		if (count($resultado) == 1)
		{
			$usuario = $resultado[0];
		}
		else if (count($resultado) > 1)
		{
			//Lanzo una excepcion
			throw new RuntimeException(sprintf("Error grave en %s.validarCredencialesUsuario: se encontró mas de un usuario con las mismas credenciales.", get_class($this)));
		}

		return $usuario;
	}

	/**
	 * Verifica el nivel de acceso de un usuario con respecto a un determinado nivel requerido.
	 * @param  Usuario $pUsuario Instancia del usuario a validar
	 * @param  string $pnombreModulo Nombre del módulo contra el cual se desea validar el usuario.
	 * @param  int $nivelRequerido Nivel contra el cual se desea comparar los permisos del usuario.
	 * @return bool True si el usuario cumple con los requisitos, false en caso contrario.
	 */
	public function validarPermisosUsuario($pUsuario, $pnombreModulo, $nivelRequerido)
	{
		// Si es nulo el usuario, es inválido.
		if (is_null($pUsuario))
			return false;

		// Ahora verifico el modulo contra el usuario
		$permisoUsuario = $this->obtenerUsuarioModulo($pUsuario->id_usuario, $pnombreModulo);
		if (is_null($permisoUsuario))
			return false;

		return ($permisoUsuario->nivel >= $nivelRequerido);
	}

	/**
	 * Valida una instancia de usuario, en cuanto a sus credenciales.
	 * @param  Usuario $pUsuario Instancia del usuario a validar
	 * @return Usuario            Usuario validado, o null en caso de no poder validar el usuario.
	 */
	public function validarUsuario(Usuario $pUsuario)
	{
		// Si es nulo, es inválido.
		if (is_null($pUsuario))
			return null;

		//$resultado = $this->obtenerUsuarios(true, $pUsuario->usuario, $pUsuario->password);
		$resultado = $this->obtenerUsuarios($pUsuario->id_usuario, null, null, null, $pUsuario->password_usuario, '1');

		$usuario = null;
		if (count($resultado) == 1)
		{
			$usuario = $resultado[0];
		}
		else if (count($resultado) > 1)
		{
			//Lanzo una excepcion
			throw new RuntimeException(sprintf("Error grave en %s.validarUsuario: se encontró mas de un usuario con las mismas credenciales.", get_class($this)));
		}

		return $usuario;
	}

	/**
	 * Se consulta a qué sistemas tiene acceso y el perfil para cada uno
	 * @param  [integer] $pid_usuario Id del usuario
	 */
	public function obtenerAccesosUsuario($pid_usuario = null)
	{
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$accesos_usuario = DB::getInstanceDBSeguridad()->obtenerAccesosUsuario($pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerAccesosUsuario: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return $accesos_usuario;
	}

	/**
	 * Se obtiene el perfil según un sistema y usuario determinados
	 * @param integer $id_sistema
	 * @param integer $id_usuario
	 * @return integer Perfil
	 */
	public function obtenerPerfilSegunSistema($pid_sistema = null, $pid_usuario = null)
	{
		DB::getInstanceDBSeguridad()->conectar();

		try {
	    	$perfil = DB::getInstanceDBSeguridad()->obtenerPerfilSegunSistema($pid_sistema, $pid_usuario);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerPerfil: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

	    return $perfil;
	}

	/**
	 * Se obtiene el nombre de un sistema determinado por su Id
	 * @param  [integer] $pid_sistema [description]
	 * @return [string]              [description]
	 */
	public function obtenerNombreSistema($pid_sistema = null)
	{
		DB::getInstanceDBSeguridad()->conectar();

		try {
	    	$perfil = DB::getInstanceDBSeguridad()->obtenerNombreSistema($pid_sistema);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerNombreSistema: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

	    return $perfil;
	}

	// ************************************************************************
	// Usuarios por Modulos
	// ************************************************************************
	/**
	 * NGSeguridad: Obtiene una coleccion de elementos tipo UsuarioModulo en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-21 11:25:02
	 * @param  integer (PK) id_usuario
	 * @param  string (PK) id_modulo
	 * @param  integer nivel
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<UsuarioModulo>
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
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBSeguridad()->obtenerUsuarioModulos($pid_usuario, $pid_modulo, $pnivel,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUsuarioModulos: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo UsuarioModulo
		$resultado = $this->arrayResultToInstance($filas, 'UsuarioModulo');

		DB::getInstanceDBSeguridad()->desconectar();

		return $resultado;
	}

	/**
	 * NGSeguridad: Determina la cantidad de elementos tipo UsuarioModulo obtenidos en base a diferentes criterios de selección.
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
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBSeguridad()->obtenerUsuarioModulosCantidad($pid_usuario, $pid_modulo, $pnivel);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUsuarioModulosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGSeguridad: Obtiene una instancia de tipo UsuarioModulo en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.5 beta @ 2016-09-21 11:25:02
	 * @param  integer (PK) id_usuario
	 * @param  string (PK) id_modulo
	 * @return UsuarioModulo Instancia de UsuarioModulo buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerUsuarioModulo(
		// Parametros
		$pid_usuario, $pid_modulo)
	{
		if (is_null($pid_usuario) || is_null($pid_modulo))
			throw new Exception(sprintf("Error en %s.obtenerUsuarioModulo: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerUsuarioModulos($pid_usuario, $pid_modulo);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerUsuarioModulo: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Guarda una instancia de tipo UsuarioModulo. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.5 beta @ 2016-09-21 11:25:02
	 * @param  UsuarioModulo $pUsuarioModulo 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return UsuarioModulo               Instancia guardada.
	 */
	public function guardarUsuarioModulo(UsuarioModulo $pUsuarioModulo, $pRecargar = true)
	{
		if (is_null($pUsuarioModulo))
			throw new Exception(sprintf("Error en %s.guardarUsuarioModulo: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBSeguridad()->conectar(false); // AutoCommit: false
		DB::getInstanceDBSeguridad()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$id = DB::getInstanceDBSeguridad()->guardarUsuarioModulo(
				$pUsuarioModulo->id_usuario,
				$pUsuarioModulo->id_modulo,
				$pUsuarioModulo->nivel);

			DB::getInstanceDBSeguridad()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->cancelarTransaccion();
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarUsuarioModulo: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerUsuarioModulo($pUsuarioModulo->id_usuario, $pUsuarioModulo->id_modulo);
		}
		else
			$resultado = $pUsuarioModulo;

		DB::getInstanceDBSeguridad()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarUsuarioModulo: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGSeguridad: Elimina un conjunto de UsuarioModulos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-21 11:25:02
	 * @param  integer (PK) id_usuario
	 * @param  string (PK) id_modulo
	 * @param  integer nivel
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarUsuarioModulos(
		// Parametros
		$pid_usuario = null,
		$pid_modulo = null,
		$pnivel = null)
	{
		DB::getInstanceDBSeguridad()->conectar(false); // AutoCommit: false
		DB::getInstanceDBSeguridad()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBSeguridad()->eliminarUsuarioModulos($pid_usuario, $pid_modulo, $pnivel);

			DB::getInstanceDBSeguridad()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->cancelarTransaccion();
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarUsuarioModulos: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return $resultado;
	}

	/**
	 * NGSeguridad: Elimina una instancia de tipo UsuarioModulo en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-21 11:25:02
	 * @param  UsuarioModulo $pUsuarioModulo 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarUsuarioModulo(UsuarioModulo $pUsuarioModulo)
	{
		if (is_null($pUsuarioModulo))
			throw new Exception(sprintf("Error en %s.eliminarUsuarioModulo: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBSeguridad()->conectar(false); // AutoCommit: false
		DB::getInstanceDBSeguridad()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarUsuarioModulos($pUsuarioModulo->id_usuario, $pUsuarioModulo->id_modulo);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarUsuarioModulo: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBSeguridad()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->cancelarTransaccion();
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarUsuarioModulo: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Modulos
	// ************************************************************************
	/**
	 * NGSeguridad: Obtiene una coleccion de elementos tipo Modulo en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:47
	 * @param  string (PK) id_modulo
	 * @param  string nombre_modulo
	 * @param  string descripcion_modulo
	 * @param  bool activo
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @return array<Modulo>
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
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBSeguridad()->obtenerModulos($pid_modulo, $pnombre_modulo, $pdescripcion_modulo, $pactivo,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerModulos: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Modulo
		$resultado = $this->arrayResultToInstance($filas, 'Modulo');

		DB::getInstanceDBSeguridad()->desconectar();

		return $resultado;
	}

	/**
	 * NGSeguridad: Determina la cantidad de elementos tipo Modulo obtenidos en base a diferentes criterios de selección.
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
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBSeguridad()->obtenerModulosCantidad($pid_modulo, $pnombre_modulo, $pdescripcion_modulo, $pactivo);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerModulosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return $cantidad_resultados;
	}

	/**
	 * NGSeguridad: Obtiene una instancia de tipo Modulo en base a su identificador.
	 * Si el elemento no se encuentra, devuelve 'null'.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:47
	 * @param  string (PK) id_modulo
	 * @return Modulo Instancia de Modulo buscada, o 'null' en caso de que no exista.
	 */
	public function obtenerModulo(
		// Parametros
		$pid_modulo)
	{
		if (is_null($pid_modulo))
			throw new Exception(sprintf("Error en %s.obtenerModulo: los campos clave no pueden ser nulos.", get_class($this)));

		$resultado = $this->obtenerModulos($pid_modulo);

		if (count($resultado) == 0)
			return null;
		else if (count($resultado) == 1)
			return $resultado[0];
		else
			throw new Exception(sprintf("Error en %s.obtenerModulo: se encontr&oacute; m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));
	}

	/**
	 * Guarda una instancia de tipo Modulo. Devuelve la instancia guardada, recargando las propiedades autocalculadas.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:47
	 * @param  Modulo $pModulo 	Instancia a guardar.
	 * @param  boolean $pRecargar 		Recargar la clase despues de ser guardada, para actualizar su estado.
	 * @return Modulo               Instancia guardada.
	 */
	public function guardarModulo(Modulo $pModulo, $pRecargar = true)
	{
		if (is_null($pModulo))
			throw new Exception(sprintf("Error en %s.guardarModulo: la instancia a guardar no puede ser nula.",get_class($this)));

		DB::getInstanceDBSeguridad()->conectar(false); // AutoCommit: false
		DB::getInstanceDBSeguridad()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$id = DB::getInstanceDBSeguridad()->guardarModulo(
				$pModulo->id_modulo,
				$pModulo->nombre_modulo,
				$pModulo->descripcion_modulo,
				$pModulo->activo);

			DB::getInstanceDBSeguridad()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->cancelarTransaccion();
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.guardarModulo: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		// recargo el contenido
		if ($pRecargar) {
			$resultado = $this->obtenerModulo($pModulo->id_modulo);
		}
		else
			$resultado = $pModulo;

		DB::getInstanceDBSeguridad()->desconectar();

		if (is_null($resultado))
			throw new Exception(sprintf("Error grave en %s.guardarModulo: no se encuentra el contenido actualizado.",get_class($this)));

		return $resultado;
	}

	/**
	 * NGSeguridad: Elimina un conjunto de Modulos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:47
	 * @param  string (PK) id_modulo
	 * @param  string nombre_modulo
	 * @param  string descripcion_modulo
	 * @param  bool activo
	 * @return integer Cantidad de entidades afectadas.
	 */
	public function eliminarModulos(
		// Parametros
		$pid_modulo = null,
		$pnombre_modulo = null,
		$pdescripcion_modulo = null,
		$pactivo = null)
	{
		DB::getInstanceDBSeguridad()->conectar(false); // AutoCommit: false
		DB::getInstanceDBSeguridad()->iniciarTransaccion(false); // SoloLectura: false

		try {
			// Obtengo los datos desde la capa de datos
			$resultado = DB::getInstanceDBSeguridad()->eliminarModulos($pid_modulo, $pnombre_modulo, $pdescripcion_modulo, $pactivo);

			DB::getInstanceDBSeguridad()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->cancelarTransaccion();
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarModulos: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return $resultado;
	}

	/**
	 * NGSeguridad: Elimina una instancia de tipo Modulo en base a su identificador.
	 * GenerateClass 0.97.5 beta @ 2016-09-20 14:08:47
	 * @param  Modulo $pModulo 	Instancia a guardar.
	 * @return boolean TRUE en caso de que se haya eliminado una instancia, FALSE en caso contrario.
	 */
	public function eliminarModulo(Modulo $pModulo)
	{
		if (is_null($pModulo))
			throw new Exception(sprintf("Error en %s.eliminarModulo: la instancia a eliminar no puede ser nula.",get_class($this)));

		DB::getInstanceDBSeguridad()->conectar(false); // AutoCommit: false
		DB::getInstanceDBSeguridad()->iniciarTransaccion(false); // SoloLectura: false

		try {
			$resultado = $this->eliminarModulos($pModulo->id_modulo);

			if ($resultado > 1)
				throw new Exception(sprintf("Error en %s.eliminarModulo: se quiso eliminar m&aacute;s de una ocurrecia para una b&uacute;squeda de resultado &uacute;nico.", get_class($this)));

			DB::getInstanceDBSeguridad()->guardarTransaccion();

		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->cancelarTransaccion();
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.eliminarModulo: transacci&oacute;n no finalizada, causa: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return ($resultado == 1);
	}

	// ************************************************************************
	// Usuarios Firmantes
	// ************************************************************************
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
		$pLimiteOffset = null)
	{
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBSeguridad()->obtenerUsuariosFirmantes($pid_usuario, $pcodigo_usuario, $pnombre_usuario, $piniciales_usuario, $ppassword_usuario, $phabilitado_usuario, $pconfirma_giros, $pobservaciones_usuario, $pu_legajo,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUsuariosFirmantes: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Usuario
		$resultado = $this->arrayResultToInstance($filas, 'Usuario');

		DB::getInstanceDBSeguridad()->desconectar();

		return $resultado;
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
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBSeguridad()->obtenerUsuariosFirmantesCantidad($pid_usuario, $pcodigo_usuario, $pnombre_usuario, $piniciales_usuario, $ppassword_usuario, $phabilitado_usuario, $pconfirma_giros, $pobservaciones_usuario, $pu_legajo);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUsuariosFirmantesCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return $cantidad_resultados;
	}

	// ************************************************************************
	// Usuarios habilitados para Giros
	// ************************************************************************
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
		$pLimiteOffset = null)
	{
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBSeguridad()->obtenerUsuariosHabilitadosParaGiros($pid_usuario, $pcodigo_usuario, $pnombre_usuario, $piniciales_usuario, $ppassword_usuario, $phabilitado_usuario, $pconfirma_giros, $pobservaciones_usuario, $pu_legajo,
				$pOrdenColumnas, $pLimiteCantidad, $pLimiteOffset);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUsuariosHabilitadosParaGiros: %s", get_class($this), $e->getMessage()));
		}

		// Transformo el array de resultados en una coleccion de elementos tipo Usuario
		$resultado = $this->arrayResultToInstance($filas, 'Usuario');

		DB::getInstanceDBSeguridad()->desconectar();

		return $resultado;
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
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$cantidad_resultados = DB::getInstanceDBSeguridad()->obtenerUsuariosHabilitadosParaGirosCantidad($pid_usuario, $pcodigo_usuario, $pnombre_usuario, $piniciales_usuario, $ppassword_usuario, $phabilitado_usuario, $pconfirma_giros, $pobservaciones_usuario, $pu_legajo);
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerUsuariosHabilitadosParaGirosCantidad: %s", get_class($this), $e->getMessage()));
		}

		DB::getInstanceDBSeguridad()->desconectar();

		return $cantidad_resultados;
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
		DB::getInstanceDBSeguridad()->conectar();

		try {
			// Obtengo los datos desde la capa de datos
			$filas = DB::getInstanceDBSeguridad()->obtenerEMailsNotificables();
		} catch (Exception $e) {
			DB::getInstanceDBSeguridad()->desconectar();
			throw new Exception(sprintf("Error en %s.obtenerEMailsNotificables: %s", get_class($this), $e->getMessage()));
		}

		// El resultado no se convierte a una coleccion de objetos de la capa de modelos,
		// sino que se trabaja 'en plano', como un objeto StdClass.
		$resultado = $this->arrayResultToStdClass($filas);

		DB::getInstanceDBSeguridad()->desconectar();

		return $resultado;
	}

}
?>
