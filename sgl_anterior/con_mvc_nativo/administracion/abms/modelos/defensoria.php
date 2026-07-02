<?php
if (!isset($_SESSION))
	session_start();

class defensoriaModel extends ModeloBaseMySQLi {

	public function conectar() {
		return parent::conectarDB(1);
	}

	/**
	 * Se obtiene la informacion de un registro determinado por su Id
	 *
	 * @param integer $id
	 * @return array $registro
	 */
	public function obtenerRegistro($candidato_id = 0) {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_moderados_defensor_pueblo . " WHERE candidato_id = " . $candidato_id;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 *
	 * @param  integer $candidato_id
	 * @param  integer $habilitado
	 * @param  string  $motivo
	 * @return boolean true|false
	 */
	public function modificarEstado($candidato_id, $habilitado = 0, $motivo = null) {

		$conexion = $this->conectar();

		$query = "INSERT INTO " . $this->tabla_moderados_defensor_pueblo . " 
					(candidato_id, 
					 habilitado, 
					 motivo
					)
				  VALUES
				  	($candidato_id, 
				  	 $habilitado, 
				  	 ".$this->revisarValorAtributo(strip_tags($motivo))."
				  	)
				  ON DUPLICATE KEY
					UPDATE 
						habilitado = $habilitado,
						motivo = ".$this->revisarValorAtributo(strip_tags($motivo));

		//LibreriaGeneral::registrarLog("query_modificarEstado", $query, '.sql');

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}
	
}
?>