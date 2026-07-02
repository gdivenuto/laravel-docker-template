<?php
if (!isset($_SESSION))
	session_start();

class ProvinciaModel extends ModeloBaseMySQLi {

	public function __construct() {
		parent::__construct();
	}

	public function conectar() {
		// Se conecta según el Id del sistema
		return parent::conectarDB(6);
	}
	
	/**
	 * Se obtienen las Provincias
	 */
	public function listar() {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_def_provincias . " WHERE habilitado = 1 ORDER BY nombre";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}
}