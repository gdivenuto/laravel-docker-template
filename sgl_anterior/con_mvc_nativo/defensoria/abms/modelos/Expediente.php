<?php
if (!isset($_SESSION))
	session_start();

class ExpedienteModel extends ModeloBaseMySQLi {

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
			$filtro .= " AND E.numero = '" . $this->filtro['f_numero'] . "%'";
		}
		if (isset($this->filtro['f_presentante']) && $this->filtro['f_presentante'] != '0') {
			$filtro .= " AND E.presentante_id = " . $this->filtro['f_presentante'];
		}
		if (isset($this->filtro['f_tipo_proceso']) && $this->filtro['f_tipo_proceso'] != '0') {
			$filtro .= " AND E.tipo_proceso_id = " . $this->filtro['f_tipo_proceso'];
		}
		if (isset($this->filtro['f_fecha']) && $this->filtro['f_fecha'] != '') {
			$filtro .= " AND E.fecha = '" . $this->filtro['f_fecha'] . "'";
		}
		if (isset($this->filtro['f_estado']) && $this->filtro['f_estado'] != '0') {
			$filtro .= " AND E.estado = '" . $this->filtro['f_estado'] . "'";
		}

		return $filtro;
	}

	/**
	 * Se obtiene el listado, en base a un criterio determinado en la query
	 * @return array $datos
	 */
	public function listar() {
		$conexion = $this->conectar();

		$registro_inicial = (isset($this->filtro['inicio']) && $this->filtro['inicio'] != '') 
			? $this->filtro['inicio'] 
			: 0;

		$sql = "SELECT 
					E.*,
					P.nombre AS presentador_nombre, 
					P.apellido AS presentador_apellido,
					TP.nombre AS tipo_proceso_nombre,
					U.codigo_usuario
				FROM " . $this->tabla_def_expedientes . " AS E
				LEFT JOIN " . $this->tabla_def_presentadores . " AS P
					ON P.id = E.presentante_id
		        LEFT JOIN " . $this->tabla_def_tipos_proceso . " AS TP
					ON TP.id = E.tipo_proceso_id
				LEFT JOIN " . $this->tabla_usuarios . " AS U
					ON U.id_usuario = E.id_usuario
				WHERE E.fecha IS NOT NULL
			    " . $this->getFiltro() . "
			    ORDER BY E." . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
				LIMIT " . $registro_inicial . ", " . $this->filtro['rango'];

		//LibreriaGeneral::registrarLog("sql_listar", $sql, '.sql');

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);
		//LibreriaGeneral::registrarLog("datos_listar", $datos);
		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene la cantidad de registros encontrados, en base a un criterio determinado en la query
	 */
	public function obtenerCantidad() {
		$conexion = $this->conectar();

		$query = "SELECT COUNT(E.numero) AS cantidad 
				  FROM " . $this->tabla_def_expedientes . " AS E
				  WHERE E.fecha IS NOT NULL
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
		
		$query = "SELECT MAX(numero) AS ultimo_codigo FROM " . $this->tabla_def_expedientes;
		
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

		$sql = "SELECT * FROM " . $this->tabla_def_expedientes . " WHERE numero = " . $numero;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	public function obtenerRegistroDetallado($numero) {
		
		$conexion = $this->conectar();

		$sql = "SELECT 
					E.*,
					P.nombre AS presentador_nombre, 
					P.apellido AS presentador_apellido,
					P.dni AS presentador_dni,
					P.localidad AS presentador_localidad,
					P.codigo_postal AS presentador_codigo_postal,
					P.direccion_calle AS presentador_direccion_calle,
					P.direccion_numero AS presentador_direccion_numero,
					P.direccion_piso AS presentador_direccion_piso,
					P.direccion_departamento AS presentador_direccion_departamento,
					P.tel_fijo_cod_area AS presentador_tel_fijo_cod_area,
					P.tel_fijo_numero AS presentador_tel_fijo_numero,
					P.movil_cod_area AS presentador_movil_cod_area,
					P.movil_numero AS presentador_movil_numero,
					TP.nombre AS tipo_proceso_nombre	
				FROM " . $this->tabla_def_expedientes . " AS E
				LEFT JOIN " . $this->tabla_def_presentadores . " AS P
					ON P.id = E.presentante_id
		        LEFT JOIN " . $this->tabla_def_tipos_proceso . " AS TP
					ON TP.id = E.tipo_proceso_id
			    WHERE E.numero = " . $numero;

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

		$datos['presentante_id'] = $this->revisarValorAtributo($datos['presentante_id'], 0);

		$datos['tipo_proceso_id'] = $this->revisarValorAtributo($datos['tipo_proceso_id'], 0);

		$datos['estado'] = $this->revisarValorAtributo($datos['estado']);

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
				  FROM " . $this->tabla_def_expedientes . "
				  WHERE numero = " . $_SESSION['numero_original'] . "
				  " . $this->adaptarValorStringParaFiltro('presentante_id') . "
				  " . $this->adaptarValorStringParaFiltro('tipo_proceso_id') . "
				  " . $this->adaptarValorStringParaFiltro('estado') . "
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

		$query = "INSERT INTO " . $this->tabla_def_expedientes . "
					(presentante_id, 
					 tipo_proceso_id, 
					 estado,
					 fecha, 
					 texto,
					 id_usuario
					)
				  VALUES(" . $datos['presentante_id'] . ",
				  		 " . $datos['tipo_proceso_id'] . ",
				  		 " . $datos['estado'] . ",
				  		 " . $datos['fecha'] . ",
						 " . $datos['texto'] . ",
						 " . $_SESSION['id_usuario'] . "
						)";

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"ALTA",
				$this->tabla_def_expedientes,
				$this->obtenerUltimoId(),
				"Se ingresa un expediente"
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

		$query = "UPDATE " . $this->tabla_def_expedientes . "
				  SET presentante_id = " . $datos['presentante_id'] . ",
				  	  tipo_proceso_id = " . $datos['tipo_proceso_id'] . ",
				  	  estado = " . $datos['estado'] . ",
				  	  fecha = " . $datos['fecha'] . ",
					  texto = " . $datos['texto'] . ",
					  id_usuario = " . $_SESSION['id_usuario'] . "
				  WHERE numero = " . $datos['numero'];

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"MODIFICA",
				$this->tabla_def_expedientes,
				$datos['numero'],
				"Se modifica el expediente"
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

		$query = "DELETE FROM " . $this->tabla_def_expedientes . " WHERE numero = " . $numero;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"BAJA",
				$this->tabla_def_expedientes,
				$numero,
				"Se elimina un expediente numero " . $numero
			);
		}
		return true;
	}

}
?>
