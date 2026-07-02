<?php
if (!isset($_SESSION)) {
	session_start();
}

class auditoriaAdministracionModel extends ModeloBaseMySQLi
{
	public function conectar() {

		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}
		
    public function listar() {

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
		
	    $sql = "SELECT * FROM ".$this->tabla_auditoria_administracion."
				WHERE id_usuario IS NOT NULL
				".$filtro."
				ORDER BY id_log ".$_SESSION['ultimo_sentido']."
				".$limite;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerCantidad() {

	    $conexion = $this->conectar();
	    
	    $filtro = "";

		// SI SE FILTRA POR UN RANGO DE FECHAS
		if ( $this->filtro['f_fecha_desde'] != '' && $this->filtro['f_fecha_hasta'] != '' )
			$filtro .= " AND fecha_hora_log BETWEEN '".$this->filtro['f_fecha_desde']." 00:00:00' AND '".$this->filtro['f_fecha_hasta']." 23:59:59'";
		
		// SI SE FILTRA POR USUARIO
		if ($this->filtro['f_usuario'] != '')
		    $filtro .= " AND netusername LIKE '".$this->filtro['f_usuario']."%'";
		
	    $sql = "SELECT COUNT(id_log) AS cantidad 
				FROM ".$this->tabla_auditoria_administracion."
				WHERE id_usuario IS NOT NULL
				".$filtro;

	    $resultado = $this->ejecutarQuery($sql);
	    
	    $dato = $this->obtenerFila($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $dato['cantidad'];
	}
	
	public function registrarMovimiento($datos_log) {
		
		$conexion = $this->conectar();
	    
	    $datos_log['observaciones'] = $this->revisarValorAtributo($datos_log['observaciones']);
	    
	    $query = "INSERT INTO ".$this->tabla_auditoria_administracion." 
	    			(id_usuario, operacion, tabla, netusername, netpcname, observaciones_log)
				  VALUES ( ".$_SESSION['id_usuario'].",
						  '".$datos_log['operacion']."',
						  '".$datos_log['tabla']."',
						  '".$_SESSION['usuario']."',
						  '".$_SESSION['netpcname']."',
						   ".$datos_log['observaciones']."
						 )";

	    $this->ejecutarQuery($query);

	    $this->desconectar($conexion);	
	}
}
?>
