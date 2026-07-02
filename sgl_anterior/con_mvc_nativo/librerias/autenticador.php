<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Clase que implementa el patrón singleton para centralización de la lógica de logueo de errores e
// información desde los scripts en PHP.
require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/Logger.php");

// Clase base de los Modelos para trabajar con MySQLi
require_once($_SERVER['DOCUMENT_ROOT']."/sgl/librerias/modelo_base_mysqli.php");

class Autenticador extends ModeloBaseMySQLi
{
	/**
	 * Se guarda en sesión información del usuario, fecha, hora, nombre de la PC
	 * @param string $usuario
	 * @param string $password
	 * @param integer $id_usuario
	 */
	public function guardarEnSesion($usuario, $password, $id_usuario)
	{
		$_SESSION['usuario']      = $usuario;
		$_SESSION['password']     = $password;
		$_SESSION['id_usuario']   = $id_usuario;
		$_SESSION['fecha']        = date("Y-m-d");// FECHA DE INGRESO
		$_SESSION['hora_ingreso'] = date("H:i");// HORA DE INGRESO
		
		// Se define la fecha y hora de inicio de sesión en formato aaaa-mm-dd hh:mm:ss
		$_SESSION["ultimoAcceso"] = strtotime(date("Y-m-d H:i:s"));
		
		// NOMBRE DE LA PC ( con gethostbyaddr se obtiene el nombre del hostname, utilizando la variable de servidor REMOTE_ADDR )
		$_SESSION['netpcname']    = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	}	
	
	/**
	 * Verifica si el usuario existe
	 * 
	 * @param string $codigo_usuario
	 * @param string $password
	 * @return array $usuario
	 */
	public function verificarUsuario($codigo_usuario, $password)
	{
		// SE ABRE LA CONEXION PRIMERO CON UN VALOR 0(CERO) DE PERFIL, SOLAMENTE PARA VERIFICAR LA EXISTENCIA DEL USUARIO
		$conexion = $this->establecerConexion(0);
	
		//SE BUSCA EL USUARIO, TAMBIEN DEBE POSEER POR LO MENOS UN PERFIL
		$sql = "SELECT * FROM ".$this->tabla_usuarios."
				WHERE id_usuario IN (SELECT id_usuario FROM ".$this->tabla_perfiles.")
				AND codigo_usuario ='".mysqli_real_escape_string($conexion, $codigo_usuario)."'
				AND password_usuario ='".md5(mysqli_real_escape_string($conexion, addslashes($password)))."'
				AND habilitado_usuario = '1'
			   ";
		
		$resultado = $this->ejecutarQuery($sql);
		
		$usuario = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $usuario;
	}	
	
	/**
	 * Se obtienen los sistemas a los que posee acceso un usuario determinado
	 * 
	 * @param integer $id_usuario
	 * @return array $accesos, del usuario logueado
	 */
	public function consultarAccesos($id_usuario)
	{
		$conexion = $this->establecerConexion(0);
			
		$sql = "SELECT * FROM ".$this->tabla_perfiles." 
				WHERE id_usuario = ".$id_usuario."
				AND id_sistema IN ( SELECT id_sistema
									FROM ".$this->tabla_sistemas."
									WHERE habilitado_sistema = 1
								  )
		       ";
		
		$resultado = $this->ejecutarQuery($sql);
		
		$accesos = $this->crearVector($resultado);
						
		$this->desconectar($conexion);
		
		return $accesos;
	}	

	/**
	 * Se obtiene el nombre del sistema en base a su Id
	 */
	public function obtenerNombreSistema($id_sistema)
	{
		$conexion = $this->establecerConexion(0);
		
		$query = "SELECT nombre_sistema
				  FROM ".$this->tabla_sistemas."
				  WHERE id_sistema = ".$id_sistema;
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['nombre_sistema'];
	}	
	
