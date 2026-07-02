<?php
if (!isset($_SESSION)) {
	session_start();
}

class participacionesDmzModel extends ModeloBaseMySQLiDMZ {

	public function conectar() {
		// Se obtiene la conexión con la DB de la DMZ
		return parent::obtenerConexion();
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
	public function listarParticipaciones($anio, $tipo, $numero, $cuerpo, $alcance, $f_fecha_desde = '', $f_fecha_hasta = '') {

		$conexion = $this->conectar();
		
		$filtro_sql = "";

		// SI SE FILTRA POR UN RANGO DE FECHAS
		if ($f_fecha_desde != '' && $f_fecha_hasta != '') {
			$filtro_sql = " AND fecha BETWEEN '" . $f_fecha_desde . " 00:00:00' AND '" . $f_fecha_hasta . " 23:59:59'";
		}

		$sql = "SELECT  participaciones_dmz.anio,
						participaciones_dmz.tipo,
						participaciones_dmz.numero,
						participaciones_dmz.cuerpo,
						participaciones_dmz.alcance,
						participaciones_dmz.numero_participacion,
						participaciones_dmz.fecha, 
						participaciones_dmz.apellidoynombre, 
						participaciones_dmz.telefono, 
						participaciones_dmz.mail, 
						participaciones_dmz.institucion_nombre, 
						participaciones_dmz.institucion_domicilio, 
						participaciones_dmz.texto, 
						participaciones_dmz.documentacion, 
						participaciones_dmz.estado AS estado_dmz, 
						participaciones_hcd.estado AS estado_hcd
				FROM dmz.expe_participaciones AS participaciones_dmz
				LEFT JOIN hcd.expe_participaciones AS participaciones_hcd
					ON  participaciones_dmz.anio = participaciones_hcd.anio AND
						participaciones_dmz.tipo = participaciones_hcd.tipo AND
						participaciones_dmz.numero = participaciones_hcd.numero AND
						participaciones_dmz.cuerpo = participaciones_hcd.cuerpo AND
						participaciones_dmz.alcance = participaciones_hcd.alcance AND
						participaciones_dmz.numero_participacion = participaciones_hcd.numero_participacion
				WHERE participaciones_dmz.anio = ".$anio."
				AND participaciones_dmz.tipo = '".$tipo."'
				AND participaciones_dmz.numero = ".$numero."
				AND participaciones_dmz.cuerpo = ".$cuerpo."
				AND participaciones_dmz.alcance = ".$alcance."
				".$filtro_sql;

		//LibreriaGeneral::registrarLog("sql_listarParticipaciones", $sql);

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
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
				  WHERE anio = " . $anio."
				  AND tipo = '" . $tipo."'
				  AND numero = " . $numero."
				  AND cuerpo = " . $cuerpo."
				  AND alcance = " . $alcance."
				  AND numero_participacion = ".$numero_participacion;

		//LibreriaGeneral::registrarLog("query_eliminarParticipacion_en_dmz", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_expe_participaciones, "Se ha eliminado la participaci&oacute;n ".$numero_participacion." del Expediente ".$anio."-".$tipo."-".$numero."-".$cuerpo."-".$alcance." en la DMZ.");
		}

		return true;
	}
}
?>