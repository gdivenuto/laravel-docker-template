<?php
if (!isset($_SESSION))
	session_start();

class DefensoriaObservacionDmzModel extends ModeloBaseMySQLiDMZ {

	public function conectar() {
		return parent::obtenerConexion();
	}

	private function getFiltro() {

		$filtro = "";

		if (isset($this->filtro['f_fecha']) && $this->filtro['f_fecha'] != '') {
			$filtro .= " AND OCDP.fecha LIKE '" . $this->filtro['f_fecha'] . "%'";
		}

		if (isset($this->filtro['f_texto']) && $this->filtro['f_texto'] != '') {
			$filtro .= " AND (
							OCDP.apellido LIKE '%" . str_replace(" ", "%", $this->filtro['f_texto']) . "%' 
							OR
							OCDP.nombre LIKE '%" . str_replace(" ", "%", $this->filtro['f_texto']) . "%' 
							OR
							IDP.apellido LIKE '%" . str_replace(" ", "%", $this->filtro['f_texto']) . "%' 
							OR
							IDP.nombre LIKE '%" . str_replace(" ", "%", $this->filtro['f_texto']) . "%' 
							OR
							OCDP.dni LIKE '%" . $this->filtro['f_texto'] . "%'
							OR
							OCDP.email LIKE '%" . $this->filtro['f_texto'] . "%'
							OR
							OCDP.entidad_email LIKE '%" . $this->filtro['f_texto'] . "%'
						)";
		}

		if (isset($this->filtro['f_habilitados']) && $this->filtro['f_habilitados'] == 1) {
			$filtro .= " AND OMDP.habilitado = 1";
		}

		if (isset($this->filtro['f_candidato_id']) && $this->filtro['f_candidato_id'] != 0) {
			$filtro .= " AND OCDP.candidato_id = ".$this->filtro['f_candidato_id'];
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
					OCDP.*,
					OMDP.habilitado,
					IDP.apellido AS candidato_apellido, 
					IDP.nombre AS candidato_nombre
				FROM " . $this->tabla_observaciones_candidato_dp . " AS OCDP
				LEFT JOIN hcd.admin_observaciones_moderadas_dp AS OMDP
					ON OMDP.observacion_id = OCDP.id
				LEFT JOIN " . $this->tabla_inscripciones_defensor_pueblo . " AS IDP
					ON IDP.id = OCDP.candidato_id
				WHERE OCDP.tipo_observacion IS NOT NULL
				" . $this->getFiltro() . "
				ORDER BY OCDP." . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
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
					COUNT(OCDP.id) AS cantidad
				  FROM " . $this->tabla_observaciones_candidato_dp . " AS OCDP
				  LEFT JOIN hcd.admin_observaciones_moderadas_dp AS OMDP
					ON OMDP.observacion_id = OCDP.id
				  LEFT JOIN " . $this->tabla_inscripciones_defensor_pueblo . " AS IDP
					ON IDP.id = OCDP.candidato_id
				  WHERE OCDP.tipo_observacion IS NOT NULL
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

		$sql = "SELECT * FROM " . $this->tabla_observaciones_candidato_dp . " WHERE id = " . $id;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	/**
	 * Se obtiene una Observación por su ID
	 * @param  integer $id 			Identificador
	 * @return array   $registro    Información
	 */
	public function obtenerObservacion($id)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT 
					OCDP.*,
					IDP.apellido AS candidato_apellido, 
					IDP.nombre AS candidato_nombre,
					IDP.email AS candidato_email
				FROM ".$this->tabla_observaciones_candidato_dp." AS OCDP
				LEFT JOIN " . $this->tabla_inscripciones_defensor_pueblo . " AS IDP
					ON IDP.id = OCDP.candidato_id
				WHERE OCDP.id = ".$id;

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