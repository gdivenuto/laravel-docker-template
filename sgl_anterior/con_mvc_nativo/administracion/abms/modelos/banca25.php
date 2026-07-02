<?php
if (!isset($_SESSION))
	session_start();

class banca25Model extends ModeloBaseMySQLi
{
    public function conectar() {
		return parent::conectarDB(1);
	}
	
	private function getFiltro() {

		$filtro = "";
		if (isset($this->filtro['f_fecha_desde']) && $this->filtro['f_fecha_desde'] != '' &&
			isset($this->filtro['f_fecha_hasta']) && $this->filtro['f_fecha_hasta'] != ''
		) {
			$filtro .= " AND S.fecha BETWEEN '" . $this->filtro['f_fecha_desde'] . " 00:00:00' AND '" . $this->filtro['f_fecha_hasta'] . " 23:59:59'";
		}

		if (isset($this->filtro['f_texto']) && $this->filtro['f_texto'] != '') {
			$filtro .= " AND (
							S.id LIKE '%" . $this->filtro['f_texto'] . "%'
							OR
							B.apellidoynombre LIKE '%" . str_replace(" ", "%", $this->filtro['f_texto']) . "%' 
							OR
							B.nrodoc LIKE '%" . $this->filtro['f_texto'] . "%'
							OR
							B.mail LIKE '%" . $this->filtro['f_texto'] . "%'
							OR
							B.institucion_nombre LIKE '%" . $this->filtro['f_texto'] . "%'
						)";
		}

		// Sólo Solicitantes
		if (isset($this->filtro['f_tipo']) && $this->filtro['f_tipo'] == '1') {
			$filtro .= " AND B.fecha_sesion IS NULL";
		}
		// Sólo Expositores
		if (isset($this->filtro['f_tipo']) && $this->filtro['f_tipo'] == '2') {
			$filtro .= " AND B.fecha_sesion IS NOT NULL";
		}

