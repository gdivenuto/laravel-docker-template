<?php
if (!isset($_SESSION)) {
	session_start();
}

class participacionesHcdModel extends ModeloBaseMySQLi {

	public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}

	/**
	 * Se obtienen los Expedientes habilitados para su participación
	 * @return array $datos
	 */
	public function listar() {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_expe_en_participacion;
		$sql .= " ORDER BY " . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'];
		$sql .= ($this->filtro['rango'] != 0) ? " LIMIT " . $this->filtro['inicio'] . ", " . $this->filtro['rango'] : "";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene la cantidad de Expedientes habilitados para su participación
	 * @return integer
	 */
	public function obtenerCantidad() {

		$conexion = $this->conectar();

		$query = "SELECT COUNT(anio) AS cantidad FROM " . $this->tabla_expe_en_participacion;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	/**
	 * Se modifica el estado de Aprobación de una Participacion determinada
	 * @param  integer $anio
	 * @param  string  $tipo
	 * @param  integer $numero
	 * @param  integer $cuerpo
	 * @param  integer $alcance
	 * @param  integer $numero_participacion
	 * @param  integer $estado
	 * @return boolean
	 */
	public function modificarEstadoAprobacion($anio, $tipo, $numero, $cuerpo, $alcance, $numero_participacion, $estado) {

		$conexion = $this->conectar();

		$valor_estado = ($estado == 0) ? 1 : 0;

		$query = "SELECT numero_participacion
				  FROM " . $this->tabla_expe_participaciones . "
	    		  WHERE anio = " . $anio . "
				  AND tipo = '" . $tipo . "'
				  AND numero = " . $numero . "
				  AND cuerpo = " . $cuerpo . "
				  AND alcance = " . $alcance . "
				  AND numero_participacion = " . $numero_participacion;

		//LibreriaGeneral::registrarLog("query_select_participacion", $query);

		$resultado = $this->ejecutarQuery($query);

		$verificacion = $this->obtenerFila($resultado);

		// Si NO existe la participación en "hcd"
		if ($verificacion['numero_participacion'] == '') {

			// Se ingresa la participación en "hcd", tomándola de "dmz", con el estado "incorporada" o "denegada"
			$query = "INSERT INTO " . $this->tabla_expe_participaciones;
			$query .= " (anio, tipo, numero, cuerpo, alcance, numero_participacion,";
			$query .= " fecha, apellidoynombre, tipodoc, nrodoc, domicilio, localidad, telefono, mail,";
			$query .= " institucion_nombre, institucion_domicilio, texto, documentacion, estado)";

			$query .= " (SELECT";
			$query .= " anio, tipo, numero, cuerpo, alcance, numero_participacion,";
			$query .= " fecha, apellidoynombre, tipodoc, nrodoc, domicilio, localidad, telefono, mail,";
			$query .= " institucion_nombre, institucion_domicilio, texto, documentacion, " . $valor_estado;

			$query .= " FROM dmz.expe_participaciones";

			$query .= " WHERE anio = " . $anio;
			$query .= " AND tipo = '" . $tipo . "'";
			$query .= " AND numero = " . $numero;
			$query .= " AND cuerpo = " . $cuerpo;
			$query .= " AND alcance = " . $alcance;
			$query .= " AND numero_participacion = " . $numero_participacion;
			$query .= ")";

			//LibreriaGeneral::registrarLog("query_insert_participacion_en_hcd", $query);
		} else {
			// si existe, sólo se modifica su estado "incorporada" o "denegada"
			$query = "UPDATE " . $this->tabla_expe_participaciones . "
		    		  	SET estado = " . $valor_estado . "
		    		  WHERE anio = " . $anio . "
					  AND tipo = '" . $tipo . "'
					  AND numero = " . $numero . "
					  AND cuerpo = " . $cuerpo . "
					  AND alcance = " . $alcance . "
					  AND numero_participacion = " . $numero_participacion;

			//LibreriaGeneral::registrarLog("query_update_participacion_en_hcd", $query);
		}

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		// Se audita
		$this->auditarEnAdministracion("MODIFICA", $this->tabla_expe_participaciones, "Se define el estado de incorporaci&oacute;n de la participaci&oacute;n " . $numero_participacion . " del " . $anio . " - " . $tipo . " - " . $numero . " - " . $cuerpo . " - " . $alcance);

		return true;
	}

	public function retirarExpeParticipaciones($anio, $tipo, $numero, $cuerpo, $alcance) {

		$conexion = $this->conectar();

		$query = "DELETE FROM " . $this->tabla_expe_en_participacion . "
				  WHERE anio = " . $anio . "
				  AND tipo = '" . $tipo . "'
				  AND numero = " . $numero . "
				  AND cuerpo = " . $cuerpo . "
				  AND alcance = " . $alcance;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_expe_en_participacion, "Se retira el Expediente " . $anio . "-" . $tipo . "-" . $numero . "-" . $cuerpo . "-" . $alcance . " de las Participaciones");
		}

		return true;
	}

	/**
	 * Se elimina una Participacion en la DB hcd
	 * @param  integer $anio                 	Año del expediente
	 * @param  string $tipo                  	Tipo del expediente
	 * @param  integer $numero               	Número del expediente
	 * @param  integer $cuerpo               	Cuerpo del expediente
	 * @param  integer $alcance              	Alcance del expediente
	 * @param  integer $numero_participacion 	Número de la participación
	 * @return boolean
	 */
	public function eliminarParticipacion($anio, $tipo, $numero, $cuerpo, $alcance, $numero_participacion) {

		$conexion = $this->conectar();

		$query = "DELETE FROM " . $this->tabla_expe_participaciones . "
				  WHERE anio = " . $anio . "
				  AND tipo = '" . $tipo . "'
				  AND numero = " . $numero . "
				  AND cuerpo = " . $cuerpo . "
				  AND alcance = " . $alcance . "
				  AND numero_participacion = " . $numero_participacion;

		//LibreriaGeneral::registrarLog("query_eliminarParticipacion_en_hcd", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_expe_participaciones, "Se ha eliminado la participaci&oacute;n " . $numero_participacion . " del Expediente " . $anio . "-" . $tipo . "-" . $numero . "-" . $cuerpo . "-" . $alcance . " en la DB hcd.");
		}

		return true;
	}

	/**
	 * Se obtienen las Participaciones de un expediente determinado
	 * @param  integer $anio
	 * @param  string  $tipo
	 * @param  integer $numero
	 * @param  integer $cuerpo
	 * @param  integer $alcance
	 * @return array   $datos
	 */
	public function listarParticipaciones($anio, $tipo, $numero, $cuerpo, $alcance, $estado = '', $f_fecha_desde = '', $f_fecha_hasta = '') {

		$conexion = $this->conectar();

		$f_estado = ($estado != '') ? " AND estado = " . $estado : "";

		// SI SE FILTRA POR UN RANGO DE FECHAS
		$f_fecha = ($f_fecha_desde != '' && $f_fecha_hasta != '') ? " AND fecha BETWEEN '" . $f_fecha_desde . " 00:00:00' AND '" . $f_fecha_hasta . " 23:59:59'" : "";

		$sql = "SELECT * FROM " . $this->tabla_expe_participaciones . "
				WHERE anio = " . $anio . "
				AND tipo = '" . $tipo . "'
				AND numero = " . $numero . "
				AND cuerpo = " . $cuerpo . "
				AND alcance = " . $alcance . "
				" . $f_estado . "
				" . $f_fecha;

		//LibreriaGeneral::registrarLog("sql_listarParticipaciones", $sql);

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerParticipacion($anio, $tipo, $numero, $cuerpo, $alcance, $numero_participacion) {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_expe_participaciones . "
		 		WHERE anio = " . $anio . "
				AND tipo = '" . $tipo . "'
				AND numero = " . $numero . "
				AND cuerpo = " . $cuerpo . "
				AND alcance = " . $alcance . "
				AND numero_participacion = " . $numero_participacion;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

}
?>
