<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class codestadosModel extends ModeloBaseMySQLi
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
		if ( $this->filtro['campo_orden'] == 'vigencia_desde_codestado' )
		{
		    if ( $this->formatearFechaMySQL($this->filtro['valor_buscado']) )
		    {
			    $busqueda = " WHERE vigencia_desde_codestado = '".$this->formatearFechaMySQL($this->filtro['valor_buscado'])."'";
		    }	
		}
		else
		{
		    $busqueda = " WHERE ".$this->filtro['campo_orden']." LIKE '".$this->filtro['valor_buscado']."%'";	
		}
		
		//para filtrar por id
		$filtroId = "";
		if ( $this->filtro['id'] != 0 && $this->filtro['id'] != '' )
		{
		    if ( $this->filtro['boton'] == 'cancelar' )
		    {
				$_SESSION['ultimo_sentido'] = 'ASC';
		    }
		    else
		    {
				$filtroId = " AND id_codestado = ".$this->filtro['id'];
		    }		
		}
			
		// 05/01/2012
		$filtro_habilitados = "AND habilitado_codestado = '1'";// POR DEFECTO MUESTRA LOS HABILITADOS
		if ( $this->filtro['mostrar_todos'] == 'si' )
		{
			$filtro_habilitados = "";// SE MUESTRAN TODOS
		}		
			
		$sql = "SELECT * FROM ".$this->tabla_codestados."
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
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT * FROM ".$this->tabla_codestados." WHERE id_codestado = '".$id."'";
	      
	    $resultado = $this->ejecutarQuery($sql);
	    
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	public function obtenerCantidad()
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT COUNT(*) AS cantidad FROM ".$this->tabla_codestados." FOR UPDATE";
		 
		$resultado = $this->ejecutarQuery($sql);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['cantidad'];
	}
	
	//	SE VERIFICA LA EXISTENCIA DE UN ESTADO DETERMINADO POR SU CODIGO 
	public function existe($codigo)
	{	
		$conexion = $this->conectar();
		
		$query = "SELECT codigo_estado
				  FROM ".$this->tabla_codestados." 
				  WHERE codigo_estado = '".$codigo."'
				 ";
		
		$resultado = $this->ejecutarQuery($query);	
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return ($dato['codigo_estado']);
	}	
	
	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function verificarRegistroEntero()
	{	
		$conexion = $this->conectar();
		
		$filtro_nombre_estado            = $this->adaptarValorStringParaFiltro('nombre_estado');
		
		$filtro_vigencia_desde_codestado = $this->adaptarValorStringParaFiltro('vigencia_desde_codestado');
		
		$filtro_vigencia_hasta_codestado = $this->adaptarValorStringParaFiltro('vigencia_hasta_codestado');
		
		$filtro_observaciones_codestado  = $this->adaptarValorStringParaFiltro('observaciones_codestado');
				
		$query = "SELECT id_codestado
				  FROM ".$this->tabla_codestados." 
				  WHERE id_codestado = ".$_SESSION['id_codestado_original']."
				  AND codigo_estado = ".$_SESSION['codigo_estado_original']."
				  ".$filtro_nombre_estado."
				  ".$filtro_vigencia_desde_codestado."
				  ".$filtro_vigencia_hasta_codestado."
				  ".$filtro_observaciones_codestado."
				  AND habilitado_codestado = ".$_SESSION['habilitado_codestado_original']."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return ($dato['id_codestado']);	
	}
	
	public function validar($datos)
	{
		$datos['nombre_estado']            = $this->revisarValorAtributo($datos['nombre_estado']);
		
		$datos['vigencia_desde_codestado'] = $this->revisarValorFechaAtributo($datos['vigencia_desde_codestado']);
		
		$datos['vigencia_hasta_codestado'] = $this->revisarValorFechaAtributo($datos['vigencia_hasta_codestado']);
		
		$datos['observaciones_codestado']  = $this->revisarValorAtributo($datos['observaciones_codestado']);

		return $datos;
	}
	
	public function insertar($datos)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validar($datos);
		
		$query = "INSERT INTO ".$this->tabla_codestados."(id_codestado,codigo_estado,nombre_estado,vigencia_desde_codestado,vigencia_hasta_codestado,observaciones_codestado,habilitado_codestado,id_usuario)
				  VALUES( null,
					  '".$datos['codigo_estado']."',
					   ".$datos['nombre_estado'].",
					   ".$datos['vigencia_desde_codestado'].",
					   ".$datos['vigencia_hasta_codestado'].",
					   ".$datos['observaciones_codestado'].",
					   ".$datos['habilitado_codestado'].",
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
			
			$this->desconectar($conexion);
  
		    //SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
		    $modelo = new auditoriaExpedientesModel();
		    
		    $datos_log = Array();
			$datos_log['operacion_log']     = "ALTA";
			$datos_log['tabla_log']         = $this->tabla_codestados;
			$datos_log['anio_log']          = 0;
			$datos_log['tipo_log']          = 'C';
			$datos_log['numero_log']        = 0;
			$datos_log['cuerpo_log']        = 0;
			$datos_log['alcance_log']       = 0;
			$datos_log['fecha_log']         = "'".date("Y-m-d")."'";
			$datos_log['orden_log']         = 0;
			$datos_log['observaciones_log'] = "Se ingresa el Codigo de Estado: ".$datos['codigo_estado']." - ".$datos['nombre_estado'].", el ".$datos['vigencia_desde_codestado'];
		    
		    //SE CARGA EN auditoria EL MOVIMIENTO
		    $modelo->registrarMovimiento($datos_log);
		}
		
		return true;	
	}

	public function modificar($datos)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE OBTIENEN LOS DATOS ACTUALES PARA AUDITAR
		$sqlA = "SELECT * FROM ".$this->tabla_codestados." WHERE id_codestado = ".$datos['id_codestado'];
		
		$resultadoA = $this->ejecutarQuery($sqlA);
		
		$datos_previos = $this->obtenerFila($resultadoA);

		$datos = $this->validar($datos);
		
		// SE MODIFICAN LOS DATOS
		$query = "UPDATE ".$this->tabla_codestados."
				  SET codigo_estado = '".$datos['codigo_estado']."',
					  nombre_estado = ".$datos['nombre_estado'].",
					  vigencia_desde_codestado = ".$datos['vigencia_desde_codestado'].",
					  vigencia_hasta_codestado = ".$datos['vigencia_hasta_codestado'].",
					  observaciones_codestado = ".$datos['observaciones_codestado'].",
					  habilitado_codestado = ".$datos['habilitado_codestado'].",
					  id_usuario = ".$datos['id_usuario']."
				  WHERE id_codestado = ".$datos['id_codestado']."
				 ";
		
		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}	
		else
		{	// LA CONSULTA FUE EXITOSA
		    
		    $this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
			
			$this->desconectar($conexion);
		
		    //SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
		    $modelo = new auditoriaExpedientesModel();
		    
		    $datos_log = Array();
			$datos_log['operacion_log']     = "MODIFICA";
			$datos_log['tabla_log']         = $this->tabla_codestados;
			$datos_log['anio_log']          = 0;
			$datos_log['tipo_log']          = 'C';
			$datos_log['numero_log']        = 0;
			$datos_log['cuerpo_log']        = 0;
			$datos_log['alcance_log']       = 0;
			$datos_log['fecha_log']         = "'".date("Y-m-d")."'";
			$datos_log['orden_log']         = 0;
			$datos_log['observaciones_log'] = $datos_previos['codigo_estado']." ".$datos_previos['nombre_estado']." ".$datos_previos['vigencia_desde_codestado'];
		    
		    //SE CARGA EN auditoria EL MOVIMIENTO
		    $modelo->registrarMovimiento($datos_log);
		}
		
		return true;	
	}

	public function eliminar($id)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE OBTIENEN LOS DATOS ACTUALES PARA AUDITAR
		$sqlA = "SELECT * FROM ".$this->tabla_codestados." WHERE id_codestado = ".$id;
		
		$resultadoA = $this->ejecutarQuery($sqlA);
		
		$datos_previos = $this->obtenerFila($resultadoA);

		// SE ELIMINA
		$query = "DELETE FROM ".$this->tabla_codestados." WHERE id_codestado = ".$id;
		
		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}	
		else
		{	// LA CONSULTA FUE EXITOSA
		    
		    $this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
		    
			$this->desconectar($conexion);
		
		    //SE TOMAN LOS DATOS A REGISTRAR EN auditoria
		    $modelo = new auditoriaExpedientesModel();
		    
		    $datos_log = Array();
			$datos_log['operacion_log']     = "BAJA";
			$datos_log['tabla_log']         = $this->tabla_codestados;
			$datos_log['anio_log']          = 0;
			$datos_log['tipo_log']          = 'C';
			$datos_log['numero_log']        = 0;
			$datos_log['cuerpo_log']        = 0;
			$datos_log['alcance_log']       = 0;
			$datos_log['fecha_log']         = "'".date("Y-m-d")."'";
			$datos_log['orden_log']         = 0;
			$datos_log['observaciones_log'] = $datos_previos['codigo_estado']." ".$datos_previos['nombre_estado']." ".$datos_previos['vigencia_desde_codestado'];
		   
		    //SE CARGA EN auditoria EL MOVIMIENTO
		    $modelo->registrarMovimiento($datos_log);
		}
		
		return true;	
	}
	
	public function listadoModal($habilitado = 1)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_codestados." WHERE habilitado_codestado = ".$habilitado;
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
}
?>