	/**
	 * Se obtiene el perfil según un sistema y usuario determinados
	 * @param integer $id_sistema
	 * @param integer $id_usuario
	 * @return integer Perfil
	 */
	public function obtenerPerfil($id_sistema, $id_usuario)
	{
	    $perfil = $this->obtenerPerfilSegunSistema($id_sistema, $id_usuario);
	
	    return $perfil;
	}
	
	/**
	 * Se registra en auditoría el Login de un usuario determinado, en un sistema determinado
	 * @param integer $id_sistema
	 */
	public function registrarLogin($id_sistema)
	{
	    $observacion = addslashes("Ingreso del usuario ".$_SESSION['usuario']." el ".date("d/m/Y")." a las ".date("H:i")." hs.");
	    
	    $this->registrarMovimiento($id_sistema, $observacion);
	}
	
	/**
	 * Se registra en auditoría el Logout de un usuario determinado
	 */
	public function registrarLogout()
	{
	    $observacion = addslashes("El usuario ".$_SESSION['usuario']." cerr&oacute; su sesi&oacute;n el ".date("d/m/Y")." a las ".date("H:i")." hs.");
	    
	    $this->registrarMovimiento('null', $observacion);
	}
	
	/**
	 * Se registra en auditoría la información de un movimiento realizado por un usuario determinado
	 */
	public function registrarMovimiento($id_sistema = 'null', $observacion)
	{
	    $conexion = $this->establecerConexion(1);
	    
		$perfil = ( $id_sistema == 'null' ) ? 'null' : $_SESSION['perfil'.$id_sistema];
		
	    $query = "INSERT INTO ".$this->tabla_auditoria_web." (id_usuario, id_sistema, perfil, netusername, netpcname, observaciones_log)
				  VALUES ( ".$_SESSION['id_usuario'].",
						   ".$id_sistema.",
						   ".$perfil.",
						  '".$_SESSION['usuario']."',
						  '".$_SESSION['netpcname']."',
						  '".$observacion."'
						 )
				";
		
	    if ( !$this->ejecutarQuery($query) )
			return false;
	    
	    $this->desconectar($conexion);
	    
	    return true;	
	}

	/**
	 * 
	 * @param unknown $perfil
	 * @param unknown $id_sistema
	 * @param unknown $nombre_controlador
	 * @param unknown $nombre_accion
	 * @return boolean
	 */
	public function tienePermisoAcceso($perfil, $id_sistema, $nombre_controlador, $nombre_accion)
	{
		return parent::tienePermisoAcceso($perfil, $id_sistema, $nombre_controlador, $nombre_accion);
	}
	
	/**
	 * Devuelve el nombre del controlador que se utiliza por defecto,
	 * según el perfil y el sistema determinados
	 * @param integer $perfil
	 * @param integer $id_sistema
	 * @return string Nombre del controlador
	 */
	public function obtenerNombreControladorPorDefecto($perfil, $id_sistema)
	{
		if ( $id_sistema == 1 )// SI ES ADMINISTRACION
		{
			switch ($perfil)
			{
				case 1:
					$controlador_por_defecto = 'usuarios';// AREA ADMINISTRACION
					break;
				case 10:
					$controlador_por_defecto = 'compras';// AREA ADMINISTRACION
					break;
				case 11:
					$controlador_por_defecto = 'concejales_historico';// AREA BIBLIOTECA
					break;
				case 12:
					$controlador_por_defecto = 'comisiones_internas';// AREA COMISIONES
					break;
				case 14:
					$controlador_por_defecto = 'usuarios';// AREA INFORMATICA
					break;
				case 15:
					$controlador_por_defecto = 'gacetillas';// AREA PRENSA
					break;
			}
		}
		elseif ($id_sistema == 2 )// SI ES EXPEDIENTES
		{
			$controlador_por_defecto = 'expedientes';
		}
		elseif ($id_sistema == 3 )// SI ES PERSONAL
		{
			$controlador_por_defecto = 'personal';
		}
		
		return $controlador_por_defecto.'_controller';
	}
}	
?>
