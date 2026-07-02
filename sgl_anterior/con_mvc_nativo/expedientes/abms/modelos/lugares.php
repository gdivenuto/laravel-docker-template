<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class lugaresModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		return parent::conectarDB(2);
	}
		
	public function listar()
	{
		$conexion = $this->conectar();
		
		//para la busqueda
		$busqueda = "";
		if ($this->filtro['campo_orden'] != '')
		{
		    $busqueda = " WHERE ".$this->filtro['campo_orden']." LIKE '".$this->filtro['valor_buscado']."%'";
		}
		
		if ($this->filtro['campo_orden'] == 'vigente_Desde_grp')
		{
		    if ($this->filtro['valor_buscado'])
		    {	//por fecha de vigencia
			    $busqueda = " WHERE ".$this->filtro['campo_orden']." = '".$this->formatearFechaMySQL($this->filtro['valor_buscado'])."'";
		    }
		}
		
		//para filtrar por tipo
		$filtroTipo = "";
		if ($this->filtro['tipo'] != '')
		{
		    $filtroTipo = " AND tipo_grp = '".$this->filtro['tipo']."'";
		}
		
		//para filtrar por codigo
		$filtroCodigo = "";
		if ($this->filtro['codigo'] != '')
		{
		    $filtroCodigo = " AND codigo_grp = '".$this->filtro['codigo']."'";
		}
			
		// 05/01/2012
		$filtro_habilitados = "AND habilitado_grp = '1'";// POR DEFECTO MUESTRA LOS HABILITADOS
		if ($this->filtro['mostrar_todos'] == 'si')
		{
			$filtro_habilitados = "";// SE MUESTRAN TODOS
		}		
					
		$sql = "SELECT * FROM ".$this->tabla_lugares."
				".$busqueda."
				".$filtroTipo."
				".$filtroCodigo."
				".$filtro_habilitados."
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
			   ";
		
		// 16/01/2012: LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango']."
		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerRegistro($tipo, $codigo)
	{	
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT * FROM ".$this->tabla_lugares." WHERE tipo_grp = '".$tipo."' AND codigo_grp = '".$codigo."'";
	      
	    $resultado = $this->ejecutarQuery($sql);
	    
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	public function obtenerCantidad()
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT COUNT(*) AS cantidad FROM ".$this->tabla_lugares." FOR UPDATE";
		 
		$resultado = $this->ejecutarQuery($sql);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['cantidad'];
	}
	
	//	SE VERIFICA LA EXISTENCIA DE UN LUGAR DETERMINADO POR SU CODIGO 
	public function existe($tipo, $codigo)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT tipo_grp
				  FROM ".$this->tabla_lugares." 
				  WHERE tipo_grp = '".$tipo."'
				  AND codigo_grp = '".$codigo."'
				 ";
		
		$resultado = $this->ejecutarQuery($query);	

		$dato = $this->obtenerFila($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return ( $dato['tipo_grp'] );	
	}	
	
	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function verificarRegistroEntero()
	{
		$filtro_descripcion_grp   = $this->adaptarValorStringParaFiltro('descripcion_grp');
		
		$filtro_bloque_tipo       = $this->adaptarValorStringParaFiltro('bloque_tipo');
		
		$filtro_bloque_codigo     = $this->adaptarValorStringParaFiltro('bloque_codigo');
		
		$filtro_observaciones_grp = $this->adaptarValorStringParaFiltro('observaciones_grp');
		
		$filtro_vigente_Desde_grp = $this->adaptarValorStringParaFiltro('vigente_Desde_grp');
		
		$filtro_vigente_Hasta_grp = $this->adaptarValorStringParaFiltro('vigente_Hasta_grp');
		
		$conexion = $this->conectar();
				
		$query = "SELECT tipo_grp
				  FROM ".$this->tabla_lugares." 
				  WHERE tipo_grp = '".$_SESSION['tipo_grp_original']."'
				  AND codigo_grp = '".$_SESSION['codigo_grp_original']."'
				  ".$filtro_descripcion_grp."
				  ".$filtro_bloque_tipo."
				  ".$filtro_bloque_codigo."
				  ".$filtro_observaciones_grp."
				  ".$filtro_vigente_Desde_grp."
				  ".$filtro_vigente_Hasta_grp."
				  AND habilitado_grp = ".$_SESSION['habilitado_grp_original']."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return ( $dato['tipo_grp'] );
	}	
	
	public function validar($datos)
	{
		// Convierte a mayuscula el Tipo del Grupo (C, G ó V)
		$datos['tipo_grp'] 			= strtoupper($datos['tipo_grp']);
		
		$datos['descripcion_grp']   = $this->revisarValorAtributo($datos['descripcion_grp']);
		
		$datos['abreviatura_grp']   = $this->revisarValorAtributo($datos['abreviatura_grp']);
		
		$datos['bloque_tipo']       = $this->revisarValorAtributo($datos['bloque_tipo']);
		
		$datos['bloque_codigo']     = $this->revisarValorAtributo($datos['bloque_codigo']);
		
		$datos['observaciones_grp'] = $this->revisarValorAtributo($datos['observaciones_grp']);
		
		$datos['vigente_Desde_grp'] = $this->revisarValorFechaAtributo($datos['vigente_Desde_grp']);
		
		$datos['vigente_Hasta_grp'] = $this->revisarValorFechaAtributo($datos['vigente_Hasta_grp']);
				
		return $datos;
	}
	
	public function insertar($datos)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validar($datos);
		
		$query = "INSERT INTO ".$this->tabla_lugares."(tipo_grp, codigo_grp, descripcion_grp, abreviatura_grp, bloque_tipo, bloque_codigo, observaciones_grp, vigente_Desde_grp, vigente_Hasta_grp, habilitado_grp, id_usuario)
				  VALUES('".$datos['tipo_grp']."',
						 '".$datos['codigo_grp']."',
						  ".$datos['descripcion_grp'].",
						  ".$datos['abreviatura_grp'].",
						  ".$datos['bloque_tipo'].",
						  ".$datos['bloque_codigo'].",
						  ".$datos['observaciones_grp'].",
						  ".$datos['vigente_Desde_grp'].",
						  ".$datos['vigente_Hasta_grp'].",
						  ".$datos['habilitado_grp'].",
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
		$queryA = " SELECT *
					FROM ".$this->tabla_lugares."
					WHERE tipo_grp = '".$datos['tipo_grp']."'
					AND codigo_grp = '".$datos['codigo_grp']."'
				  ";
		$resultadoA = $this->ejecutarQuery($queryA);
		
		$datos_previos = $this->crearVector($resultadoA);
		
		$datos = $this->validar($datos);
		
		// SE MODIFICA
		$query = "UPDATE ".$this->tabla_lugares."
				  SET descripcion_grp = ".$datos['descripcion_grp'].",
					  abreviatura_grp = ".$datos['abreviatura_grp'].",
					  bloque_tipo = ".$datos['bloque_tipo'].",
					  bloque_codigo = ".$datos['bloque_codigo'].",
					  observaciones_grp = ".$datos['observaciones_grp'].",
					  vigente_Desde_grp = ".$datos['vigente_Desde_grp'].",
					  vigente_Hasta_grp = ".$datos['vigente_Hasta_grp'].",
					  habilitado_grp = ".$datos['habilitado_grp'].",
					  id_usuario = ".$datos['id_usuario']."
				  WHERE tipo_grp = '".$datos['tipo_grp']."'
				  AND codigo_grp = '".$datos['codigo_grp']."'
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

	public function eliminar($tipo, $codigo)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE OBTIENEN LOS DATOS ACTUALES PARA AUDITAR
		$queryA = " SELECT *
					FROM ".$this->tabla_lugares."
					WHERE tipo_grp = '".$tipo."'
					AND codigo_grp = '".$codigo."'
				  ";
		$resultadoA = $this->ejecutarQuery($queryA);
		
		$datos_previos = $this->crearVector($resultadoA);
		
		// SE ELIMINA
		$query = "DELETE FROM ".$this->tabla_lugares." 
				  WHERE tipo_grp = '".$tipo."'
				  AND codigo_grp = '".$codigo."'
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
	
	public function listadoModal($autor_tipo = '')
	{
		$conexion = $this->conectar();
		
		$filtro_tipo = " WHERE habilitado_grp = '1'";
		
		if ( $autor_tipo != '' )
		{
			// AL BUSCAR EL AUTOR
			$filtro_tipo .=  " AND tipo_grp = '".$autor_tipo."'";
		}
		else
		{
			// AL BUSCAR EL INICIADOR
			$filtro_tipo .= " AND tipo_grp <> 'C'";
		}
		
		$sql = "SELECT * FROM ".$this->tabla_lugares." ".$filtro_tipo;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
			
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function listadoModalIniciador($iniciador_tipo = '')
	{
		$conexion = $this->conectar();
		
		$filtro_tipo = " WHERE habilitado_grp = '1'";
		
		// AL BUSCAR EL AUTOR
		$filtro_tipo .=  ($iniciador_tipo != '') ? " AND tipo_grp = '".$iniciador_tipo."'" : " AND tipo_grp = 'G'";
				
		$sql = "SELECT * FROM ".$this->tabla_lugares." ".$filtro_tipo;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
			
		$this->desconectar($conexion);
		
		return $datos;
	}

    public function buscarNombreIniciador($iniciador_tipo, $iniciador_codigo, $para_giro = '')
    {
		$conexion = $this->conectar();
		
		$sin_comision = ( $para_giro == '' ) ? "AND tipo_grp <> 'C'" : "";
		
		$sql = "SELECT descripcion_grp 
				FROM ".$this->tabla_lugares."
                WHERE tipo_grp = '".$iniciador_tipo."'
				AND codigo_grp = '".$iniciador_codigo."'
			    ".$sin_comision."	 
               ";
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos[0]['descripcion_grp'];
	}
	
	public function buscarCodigoNombreIniciador($iniciador_tipo, $iniciador_codigo, $para_giro = '')
	{
		$conexion = $this->conectar();
		
		$sin_comision = ( $para_giro == '' ) ? "AND tipo_grp <> 'C'" : "";
		
		$sql = "SELECT codigo_grp, descripcion_grp 
				FROM ".$this->tabla_lugares."
                WHERE tipo_grp = '".$iniciador_tipo."'
				AND codigo_grp = '".$iniciador_codigo."'
			    ".$sin_comision."	 
               ";
		  
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
}
?>