		return $filtro;
	}

	/**
	 * Se obtiene el listado, en base a un criterio determinado en la query
	 * @return array $datos
	 */
	public function listar($para_pdf = false) {

		$conexion = $this->conectar();
		
		$filtro_para_pdf = ($para_pdf) ? " AND B.descartada = 0" : "";

		$limite = (isset($this->filtro['rango']) && $this->filtro['rango'] != 0) 
			? " LIMIT " . $this->filtro['inicio'] . ", " . $this->filtro['rango']
			: "";

		$sql = "SELECT 
					S.*,
					B.apellidoynombre AS b_apellidoynombre, 
				  	B.tipodoc AS b_tipodoc, 
				  	B.nrodoc AS b_nrodoc, 
				  	B.domicilio AS b_domicilio, 
				  	B.localidad AS b_localidad, 
				  	B.telefono AS b_telefono, 
				  	B.mail AS b_mail, 
				  	B.institucion_nombre AS b_institucion_nombre, 
				  	B.institucion_domicilio AS b_institucion_domicilio, 
				    B.tema,
				    B.fecha_sesion,
				    B.expe_anio,
				    B.expe_tipo,
				    B.expe_numero,
				    B.observaciones,
				    B.descartada
				FROM dmz.solicitudes_banca25 AS S
				LEFT JOIN hcd.admin_banca_25 AS B
					ON B.id = S.id
				WHERE S.fecha IS NOT NULL
				" . $this->getFiltro() . "
				" . $filtro_para_pdf . "
				ORDER BY S." . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
				" . $limite;

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

		$query = "SELECT 
					COUNT(S.id) AS cantidad
				  FROM dmz.solicitudes_banca25 AS S
				  LEFT JOIN hcd.admin_banca_25 AS B
					ON B.id = S.id
				  WHERE S.fecha IS NOT NULL
				  " . $this->getFiltro();

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	/**
	 * Se obtiene la informacion de un registro determinado por su Id
	 *
	 * @param integer $id
	 * @return array $registro
	 */
	public function obtenerRegistro($id = 0) {

		$conexion = $this->conectar();

		$sql = "SELECT 
					S.*,
					B.apellidoynombre AS b_apellidoynombre, 
				  	B.tipodoc AS b_tipodoc, 
				  	B.nrodoc AS b_nrodoc, 
				  	B.domicilio AS b_domicilio, 
				  	B.localidad AS b_localidad, 
				  	B.telefono AS b_telefono, 
				  	B.mail AS b_mail, 
				  	B.institucion_nombre AS b_institucion_nombre, 
				  	B.institucion_domicilio AS b_institucion_domicilio, 
				    B.tema,
				    B.fecha_sesion,
				    B.expe_anio,
				    B.expe_tipo,
				    B.expe_numero,
				    B.observaciones,
				    B.descartada
				FROM dmz.solicitudes_banca25 AS S
				LEFT JOIN hcd.admin_banca_25 AS B
					ON B.id = S.id
				WHERE S.id = " . $id;

		//LibreriaGeneral::registrarLog("sql_obtenerRegistro", $sql, '.sql');

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

    public function validarDatos($datos)
    {
    	$datos['fecha'] = $this->revisarValorFechaAtributo($datos['fecha']);
    	$datos['apellidoynombre'] = $this->revisarValorAtributo(strip_tags($datos['apellidoynombre']));
		$datos['tipodoc'] = $this->revisarValorAtributo(strip_tags($datos['tipodoc']));
		$datos['nrodoc'] = $this->revisarValorAtributo(strip_tags($datos['nrodoc']));
		$datos['domicilio'] = $this->revisarValorAtributo(strip_tags($datos['domicilio']));
		$datos['localidad'] = $this->revisarValorAtributo(strip_tags($datos['localidad']));
		$datos['telefono'] = $this->revisarValorAtributo(strip_tags($datos['telefono']));
		$datos['mail'] = $this->revisarValorAtributo(strip_tags($datos['mail']));
		$datos['institucion_nombre'] = $this->revisarValorAtributo(strip_tags($datos['institucion_nombre']));
		$datos['institucion_domicilio'] = $this->revisarValorAtributo(strip_tags($datos['institucion_domicilio']));

		$datos['tema'] = $this->revisarValorAtributo(strip_tags($datos['tema']));
		$datos['fecha_sesion'] = $this->revisarValorFechaAtributo($datos['fecha_sesion']);
		$datos['expe_anio'] = $this->revisarValorAtributo(strip_tags($datos['expe_anio']));
		$datos['expe_tipo'] = $this->revisarValorAtributo(strip_tags($datos['expe_tipo']));
		$datos['expe_numero'] = $this->revisarValorAtributo(strip_tags($datos['expe_numero']));
		$datos['observaciones'] = $this->revisarValorAtributo(strip_tags($datos['observaciones']));
		$datos['descartada'] = $this->revisarValorAtributo($datos['descartada'], 0);
		
		return $datos;
    }
	
    public function guardar($datos) {

		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "INSERT INTO ".$this->tabla_banca_25." 
					(id, 
					 fecha, 
					 apellidoynombre, 
				  	 tipodoc, 
				  	 nrodoc, 
				  	 domicilio, 
				  	 localidad, 
				  	 telefono, 
				  	 mail, 
				  	 institucion_nombre, 
				  	 institucion_domicilio, 
				  	 tema, 
					 fecha_sesion, 
					 expe_anio, 
					 expe_tipo, 
					 expe_numero, 
					 observaciones,
					 descartada)
				  VALUES
				  	(".$datos['id'].", 
				  	 ".$datos['fecha'].", 
				  	 ".$datos['apellidoynombre'].", 
				  	 ".$datos['tipodoc'].", 
				  	 ".$datos['nrodoc'].", 
				  	 ".$datos['domicilio'].",
				  	 ".$datos['localidad'].", 
				  	 ".$datos['telefono'].", 
				  	 ".$datos['mail'].", 
				  	 ".$datos['institucion_nombre'].",
				  	 ".$datos['institucion_domicilio'].",
				  	 ".$datos['tema'] . ",
				  	 ".$datos['fecha_sesion'].", 
				  	 ".$datos['expe_anio'].", 
				  	 ".$datos['expe_tipo'].", 
				  	 ".$datos['expe_numero'].", 
				  	 ".$datos['observaciones'].",
				  	 ".$datos['descartada']."
				    )
				  ON DUPLICATE KEY UPDATE 
				  	apellidoynombre = ".$datos['apellidoynombre'].", 
				  	tipodoc =  ".$datos['tipodoc'].", 
				  	nrodoc =  ".$datos['nrodoc'].", 
				  	domicilio =  ".$datos['domicilio'].",
				  	localidad =  ".$datos['localidad'].", 
				  	telefono =  ".$datos['telefono'].", 
				  	mail =  ".$datos['mail'].", 
				  	institucion_nombre =  ".$datos['institucion_nombre'].",
				  	institucion_domicilio =  ".$datos['institucion_domicilio'].",
				  	tema =  ".$datos['tema'] . ",
				  	fecha_sesion = ".$datos['fecha_sesion'].",
				  	expe_anio = ".$datos['expe_anio'].", 
				  	expe_tipo = ".$datos['expe_tipo'].", 
				  	expe_numero = ".$datos['expe_numero'].", 
				  	observaciones = ".$datos['observaciones'].", 
				  	descartada = ".$datos['descartada'];
		
		//LibreriaGeneral::registrarLog("query_guardar", $query, '.sql');

		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			$this->desconectar($conexion);

			$this->auditarEnAdministracion(
				"MODIFICA", 
				$this->tabla_banca_25, 
				"Se guarda la informaci&oacute;n para la banca solicitada."
			);
		}
		return true;
	}

	/**
	 * Se borra la informacion de un registro determinado por su Id
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function borrar($id = 0) {

		$conexion = $this->conectar();

		$sql = "UPDATE " . $this->tabla_banca_25 . " 
				SET fecha_sesion = NULL,
					expe_anio = NULL,
					expe_tipo = NULL,
					expe_numero = NULL
				WHERE id = " . $id;

		if (!$this->ejecutarQuery($sql)) {
			return false;
		} else {
			$this->desconectar($conexion);

			$this->auditarEnAdministracion(
				"MODIFICA", 
				$this->tabla_banca_25, 
				"Se borra la fecha de sesión y la clave del exped/nota, de la Solicitud ID # " . $id);
		}
		return true;
	}

}
?>