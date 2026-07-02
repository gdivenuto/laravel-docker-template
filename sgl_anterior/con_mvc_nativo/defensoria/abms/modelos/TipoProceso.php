<?php
if (!isset($_SESSION))
	session_start();

class TipoProcesoModel extends ModeloBaseMySQLi {
	
	public function __construct() {
		parent::__construct();
	}

	public function conectar() {
		// Se conecta según el Id del sistema
		return parent::conectarDB(6);
	}

	private function getFiltro() {

		if (isset($this->filtro['valor_buscado']) && $this->filtro['valor_buscado'] != '')
			return " AND nombre LIKE '%" . str_replace(" ", "%", $this->filtro['valor_buscado']) . "%'";

		return "";
	}

	/**
	 * Se obtiene el listado de rubros, en base a un criterio determinado en la query
	 * @return array $datos
	 */
	public function listar($id = 0) {
		$conexion = $this->conectar();

		$registro_inicial = ($this->filtro['inicio'] != '') ? $this->filtro['inicio'] : 0;

		$sql = "SELECT * FROM " . $this->tabla_def_tipos_proceso . "
			    WHERE habilitado <> 3
			    " . $this->getFiltro() . "
			    ORDER BY " . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
			    LIMIT " . $registro_inicial . ", " . $this->filtro['rango'];

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene la cantidad de registros encontrados, en base a un criterio determinado en la query
	 * @see ModeloBaseMysql::obtenerCantidad()
	 */
	public function obtenerCantidad() {
		$conexion = $this->conectar();

		$query = "SELECT COUNT(id) AS cantidad
				  FROM " . $this->tabla_def_tipos_proceso . "
				  WHERE habilitado <> 3
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
		
		$query = "SELECT MAX(id) AS ultimo_codigo FROM " . $this->tabla_def_tipos_proceso;
		
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

		$query = 'SELECT * FROM ' . $this->tabla_def_tipos_proceso . ' WHERE id = ' . $id;

		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerFila($resultado);

		return $registro;
	}

	/**
	 * Se obtiene el listado de rubros habilitados
	 *
	 * @return array $datos
	 */
	public function listarHabilitados() {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_def_tipos_proceso . " WHERE habilitado = 1 ORDER BY nombre";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se verifica si ya existe un registro con un nombre determinado
	 *
	 * @param array $datos
	 * @return boolean
	 */
	public function existe($datos) {
		$conexion = $this->conectar();

		$query = "SELECT id
				  FROM " . $this->tabla_def_tipos_proceso . "
				  WHERE nombre = '" . $datos['nombre'] . "'
				 ";
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return ($dato['id']);
	}

	/**
	 * Se verifica si el registro no ha sido modificado por otro usuario
	 *
	 * @return boolean
	 */
	public function noLoModificoOtroUsuario() {
		$filtro_nombre = $this->adaptarValorStringParaFiltro('nombre');

		$conexion = $this->conectar();

		$query = "SELECT id
				  FROM " . $this->tabla_def_tipos_proceso . "
				  WHERE id = " . $_SESSION['id_original'] . "
				  " . $filtro_nombre . "
				  AND habilitado = " . $_SESSION['habilitado_original'] . "
				 ";

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($datos['id']);
	}

	/**
	 * Se ingresa la informacion de un rubro nuevo
	 *
	 * @param array $datos
	 * @return boolean
	 */
	public function insertar($datos) {

		$conexion = $this->conectar();

		$datos['nombre'] = $this->revisarValorAtributo($datos['nombre']);

		$query = "INSERT INTO " . $this->tabla_def_tipos_proceso . " (nombre, habilitado)
				  VALUES( " . $datos['nombre'] . ", 1)";

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"ALTA",
				$this->tabla_def_tipos_proceso,
				$this->obtenerUltimoId(),
				str_replace("'", "", "Se ingresa el Tipo de Proceso " . $datos['nombre'])
			);
		}
		return true;
	}

	/**
	 * Se modifica la informacion de un rubro determinado
	 *
	 * @param array $datos
	 * @return boolean
	 */
	public function modificar($datos) {
		$conexion = $this->conectar();

		$datos['nombre'] = $this->revisarValorAtributo($datos['nombre']);

		$query = "UPDATE " . $this->tabla_def_tipos_proceso . "
				  SET nombre = " . $datos['nombre'] . "
				  WHERE id = " . $datos['id'];

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"MODIFICA",
				$this->tabla_def_tipos_proceso,
				$datos['id'],
				str_replace("'", "", "Se modifica el Tipo de Proceso " . $datos['nombre'])
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

		$query = "DELETE FROM " . $this->tabla_def_tipos_proceso . " WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"BAJA",
				$this->tabla_def_tipos_proceso,
				$id,
				"Se elimina el Tipo de Proceso, Id # " . $id
			);
		}
		return true;
	}

	/**
	 * Se modifica el estado habilitado|deshabilitado del Rubro
	 *
	 * @param  integer $id
	 * @param  integer $habilitado
	 * @return boolean true|false
	 */
	public function modificarEstado($id, $habilitado) {
		$conexion = $this->conectar();

		$valor_habilitado = ($habilitado == 1) ? 0 : 1;

		$query = "UPDATE " . $this->tabla_def_tipos_proceso . " 
				  SET habilitado = " . $valor_habilitado . " 
				  WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

}
?>