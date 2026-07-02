<?php
if (!isset($_SESSION))
	session_start();

class auditoriaDefensoriaModel extends ModeloBaseMySQLi
{
	public function conectar() {
		// Se conecta con el ID del sistema
		return parent::conectarDB(6);
	}
	
	private function getFiltro() {
		$filtro = "";

		if ( $this->filtro['f_fecha_desde'] != '' && $this->filtro['f_fecha_hasta'] != '' )
			$filtro .= " AND fecha_hora_log BETWEEN '".$this->filtro['f_fecha_desde']." 00:00:00' AND '".$this->filtro['f_fecha_hasta']." 23:59:59'";

		if ($this->filtro['f_usuario'] != '')
		    $filtro .= " AND netusername LIKE '".$this->filtro['f_usuario']."%'";

		return $filtro;
	}

    public function listar() {

		$conexion = $this->conectar();
	    
	    $limite = "";

		if ( $this->filtro['rango'] != 0 )
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];
		
	    $sql = "SELECT * FROM ".$this->tabla_auditoria_defensoria."
				WHERE id_usuario IS NOT NULL
				".$this->getFiltro()."
				ORDER BY id_log ".$_SESSION['ultimo_sentido']."
				".$limite;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerCantidad() {

	    $conexion = $this->conectar();
	    
	    $sql = "SELECT COUNT(id_log) AS cantidad 
				FROM ".$this->tabla_auditoria_defensoria."
				WHERE id_usuario IS NOT NULL
				".$this->getFiltro();

	    $resultado = $this->ejecutarQuery($sql);
	    
	    $dato = $this->obtenerFila($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $dato['cantidad'];
	}
	
	public function registrarMovimiento($operacion, $tabla, $id_registro, $observaciones) {
		
		$conexion = $this->conectar();
	    
	    $observaciones = $this->revisarValorAtributo($observaciones);
	    
	    $query = "INSERT INTO ".$this->tabla_auditoria_defensoria." 
	    			(id_usuario, 
	    			 operacion, 
	    			 tabla, 
	    			 id_registro,
	    			 netusername, 
	    			 netpcname, 
	    			 observaciones_log
	    			)
				  VALUES 
				  	( ".$_SESSION['id_usuario'].",
					 '".$operacion."',
					 '".$tabla."',
					  ".$id_registro.",
					 '".$_SESSION['usuario']."',
					 '".$_SESSION['netpcname']."',
					  ".$observaciones."
					)";

	    $this->ejecutarQuery($query);

	    $this->desconectar($conexion);	
	}
}
?>
