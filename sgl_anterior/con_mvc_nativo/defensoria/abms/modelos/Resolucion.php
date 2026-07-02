<?php
if (!isset($_SESSION))
	session_start();

class ResolucionModel extends ModeloBaseMySQLi {

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
			$filtro .= " AND RES.numero = '" . $this->filtro['f_numero'] . "%'";
		}
		if (isset($this->filtro['f_remitente']) && $this->filtro['f_remitente'] != '0') {
			$filtro .= " AND RES.remitente_id = " . $this->filtro['f_remitente'];
		}
		if (isset($this->filtro['f_fecha']) && $this->filtro['f_fecha'] != '') {
			$filtro .= " AND RES.fecha = '" . $this->filtro['f_fecha'] . "'";
		}
		
		return $filtro;
	}

	/**
	 * Se obtiene el listado de Clientes, en base a un criterio determinado en la query
	 * @return array $datos
	 */
	public function listar() {
		$conexion = $this->conectar();

		$registro_inicial = (isset($this->filtro['inicio']) && $this->filtro['inicio'] != '') 
			? $this->filtro['inicio'] 
			: 0;

		$sql = "SELECT 
					RES.*,
					REM.nombre AS remitente_nombre, 
					REM.apellido AS remitente_apellido
				FROM " . $this->tabla_def_resoluciones . " AS RES
				LEFT JOIN " . $this->tabla_def_remitentes . " AS REM
					ON REM.id = RES.remitente_id
			    WHERE RES.fecha IS NOT NULL
			    " . $this->getFiltro() . "
			    ORDER BY RES." . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
				LIMIT " . $registro_inicial . ", " . $this->filtro['rango'];

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

		$query = "SELECT COUNT(RES.numero) AS cantidad 
				  FROM " . $this->tabla_def_resoluciones . " AS RES
				  WHERE RES.fecha IS NOT NULL
				  " . $this->getFiltro();

		//LibreriaGeneral::registrarLog("query_obtenerCantidad", $query, '.sql');

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	/**
	 * Se obtiene el ultimo Id registrado en la DB
	 */
	public function obtenerUltimoId() {
		$conexion = $this->conectar();
		
		$query = "SELECT MAX(numero) AS ultimo_codigo FROM " . $this->tabla_def_resoluciones;
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerFila($resultado);
		
		$ultimo_codigo = ($dato['ultimo_codigo'] != null) ? $dato['ultimo_codigo'] : 0;
				
		$this->desconectar($conexion);
		
		return $ultimo_codigo;
	}

	/**
	 * Se obtiene la informacion de un registro determinado por su Id
	 *
	 * @param integer $numero
	 * @return array $registro
	 */
	public function obtenerRegistro($numero = 0) {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_def_resoluciones . " WHERE numero = " . $numero;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	public function obtenerRegistroDetallado($numero) {
		
		$conexion = $this->conectar();

		$sql = "SELECT 
					RES.*,
					REM.nombre AS remitente_nombre, 
					REM.apellido AS remitente_apellido,
					REM.dni AS remitente_dni,
					REM.localidad AS remitente_localidad,
					REM.codigo_postal AS remitente_codigo_postal,
					REM.direccion_calle AS remitente_direccion_calle,
					REM.direccion_numero AS remitente_direccion_numero,
					REM.direccion_piso AS remitente_direccion_piso,
					REM.direccion_departamento AS remitente_direccion_departamento,
					REM.tel_fijo_cod_area AS remitente_tel_fijo_cod_area,
					REM.tel_fijo_numero AS remitente_tel_fijo_numero,
					REM.movil_cod_area AS remitente_movil_cod_area,
					REM.movil_numero AS remitente_movil_numero
				FROM " . $this->tabla_def_resoluciones . " AS RES
				LEFT JOIN " . $this->tabla_def_remitentes . " AS REM
					ON REM.id = RES.remitente_id
			    WHERE RES.numero = " . $numero;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	/**
	 * Se validan determinados datos para utilizar en las querys
	 *
	 * @param array $datos
	 * @return array
	 */
	public function validarDatos($datos) {
		$datos['remitente_id'] = $this->revisarValorAtributo($datos['remitente_id'], 0);
		$datos['fecha'] = $this->revisarValorFechaAtributo($datos['fecha']);
		$datos['texto'] = $this->revisarValorAtributo($datos['texto']);
		return $datos;
	}

	/**
	 * Se verifica si el registro no ha sido modificado por otro usuario
	 *
	 * @return boolean
	 */
	public function noLoModificoOtroUsuario() {

		$conexion = $this->conectar();

		$query = "SELECT numero
				  FROM " . $this->tabla_def_resoluciones . "
				  WHERE numero = " . $_SESSION['numero_original'] . "
				  " . $this->adaptarValorStringParaFiltro('remitente_id') . "
				  " . $this->adaptarValorStringParaFiltro('fecha') . "
				  " . $this->adaptarValorStringParaFiltro('texto');

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($datos['numero']);
	}

	/**
	 * Se ingresa la informacion de un Cliente nuevo
	 *
	 * @param array $datos
	 * @return boolean
	 */
	public function insertar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO " . $this->tabla_def_resoluciones . "
					(remitente_id, 
					 fecha, 
					 texto
					)
				  VALUES(" . $datos['remitente_id'] . ",
				  		 " . $datos['fecha'] . ",
						 " . $datos['texto'] . "
						)";

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"ALTA",
				$this->tabla_def_resoluciones,
				$this->obtenerUltimoId(),
				"Se ingresa una resolucion"
			);
		}
		return true;
	}

	/**
	 * Se modifica la informacion de un cliente determinado
	 *
	 * @param array $datos
	 * @return boolean
	 */
	public function modificar($datos) {
		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "UPDATE " . $this->tabla_def_resoluciones . "
				  SET remitente_id = " . $datos['remitente_id'] . ",
				  	  fecha = " . $datos['fecha'] . ",
					  texto = " . $datos['texto'] . "
				  WHERE numero = " . $datos['numero'];

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"MODIFICA",
				$this->tabla_def_resoluciones,
				$datos['numero'],
				"Se modifica la resolucion"
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

		$query = "DELETE FROM " . $this->tabla_def_resoluciones . " WHERE numero = " . $numero;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"BAJA",
				$this->tabla_def_resoluciones,
				$numero,
				"Se elimina una resolucion numero " . $numero
			);
		}
		return true;
	}

}
?>
