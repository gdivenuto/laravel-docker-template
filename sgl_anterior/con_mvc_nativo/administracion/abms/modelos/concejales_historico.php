<?php
if (!isset($_SESSION)) {
	session_start();
}

class concejales_historicoModel extends ModeloBaseMySQLi
{
    public function conectar()
	{
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}
		
    public function listar()
	{
		$conexion = $this->conectar();
		
		$filtro = "";
		$limite = "";
		
		// PARA FILTRAR POR APELLIDO Y NOMBRE
		if ( $this->filtro['f_apellido_nombre'] != '' )
		{
			$filtro .= " AND ch_apellido_nombre LIKE '%".$this->filtro['f_apellido_nombre']."%'";
		}
		
		// PARA FILTRAR POR BLOQUE
		if ( $this->filtro['f_bloque'] != '' )
		{
			$filtro .= " AND ch_bloque LIKE '%".$this->filtro['f_bloque']."%'";
		}
		
		// PARA FILTRAR POR FECHA DESDE
		if ( $this->filtro['f_desde'] != '' )
		{
			$filtro .= " AND ch_desde = ".$this->filtro['f_desde']."";
		}
		
		// PARA FILTRAR POR FECHA HASTA
		if ( $this->filtro['f_hasta'] != '' )
		{
			$filtro .= " AND ch_hasta = ".$this->filtro['f_hasta']."";
		}
		
		// PARA FILTRAR POR CARGO
		if ( $this->filtro['f_cargo'] != '' )
		{
			$filtro .= " AND ch_cargo LIKE '%".$this->filtro['f_cargo']."%'";
		}
		
		// PARA LIMITAR EL LISTADO
		if ( $this->filtro['rango'] != 0 )
		{
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];
		}
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS * 
				FROM ( SELECT *
					   FROM ".$this->tabla_concejales_historico."
					   WHERE ch_id > 0
					   ".$filtro."
					 )AS AUX
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido'].", ch_hasta ".$_SESSION['ultimo_sentido']."
				" . $limite;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
    
	public function obtenerCantidad()
	{
		$conexion = $this->conectar();
		
		$filtro = "";
		
		// PARA FILTRAR POR APELLIDO Y NOMBRE
		if ( $this->filtro['f_apellido_nombre'] != '' )
		{
			$filtro .= " AND ch_apellido_nombre LIKE '%".$this->filtro['f_apellido_nombre']."%'";
		}
		
		// PARA FILTRAR POR BLOQUE
		if ( $this->filtro['f_bloque'] != '' )
		{
			$filtro .= " AND ch_bloque LIKE '%".$this->filtro['f_bloque']."%'";
		}
		
		// PARA FILTRAR POR FECHA DESDE
		if ( $this->filtro['f_desde'] != '' )
		{
			$filtro .= " AND ch_desde = ".$this->filtro['f_desde']."";
		}
		
		// PARA FILTRAR POR FECHA HASTA
		if ( $this->filtro['f_hasta'] != '' )
		{
			$filtro .= " AND ch_hasta = ".$this->filtro['f_hasta']."";
		}
		
		// PARA FILTRAR POR CARGO
		if ( $this->filtro['f_cargo'] != '' )
		{
			$filtro .= " AND ch_cargo LIKE '%".$this->filtro['f_cargo']."%'";
		}
		
		
		$query = "SELECT COUNT(ch_id) AS cantidad
				  FROM ".$this->tabla_concejales_historico."
				  WHERE ch_id > 0
				  ".$filtro;
		   		  
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		return $dato['cantidad'];
	}
	
	public function obtenerUltimoId()
	{
		return parent::obtenerUltimoCodigo($this->tabla_concejales_historico, 'ch_id');
	}
	
    public function obtenerRegistro($ch_id)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_concejales_historico." WHERE ch_id = ".$ch_id;
			   
		$resultado = $this->ejecutarQuery($sql);
		
		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro;
    }

    public function existe($nombre) 
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT ch_id FROM ".$this->tabla_concejales_historico." WHERE ch_apellido_nombre = '".$nombre."'";
		  		  
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		// Si existe o no
		return ( $dato['ch_id'] != '' );	
    }

    public function validarDatos($datos)
    {
		$datos['ch_apellido_nombre'] = $this->revisarValorAtributo(strip_tags($datos['ch_apellido_nombre']));
		
		$datos['ch_bloque'] = $this->revisarValorAtributo(strip_tags($datos['ch_bloque']));
		
		$datos['ch_desde'] = $this->revisarValorAtributo(strip_tags($datos['ch_desde']));
		
		$datos['ch_hasta'] = $this->revisarValorAtributo(strip_tags($datos['ch_hasta']));
		
		$datos['ch_cargo'] = $this->revisarValorAtributo(strip_tags($datos['ch_cargo']));
		
		return $datos;
    }
	
    //	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
    public function noLoModificoOtroUsuario()
    {
		$conexion = $this->conectar();
		
		$query = "SELECT ch_id
				  FROM ".$this->tabla_concejales_historico." 
				  WHERE ch_id = ".$_SESSION['ch_id_original']."
				  ".$this->adaptarValorStringParaFiltro('ch_apellido_nombre')."
				  ".$this->adaptarValorStringParaFiltro('ch_bloque')."
				  ".$this->adaptarValorStringParaFiltro('ch_desde')."
				  ".$this->adaptarValorStringParaFiltro('ch_hasta')."
				  ".$this->adaptarValorStringParaFiltro('ch_cargo');

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( $dato['ch_id'] );
    }

    public function insertar($datos) {

		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "INSERT INTO ".$this->tabla_concejales_historico." 
					(ch_apellido_nombre, ch_bloque, ch_desde, ch_hasta, ch_cargo)
				  VALUES
				  	(".$datos['ch_apellido_nombre'].", 
				  	 ".$datos['ch_bloque'].", 
				  	 ".$datos['ch_desde'].", 
				  	 ".$datos['ch_hasta'].", 
				  	 ".$datos['ch_cargo'].");";
		
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion("ALTA", $this->tabla_concejales_historico, "Se ingresa el Cjal. ".LibreriaGeneral::eliminarComillaSimple($datos['ch_apellido_nombre'])." como histórico.");
		}
		
		return true;	
    }
	
    public function modificar($datos) {
		
		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "UPDATE ".$this->tabla_concejales_historico."
				  SET ch_apellido_nombre = ".$datos['ch_apellido_nombre'].",
					  ch_bloque = ".$datos['ch_bloque'].",
					  ch_desde = ".$datos['ch_desde'].",
					  ch_hasta = ".$datos['ch_hasta'].",
					  ch_cargo = ".$datos['ch_cargo']."
				  WHERE ch_id = ".$datos['ch_id'];
				
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_concejales_historico, "Se modifica el Cjal. ".LibreriaGeneral::eliminarComillaSimple($datos['ch_apellido_nombre'])." como histórico.");
		}
		
		return true;	
    }
    
    public function eliminar($codigo) {
    	
    	// Previamente se obtiene la info para auditar
    	$info = $this->obtenerRegistro($codigo);

		$conexion = $this->conectar();
		
		$query = "DELETE FROM ".$this->tabla_concejales_historico." WHERE ch_id = ".$codigo;
		
		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {			
			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_concejales_historico, "Se elimina el Cjal. ".$info['ch_apellido_nombre']." del histórico.");
		}
		
		return true;	
    }
}
?>
