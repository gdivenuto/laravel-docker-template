<?php
if (!isset($_SESSION))
	session_start();

class NotaModel extends ModeloBaseMySQLi {
	
	public function __construct() {
		parent::__construct();
	}

	public function conectar() {
		// Se conecta según el Id del sistema
		return parent::conectarDB(6);
	}

	private function getFiltro() {

		$filtro = "";

		if (isset($this->filtro['f_numero']) && $this->filtro['f_numero'] != '') {
			$filtro .= " AND numero = " . $this->filtro['f_numero'];
		}
		if (isset($this->filtro['f_fecha_desde']) && $this->filtro['f_fecha_desde'] != '' &&
			isset($this->filtro['f_fecha_hasta']) && $this->filtro['f_fecha_hasta'] != ''
		) {
			$filtro .= " AND fecha BETWEEN '" . $this->filtro['f_fecha_desde'] . " 00:00:00' AND '" . $this->filtro['f_fecha_hasta'] . " 23:59:59'";
		}
		return $filtro;
	}

	/**
	 * Se obtiene el listado
	 * @return array $listado
	 */
	public function listar() {
		$conexion = $this->conectar();

		$registro_inicial = (isset($this->filtro['inicio']) && $this->filtro['inicio'] != '') 
			? $this->filtro['inicio'] 
			: 0;

		$sql = "SELECT * FROM " . $this->tabla_def_notas . "
			    WHERE fecha IS NOT NULL
			    " . $this->getFiltro() . "
			    ORDER BY " . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
			    LIMIT " . $registro_inicial . ", " . $this->filtro['rango'];

		$resultado = $this->ejecutarQuery($sql);

		$listado = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $listado;
	}

	/**
	 * Se obtiene la cantidad de registros encontrados, en base a un criterio determinado en la query
	 * @see ModeloBaseMysql::obtenerCantidad()
	 */
	public function obtenerCantidad() {
		$conexion = $this->conectar();

		$query = "SELECT COUNT(numero) AS cantidad
				  FROM " . $this->tabla_def_notas . "
				  WHERE fecha IS NOT NULL
				  " . $this->getFiltro();

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	/**
	 * Se obtiene el ultimo Id registrado en la DB
	 */
	public function obtenerUltimoId() {
		$conexion = $this->conectar();
		
		$query = "SELECT MAX(numero) AS ultimo_codigo FROM " . $this->tabla_def_notas;
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerFila($resultado);
		
		$ultimo_codigo = ($dato['ultimo_codigo'] != null) ? $dato['ultimo_codigo'] : 0;
				
		$this->desconectar($conexion);
		
		return $ultimo_codigo;
	}

	/**
	 * Se obtiene la informacion de un registro determinado por su numero
	 *
	 * @param integer $numero
	 * @return array $registro
	 */
	public function obtenerRegistro($numero) {
		$conexion = $this->conectar();

		$query = "SELECT * FROM " . $this->tabla_def_notas . " WHERE numero = " . $numero;

		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerFila($resultado);

		return $registro;
	}

	/**
	 * Se ingresa una nota
	 *
	 * @param string 	$documento 	Nombre del documento
	 * @return boolean
	 */
	public function insertar($documento) {

		$conexion = $this->conectar();

		$documento = $this->revisarValorAtributo($documento);

		$query = "INSERT INTO " . $this->tabla_def_notas . " 
					(fecha, documento)
				  VALUES 
				  	('" . date('Y-m-d H:i:s') . "', 
				  	  " . $documento . "
				  	)";

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"ALTA",
				$this->tabla_def_notas,
				$this->obtenerUltimoId(),
				"Se ingresa una Nota"
			);
		}
		return true;
	}

	/**
	 * Se elimina
	 *
	 * @param integer $numero
	 * @return boolean
	 */
	public function eliminar($numero) {
		$conexion = $this->conectar();

		$query = "DELETE FROM " . $this->tabla_def_notas . " WHERE numero = " . $numero;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"BAJA",
				$this->tabla_def_notas,
				$numero,
				"Se elimina una Nota, numero " . $numero
			);
		}
		return true;
	}

}
?>