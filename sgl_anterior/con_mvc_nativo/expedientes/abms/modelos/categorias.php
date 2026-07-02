<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class categoriasModel extends ModeloBaseMySQLi
{
	private $id_sistema = 2;
		
	public function listadoTotal()
	{
	    $conexion = $this->conectar($this->id_sistema);
		    
	    // SE ARMA EL FILTRO PARA LA BUSQUEDA
	    $busqueda = "";
	    if ( $this->filtro['campo_orden'] == 'vigencia_desde_categoria' )
	    {
			if ( $this->formatearFechaMySQL($this->filtro['valor_buscado']) )
			{
				$busqueda = " WHERE vigencia_desde_categoria = '".$this->formatearFechaMySQL($this->filtro['valor_buscado'])."'";
			}	
	    }
	    else
	    {
			$busqueda = " WHERE ".$this->filtro['campo_orden']." LIKE '".$this->filtro['valor_buscado']."%'";	
	    }
	    
	    // PARA FILTRAR POR id
	    $filtroId = "";
	    if ($this->filtro['id'] != 0 && $this->filtro['id'] != '')
	    {
			if ($this->filtro['boton'] == 'cancelar')
			{
				$_SESSION['ultimo_sentido'] = 'ASC';
			}
			else
			{
				$filtroId = " AND id_codcategoria = ".$this->filtro['id'];
			}	
	    }
	    	
		// 05/01/2012
		$filtro_habilitados = "AND habilitado_categoria = '1'";// POR DEFECTO MUESTRA LOS HABILITADOS
		if ($this->filtro['mostrar_todos'] == 'si')
		{
			$filtro_habilitados = "";// SE MUESTRAN TODOS
		}		
			
	    $sql = "SELECT * FROM ".$this->tabla_codcategoria."
				".$busqueda."	
				".$filtroId."
				".$filtro_habilitados."
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
				FOR UPDATE
			   ";
	   
	    // 16/01/2012: LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango']."
	    
	    $resultado = $this->ejecutarQuery($sql);
	    if (!$resultado)
			return false;
	    else
			$datos = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	public function obtenerRegistro($id)
	{	
	    $conexion = $this->conectar($this->id_sistema);
	    
	    $sql = "SELECT * FROM ".$this->tabla_codcategoria." WHERE id_codcategoria = '".$id."'";
	      
	    $resultado = $this->ejecutarQuery($sql);
	    
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	public function obtenerCantidad()
	{	
	    $conexion = $this->conectar($this->id_sistema);
	    
	    $sql = "SELECT COUNT(*) AS cantidad FROM ".$this->tabla_codcategoria." FOR UPDATE";
	      
	    $resultado = $this->ejecutarQuery($sql);
	    
	    $dato = $this->obtenerFila($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $dato['cantidad'];
	}
	
	//	SE VERIFICA LA EXISTENCIA DE UNA CATEGORIA DETERMINADA POR SU CODIGO 
	public function existe($codigo)
	{	
	    $conexion = $this->conectar($this->id_sistema);
	    
	    $query = "SELECT codigo_categoria
			      FROM ".$this->tabla_codcategoria." 
			      WHERE codigo_categoria = '".$codigo."'
			     ";
	    
	    $resultado = $this->ejecutarQuery($query);	
	    
	    $dato = $this->obtenerFila($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return ( $dato['codigo_categoria'] );	
	}	
	
	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function verificarRegistroEntero()
	{	
		$conexion = $this->conectar($this->id_sistema);
		
		$filtro_descripcion_categoria    = $this->adaptarValorStringParaFiltro('descripcion_categoria');
		
		$filtro_vigencia_desde_categoria = $this->adaptarValorStringParaFiltro('vigencia_desde_categoria');
		
		$filtro_vigencia_hasta_categoria = $this->adaptarValorStringParaFiltro('vigencia_hasta_categoria');
				
		$query = "SELECT id_codcategoria
				  FROM ".$this->tabla_codcategoria." 
				  WHERE id_codcategoria = ".$_SESSION['id_codcategoria_original']."
				  AND codigo_categoria = ".$_SESSION['codigo_categoria_original']."
				  ".$filtro_descripcion_categoria."
				  ".$filtro_vigencia_desde_categoria."
				  ".$filtro_vigencia_hasta_categoria."
				  AND habilitado_categoria = ".$_SESSION['habilitado_categoria_original']."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return ( $dato['id_codcategoria'] );
	}	
	
	public function validar($datos)
	{
		$datos['descripcion_categoria']    = $this->revisarValorAtributo($datos['descripcion_categoria']);
		
		$datos['vigencia_desde_categoria'] = $this->revisarValorFechaAtributo($datos['vigencia_desde_categoria']);

		$datos['vigencia_hasta_categoria'] = $this->revisarValorFechaAtributo($datos['vigencia_hasta_categoria']);		
				
		return $datos;
	}
	
	public function insertar($datos)
	{
		$conexion = $this->conectar($this->id_sistema);
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validar($datos);
		
		$query = "INSERT INTO ".$this->tabla_codcategoria."(id_codcategoria,codigo_categoria,descripcion_categoria,vigencia_desde_categoria,vigencia_hasta_categoria,habilitado_categoria,id_usuario)
				  VALUES(null,
						'".$datos['codigo_categoria']."',
						 ".$datos['descripcion_categoria'].",
						 ".$datos['vigencia_desde_categoria'].",
						 ".$datos['vigencia_hasta_categoria'].",
						 ".$datos['habilitado_categoria'].",
						 ".$datos['id_usuario']."
						)
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

	public function modificar($datos)
	{
		$conexion = $this->conectar($this->id_sistema);
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validar($datos);
		
		$query = "UPDATE ".$this->tabla_codcategoria."
				  SET codigo_categoria = '".$datos['codigo_categoria']."',
				  descripcion_categoria = ".$datos['descripcion_categoria'].",
				  vigencia_desde_categoria = ".$datos['vigencia_desde_categoria'].",
				  vigencia_hasta_categoria = ".$datos['vigencia_hasta_categoria'].",
				  habilitado_categoria = ".$datos['habilitado_categoria'].",
				  id_usuario = ".$datos['id_usuario']."
				  WHERE id_codcategoria = ".$datos['id_codcategoria']."
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
		$conexion = $this->conectar($this->id_sistema);
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$query = "DELETE FROM ".$this->tabla_codcategoria." WHERE id_codcategoria = ".$id;
		
		if ( !$this->ejecutarQuery($query) )
		{
		    $this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
		    return false;
		}	
		else
		{	
			// LA CONSULTA FUE EXITOSA
		    $this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
		}
		
		$this->desconectar($conexion);
		
		return true;	
	}
	
	public function listadoModal($habilitado = 1)
	{
		$conexion = $this->conectar($this->id_sistema);
		
		$sql = "SELECT * FROM ".$this->tabla_codcategoria." WHERE habilitado_categoria = ".$habilitado."";
		
		$resultado = $this->ejecutarQuery($sql);
		
		if (!$resultado)
		{
			return false;
		}
		else
		{
			$datos = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO
		}
		
		$this->desconectar($conexion);
		
		return $datos;
	}
}
?>
