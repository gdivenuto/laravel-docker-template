<?php
if (!isset($_SESSION))
	session_start();

class RemitenteModel extends ModeloBaseMySQLi {

	public function __construct() {
		parent::__construct();
	}

	public function conectar() {
		// Se conecta según el Id del sistema
		return parent::conectarDB(6);
	}

	private function getFiltro() {

		if (isset($this->filtro['valor_buscado']) && $this->filtro['valor_buscado'] != '') {

			$criterio_ingresado = str_replace(" ", "%", $this->filtro['valor_buscado']);

			return " AND ( nombre LIKE '%" . $criterio_ingresado . "%' OR
						   apellido LIKE '%" . $criterio_ingresado . "%' OR
						   localidad LIKE '%" . $criterio_ingresado . "%' OR
						   direccion_calle LIKE '%" . $criterio_ingresado . "%' OR
						   tel_fijo_numero LIKE '%" . $criterio_ingresado . "%' OR
						   movil_numero LIKE '%" . $criterio_ingresado . "%' OR
						   mail LIKE '%" . $criterio_ingresado . "%' OR
						   dni LIKE '%" . $criterio_ingresado . "%' OR
						   observaciones LIKE '%" . $criterio_ingresado . "%'
                         )";
		}
		return "";
	}

	/**
	 * Se obtiene el listado de Clientes, en base a un criterio determinado en la query
	 * @return array $datos
	 */
	public function listar() {
		$conexion = $this->conectar();

		$registro_inicial = (isset($this->filtro['inicio']) && $this->filtro['inicio'] != '') ? $this->filtro['inicio'] : 0;

		$sql = "SELECT *
				FROM " . $this->tabla_def_remitentes . "
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
	 */
	public function obtenerCantidad() {
		$conexion = $this->conectar();

		$query = "SELECT COUNT(id) AS cantidad 
				  FROM " . $this->tabla_def_remitentes . " 
				  WHERE habilitado <> 3 " . $this->getFiltro();

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	/**
	 * Se obtiene el listado de Clientes, para el informe
	 * @return array $datos
	 */
	public function obtenerListadoParaPDF($criterio) {

		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM " . $this->tabla_def_remitentes . "
			    WHERE habilitado = 1
			    ".$this->getFiltro()."
			    ORDER BY id";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene el ultimo Id registrado en la DB
	 */
	public function obtenerUltimoId() {
		$conexion = $this->conectar();
		
		$query = "SELECT MAX(id) AS ultimo_codigo FROM " . $this->tabla_def_remitentes;
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerFila($resultado);
		
		$ultimo_codigo = ($dato['ultimo_codigo'] != null) ? $dato['ultimo_codigo'] : 0;
				
		$this->desconectar($conexion);
		
		return $ultimo_codigo;
	}

	/**
	 * Se obtiene la informacion de un registro determinado por su Id
	 *
	 * @param integer $id
	 * @return array $registro
	 */
	public function obtenerRegistro($id = 0) {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_def_remitentes . " WHERE id = " . $id;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	/**
	 * Se verifica si existe
	 * @param  array $datos
	 * @return boolean
	 */
	public function existe($datos) {
		$conexion = $this->conectar();

		$query = "SELECT nombre FROM " . $this->tabla_def_remitentes . " WHERE dni = " . $datos['dni'];

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return ($dato['nombre'] != '');
	}

	/**
	 * Se validan determinados datos para utilizar en las querys
	 *
	 * @param array $datos
	 * @return array
	 */
	public function validarDatos($datos) {
		$datos['provincia_id'] = $this->revisarValorAtributo($datos['provincia_id'], 0);
		$datos['nombre'] = $this->revisarValorAtributo($datos['nombre']);
		$datos['apellido'] = $this->revisarValorAtributo($datos['apellido']);
		$datos['dni'] = $this->revisarValorAtributo($datos['dni']);
		$datos['localidad'] = $this->revisarValorAtributo($datos['localidad']);
		$datos['codigo_postal'] = $this->revisarValorAtributo($datos['codigo_postal']);
		$datos['direccion_calle'] = $this->revisarValorAtributo($datos['direccion_calle']);
		$datos['direccion_numero'] = $this->revisarValorAtributo($datos['direccion_numero']);
		$datos['direccion_piso'] = $this->revisarValorAtributo($datos['direccion_piso']);
		$datos['direccion_departamento'] = $this->revisarValorAtributo($datos['direccion_departamento']);
		$datos['tel_fijo_cod_area'] = $this->revisarValorAtributo($datos['tel_fijo_cod_area']);
		$datos['tel_fijo_numero'] = $this->revisarValorAtributo($datos['tel_fijo_numero']);
		$datos['movil_cod_area'] = $this->revisarValorAtributo($datos['movil_cod_area']);
		$datos['movil_numero'] = $this->revisarValorAtributo($datos['movil_numero']);
		$datos['mail'] = $this->revisarValorAtributo($datos['mail']);
		$datos['fecha_alta'] = $this->revisarValorFechaAtributo($datos['fecha_alta']);
		$datos['observaciones'] = $this->revisarValorAtributo($datos['observaciones']);
		$datos['habilitado'] = $this->revisarValorAtributo($datos['habilitado'], 0);
		return $datos;
	}

	/**
	 * Se verifica si el registro no ha sido modificado por otro usuario
	 *
	 * @return boolean
	 */
	public function noLoModificoOtroUsuario() {

		$conexion = $this->conectar();

		$query = "SELECT id
				  FROM " . $this->tabla_def_remitentes . "
				  WHERE id = " . $_SESSION['id_original'] . "
				  " . $this->adaptarValorStringParaFiltro('provincia_id') . "
				  " . $this->adaptarValorStringParaFiltro('nombre') . "
				  " . $this->adaptarValorStringParaFiltro('apellido') . "
				  " . $this->adaptarValorStringParaFiltro('dni') . "
				  " . $this->adaptarValorStringParaFiltro('localidad') . "
				  " . $this->adaptarValorStringParaFiltro('codigo_postal') . "
				  " . $this->adaptarValorStringParaFiltro('direccion_calle') . "
				  " . $this->adaptarValorStringParaFiltro('direccion_numero') . "
				  " . $this->adaptarValorStringParaFiltro('direccion_piso') . "
				  " . $this->adaptarValorStringParaFiltro('direccion_departamento') . "
				  " . $this->adaptarValorStringParaFiltro('tel_fijo_cod_area') . "
				  " . $this->adaptarValorStringParaFiltro('tel_fijo_numero') . "
				  " . $this->adaptarValorStringParaFiltro('movil_cod_area') . "
				  " . $this->adaptarValorStringParaFiltro('movil_numero') . "
				  " . $this->adaptarValorStringParaFiltro('mail') . "
				  " . $this->adaptarValorStringParaFiltro('fecha_alta') . "
				  " . $this->adaptarValorStringParaFiltro('observaciones') . "
				  AND habilitado = " . $_SESSION['habilitado_original'];

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($datos['id']);
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

		$query = "INSERT INTO " . $this->tabla_def_remitentes . "(
						nombre, 
						apellido, 
						dni,
						provincia_id, 
						localidad, 
						codigo_postal,
						direccion_calle, 
						direccion_numero, 
						direccion_piso, 
						direccion_departamento,
						tel_fijo_cod_area, 
						tel_fijo_numero, 
						movil_cod_area, 
						movil_numero, 
						mail, 
						fecha_alta, 
						observaciones, 
						habilitado)
				  VALUES(" . $datos['nombre'] . ",
				  		 " . $datos['apellido'] . ",
				  		 " . $datos['dni'] . ",
				  		 " . $datos['provincia_id'] . ",
				  		 " . $datos['localidad'] . ",
						 " . $datos['codigo_postal'] . ",
						 " . $datos['direccion_calle'] . ",
						 " . $datos['direccion_numero'] . ",
						 " . $datos['direccion_piso'] . ",
						 " . $datos['direccion_departamento'] . ",
						 " . $datos['tel_fijo_cod_area'] . ",
						 " . $datos['tel_fijo_numero'] . ",
						 " . $datos['movil_cod_area'] . ",
						 " . $datos['movil_numero'] . ",
						 " . $datos['mail'] . ",
						 " . $datos['fecha_alta'] . ",
						 " . $datos['observaciones'] . ",
						 1)";

		//LibreriaGeneral::registrarLog("query_insertar_remitente", $query, '.sql');

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"ALTA",
				$this->tabla_def_remitentes,
				$this->obtenerUltimoId(),
				str_replace("'", "", "Se ingresa el Remitente " . $datos['nombre'] . " " . $datos['apellido'])
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

		$query = "UPDATE " . $this->tabla_def_remitentes . "
				  SET nombre = " . $datos['nombre'] . ",
				  	  apellido = " . $datos['apellido'] . ",
				  	  dni = " . $datos['dni'] . ",
				  	  provincia_id = " . $datos['provincia_id'] . ",
				  	  localidad = " . $datos['localidad'] . ",
				  	  codigo_postal = " . $datos['codigo_postal'] . ",
				  	  direccion_calle = " . $datos['direccion_calle'] . ",
				  	  direccion_numero = " . $datos['direccion_numero'] . ",
				  	  direccion_piso = " . $datos['direccion_piso'] . ",
				  	  direccion_departamento = " . $datos['direccion_departamento'] . ",
				  	  tel_fijo_cod_area = " . $datos['tel_fijo_cod_area'] . ",
				  	  tel_fijo_numero = " . $datos['tel_fijo_numero'] . ",
					  movil_cod_area = " . $datos['movil_cod_area'] . ",
					  movil_numero = " . $datos['movil_numero'] . ",
					  mail = " . $datos['mail'] . ",
					  fecha_alta = " . $datos['fecha_alta'] . ",
					  observaciones = " . $datos['observaciones'] . "
				  WHERE id = " . $datos['id'];

		//LibreriaGeneral::registrarLog("query_modificar_remitente", $query, '.sql');

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"MODIFICA",
				$this->tabla_def_remitentes,
				$datos['id'],
				str_replace("'", "", "Se modifica el Remitente " . $datos['nombre'] . " " . $datos['apellido'])
			);
		}
		return true;
	}

	/**
	 * Se elimina un Cliente
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function eliminar($id) {
		$conexion = $this->conectar();

		$query = "DELETE FROM " . $this->tabla_def_remitentes . " WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnDefensoria(
				"BAJA",
				$this->tabla_def_remitentes,
				$id,
				"Se elimina un Remitente con Id " . $id
			);
		}
		return true;
	}

	/**
	 * Se obtiene el nombre de un Cliente determinado
	 *
	 * @param integer $id 				Identificador del Cliente
	 * @return string $dato['nombre'] 	Nombre
	 */
	public function obtenerNombre($id) {
		$conexion = $this->conectar();

		$sql = "SELECT nombre
				FROM " . $this->tabla_def_remitentes . "
				WHERE id = " . $id . "
				AND habilitado = 1";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['nombre'];
	}

	/**
	 * Se obtiene el listado de habilitados
	 *
	 * @return array $datos
	 */
	public function listarHabilitados() {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_def_remitentes . " 
				WHERE habilitado = 1 
				ORDER BY apellido, nombre";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
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

		$query = "UPDATE " . $this->tabla_def_remitentes . " 
				  SET habilitado = $valor_habilitado 
				  WHERE id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

	/**
	 *  Se obtiene la información de los clientes para ser utilizada como sugerencias
	 */
	public function obtenerSugerencias() {
		$conexion = $this->conectar();

		$sql = "SELECT id, nombre, apellido
				FROM " . $this->tabla_def_remitentes . "
				WHERE habilitado = 1
				ORDER BY nombre";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene la información por su nombre, apellido o dni
	 * @param  string $nombre   Nombre del cliente
	 * @return array  $datos    Info del Cliente
	 */
	public function obtenerInfo($nombre) {

		$conexion = $this->conectar();

		$filtro = "";

		if ($nombre != '') {
			$criterio_ingresado = str_replace(" ", "%", $nombre);

			$filtro .= " AND ( nombre LIKE '%" . $criterio_ingresado . "%' OR
							   apellido LIKE '%" . $criterio_ingresado . "%' OR
							   dni LIKE '%" . $criterio_ingresado . "%')";
		}

		$sql = "SELECT * FROM " . $this->tabla_def_remitentes . " WHERE habilitado = '1' " . $filtro;

		$resultado = $this->ejecutarQuery($sql);

		$info_cliente = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $info_cliente;
	}

	/**
	 * Se obtiene el nombre de un Presentante determinado por su email
	 *
	 * @param string $mail
	 * @return string nombre
	 */
	public function obtenerNombreSegunMail($mail) {

		$conexion = $this->conectar();

		$sql = "SELECT nombre
				FROM " . $this->tabla_def_remitentes . "
				WHERE mail = '" . $mail . "'
				AND habilitado = 1";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['nombre'];
	}

}
?>
