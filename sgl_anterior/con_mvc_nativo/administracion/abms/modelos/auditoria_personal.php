<?php
if (!isset($_SESSION)) {
	session_start();
}

class auditoriaPersonalModel extends ModeloBaseMySQLi
{
	public function conectar() {

		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(3);
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
		
		// SI SE FILTRA POR LEGAJO
		if ($this->filtro['f_legajo'] != '')
		{
		    $filtro .= " AND observaciones_log LIKE '%".$this->filtro['f_legajo']."%'";
		}
		
	    // PARA LIMITAR EL LISTADO
		if ( $this->filtro['rango'] != 0 )
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];
		
	    $sql = "SELECT * FROM ".$this->tabla_auditoria_personal."
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
				
		// SI SE FILTRA POR LEGAJO
		if ($this->filtro['f_legajo'] != '') {
		    $filtro .= " AND observaciones_log LIKE '%".$this->filtro['f_legajo']."%'";
		}
		
	    $sql = "SELECT COUNT(id_log) AS cantidad 
				FROM ".$this->tabla_auditoria_personal."
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

	    $datos_log['observaciones'] = $this->revisarValorAtributo($datos_log['observaciones']);
	
	    $query = "INSERT INTO ".$this->tabla_auditoria_personal." (id_log, fecha_hora_log, id_usuario, operacion, tabla, legajo, netusername, netpcname, observaciones_log)
				  VALUES ( null,
						  '".date("Y-m-d H:i")."',
						  ".$_SESSION['id_usuario'].",
						  '".$datos_log['operacion']."',
						  '".$datos_log['tabla']."',
						  '".$datos_log['legajo']."',
						  '".$_SESSION['usuario']."',
						  '".$_SESSION['netpcname']."',
						   ".$datos_log['observaciones']."
						 )";
		
	    if ( !$this->ejecutarQuery($query) )
			return false;
	    
	    $this->desconectar($conexion);
	    
	    return true;
	}
	
	public function obtenerObservaciones()
	{
		$conexion = $this->conectar();
	    
		$sql = "SELECT id_log, observaciones_log FROM ".$this->tabla_auditoria_personal." WHERE observaciones_log LIKE '%egajo %'";
				
		$resultado = $this->ejecutarQuery($sql);
		
		$observaciones = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $observaciones;
	}
	
	// SE EXTRAE EL LEGAJO DE LA OBSERVACION
    public function extraerLegajo($observacion)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT hcd.extraerNumero('".$observacion."') AS legajo;";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		return $dato['legajo'];
    }
    
    public function asignarLegajo($legajo, $id_log)
	{
		$conexion = $this->conectar();
		
		$query = "UPDATE ".$this->tabla_auditoria_personal." SET legajo = ".$legajo." WHERE id_log = ".$id_log;
				
		if ( !$this->ejecutarQuery($query) )
			return false;
			
		$this->desconectar($conexion);
		
		return true;	
    }
}
?>
