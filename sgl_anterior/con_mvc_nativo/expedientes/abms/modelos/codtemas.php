<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class codtemasModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		return parent::conectarDB(2);
	}
		
	public function listadoTotal()
	{
	    $conexion = $this->conectar();
			    
	    //para la busqueda
	    $busqueda = "";
	    if ( $this->filtro['campo_orden'] == 'vigencia_desde_tema' )
	    {
			if ( $this->formatearFechaMySQL($this->filtro['valor_buscado']) )
			{
				$busqueda = " WHERE vigencia_desde_tema = '".$this->formatearFechaMySQL($this->filtro['valor_buscado'])."'";
			}	
	    }
	    else
	    {
		    $busqueda = " WHERE ".$this->filtro['campo_orden']." LIKE '".$this->filtro['valor_buscado']."%'";	
	    }
	    
	    // PARA FILTRAR POR id
	    $filtroId = "";
	    if ( $this->filtro['id'] != 0 && $this->filtro['id'] != '' )
	    {
			if ( $this->filtro['boton'] == 'cancelar' )
			{
				$_SESSION['ultimo_sentido'] = 'ASC';
			}
			else
			{
				$filtroId = " AND id_codtema = ".$this->filtro['id'];
			}	
	    }
	    	
		// 05/01/2012
		$filtro_habilitados = "AND habilitado_tema = '1'";// POR DEFECTO MUESTRA LOS HABILITADOS
		if ( $this->filtro['mostrar_todos'] == 'si' )
		{
			$filtro_habilitados = "";// SE MUESTRAN TODOS
		}		
			
	    $sql = "SELECT * FROM ".$this->tabla_codtemas."
				".$busqueda."
				".$filtroId."
				".$filtro_habilitados."
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
				FOR UPDATE
			   ";
	    // 16/01/2012: LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango']."
	    $resultado = $this->ejecutarQuery($sql);
	    if ( !$resultado )
			return false;
	    else
			$datos = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	public function obtenerRegistro($id)
	{	
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT * FROM ".$this->tabla_codtemas." WHERE id_codtema = '".$id."'";
	      
	    $resultado = $this->ejecutarQuery($sql);
	    
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	public function obtenerCantidad()
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT COUNT(*) AS cantidad FROM ".$this->tabla_codtemas." FOR UPDATE";
		 
		$resultado = $this->ejecutarQuery($sql);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['cantidad'];
	}
	
	//	SE VERIFICA LA EXISTENCIA DE UN TEMA DETERMINADO POR SU CODIGO 
	public function existe($codigo)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT codigo_tema
				  FROM ".$this->tabla_codtemas." 
				  WHERE codigo_tema = '".$codigo."'
				 ";
		
		$resultado = $this->ejecutarQuery($query);	
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return ( $dato['codigo_tema'] );	
	}	
	
	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function verificarRegistroEntero()
	{
		$conexion = $this->conectar();
		
		$filtro_descripcion_tema    = $this->adaptarValorStringParaFiltro('descripcion_tema');
		
		$filtro_vigencia_desde_tema = $this->adaptarValorStringParaFiltro('vigencia_desde_tema');
		
		$filtro_vigencia_hasta_tema = $this->adaptarValorStringParaFiltro('vigencia_hasta_tema');
				
		$query = "SELECT id_codtema
				  FROM ".$this->tabla_codtemas." 
				  WHERE id_codtema = ".$_SESSION['id_codtema_original']."
				  AND codigo_tema = ".$_SESSION['codigo_tema_original']."
				  ".$filtro_descripcion_tema."
				  ".$filtro_vigencia_desde_tema."
				  ".$filtro_vigencia_hasta_tema."
				  AND habilitado_tema = ".$_SESSION['habilitado_tema_original']."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return ( $dato['id_codtema'] );	
	}	
	
	public function validar($datos)
	{
		$datos['descripcion_tema']    = $this->revisarValorAtributo($datos['descripcion_tema']);
		
		$datos['vigencia_desde_tema'] = $this->revisarValorFechaAtributo($datos['vigencia_desde_tema']);

		$datos['vigencia_hasta_tema'] = $this->revisarValorFechaAtributo($datos['vigencia_hasta_tema']);

		return $datos;
	}
	
	public function insertar($datos)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validar($datos);
		
		$query = "INSERT INTO ".$this->tabla_codtemas."(id_codtema,codigo_tema,descripcion_tema,vigencia_desde_tema,vigencia_hasta_tema,habilitado_tema,id_usuario)
				  VALUES(null,
						 '".$datos['codigo_tema']."',
						  ".$datos['descripcion_tema'].",
						  ".$datos['vigencia_desde_tema'].",
						  ".$datos['vigencia_hasta_tema'].",
						  ".$datos['habilitado_tema'].",
						  ".$datos['id_usuario']."
						)";
		
		if ( !$this->ejecutarQuery($query) )
		{
		    $this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
		    return false;
		}	
		else
		{	// LA CONSULTA FUE EXITOSA
		 
		    $this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
		}
		
		$this->desconectar($conexion);
		
		return true;	
	}

	public function modificar($datos)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE OBTIENEN LOS DATOS ACTUALES PARA AUDITAR
		$sqlA = "SELECT * FROM ".$this->tabla_codtemas." WHERE id_codtema = ".$datos['id_codtema']."";
		
		$resultadoA = $this->ejecutarQuery($sqlA);
		
		$datos_previos = $this->crearVector($resultadoA);
		
		$datos = $this->validar($datos);
		
		$query = "UPDATE ".$this->tabla_codtemas."
				  SET codigo_tema = '".$datos['codigo_tema']."',
					  descripcion_tema = ".$datos['descripcion_tema'].",
					  vigencia_desde_tema = ".$datos['vigencia_desde_tema'].",
					  vigencia_hasta_tema = ".$datos['vigencia_hasta_tema'].",
					  habilitado_tema = ".$datos['habilitado_tema'].",
					  id_usuario = ".$datos['id_usuario']."
				  WHERE id_codtema = ".$datos['id_codtema']."
				 ";
		
		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{	// LA CONSULTA FUE EXITOSA
		    
		    $this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
   		}
		
		$this->desconectar($conexion);
		
		return true;	
	}

	public function eliminar($id)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE ELIMINA
		$query = "DELETE FROM ".$this->tabla_codtemas." WHERE id_codtema = ".$id;
		
		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}	
		else
		{	// LA CONSULTA FUE EXITOSA
		    
		    $this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
		}
		
		$this->desconectar($conexion);
		
		return true;	
	}
	
	public function listadoModal($habilitado = 1)
	{	
		$conexion = $this->conectar();
				
		$sql = "SELECT * FROM ".$this->tabla_codtemas." WHERE habilitado_tema = ".$habilitado;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
}
?>