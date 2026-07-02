<?php
if (!isset($_SESSION)) {
	session_start();
}

class actividadesModel extends ModeloBaseMySQLi {

	public function conectar() {
		// Se conecta según el Id del sistema
		return parent::conectarDB(1);
	}

	public function listar() {
		$conexion = $this->conectar();

		$filtro = "";
		$limite = "";

		// PARA FILTRAR POR FECHA
		if ($this->filtro['f_fecha'] != '') {
			$filtro .= " AND a_fecha = '" . $this->filtro['f_fecha'] . "'";
		}

		// PARA FILTRAR POR TITULO
		if ($this->filtro['f_titulo'] != '') {
			$filtro .= " AND a_titulo LIKE '%" . $this->filtro['f_titulo'] . "%'";
		}

		// PARA FILTRAR POR CONTENIDO
		if ($this->filtro['f_contenido'] != '') {
			$filtro .= " AND a_contenido LIKE '%" . $this->filtro['f_contenido'] . "%'";
		}

		// PARA LIMITAR EL LISTADO
		if ($this->filtro['rango'] != 0) {
			$limite = " LIMIT " . $this->filtro['inicio'] . ", " . $this->filtro['rango'];
		}

		$sql = "SELECT * FROM " . $this->tabla_actividades . "
				WHERE a_habilitado <> 3
				" . $filtro . "
				ORDER BY a_fecha " . $_SESSION['ultimo_sentido'] . ", a_titulo " . $_SESSION['ultimo_sentido'] . "
				" . $limite;

		//LibreriaGeneral::registrarLog("sql_listar_actividades", $sql);

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerCantidad() {
		$conexion = $this->conectar();

		$filtro = "";

		// PARA FILTRAR POR FECHA
		if ($this->filtro['f_fecha'] != '') {
			$filtro .= " AND a_fecha = '" . $this->filtro['f_fecha'] . "'";
		}

		// PARA FILTRAR POR TITULO
		if ($this->filtro['f_titulo'] != '') {
			$filtro .= " AND a_titulo LIKE '%" . $this->filtro['f_titulo'] . "%'";
		}

		// PARA FILTRAR POR CONTENIDO
		if ($this->filtro['f_contenido'] != '') {
			$filtro .= " AND a_contenido LIKE '%" . $this->filtro['f_contenido'] . "%'";
		}

		$query = "SELECT COUNT(a_codigo) AS cantidad
				  FROM " . $this->tabla_actividades . "
				  WHERE a_habilitado <> 3
				  " . $filtro;

		//LibreriaGeneral::registrarLog("query_obtenerCantidad_actividades", $query);

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	public function obtenerUltimoId() {
		return parent::obtenerUltimoCodigo($this->tabla_actividades, 'a_codigo');
	}

	public function obtenerRegistro($a_codigo) {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_actividades . " WHERE a_codigo = " . $a_codigo;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	public function existe($fecha, $hora) {
		$conexion = $this->conectar();

		$query = "SELECT a_codigo
				  FROM " . $this->tabla_actividades . "
				  WHERE a_fecha = '" . $this->formatearFechaMySQL($fecha) . "'
				  AND a_hora = '" . $hora . "'
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ($dato['a_codigo'] != '');
	}

	public function validarDatos($datos) {
		$datos['a_fecha'] = $this->revisarValorFechaAtributo($datos['a_fecha']);

		$datos['a_hora'] = $this->revisarValorAtributo($datos['a_hora']);

		$datos['a_titulo'] = $this->revisarValorAtributo(strip_tags($datos['a_titulo']));

		$datos['a_contenido'] = $this->revisarValorAtributo(strip_tags($datos['a_contenido']));

		return $datos;
	}

	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function noLoModificoOtroUsuario() {
		$conexion = $this->conectar();

		$query = "SELECT a_codigo
				  FROM " . $this->tabla_actividades . "
				  WHERE a_codigo = " . $_SESSION['a_codigo_original'] . "
				  " . $this->adaptarValorStringParaFiltro('a_fecha') . "
				  " . $this->adaptarValorStringParaFiltro('a_hora') . "
				  " . $this->adaptarValorStringParaFiltro('a_titulo') . "
				  " . $this->adaptarValorStringParaFiltro('a_contenido') . "
				  AND a_habilitado = " . $_SESSION['a_habilitado_original'] . "
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($dato['a_codigo']);
	}

	public function insertar($datos) {

		// Se obtiene el siguiente Id
		$datos['a_codigo'] = $this->obtenerUltimoId() + 1;

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO " . $this->tabla_actividades . " (a_codigo, a_fecha, a_hora, a_titulo, a_contenido, a_habilitado)
				  VALUES('" . $datos['a_codigo'] . "', " . $datos['a_fecha'] . ", " . $datos['a_hora'] . ", " . $datos['a_titulo'] . ", " . $datos['a_contenido'] . ", 1);";

		//LibreriaGeneral::registrarLog("query_insertar", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("ALTA", $this->tabla_actividades, "Se ingresa la Actividad # " . $datos['a_codigo']);
		}

		return true;
	}

	public function modificar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "UPDATE " . $this->tabla_actividades . "
				  SET a_fecha = " . $datos['a_fecha'] . ",
					  a_hora = " . $datos['a_hora'] . ",
					  a_titulo = " . $datos['a_titulo'] . ",
					  a_contenido = " . $datos['a_contenido'] . "
				  WHERE a_codigo = " . $datos['a_codigo'];

		//LibreriaGeneral::registrarLog("query_modificar", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_actividades, "Se modifica la Actividad # " . $datos['a_codigo']);
		}

		return true;
	}

	public function eliminar($a_codigo) {

		// Previamente se obtiene la info para auditar
		$info = $this->obtenerRegistro($a_codigo);

		$conexion = $this->conectar();

		$query = "DELETE FROM " . $this->tabla_actividades . " WHERE a_codigo = " . $a_codigo;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_actividades, "Se elimina la Actividad # " . $info['a_codigo']);
		}

		return true;
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

		$query = "UPDATE " . $this->tabla_actividades . " SET a_habilitado = $valor_habilitado WHERE a_codigo = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

}
?>
