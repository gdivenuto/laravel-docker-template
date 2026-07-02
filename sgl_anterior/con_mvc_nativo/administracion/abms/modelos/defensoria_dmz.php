<?php
if (!isset($_SESSION))
	session_start();

class defensoriaDmzModel extends ModeloBaseMySQLiDMZ {

	public function conectar() {
		return parent::obtenerConexion();
	}

	private function getFiltro() {

		$filtro = "";
		if (isset($this->filtro['f_fecha']) && $this->filtro['f_fecha'] != '') {
			$filtro .= " AND IDP.fecha LIKE '" . $this->filtro['f_fecha'] . "%'";
		}

		if (isset($this->filtro['f_texto']) && $this->filtro['f_texto'] != '') {
			$filtro .= " AND (
							IDP.apellido LIKE '%" . str_replace(" ", "%", $this->filtro['f_texto']) . "%' 
							OR
							IDP.nombre LIKE '%" . str_replace(" ", "%", $this->filtro['f_texto']) . "%' 
							OR
							IDP.dni LIKE '%" . $this->filtro['f_texto'] . "%'
							OR
							IDP.email LIKE '%" . $this->filtro['f_texto'] . "%'
						)";
		}

		if (isset($this->filtro['f_habilitados']) && $this->filtro['f_habilitados'] == 1) {
			$filtro .= " AND MDP.habilitado = 1";
		}

		return $filtro;
	}

	/**
	 * Se obtiene el listado, en base a un criterio determinado en la query
	 * @return array $datos
	 */
	public function listar() {

		$conexion = $this->conectar();
		
		$limite = (isset($this->filtro['rango']) && $this->filtro['rango'] != 0) 
			? " LIMIT " . $this->filtro['inicio'] . ", " . $this->filtro['rango']
			: "";

		$sql = "SELECT 
					IDP.*,
					MDP.habilitado
				FROM " . $this->tabla_inscripciones_defensor_pueblo . " AS IDP
				LEFT JOIN hcd.admin_moderados_defensor_pueblo AS MDP
					ON MDP.candidato_id = IDP.id
				WHERE IDP.apellido IS NOT NULL
				" . $this->getFiltro() . "
				ORDER BY IDP." . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
				" . $limite;

		//LibreriaGeneral::registrarLog("sql_listar", $sql, '.sql');

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene la cantidad de registros encontrados, en base a un criterio determinado en la query
	 */
	public function obtenerCantidad() {

		$conexion = $this->conectar();

		$query = "SELECT 
					COUNT(IDP.id) AS cantidad
				  FROM " . $this->tabla_inscripciones_defensor_pueblo . " AS IDP
				  LEFT JOIN hcd.admin_moderados_defensor_pueblo AS MDP
					ON MDP.candidato_id = IDP.id
				  WHERE IDP.apellido IS NOT NULL
				  " . $this->getFiltro();

		//LibreriaGeneral::registrarLog("query_obtenerCantidad", $query, '.sql');

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	/**
	 * Se obtiene la informacion de un registro determinado por su Id
	 *
	 * @param integer $id
	 * @return array $registro
	 */
	public function obtenerRegistro($id = 0) {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_inscripciones_defensor_pueblo . " WHERE id = " . $id;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	/**
	 * Se obtiene el nombre de la Provincia por su Id
	 * 
	 * @param  integer $id_provincia 	Identificador de la Provincia
	 * @return string  					Nombre
	 */
	public function obtenerNombreProvinciaPorId($id_provincia) {
		
		$conexion = $this->conectar();

		$query = "SELECT nombre FROM " . $this->tabla_provincias . " WHERE id = " . $id_provincia;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['nombre'];
	}
}
?>