<?php
if (!isset($_SESSION)) {
	session_start();
}

class usuariosModel extends ModeloBaseMySQLi {
	
	private $modulo_expedientes = 'EXPEDIENTES';

	public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}
	/**
	 * Se listan los usuarios
	 * @return [array] Listado de Usuarios
	 */
	public function listar() {
		$conexion = $this->conectar();

		//para la busqueda
		$filtro_sql = "";
		if ($this->filtro['campo_orden'] != '' && $this->filtro['valor_buscado'] != '') {
			$filtro_sql .= " WHERE " . $this->filtro['campo_orden'] . " LIKE '%" . $this->filtro['valor_buscado'] . "%'";
		}

		$sql = "SELECT * FROM " . $this->tabla_usuarios . "
				" . $filtro_sql . "
				ORDER BY " . $this->filtro['campo_orden'] . " " . $this->filtro['sentido'] . "
				LIMIT " . $this->filtro['inicio'] . ", " . $this->filtro['rango'];

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene la cantidad, en base a un criterio de búsqueda determinado
	 * @return [integer]       Cantidad
	 */
	public function obtenerCantidad() {
		$conexion = $this->conectar();

		// Para mantener el filtro de búsqueda
		$filtro = ($this->filtro['valor_buscado'] != '') ? " WHERE " . $this->filtro['campo_orden'] . " LIKE '%" . $this->filtro['valor_buscado'] . "%'" : "";

		$sql = "SELECT COUNT(*) AS cantidad FROM " . $this->tabla_usuarios . $filtro;

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['cantidad'];
	}

	/**
	 * Se obtiene la información de un Usuario, determinado por su ID
	 * @param  [integer] $id_usuario Identificador del usuario
	 * @return [array]               Info del Usuario respectivo
	 */
	public function obtenerRegistro($id_usuario) {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_usuarios . " WHERE id_usuario = " . $id_usuario;

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtienen los perfiles de un usuario, determinado por su Id
	 * @param  [integer] $id_usuario Identificador del Usuario
	 * @return [array]               Perfiles del Usuario respectivo
	 */
	public function obtenerPerfiles($id_usuario) {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_perfiles . "
				WHERE id_usuario = " . $id_usuario . "
				AND id_sistema IN (SELECT id_sistema
								   FROM " . $this->tabla_sistemas . "
								   WHERE habilitado_sistema = 1
								  )";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se verifica la existencia de un Usuario determinado por su código (nombre de usuario)
	 * @param  [string] $codigo_usuario Nombre de Usuario
	 * @return [boolean]                True|False
	 */
	public function existe($codigo_usuario) {
		$conexion = $this->conectar();

		$query = "SELECT codigo_usuario FROM " . $this->tabla_usuarios . " WHERE codigo_usuario = '" . $codigo_usuario . "'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		// True/False, si existe o no
		return ($dato['codigo_usuario']);
	}

	/**
	 * Se valida determinada información
	 * @param  [array] $datos 	Info a validar
	 * @return [array] $datos 	Info validada
	 */
	public function validarDatos($datos)
	{
		$datos['nombre_usuario'] = $this->revisarValorAtributo($datos['nombre_usuario']);

		$datos['iniciales_usuario'] = $this->revisarValorAtributo($datos['iniciales_usuario']);
		
		$datos['u_mail'] = $this->revisarValorAtributo($datos['u_mail']);

		$datos['u_legajo'] = $this->revisarValorAtributo($datos['u_legajo']);

		$datos['habilitado_usuario'] = $this->revisarValorAtributo($datos['habilitado_usuario'], 0);

		$datos['confirma_giros'] = $this->revisarValorAtributo($datos['confirma_giros'], 0);

		$datos['observaciones_usuario'] = $this->revisarValorAtributo(strip_tags($datos['observaciones_usuario']));

		return $datos;
	}

	/**
	 * Se verifica si el registro no ha sido modificado por otro usuario
	 * @return [boolean]	True|false
	 */
	public function noLoModificoOtroUsuario()
	{
		$conexion = $this->conectar();

		$query = "SELECT id_usuario
				  FROM " . $this->tabla_usuarios . "
				  WHERE id_usuario = " . $_SESSION['id_usuario_original'] . "
				  AND codigo_usuario = '" . $_SESSION['codigo_usuario_original'] . "'
				  " . $this->adaptarValorStringParaFiltro('nombre_usuario') . "
				  " . $this->adaptarValorStringParaFiltro('iniciales_usuario') . "
				  " . $this->adaptarValorStringParaFiltro('password_usuario') . "
				  " . $this->adaptarValorStringParaFiltro('u_legajo') . "
				  " . $this->adaptarValorStringParaFiltro('u_mail') . "
				  AND habilitado_usuario = " . $_SESSION['habilitado_usuario_original'] . "
				  AND confirma_giros = " . $_SESSION['confirma_giros_original'] . "
				  " . $this->adaptarValorStringParaFiltro('observaciones_usuario');

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($dato['id_usuario']);
	}

	/**
	 * Se ingresa un Usuario
	 * @param  [array] $datos Información a ingresar
	 * @return [boolean]      True|False
	 */
	public function insertar($datos)
	{
		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		// Si el password tiene un valor, se registra
		$password = ($datos['password_usuario'] != '') ? "MD5('" . $datos['password_usuario'] . "')" : "";

		$query = "INSERT INTO " . $this->tabla_usuarios . "
					(codigo_usuario, nombre_usuario, iniciales_usuario, password_usuario, u_legajo, u_mail, habilitado_usuario, confirma_giros, observaciones_usuario)
				  VALUES(
				  	'" . $datos['codigo_usuario'] . "',
					 " . $datos['nombre_usuario'] . ",
					 " . $datos['iniciales_usuario'] . ",
					 " . $password . ",
					 " . $datos['u_legajo'] . ",
					 " . $datos['u_mail'] . ",
					 1,
					 " . $datos['confirma_giros'] . ",
					 " . $datos['observaciones_usuario'] . "
				  )";

		// Si no se puede ejecutar la query
		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			// Se obtiene el Id generado en el INSERT
			$id_usuario_generado = mysqli_insert_id($conexion);

			// Se guardan los Perfiles del usuario para cada Sistema asignado
			$this->guardarPerfiles(
				$id_usuario_generado, 
				$datos['perfil_admin'], 
				$datos['perfil_exped'], 
				$datos['perfil_personal'],
				$datos['perfil_biblioteca'],
				$datos['perfil_inventario'],
				$datos['perfil_defensoria']
			);
		}

		$this->desconectar($conexion);

		// Se audita
		$this->auditarEnAdministracion("ALTA", $this->tabla_usuarios, "Se ingresa el Usuario " . $datos['codigo_usuario']);

		return true;
	}

	/**
	 * Se modifica un Usuario
	 * @param  [array] $datos Información a modificar
	 * @return [boolean]      True|False
	 */
	public function modificar($datos)
	{
		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		// Si el password tiene un valor, se registra
		$password = ($datos['password_usuario'] != '') ? "password_usuario  = MD5('" . $datos['password_usuario'] . "')," : "";

		$query = "UPDATE " . $this->tabla_usuarios . "
				  SET codigo_usuario = '" . $datos['codigo_usuario'] . "',
					  nombre_usuario = " . $datos['nombre_usuario'] . ",
					  iniciales_usuario = " . $datos['iniciales_usuario'] . ",
					  " . $password . "
					  u_legajo = " . $datos['u_legajo'] . ",
					  u_mail = " . $datos['u_mail'] . ",
					  habilitado_usuario = " . $datos['habilitado_usuario'] . ",
					  confirma_giros = " . $datos['confirma_giros'] . ",
					  observaciones_usuario = " . $datos['observaciones_usuario'] . "
				  WHERE id_usuario = " . $datos['id_usuario'];

		if (!$this->ejecutarQuery($query)) {
			return false;
		}
		else // Se guardan los Perfiles del usuario para cada Sistema asignado
		{
			$this->guardarPerfiles(
				$datos['id_usuario'], 
				$datos['perfil_admin'], 
				$datos['perfil_exped'], 
				$datos['perfil_personal'],
				$datos['perfil_biblioteca'],
				$datos['perfil_inventario'],
				$datos['perfil_defensoria']
			);
		}

		$this->desconectar($conexion);

		// Se audita
		$this->auditarEnAdministracion("MODIFICA", $this->tabla_usuarios, "Se modifica el Usuario " . $datos['codigo_usuario']);

		return true;
	}

	/**
	 * Se elimina un Usuario
	 * @param  [integer] $id 	Identificador del usuario
	 * @return [boolean]     	True|False
	 */
	public function eliminar($id)
	{
		try {
			// Previamente se obtiene la info para auditar
			$info = $this->obtenerRegistro($id);

			$conexion = $this->conectar();

			$this->iniciarTransaccion();

			// Se eliminan los perfiles del usuario a eliminar
			$query = "DELETE FROM " . $this->tabla_perfiles . " WHERE id_usuario = " . $id;
			
			if (!$this->ejecutarQuery($query)) {
				$this->revertirTransaccion(); // Se deshace la transacción
				return false;
			} else {
				// Agregado para SGLv2
				// Se eliminan los niveles del usuario a eliminar
				$query = "DELETE FROM " . $this->tabla_usuarios_x_modulo . " WHERE id_usuario = " . $id;
				
				if (!$this->ejecutarQuery($query)) {
					$this->revertirTransaccion(); // Se deshace la transacción
					return false;
				} else {
					// Se elimina el usuario
					$query = "DELETE FROM " . $this->tabla_usuarios . " WHERE id_usuario = " . $id;
					
					if (!$this->ejecutarQuery($query)) {
						$this->revertirTransaccion(); // Se deshace la transacción
						return false;
					} else {
						$this->confirmarTransaccion();
					}
				}
			}

			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_usuarios, "Se elimina el Usuario " . $info['codigo_usuario']);

			return true;
		} catch (\Throwable $error) {
			LibreriaGeneral::registrarLog("error_".date('Ymd_Hi'), $error->getMessage());
		}
	}

	/**
	 * Se guarda el perfil para el usuario en un sistema respectivo
	 * @param  [integer] $id_usuario Identificador del Usuario
	 * @param  [integer] $perfil     Valor del Perfil
	 * @param  [integer] $id_sistema Identificador del Sistema
	 */
	public function guardarPerfilPorSistema($id_usuario, $perfil, $id_sistema)
	{
		// Si se eligió un perfil
		if ($perfil != 0)
		{
			// Si el usuario ya posee uno en dicho sistema
			if ($this->existePerfil($id_sistema, $id_usuario)) {
				// Se modifica
				$query = "UPDATE " . $this->tabla_perfiles . "
						  SET perfil = " . $perfil . "
						  WHERE id_sistema = " . $id_sistema . "
						  AND id_usuario = " . $id_usuario;
			} else {
				// sino, se ingresa para el usuario en dicho sistema
				$query = "INSERT INTO " . $this->tabla_perfiles . "(perfil, id_sistema, id_usuario)
						  VALUES(" . $perfil . ", " . $id_sistema . ", " . $id_usuario . ")";
			}
		} else {
			// Si se retiró el perfil, se elimina para el usuario en dicho sistema
			$query = "DELETE FROM " . $this->tabla_perfiles . "
					  WHERE id_sistema = " . $id_sistema . "
					  AND id_usuario = " . $id_usuario;
		}

		$this->ejecutarQuery($query);

		// Si el sistema es Expedientes
		if ($id_sistema == 2) {
			// Se guarda el Nivel del Usuario en el Módulo de Expedientes SGLv2
			$this->guardarNivelPorModuloSGLv2($id_usuario, $perfil, $this->modulo_expedientes);
		}

	}

	/**
	 * Se guardan los perfiles asignados al usuario en cada sistema
	 * @param  [integer] $id_usuario      Identificador del Usuario
	 * @param  [integer] $perfil_admin    Perfil para Administración
	 * @param  [integer] $perfil_exped    Perfil para Expedientes
	 * @param  [integer] $perfil_personal Perfil para Personal
	 */
	public function guardarPerfiles($id_usuario, $perfil_admin, $perfil_exped, $perfil_personal, $perfil_biblioteca, $perfil_inventario, $perfil_defensoria) {
		
		// Para el Sistema de Administración
		$this->guardarPerfilPorSistema($id_usuario, $perfil_admin, 1);

		// Para el Sistema de Expedientes
		$this->guardarPerfilPorSistema($id_usuario, $perfil_exped, 2);

		// Para el Sistema de Personal
		$this->guardarPerfilPorSistema($id_usuario, $perfil_personal, 3);

		// Para el Sistema de Biblioteca
		$this->guardarPerfilPorSistema($id_usuario, $perfil_biblioteca, 4);

		// Para el Sistema de Inventario
		$this->guardarPerfilPorSistema($id_usuario, $perfil_inventario, 5);
		
		// Para el Sistema de Defensoria
		$this->guardarPerfilPorSistema($id_usuario, $perfil_defensoria, 6);
	}

	/**
	 * Se verifica el perfil de un usuario para un sistema determinado
	 * @param  [integer] $id_sistema Identificador del Sistema
	 * @param  [integer] $id_usuario Identificador del Usuario
	 * @return [boolean]             True|False
	 */
	public function existePerfil($id_sistema, $id_usuario) {
		$query = "SELECT perfil
				  FROM " . $this->tabla_perfiles . "
				  WHERE id_sistema = " . $id_sistema . "
				  AND id_usuario = " . $id_usuario;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return ($dato['perfil']);
	}

	/**
	 * Se guarda el Nivel del Usuario en un Módulo determinado del SGLv2, en base al perfil que posee en el SGLv1
	 * @param  [integer] $id_usuario 	Identificador del Usuario
	 * @param  [integer] $perfil      	Valor del Perfil
	 * @param  [string]  $id_modulo  	Identificador del Módulo
	 */
	public function guardarNivelPorModuloSGLv2($id_usuario, $perfil, $id_modulo)
	{
		// En base al perfil que posee en el SGLv1
		switch ($perfil) {
			// Administrador en SGLv1
			case 1:
				$nivel = 100; // Administrador en SGLv2
				break;
			// Supervisor en SGLv1
			case 2:
				$nivel = 50; // Operador en SGLv2
				break;
			// en SGLv1: 3 = Concejales | 5 = Secretario HCD
			case 3:
			case 5:
				$nivel = 1;   // Invitado y Concejal son alias del mismo perfil
				break;
			// Consulta Web en SGLv1
			case 4:
				$nivel = 0; // Periodista en SGLv2 (Consulta Web en SGLv1)
				break;
			default:
				$nivel = null; // Si no se especificó ningún perfil
				break;
		}

		// Si NO le corresponde ningún nivel (pudo haberse retirado el usuario del módulo)
		if (is_null($nivel)) {
			// Se elimina para el usuario en dicho módulo
			$query = "DELETE FROM " . $this->tabla_usuarios_x_modulo . "
					  WHERE id_modulo = '" . $id_modulo . "'
					  AND id_usuario = " . $id_usuario;
		} else {
			// Si el usuario ya posee uno
			if ($this->existeNivelSGLv2($id_modulo, $id_usuario) != '') {
				// Se modifica
				$query = "UPDATE " . $this->tabla_usuarios_x_modulo . "
						  SET nivel = " . $nivel . "
						  WHERE id_modulo = '" . $id_modulo . "'
						  AND id_usuario = " . $id_usuario;
			} else {
				// sino, se ingresa para el usuario en dicho módulo
				$query = "INSERT INTO " . $this->tabla_usuarios_x_modulo . "(nivel, id_modulo, id_usuario)
						  VALUES(" . $nivel . ", '" . $id_modulo . "', " . $id_usuario . ")";
			}
		}

		$this->ejecutarQuery($query);
	}

	/**
	 * Se verifica el Nivel de un Usuario para un Módulo determinado
	 * @param  [string]  $id_modulo  Identificador del Sistema
	 * @param  [integer] $id_usuario Identificador del Usuario
	 * @return [boolean]             True|False
	 */
	public function existeNivelSGLv2($id_modulo, $id_usuario) {
		$query = "SELECT nivel
				  FROM " . $this->tabla_usuarios_x_modulo . "
				  WHERE id_modulo = '" . $id_modulo . "'
				  AND id_usuario = " . $id_usuario;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return ($dato['nivel']);
	}

	/**
	 * Se obtienen los nombres de los usuarios habilitados
	 * @return [array] $datos  	Listado de nombres de usuarios
	 */
	public function obtenerNombresUsuariosExistentes() {
		$conexion = $this->conectar();

		$query = "SELECT codigo_usuario
				  FROM " . $this->tabla_usuarios . "
				  WHERE habilitado_usuario = 1";

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 *
	 * @param  integer $id
	 * @param  integer $habilitado
	 * @return boolean true|false
	 */
	public function modificarEstado($id, $habilitado) {
		$conexion = $this->conectar();

		$valor_habilitado = ($habilitado == 1) ? 0 : 1;

		$query = "UPDATE " . $this->tabla_usuarios . " SET habilitado_usuario = $valor_habilitado WHERE id_usuario = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

	/**
	 * Se verifica si está disponible el Nombre de usuario
	 * @param  string $codigo_usuario   Nombre del usuario
	 * @return boolean
	 */
	public function estaDisponibleNombreUsuario($codigo_usuario) {

		$conexion = $this->conectar();

		$sql = "SELECT codigo_usuario FROM " . $this->tabla_usuarios . " WHERE codigo_usuario = '" . $codigo_usuario . "'";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['codigo_usuario'];
	}

	/**
	 * Se verifica si existe un Legajo
	 * @param  integer $legajo   Legajo del usuario
	 * @return boolean
	 */
	public function existeLegajo($legajo) {

		$conexion = $this->conectar();

		$sql = "SELECT p_legajo, p_apellido, p_nombre FROM " . $this->tabla_personal . " WHERE p_legajo = " . $legajo;

		$resultado = $this->ejecutarQuery($sql);

		$info = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $info;
	}

	/**
	 * Se busca el legajo por el nombre y/o el apellido
	 * @param  string 	$nombre   	Nombre del usuario
	 * @param  string 	$apellido 	Apellido del usuario
	 * @return integer 	$p_legajo
	 */
	public function buscarLegajoPoNombreUsuario($nombre, $apellido) {

		$conexion = $this->conectar();

		$sql = "SELECT p_legajo
				FROM ".$this->tabla_personal."
				WHERE p_nombre LIKE '%".$nombre."%' 
				AND p_apellido LIKE '%".$apellido."%'";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos[0]['p_legajo'];
	}

}
?>
