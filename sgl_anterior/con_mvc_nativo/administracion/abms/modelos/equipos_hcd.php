<?php
if (!isset($_SESSION)) {
	session_start();
}

class equiposHcdModel extends ModeloBaseMySQLi {

	public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}

	public function listar() {
		$conexion = $this->conectar();

		$filtro = "";
		$limite = "";

		// Para filtrar por área
		if (($this->filtro['f_cod_area'] != 0) && ($this->filtro['f_cod_area'] != '')) {
			$filtro .= " AND cod_area = '" . $this->filtro['f_cod_area'] . "'";
		}

		// Para filtrar por responsable
		if (($this->filtro['f_cod_responsable'] != 0) && ($this->filtro['f_cod_responsable'] != '')) {
			$filtro .= " AND cod_responsable = " . $this->filtro['f_cod_responsable'];
		}

		// Para filtrar por nombre netbios
		if ($this->filtro['f_nombre_netbios'] != '') {
			$filtro .= " AND nombre_netbios LIKE '%" . $this->filtro['f_nombre_netbios'] . "%'";
		}

		// Para filtrar por dirección MAC
		if ($this->filtro['f_direccion_mac'] != '') {
			// Se reemplazan los dos puntos por el guión, en caso de poseer la MAC respectiva
			$filtro .= " AND direccion_mac LIKE '%" . str_replace(':', '-', $this->filtro['f_direccion_mac']) . "%'";
		}

		// PARA LIMITAR EL LISTADO
		if ($this->filtro['rango'] != 0) {
			$limite = "LIMIT " . $this->filtro['inicio'] . ", " . $this->filtro['rango'] . "";
		}

		$sql = "SELECT * FROM " . $this->tabla_pc_en_red . "
				WHERE habilitado <> 3
				" . $filtro . "
				ORDER BY " . $this->filtro['campo_orden'] . " " . $this->filtro['sentido'] . "
		       " . $limite;

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene la cantidad de registros devueltos en base a un criterio de búsqueda determinado
	 * @param array $filtro, contiene el criterio de busqueda
	 * @return int $dato['cantidad'], cantidad de registros devueltos por la consulta
	 */
	public function obtenerCantidad() {
		$conexion = $this->conectar();

		$filtro = "";

		// PARA FILTRAR POR AREA
		if (($this->filtro['f_cod_area'] != 0) && ($this->filtro['f_cod_area'] != '')) {
			$filtro .= " AND cod_area = '" . $this->filtro['f_cod_area'] . "'";
		}

		// PARA FILTRAR POR RESPONSABLE
		if (($this->filtro['f_cod_responsable'] != 0) && ($this->filtro['f_cod_responsable'] != '')) {
			$filtro .= " AND cod_responsable = " . $this->filtro['f_cod_responsable'];
		}

		// Para filtrar por nombre netbios
		if ($this->filtro['f_nombre_netbios'] != '') {
			$filtro .= " AND nombre_netbios LIKE '%" . $this->filtro['f_nombre_netbios'] . "%'";
		}

		// Para filtrar por dirección MAC
		if ($this->filtro['f_direccion_mac'] != '') {
			// Se reemplazan los dos puntos por el guión, en caso de poseer la MAC respectiva
			$filtro .= " AND direccion_mac LIKE '%" . str_replace(':', '-', $this->filtro['f_direccion_mac']) . "%'";
		}

		$sql = "SELECT COUNT(id) AS cantidad
				FROM " . $this->tabla_pc_en_red . "
				WHERE habilitado <> 3
				" . $filtro;

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['cantidad'];
	}

	public function obtenerRegistro($id) {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_pc_en_red . " WHERE id = " . $id;

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se verifica si está disponible el Nombre Netbios
	 * @param  string $nombre_netbios   Nombre Netbios
	 * @return boolean
	 */
	public function estaDisponibleNombreNetbios($nombre_netbios) {

		$conexion = $this->conectar();

		$sql = "SELECT nombre_netbios FROM " . $this->tabla_pc_en_red . " WHERE nombre_netbios = '" . $nombre_netbios . "'";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['nombre_netbios'];
	}

	//	SE VERIFICA LA EXISTENCIA DE LA MAC
	public function existeMAC($direccion_mac) {
		$conexion = $this->conectar();

		$query = "SELECT direccion_mac FROM " . $this->tabla_pc_en_red . " WHERE direccion_mac = '" . $direccion_mac . "'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ($dato['direccion_mac'] != '');
	}

	//	SE VERIFICA LA EXISTENCIA DE UN NOMBRE NETBIOS
	public function existeNetBios($nombre_netbios) {
		$conexion = $this->conectar();

		$query = "SELECT nombre_netbios  FROM " . $this->tabla_pc_en_red . " WHERE nombre_netbios = '" . $nombre_netbios . "'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ($dato['nombre_netbios'] != '');
	}

	public function formatearFechaArchivoConfiguracion($fecha) {
		if ($fecha) {
			if ($fecha != '0000-00-00') {
				// SE DIVIDE LA FECHA RECIBIDA POR CADA OCURRENCIA DEL GUION
				$fec_partes = explode("-", $fecha);

				// SE LE DA EL FORMATO DD/MM/AA A LA FECHA
				$fecha_formateada = $fec_partes[2] . '/' . $fec_partes[1] . '/' . substr($fec_partes[0], -2);

				return $fecha_formateada;
			} else {
				return '';
			}

		} else {
			return '';
		}

	}

	public function validarDatos($datos) {
		$datos['nombre_netbios'] = $this->revisarValorAtributo($datos['nombre_netbios']);

		$datos['direccion_mac'] = $this->revisarValorAtributo($datos['direccion_mac']);

		$datos['ip'] = $this->revisarValorAtributo($datos['ip']);

		$datos['nameserver'] = $this->revisarValorAtributo($datos['nameserver']);

		$datos['wins'] = $this->revisarValorAtributo($datos['wins']);

		$datos['gateway'] = $this->revisarValorAtributo($datos['gateway']);

		$datos['nro_inventario'] = $this->revisarValorAtributo($datos['nro_inventario']);

		$datos['fecha_alta'] = $this->revisarValorFechaAtributo($datos['fecha_alta']);

		$datos['fecha_caducidad'] = $this->revisarValorFechaAtributo($datos['fecha_caducidad']);

		$datos['comentario'] = $this->revisarValorAtributo($datos['comentario']);

		$datos['cod_area'] = $this->revisarValorAtributo($datos['cod_area']);

		$datos['cod_responsable'] = $this->revisarValorAtributo($datos['cod_responsable']);

		$datos['observaciones'] = $this->revisarValorAtributo($datos['observaciones']);

		return $datos;
	}

	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function noLoModificoOtroUsuario() {
		$conexion = $this->conectar();

		$query = "SELECT id
				  FROM " . $this->tabla_pc_en_red . "
				  WHERE id = " . $_SESSION['id_original'] . "
				  " . $this->adaptarValorStringParaFiltro('nombre_netbios') . "
				  " . $this->adaptarValorStringParaFiltro('direccion_mac') . "
				  " . $this->adaptarValorStringParaFiltro('ip') . "
				  " . $this->adaptarValorStringParaFiltro('nameserver') . "
				  " . $this->adaptarValorStringParaFiltro('wins') . "
				  " . $this->adaptarValorStringParaFiltro('gateway') . "
				  " . $this->adaptarValorStringParaFiltro('nro_inventario') . "
				  " . $this->adaptarValorStringParaFiltro('fecha_alta') . "
				  " . $this->adaptarValorStringParaFiltro('fecha_caducidad') . "
				  " . $this->adaptarValorStringParaFiltro('comentario') . "
				  " . $this->adaptarValorStringParaFiltro('cod_area') . "
				  " . $this->adaptarValorStringParaFiltro('cod_responsable') . "
				  " . $this->adaptarValorStringParaFiltro('observaciones') . "
				  AND habilitado = " . $_SESSION['habilitado_original'];

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($dato['id']);
	}

	public function insertar($datos) {
		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO " . $this->tabla_pc_en_red . "(nombre_netbios, direccion_mac, ip, nameserver, wins, gateway, nro_inventario, fecha_alta, fecha_caducidad, comentario, cod_area, cod_responsable, observaciones, habilitado)
				  VALUES(" . $datos['nombre_netbios'] . ",
						 " . $datos['direccion_mac'] . ",
						 " . $datos['ip'] . ",
						 " . $datos['nameserver'] . ",
						 " . $datos['wins'] . ",
						 " . $datos['gateway'] . ",
						 " . $datos['nro_inventario'] . ",
						 " . $datos['fecha_alta'] . ",
						 " . $datos['fecha_caducidad'] . ",
						 " . $datos['comentario'] . ",
						 " . $datos['cod_area'] . ",
						 " . $datos['cod_responsable'] . ",
						 " . $datos['observaciones'] . ",
						 1
						)";

		// SI NO SE PUEDE EJECUTAR LA QUERY
		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);
		// Se audita
		$this->auditarEnAdministracion("ALTA", $this->tabla_pc_en_red, "Se ingresa el Equipo " . LibreriaGeneral::eliminarComillaSimple($datos['nombre_netbios']) . " a la red.");

		return true;
	}

	public function modificar($datos) {
		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "UPDATE " . $this->tabla_pc_en_red . "
				  SET nombre_netbios = " . $datos['nombre_netbios'] . ",
					  direccion_mac = " . $datos['direccion_mac'] . ",
					  ip = " . $datos['ip'] . ",
					  nameserver = " . $datos['nameserver'] . ",
					  wins = " . $datos['wins'] . ",
					  gateway = " . $datos['gateway'] . ",
					  nro_inventario = " . $datos['nro_inventario'] . ",
					  fecha_alta = " . $datos['fecha_alta'] . ",
					  fecha_caducidad = " . $datos['fecha_caducidad'] . ",
					  comentario = " . $datos['comentario'] . ",
					  cod_area = " . $datos['cod_area'] . ",
					  cod_responsable = " . $datos['cod_responsable'] . ",
					  observaciones = " . $datos['observaciones'] . "
				  WHERE id = " . $datos['id'];

		//LibreriaGeneral::registrarLog("query_modificar", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);
		// Se audita
		$this->auditarEnAdministracion("MODIFICA", $this->tabla_pc_en_red, "Se modifica el Equipo " . LibreriaGeneral::eliminarComillaSimple($datos['nombre_netbios']) . " de la red.");

		return true;
	}

	public function eliminar($id) {
		// Previamente se obtiene la info para auditar
		$info = $this->obtenerRegistro($id);

		$conexion = $this->conectar();

		$query = "DELETE FROM " . $this->tabla_pc_en_red . " WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);
		// Se audita
		$this->auditarEnAdministracion("BAJA", $this->tabla_pc_en_red, "Se elimina el Equipo " . $info['nombre_netbios'] . " de la red.");

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

		$query = "UPDATE " . $this->tabla_pc_en_red . " SET habilitado = $valor_habilitado WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

	public function obtenerAreasActivas() {
		$conexion = $this->conectar();

		$sql = "SELECT ca_id AS cod_area, ca_nombre AS nombre_area
				FROM " . $this->tabla_codareas . "
				WHERE ca_habilitado = 1
				AND ca_id <> '01000000'";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerNombreArea($id) {
		$conexion = $this->conectar();

		$sql = "SELECT ca_nombre AS nombre_area FROM " . $this->tabla_codareas . " WHERE ca_id = '" . $id . "'";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return isset($dato['nombre_area']) ? $dato['nombre_area'] : '';
	}

	public function obtenerNombreResponsable($legajo) {
		$conexion = $this->conectar();

		$sql = "SELECT CONCAT( p_apellido, ', ', p_nombre ) AS nombre_responsable
				FROM " . $this->tabla_personal . "
				WHERE p_legajo = " . $legajo;

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return isset($dato['nombre_responsable']) ? $dato['nombre_responsable'] : '';
	}

	public function obtenerAreasRegistradas() {
		$conexion = $this->conectar();

		$sql = "SELECT ca_id AS cod_area, ca_nombre AS nombre_area
				FROM " . $this->tabla_codareas . "
				WHERE ca_id IN (SELECT cod_area FROM " . $this->tabla_pc_en_red . " WHERE habilitado = 1)";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerResponsablesPorArea($cod_area = '0', $se_edita = 0) {
		$conexion = $this->conectar();

		$filtro_por_area = "";
		$filtro_por_existencia_en_red = "";
		$filtro_solo_activos = "";

		// Si se eligió un Area
		if ($cod_area != '0') {
			$filtro_por_area = " AND A.a_id_area = '" . $cod_area . "'";
		}

		// Si NO se edita, se filtra por los que ya poseen equipos registrados en la Red HCD
		if ($se_edita == 0) {
			$filtro_por_existencia_en_red = " AND p_legajo IN ( SELECT cod_responsable
																FROM " . $this->tabla_pc_en_red . "
																WHERE habilitado = 1
																AND cod_responsable IS NOT NULL)";
		} else {
			$filtro_solo_activos = " AND c_fecha_baja IS NULL";
		}

		$sql = "SELECT p_legajo AS cod_responsable, CONCAT( p_apellido, ', ', p_nombre ) AS nombre_responsable
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT A.a_legajo
								    FROM " . $this->tabla_areas . " AS A
								    WHERE A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
														     FROM " . $this->tabla_areas . "
														     WHERE a_legajo = A.a_legajo
													       )
								   " . $filtro_por_area . "
								  )
				AND p_legajo IN ( SELECT c_legajo
								  FROM " . $this->tabla_cargos . "
								  WHERE c_nomenclador = ( SELECT cc_nomenclador
														  FROM " . $this->tabla_codcargos . "
														  WHERE cc_nomenclador = " . $this->id_cargo_concejal . "
														)
								  " . $filtro_solo_activos . "
								)
				" . $filtro_por_existencia_en_red . "
				ORDER BY nombre_responsable";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerDatosPCs($cod_area = '', $cod_responsable = '') {
		$conexion = $this->conectar();

		$filtro = "";

		if ($cod_area != '') {
			$filtro .= "AND cod_area = '" . $cod_area . "'";
		}

		if ($cod_responsable != '') {
			$filtro .= "AND cod_responsable = " . $cod_responsable . "";
		}

		$sql = "SELECT *
				FROM " . $this->tabla_pc_en_red . "
				WHERE habilitado = 1
				" . $filtro . "
				GROUP BY id, cod_area, cod_responsable
			   ";
		//Area Informatica: 01010400

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerDatosPC_sin_responsable($cod_area) {
		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM " . $this->tabla_pc_en_red . "
				WHERE habilitado = 1
				AND cod_area = '" . $cod_area . "'
				AND cod_responsable IS NULL
			   ";
		//Area Informatica: 01010400

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtienen los equipos registrados en la red HCD, que NO posean fecha de caducidad ó dicha fecha sea mayor a la fecha actual
	 *
	 * @return boolean|array <NULL, $datos>
	 */
	public function obtenerInfoEquiposRedHCD() {

		$conexion = $this->conectar();

		$sql = "SELECT
				    CONCAT(
				        CASE
							WHEN PER.cod_area = '01010400' THEN 'informatica:'
							WHEN SUBSTR(PER.cod_area, 1, 2) = '01' THEN 'administracion:'
							WHEN SUBSTR(PER.cod_area, 1, 2) = '02' THEN 'bloques:'
							ELSE '#indefinido:'
						END,
						REPLACE(IFNULL(PER.nombre_netbios, ''), ':', '.'), ':',
						REPLACE(IFNULL(PER.direccion_mac, ''), ':', '-'), ':',
						IFNULL(PER.ip, ''), ':',
						IFNULL(PER.nameserver, ''), ':',
						IFNULL(PER.wins, ''), ':',
						IFNULL(PER.gateway, ''), ':',
						REPLACE(IFNULL(PER.nro_inventario, ''), ':', '.'), ':',
						IFNULL(PER.fecha_caducidad, ''), ':',
						REPLACE(IFNULL(PER.comentario, ''), ':', '.')
				    ) as host_data
				FROM " . $this->tabla_pc_en_red . " PER
				WHERE
					PER.habilitado = 1
				AND (
						PER.fecha_caducidad IS NULL
					OR 	PER.fecha_caducidad > CURDATE()
				    )
				ORDER BY host_data;";

		// 09/11/2021 XXXX
		// Se retiró de la query el uso de '0000-00-00',
		// para la verificacion de fecha invalida
		// dentro del CONCAT, para 'fecha_caducidad'
		// ---------------------------------------------
		// IF(
		//  DATE_FORMAT(PER.fecha_caducidad, '%Y-%m-%d') = '0000-00-00', -- verificacion de fecha invalida
		// 	'', -- then
  		//  DATE_FORMAT(PER.fecha_caducidad, '%Y-%m-%d') -- else
		// ), ':',
		// ------------------
		// y en el AND
		// -----------
		// PER.fecha_caducidad = '0000-00-00'

		$resultado = $this->ejecutarQuery($sql);

		$listado = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $listado;
	}

	public function obtenerNombresNetbiosExistentes() {
		$conexion = $this->conectar();

		$query = "SELECT nombre_netbios FROM " . $this->tabla_pc_en_red . " WHERE habilitado = 1";

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}
}
?>
