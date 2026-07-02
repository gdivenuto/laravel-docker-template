<?php
if (!isset($_SESSION)) {
	session_start();
}

class notificacionesModel extends ModeloBaseMySQLi {
	
	public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}

	public function listar() {
		$conexion = $this->conectar();

		$filtro = "";
		$limite = "";

		// Para filtrar por Fecha
		if ($this->filtro['f_fecha'] != '') {
			$filtro .= " AND n_fecha = '" . $this->filtro['f_fecha'] . "'";
		}

		// Para filtrar por Asunto
		if ($this->filtro['f_asunto'] != '') {
			$filtro .= " AND n_asunto LIKE '%" . $this->filtro['f_asunto'] . "%'";
		}

		// Para filtrar por Grupo Destino
		if (isset($this->filtro['f_id_grupo_destino']) && $this->filtro['f_id_grupo_destino'] != '' && $this->filtro['f_id_grupo_destino'] != '0') {
			$filtro .= " AND n_id_grupo_destino = '" . $this->filtro['f_id_grupo_destino'] . "'";
		}

		// Para limitar el listado
		if ($this->filtro['rango'] != 0) {
			$limite = " LIMIT " . $this->filtro['inicio'] . ", " . $this->filtro['rango'];
		}

		$sql = "SELECT * FROM " . $this->tabla_notificaciones . "
				WHERE n_habilitada <> 3
				" . $filtro . "
				ORDER BY " . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
				" . $limite;

		//LibreriaGeneral::registrarLog("sql_listar_notificaciones", $sql);

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerCantidad() {
		$conexion = $this->conectar();

		$filtro = "";

		// Para filtrar por Fecha
		if ($this->filtro['f_fecha'] != '') {
			$filtro .= " AND n_fecha = '" . $this->filtro['f_fecha'] . "'";
		}

		// Para filtrar por Asunto
		if ($this->filtro['f_asunto'] != '') {
			$filtro .= " AND n_asunto LIKE '%" . $this->filtro['f_asunto'] . "%'";
		}

		// Para filtrar por Grupo Destino
		if (isset($this->filtro['f_id_grupo_destino']) && $this->filtro['f_id_grupo_destino'] != '' && $this->filtro['f_id_grupo_destino'] != '0') {
			$filtro .= " AND n_id_grupo_destino = '" . $this->filtro['f_id_grupo_destino'] . "'";
		}

		$query = "SELECT COUNT(n_id) AS cantidad
				  FROM " . $this->tabla_notificaciones . "
				  WHERE n_habilitada <> 3
				  " . $filtro;

		//LibreriaGeneral::registrarLog("query_obtenerCantidad_notificaciones", $query);

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	public function obtenerUltimoId() {
		return parent::obtenerUltimoCodigo($this->tabla_notificaciones, 'n_id');
	}

	public function obtenerRegistro($n_id) {
		$conexion = $this->conectar();

		$query = "SELECT * FROM " . $this->tabla_notificaciones . " WHERE n_id = " . $n_id;

		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	public function existe($fecha, $asunto) {
		$conexion = $this->conectar();

		$query = "SELECT n_id
				  FROM " . $this->tabla_notificaciones . "
				  WHERE n_fecha = '" . $this->formatearFechaMySQL($fecha) . "'
				  AND n_asunto = '" . $asunto . "'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ($dato['n_id'] != '');
	}

	/**
	 * Se existe un código determinado
	 * @param  [integer] $codigo Código de la Notificacion
	 * @return [boolean]         true|False
	 */
	public function existeCodigo($codigo) {
		$conexion = $this->conectar();

		$query = "SELECT n_id FROM " . $this->tabla_notificaciones . " WHERE n_id = " . $codigo;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ($dato['n_id'] != '');
	}

	public function validarDatos($datos) {
		$datos['n_fecha'] = $this->revisarValorFechaAtributo($datos['n_fecha']);

		$datos['n_asunto'] = $this->revisarValorAtributo(strip_tags($datos['n_asunto']));

		$datos['n_mensaje'] = $this->revisarValorAtributo($datos['n_mensaje']);

		$datos['n_id_grupo_destino'] = $this->revisarValorAtributo($datos['n_id_grupo_destino']);

		$datos['n_phplist_ids_destino'] = $this->revisarValorAtributo($datos['n_phplist_ids_destino']);

		$datos['n_enviada'] = $this->revisarValorAtributo($datos['n_enviada'], 0);

		$datos['n_id_mail'] = $this->revisarValorAtributo($datos['n_id_mail'], 0);

		return $datos;
	}

	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function noLoModificoOtroUsuario() {

		$conexion = $this->conectar();

		$query = "SELECT n_id
				  FROM " . $this->tabla_notificaciones . "
				  WHERE n_id = '" . $_SESSION['n_id_original'] . "'
				  " . $this->adaptarValorStringParaFiltro('n_fecha') . "
				  " . $this->adaptarValorStringParaFiltro('n_asunto') . "
				  " . $this->adaptarValorStringParaFiltro('n_mensaje') . "
				  " . $this->adaptarValorStringParaFiltro('n_id_grupo_destino') . "
				  " . $this->adaptarValorStringParaFiltro('n_enviada') . "
				  " . $this->adaptarValorStringParaFiltro('n_id_mail') . "
				   AND n_habilitada = " . $_SESSION['n_habilitada_original'] . "
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($dato['n_id']);
	}

	public function insertar($datos) {
		// Se obtiene el siguiente Id
		$n_id = $this->obtenerUltimoId() + 1;

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO " . $this->tabla_notificaciones . " (
					n_id,
					n_fecha,
					n_asunto,
					n_mensaje,
					n_id_grupo_destino,
					n_phplist_ids_destino,
					n_habilitada,
					n_enviada,
					n_id_mail)
				  VALUES(
				  	" . $n_id . ",
					" . $datos['n_fecha'] . ",
					" . $datos['n_asunto'] . ",
					" . $datos['n_mensaje'] . ",
					" . $datos['n_id_grupo_destino'] . ",
				    " . $datos['n_phplist_ids_destino'] . ",
					1,
					0,
					" . $datos['n_id_mail'] . ");";

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion(
				"ALTA",
				$this->tabla_notificaciones,
				"Se ingresa la Notificacion: " . LibreriaGeneral::eliminarComillaSimple($datos['n_asunto'])
			);
		}

		return true;
	}

	public function modificar($datos) {
		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "UPDATE " . $this->tabla_notificaciones . "
				  SET n_fecha = " . $datos['n_fecha'] . ",
					  n_asunto = " . $datos['n_asunto'] . ",
					  n_mensaje = " . $datos['n_mensaje'] . ",
					  n_id_grupo_destino = " . $datos['n_id_grupo_destino'] . ",
					  n_phplist_ids_destino = " . $datos['n_phplist_ids_destino'] . ",
					  n_enviada = " . $datos['n_enviada'] . ",
					  n_id_mail = " . $datos['n_id_mail'] . "
				  WHERE n_id = " . $datos['n_id'];

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion(
				"MODIFICA",
				$this->tabla_notificaciones,
				"Se modifica la Notificacion: " . LibreriaGeneral::eliminarComillaSimple($datos['n_asunto'])
			);
		}

		return true;
	}

	public function eliminar($id) {
		// Previamente se obtiene la info para auditar
		$info = $this->obtenerRegistro($id);

		$conexion = $this->conectar();

		// luego se elimina la Notificacion
		$query = "DELETE FROM " . $this->tabla_notificaciones . " WHERE n_id = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion(
				"BAJA",
				$this->tabla_notificaciones,
				"Se elimina la Notificaci&oacute;n: " . LibreriaGeneral::eliminarComillaSimple($info['n_asunto'])
			);
		}

		return true;
	}

	/**
	 * Se obtiene la lista de Grupos de Distribución
	 * @return array $listado
	 */
	public function obtenerListaGruposDistribucion() {
		$conexion = $this->conectar();

		$query = "SELECT * FROM " . $this->tabla_lista_notificaciones_grupos;

		$resultado = $this->ejecutarQuery($query);

		$listado = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $listado;
	}

	/**
	 * Se obtienen las Listas de Distribución
	 * @return array $datos  Conjunto de resultados
	 */
	public function obtenerListas() {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_listas_notificaciones;

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene el Id del Mail de una Notificación determinada
	 * @param  [integer] $n_id  	Identificador de la Notificación
	 * @return [integer]       		Identificador del mail enviado
	 */
	public function obtenerIdMail($n_id) {
		$conexion = $this->conectar();

		$query = "SELECT n_id_mail FROM " . $this->tabla_notificaciones . " WHERE n_id = " . $n_id;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['n_id_mail'];
	}

	/**
	 * Se obtiene el Asunto del Mail de una Notificación determinada
	 * @param  [integer] $n_id_mail  	Identificador de la campaña de la Notificación
	 * @return [string]       			Asunto del mail enviado
	 */
	public function obtenerAsuntoCampania($n_id_mail) {
		$conexion = $this->conectar();

		$query = "SELECT n_asunto FROM " . $this->tabla_notificaciones . " WHERE n_id_mail = " . $n_id_mail;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['n_asunto'];
	}

	public function auditarEnvioNotificacion($n_id, $n_asunto) {
		// Se obtiene el Id de la campaña (PHPList)
		$id_mail = $this->obtenerIdMail($n_id);

		// Se audita el ENVIO de la Notificación
		$this->auditarEnAdministracion("ENVIO", $this->tabla_notificaciones, "Se envia la Notificacion: " . LibreriaGeneral::eliminarComillaSimple($n_asunto) . " (Id Camp." . $id_mail . ")");
	}

	public function auditarVerSuscriptores($n_id_mail) {
		// Se obtiene el Asunto del Mail de una Notificación determinada
		$n_asunto = $this->obtenerAsuntoCampania($n_id_mail);

		// Se audita la visualización de los Suscriptores de la Notificación
		$this->auditarEnAdministracion("VER SUSCRIPTORES", $this->tabla_notificaciones, "Se visualizan los Suscriptores de la Notificacion " . LibreriaGeneral::eliminarComillaSimple($n_asunto) . " (Id Camp." . $n_id_mail . ")");
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
?>
