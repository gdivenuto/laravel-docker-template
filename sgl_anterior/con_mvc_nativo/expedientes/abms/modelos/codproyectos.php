<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class codproyectosModel extends ModeloBaseMySQLi
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
		if ( $this->filtro['campo_orden'] == 'vigencia_desde_codproy' )
		{
		
			if ( $this->formatearFechaMySQL($this->filtro['valor_buscado']) )
			{
				$busqueda = " WHERE vigencia_desde_codproy = '".$this->formatearFechaMySQL($this->filtro['valor_buscado'])."'";
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
				$filtroId = " AND id_codproyecto = ".$this->filtro['id'];
			}	
		}
			
		// 05/01/2012
		$filtro_habilitados = "AND habilitado_codproy = '1'";// POR DEFECTO MUESTRA LOS HABILITADOS
		if ( $this->filtro['mostrar_todos'] == 'si' )
		{
			$filtro_habilitados = "";// SE MUESTRAN TODOS
		}		
					
		$sql = "SELECT * FROM ".$this->tabla_codproyectos."
		       ".$busqueda."
		       ".$filtroId."
		       ".$filtro_habilitados."
		       ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
		       ";
		
		// 16/01/2012: LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango']."
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
			
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerRegistro($id)
	{	
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT * FROM ".$this->tabla_codproyectos." WHERE id_codproyecto = '".$id."'";
	      
	    $resultado = $this->ejecutarQuery($sql);
	    
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	public function obtenerCantidad()
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT COUNT(*) AS cantidad FROM ".$this->tabla_codproyectos." FOR UPDATE";
		 
		$resultado = $this->ejecutarQuery($sql);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['cantidad'];
	}
	
	public function getCodigos()
	{
		$conexion = $this->conectar();
				
		$sql = "SELECT * FROM ".$this->tabla_codproyectos;	
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;		
	}			
		
	//	SE VERIFICA LA EXISTENCIA DE UN CODIGO DE PROYECTO DETERMINADO POR SU CODIGO 
	public function existe($codigo)
	{	
		$conexion = $this->conectar();
		
		$query = "SELECT codigo_proyecto
				  FROM ".$this->tabla_codproyectos." 
				  WHERE codigo_proyecto = ".$codigo."
				 ";
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
	    
	    return ( $dato['codigo_proyecto'] );	
	}	
	
	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function verificarRegistroEntero()
	{	
		$conexion = $this->conectar();
		
		$filtro_descripcion_proyecto   = $this->adaptarValorStringParaFiltro('descripcion_proyecto');
		
		$filtro_vigencia_desde_codproy = $this->adaptarValorStringParaFiltro('vigencia_desde_codproy');
		
		$filtro_vigencia_hasta_codproy = $this->adaptarValorStringParaFiltro('vigencia_hasta_codproy');
				
		$query = "SELECT id_codproyecto
				  FROM ".$this->tabla_codproyectos." 
				  WHERE id_codproyecto = ".$_SESSION['id_codproyecto_original']."
				  AND codigo_proyecto = ".$_SESSION['codigo_proyecto_original']."
				  ".$filtro_descripcion_proyecto."
				  ".$filtro_vigencia_desde_codproy."
				  ".$filtro_vigencia_hasta_codproy."
				  AND habilitado_codproy = ".$_SESSION['habilitado_codproy_original']."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return ( $dato['id_codproyecto'] );		
	}	
	
	public function validar($datos)
	{
		$datos['descripcion_proyecto']   = $this->revisarValorAtributo($datos['descripcion_proyecto']);
		
		$datos['vigencia_desde_codproy'] = $this->revisarValorFechaAtributo($datos['vigencia_desde_codproy']);

		$datos['vigencia_hasta_codproy'] = $this->revisarValorFechaAtributo($datos['vigencia_hasta_codproy']);
		
		return $datos;
	}
	
	public function insertar($datos)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validar($datos);
		
		$query = "INSERT INTO ".$this->tabla_codproyectos."(id_codproyecto,codigo_proyecto,descripcion_proyecto,vigencia_desde_codproy,vigencia_hasta_codproy,habilitado_codproy,id_usuario)
				  VALUES( null,
					    '".$datos['codigo_proyecto']."',
					     ".$datos['descripcion_proyecto'].",
					     ".$datos['vigencia_desde_codproy'].",
					     ".$datos['vigencia_hasta_codproy'].",
					     ".$datos['habilitado_codproy'].",
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
		
		$datos = $this->validar($datos);
		
		$query = "UPDATE ".$this->tabla_codproyectos."
				  SET codigo_proyecto = '".$datos['codigo_proyecto']."',
					  descripcion_proyecto = ".$datos['descripcion_proyecto'].",
					  vigencia_desde_codproy = ".$datos['vigencia_desde_codproy'].",
					  vigencia_hasta_codproy = ".$datos['vigencia_hasta_codproy'].",
					  habilitado_codproy = ".$datos['habilitado_codproy'].",
					  id_usuario = ".$datos['id_usuario']."
				  WHERE id_codproyecto = ".$datos['id_codproyecto']."
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
		
		$query = "DELETE FROM ".$this->tabla_codproyectos." WHERE id_codproyecto = ".$id;
		
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
}
?>