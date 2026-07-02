<?php
if (!isset($_SESSION)) {
	session_start();
}

class comprasModel extends ModeloBaseMySQLi {
	
	public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}

	public function listar() {
		$conexion = $this->conectar();

		$filtro = "";
		$limite = "";

		// PARA FILTRAR POR AÑO
		if ($this->filtro['f_anio'] != '' && $this->filtro['f_anio'] != 0) {
			$filtro .= " AND YEAR( comp_fecha ) = '" . $this->filtro['f_anio'] . "'";
		}

		// PARA FILTRAR POR FECHA
		if ($this->filtro['f_fecha'] != '') {
			$filtro .= " AND comp_fecha = '" . $this->filtro['f_fecha'] . "'";
		}

		// PARA FILTRAR POR CONCEPTO Ó CODIGO
		if ($this->filtro['f_concepto'] != '') {
			$filtro .= " AND comp_concepto LIKE '%" . $this->filtro['f_concepto'] . "%'
						 OR comp_codigo LIKE '%" . $this->filtro['f_concepto'] . "%'";
		}

		// PARA LIMITAR EL LISTADO
		if ($this->filtro['rango'] != 0) {
			$limite = " LIMIT " . $this->filtro['inicio'] . ", " . $this->filtro['rango'];
		}

		$sql = "SELECT * FROM " . $this->tabla_compras . "
				WHERE comp_habilitado <> 3
				" . $filtro . "
				ORDER BY comp_fecha " . $_SESSION['ultimo_sentido'] . ", comp_codigo " . $_SESSION['ultimo_sentido'] . "
				" . $limite;

		//LibreriaGeneral::registrarLog("sql_listar_compras", $sql);

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerCantidad() {
		$conexion = $this->conectar();

		$filtro = "";

		// PARA FILTRAR POR AÑO
		if ($this->filtro['f_anio'] != '' && $this->filtro['f_anio'] != 0) {
			$filtro .= " AND YEAR( comp_fecha ) = '" . $this->filtro['f_anio'] . "'";
		}

		// PARA FILTRAR POR FECHA
		if ($this->filtro['f_fecha'] != '') {
			$filtro .= " AND comp_fecha = '" . $this->filtro['f_fecha'] . "'";
		}

		// PARA FILTRAR POR CONCEPTO Ó CODIGO
		if ($this->filtro['f_concepto'] != '') {
			$filtro .= " AND comp_concepto LIKE '%" . $this->filtro['f_concepto'] . "%'
						 OR comp_codigo LIKE '%" . $this->filtro['f_concepto'] . "%'";
		}

		$query = "SELECT COUNT(comp_codigo) AS cantidad
				  FROM " . $this->tabla_compras . "
				  WHERE comp_habilitado <> 3
				  " . $filtro;

		//LibreriaGeneral::registrarLog("query_obtenerCantidad_compras", $query);

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	public function obtenerUltimoId() {
		return parent::obtenerUltimoCodigo($this->tabla_compras, 'comp_codigo');
	}

	public function obtenerRegistro($comp_id) {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_compras . " WHERE comp_id = " . $comp_id;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	public function existe($codigo, $fecha) {
		$conexion = $this->conectar();

		$query = "SELECT comp_codigo
				  FROM " . $this->tabla_compras . "
				  WHERE comp_codigo = '" . $codigo . "'
				  AND comp_fecha = '" . $this->formatearFechaMySQL($fecha) . "'
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ($dato['comp_codigo'] != '');
	}

	public function validarDatos($datos) {
		$datos['comp_proveedor'] = $this->revisarValorAtributo(strip_tags($datos['comp_proveedor']));

		$datos['comp_fecha'] = $this->revisarValorFechaAtributo($datos['comp_fecha']);

		$datos['comp_concepto'] = $this->revisarValorAtributo(strip_tags($datos['comp_concepto']));

		$datos['comp_monto'] = $this->revisarValorAtributo($datos['comp_monto'], "0.00");

		return $datos;
	}

	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function noLoModificoOtroUsuario() {
		$conexion = $this->conectar();

		$query = "SELECT comp_codigo
				  FROM " . $this->tabla_compras . "
				  WHERE comp_codigo = " . $_SESSION['comp_codigo_original'] . "
				  " . $this->adaptarValorStringParaFiltro('comp_proveedor') . "
				  " . $this->adaptarValorStringParaFiltro('comp_fecha') . "
				  " . $this->adaptarValorStringParaFiltro('comp_concepto') . "
				  " . $this->adaptarValorStringParaFiltro('comp_monto') . "
				  AND comp_habilitado = " . $_SESSION['comp_habilitado_original'] . "
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($dato['comp_codigo']);
	}

	public function insertar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO " . $this->tabla_compras . " (comp_codigo, comp_proveedor, comp_fecha, comp_concepto, comp_monto, comp_habilitado)
				  VALUES('" . $datos['comp_codigo'] . "', " . $datos['comp_proveedor'] . ", " . $datos['comp_fecha'] . ", " . $datos['comp_concepto'] . ", " . $datos['comp_monto'] . ", 1);";

		//LibreriaGeneral::registrarLog("query_insertar", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("ALTA", $this->tabla_compras, "Se ingresa la Orden de Compra # " . $datos['comp_codigo']);
		}

		return true;
	}

	public function modificar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "UPDATE " . $this->tabla_compras . "
				  SET comp_codigo = " . $datos['comp_codigo'] . ",
					  comp_proveedor = " . $datos['comp_proveedor'] . ",
					  comp_fecha = " . $datos['comp_fecha'] . ",
					  comp_concepto = " . $datos['comp_concepto'] . ",
					  comp_monto = " . $datos['comp_monto'] . "
				  WHERE comp_id = " . $datos['comp_id'] . "
				 ";

		//LibreriaGeneral::registrarLog("query_modificar", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_compras, "Se modifica la Orden de Compra # " . $datos['comp_codigo']);
		}

		return true;
	}

	public function eliminar($comp_id) {

		// Previamente se obtiene la info para auditar
		$info = $this->obtenerRegistro($comp_id);

		$conexion = $this->conectar();

		$query = "DELETE FROM " . $this->tabla_compras . " WHERE comp_id = " . $comp_id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_compras, "Se elimina la Orden de Compra # " . $info['comp_codigo']);
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

		$query = "UPDATE " . $this->tabla_compras . " SET comp_habilitado = $valor_habilitado WHERE comp_id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

}
?>
