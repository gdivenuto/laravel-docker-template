<?php
if (!isset($_SESSION)) {
	session_start();
}

class notificacionesGruposModel extends ModeloBaseMySQLi {

	public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}

	public function listar() {

		$conexion = $this->conectar();

		$filtro = "";
		$limite = "";

		if (isset($this->filtro['f_descripcion']) && $this->filtro['f_descripcion'] != '') {

			$criterio_ingresado = str_replace(" ", "%", $this->filtro['f_descripcion']);

			$filtro .= " WHERE descripcion LIKE '%" . $criterio_ingresado . "%'";
		}

		// Para limitar el listado
		if ($this->filtro['rango'] != 0) {
			$limite = " LIMIT " . $this->filtro['inicio'] . ", " . $this->filtro['rango'];
		}

		$sql = "SELECT * FROM " . $this->tabla_lista_notificaciones_grupos . $filtro . "
				ORDER BY " . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
				" . $limite;

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerCantidad() {

		$conexion = $this->conectar();

		$filtro = "";

		if (isset($this->filtro['f_descripcion']) && $this->filtro['f_descripcion'] != '') {

			$criterio_ingresado = str_replace(" ", "%", $this->filtro['f_descripcion']);

			$filtro .= " WHERE descripcion LIKE '%" . $criterio_ingresado . "%'";
		}

		$query = "SELECT COUNT(id) AS cantidad FROM " . $this->tabla_lista_notificaciones_grupos . $filtro;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	public function obtenerRegistro($id_lista) {

		$conexion = $this->conectar();

		$query = "SELECT * FROM " . $this->tabla_lista_notificaciones_grupos . " WHERE id = " . $id_lista;

		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	public function insertar($datos) {

		$conexion = $this->conectar();

		$datos['descripcion'] = $this->revisarValorAtributo($datos['descripcion']);

		$query = "INSERT INTO " . $this->tabla_lista_notificaciones_grupos . " (
					id,
					descripcion,
					phplist_ids)
				  VALUES(
				  	" . $datos['id'] . ",
					" . $datos['descripcion'] . ",
				   '" . $datos['phplist_ids'] . "');";

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion(
				"ALTA",
				$this->tabla_lista_notificaciones_grupos,
				"Se ingresa el Grupo de distribucion: " . LibreriaGeneral::eliminarComillaSimple($datos['descripcion']));
		}

		return true;
	}

	public function modificar($datos) {

		$conexion = $this->conectar();

		$datos['descripcion'] = $this->revisarValorAtributo($datos['descripcion']);

		$query = "UPDATE " . $this->tabla_lista_notificaciones_grupos . "
				  SET descripcion = " . $datos['descripcion'] . ",
				  	  phplist_ids = '" . $datos['phplist_ids'] . "'
				  WHERE id = " . $datos['id'];

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion(
				"MODIFICA",
				$this->tabla_lista_notificaciones_grupos,
				"Se modifica el Grupo de distribucion: " . LibreriaGeneral::eliminarComillaSimple($datos['descripcion']));
		}

		return true;
	}

	public function eliminar($id_lista) {

		// Previamente se obtiene la info para auditar
		$info = $this->obtenerRegistro($id_lista);

		$conexion = $this->conectar();

		// luego se elimina el Grupo de distribucion
		$query = "DELETE FROM " . $this->tabla_lista_notificaciones_grupos . " WHERE id = " . $id_lista;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion(
				"BAJA",
				$this->tabla_lista_notificaciones_grupos,
				"Se elimina el Grupo de distribucion: " . LibreriaGeneral::eliminarComillaSimple($info['descripcion']));
		}

		return true;
	}

	public function obtenerListas() {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_listas_notificaciones;

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerNombreLista($id_lista) {

		$conexion = $this->conectar();

		$sql = "SELECT name FROM " . $this->tabla_listas_notificaciones . " WHERE id = " . $id_lista;

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['name'];
	}
}
