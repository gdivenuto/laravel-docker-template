<?php
/**
 * Capa de negocio para adaptar la nueva versìón (SGL-v2) con la anterior (SGL-v1).
 *
 * @author XXXX
 *
 */
class NGAdaptadorSGLv1 extends NGBaseClass {

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
	// Manipulación de variables de sesión ************************************
	// ************************************************************************

	/**
	 * Se guarda en sesión información del usuario, fecha, hora, nombre de la PC
	 * @param string $usuario
	 * @param string $password
	 * @param integer $id_usuario
	 */
	public function guardarEnSesionInfoAlAcceder($usuario, $password, $id_usuario, $nombre_usuario) {
		$_SESSION['usuario'] = $usuario;
		$_SESSION['password'] = $password;
		$_SESSION['id_usuario'] = $id_usuario;
		$_SESSION['nombre_usuario'] = $nombre_usuario;
		$_SESSION['fecha'] = date("Y-m-d"); // FECHA DE INGRESO
		$_SESSION['hora_ingreso'] = date("H:i"); // HORA DE INGRESO
		// Se define la fecha y hora de inicio de sesión en formato aaaa-mm-dd hh:mm:ss
		$_SESSION["ultimoAcceso"] = strtotime(date("Y-m-d H:i:s"));
		// Nombre de la PC ( con gethostbyaddr se obtiene el nombre del hostname, utilizando la variable de servidor REMOTE_ADDR )
		$_SESSION['netpcname'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	}

	/**
	 * Se eliminan las variables de sesion utilizadas durante el ingreso del usuario respectivo
	 */
	public function eliminarEnSesionInfoAlAcceder() {
		$_SESSION['usuario'] = null;
		$_SESSION['password'] = null;
		$_SESSION['id_usuario'] = null;
		$_SESSION['nombre_usuario'] = null;
		$_SESSION['fecha'] = null;
		$_SESSION['hora_ingreso'] = null;
		$_SESSION["ultimoAcceso"] = null;
		$_SESSION['netpcname'] = null;
		$_SESSION['perfil1'] = null;
		$_SESSION['perfil2'] = null;
		$_SESSION['perfil3'] = null;
		$_SESSION['verificacion_ppc_hecha'] = null;
	}
}
?>
