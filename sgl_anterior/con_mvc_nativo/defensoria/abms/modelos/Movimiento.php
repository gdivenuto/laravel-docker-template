<?php
if (!isset($_SESSION))
	session_start();

class MovimientoModel extends ModeloBaseMySQLi {
	
	public function __construct() {
		parent::__construct();
	}

	public function conectar() {
		// Se conecta según el Id del sistema
		return parent::conectarDB(6);
	}

	/**
	 * Se obtiene el listado de movimientos
	 * @param  integer 	$numero 	Nro del expediente
	 * @return array 	$listado  	Listado de movimientos del expediente
	 */
	public function listar($numero = 0) {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_def_movimientos . " WHERE numero = " . $numero;

		$resultado = $this->ejecutarQuery($sql);

		$listado = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $listado;
	}

	/**
	 * Se obtiene el ultimo Id registrado en la DB
	 */
	public function obtenerUltimoId() {
		$conexion = $this->conectar();
		
		$query = "SELECT MAX(id) AS ultimo_codigo FROM " . $this->tabla_def_movimientos;
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerFila($resultado);
		
		$ultimo_codigo = ($dato['ultimo_codigo'] != null) ? $dato['ultimo_codigo'] : 0;
				
		$this->desconectar($conexion);
		
		return $ultimo_codigo;
	}

	/**
	 * Se obtiene la informacion de un rubro determinado por su Id
	 *
	 * @param integer $id
	 * @return array $registro
	 */
	public function obtenerRegistro($id) {
		$conexion = $this->conectar();

		$query = 'SELECT * FROM ' . $this->tabla_def_movimientos . ' WHERE id = ' . $id;

		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerFila($resultado);

		return $registro;
	}

	/**
	 * Se ingresa la informacion de un rubro nuevo
	 *
	 * @param integer 	$numero		ID del expediente
	 * @param string 	$documento 	Nombre del documento
	 * @return boolean
	 */
	public function insertar($numero, $documento) {

		$conexion = $this->conectar();

		$documento = $this->revisarValorAtributo($documento);

		$query = "INSERT INTO " . $this->tabla_def_movimientos . " (numero, documento)
				  VALUES (" . $numero . ", " . $documento . ")";

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"ALTA",
				$this->tabla_def_movimientos,
				$this->obtenerUltimoId(),
				"Se ingresa un movimiento al expediente ID ". $numero
			);
		}
		return true;
	}

	/**
	 * Se elimina
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function eliminar($id) {
		$conexion = $this->conectar();

		$query = "DELETE FROM " . $this->tabla_def_movimientos . " WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"BAJA",
				$this->tabla_def_movimientos,
				$id,
				"Se elimina un movimiento, Id " . $id
			);
		}
		return true;
	}

}
?>