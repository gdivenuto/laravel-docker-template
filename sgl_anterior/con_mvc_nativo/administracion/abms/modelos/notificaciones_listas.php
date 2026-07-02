<?php
if (!isset($_SESSION)) {
	session_start();
}

class notificacionesListasModel extends ModeloBaseMySQLi {

	public function conectar() {
		// Se conecta según el Id del sistema
		return parent::conectarDB(1);
	}

	public function listar() {

		$conexion = $this->conectar();

		$filtro = "";
		$limite = "";

		if (isset($this->filtro['f_nombre_descripcion']) && $this->filtro['f_nombre_descripcion'] != '') {

			$criterio_ingresado = str_replace(" ", "%", $this->filtro['f_nombre_descripcion']);

			$filtro .= " AND (name LIKE '%" . $criterio_ingresado . "%' OR
							  description LIKE '%" . $criterio_ingresado . "%')";
		}

		// Para limitar el listado
		if ($this->filtro['rango'] != 0) {
			$limite = " LIMIT " . $this->filtro['inicio'] . ", " . $this->filtro['rango'];
		}

		$sql = "SELECT * FROM " . $this->tabla_listas_notificaciones . "
				WHERE active <> 3
				" . $filtro . "
				ORDER BY " . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'];

		// Se agrega el límite a la query
		$sql .= $limite;

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerCantidad() {

		$conexion = $this->conectar();

		$filtro = "";

		if (isset($this->filtro['f_nombre_descripcion']) && $this->filtro['f_nombre_descripcion'] != '') {

			$criterio_ingresado = str_replace(" ", "%", $this->filtro['f_nombre_descripcion']);

			$filtro .= " AND (name LIKE '%" . $criterio_ingresado . "%' OR
							  description LIKE '%" . $criterio_ingresado . "%')";
		}

		$query = "SELECT COUNT(id) AS cantidad
				  FROM " . $this->tabla_listas_notificaciones . "
				  WHERE active <> 3
				  " . $filtro;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	public function obtenerRegistro($id_lista) {

		$conexion = $this->conectar();

		$query = "SELECT * FROM " . $this->tabla_listas_notificaciones . " WHERE id = " . $id_lista;

		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	public function validarDatos($datos) {

		$datos['name'] = $this->revisarValorAtributo($datos['name']);

		$datos['description'] = $this->revisarValorAtributo($datos['description']);

		return $datos;
	}

	public function insertar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO " . $this->tabla_listas_notificaciones . " (
					id,
					name,
					description,
					active)
				  VALUES(
				  	" . $datos['id'] . ",
					" . $datos['name'] . ",
					" . $datos['description'] . ",
					0);";
		// 16/11/2020 XXXX
		// el cero en el campo active define a la lista PRIVADA, ESTABA ANTES EN 1 (PÚBLICA)

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion(
				"ALTA",
				$this->tabla_listas_notificaciones,
				"Se ingresa la Lista de distribucion: " . LibreriaGeneral::eliminarComillaSimple($datos['name']));
		}

		return true;
	}

	public function modificar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "UPDATE " . $this->tabla_listas_notificaciones . "
				  SET name = " . $datos['name'] . ",
					  description = " . $datos['description'] . "
				  WHERE id = " . $datos['id'];

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion(
				"MODIFICA",
				$this->tabla_listas_notificaciones,
				"Se modifica la Lista de distribucion: " . LibreriaGeneral::eliminarComillaSimple($datos['name']));
		}

		return true;
	}

	public function eliminar($id_lista) {

		// Previamente se obtiene la info para auditar
		$info = $this->obtenerRegistro($id_lista);

		$conexion = $this->conectar();

		// luego se elimina la Lista de distribucion
		$query = "DELETE FROM " . $this->tabla_listas_notificaciones . " WHERE id = " . $id_lista;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion(
				"BAJA",
				$this->tabla_listas_notificaciones,
				"Se elimina la Lista de distribucion: " . LibreriaGeneral::eliminarComillaSimple($info['name']));
		}

		return true;
	}

}
