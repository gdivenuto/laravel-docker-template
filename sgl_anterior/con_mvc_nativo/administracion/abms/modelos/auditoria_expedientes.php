<?php
if (!isset($_SESSION)) {
	session_start();
}

class auditoriaExpedientesModel extends ModeloBaseMySQLi
{
	public function conectar() {

		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(2);
	}
		
	public function listar()
	{
		$conexion = $this->conectar();
		
	    $filtro = "";
	    $limite = "";

		// SI SE FILTRA POR UN RANGO DE FECHAS
		if ( $this->filtro['f_fecha_desde'] != '' && $this->filtro['f_fecha_hasta'] != '' )
			$filtro .= " AND fecha_hora_log BETWEEN '".$this->filtro['f_fecha_desde']." 00:00:00' AND '".$this->filtro['f_fecha_hasta']." 23:59:59'";
		
		// SI SE FILTRA POR USUARIO
		if ($this->filtro['f_usuario'] != '')
		    $filtro .= " AND netusername LIKE '".$this->filtro['f_usuario']."%'";
		
		// PARA LIMITAR EL LISTADO
		if ( $this->filtro['rango'] != 0 )
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];
		
		// SI SE FILTRA POR LA CLAVE DEL EXPEDIENTE/NOTA
		if ( $this->filtro['f_anio'] != '' ) { 
			$filtro .= " AND anio_log = ".$this->filtro['f_anio']."
						 AND tipo_log = '".$this->filtro['f_tipo']."'
						 AND numero_log = ".$this->filtro['f_numero']."
						 AND cuerpo_log = ".$this->filtro['f_cuerpo']."
						 AND alcance_log = ".$this->filtro['f_alcance'];
		}

		$sql = "SELECT * FROM ".$this->tabla_auditoria_expedientes."
				WHERE id_usuario IS NOT NULL
				".$filtro."
				ORDER BY id_log ".$_SESSION['ultimo_sentido']."
				".$limite;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerCantidad()
	{
	    $conexion = $this->conectar();
	    
	    $filtro = "";
	    
		// SI SE FILTRA POR UN RANGO DE FECHAS
		if ( $this->filtro['f_fecha_desde'] != '' && $this->filtro['f_fecha_hasta'] != '' )
			$filtro .= " AND fecha_hora_log BETWEEN '".$this->filtro['f_fecha_desde']." 00:00:00' AND '".$this->filtro['f_fecha_hasta']." 23:59:59'";
		
		// SI SE FILTRA POR USUARIO
		if ($this->filtro['f_usuario'] != '')
		    $filtro .= " AND netusername LIKE '".$this->filtro['f_usuario']."%'";
		
		// SI SE FILTRA POR LA CLAVE DEL EXPEDIENTE/NOTA
		if ( $this->filtro['f_anio'] != '' ) { 
			$filtro .= " AND anio_log = ".$this->filtro['f_anio']."
						 AND tipo_log = '".$this->filtro['f_tipo']."'
						 AND numero_log = ".$this->filtro['f_numero']."
						 AND cuerpo_log = ".$this->filtro['f_cuerpo']."
						 AND alcance_log = ".$this->filtro['f_alcance'];
		}
		
	    $sql = "SELECT COUNT(id_log) AS cantidad 
				FROM ".$this->tabla_auditoria_expedientes."
				WHERE id_usuario IS NOT NULL
				".$filtro;

	    $resultado = $this->ejecutarQuery($sql);
	    
	    $dato = $this->obtenerFila($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $dato['cantidad'];
	}
	
	public function registrarMovimiento($datos_log)
	{
		$conexion = $this->conectar();

	    $datos_log['observaciones_log'] = $this->revisarValorAtributo($datos_log['observaciones_log']);
	
	    $query = "INSERT INTO ".$this->tabla_auditoria_expedientes." 
	    			(id_log, fecha_hora_log, id_usuario, operacion, tabla, anio_log, tipo_log, numero_log, cuerpo_log, alcance_log, fecha_log, orden_log, netusername, netpcname, observaciones_log)
				  VALUES ( null,
						  '".date("Y-m-d H:i")."',
						   ".$_SESSION['id_usuario'].",
						  '".$datos_log['operacion_log']."',
						  '".$datos_log['tabla_log']."',
						  '".$datos_log['anio_log']."',
						  '".$datos_log['tipo_log']."',
						   ".$datos_log['numero_log'].",
						   ".$datos_log['cuerpo_log'].",
						   ".$datos_log['alcance_log'].",
						   ".$datos_log['fecha_log'].",
						   ".$datos_log['orden_log'].",
						  '".$_SESSION['usuario']."',
						  '".$_SESSION['netpcname']."',
						   ".$datos_log['observaciones_log']."
						 )";
		
	    $this->ejecutarQuery($query);

	    $this->desconectar($conexion);
	}
}
?>
