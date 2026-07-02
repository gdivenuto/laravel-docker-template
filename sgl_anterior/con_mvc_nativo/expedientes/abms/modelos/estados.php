<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class estadosModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		return parent::conectarDB(2);
	}
		
	public function listadoTotal()
	{	
		$conexion = $this->conectar();
		
		$filtro = "";
		
		//para filtrar por anio
		if ( $this->filtro['anio'] != '' )
		{
			$filtro .= " WHERE anio = ".$this->filtro['anio']."";
		}
		
		//para filtrar por tipo
		if ( $this->filtro['tipo'] != '' )
		{
			$filtro .= " AND tipo = '".$this->filtro['tipo']."'";
		}
		
		//para filtrar por numero
		if ( $this->filtro['numero'] != '' )
		{
			$filtro .= " AND numero = ".$this->filtro['numero']."";
		}
		
		//para filtrar por cuerpo
		if ( $this->filtro['cuerpo'] != '' )
		{
			$filtro .= " AND cuerpo = ".$this->filtro['cuerpo']."";
		}
		
		//para filtrar por alcance
		if ( $this->filtro['alcance'] != '' )
		{
			$filtro .= " AND alcance = ".$this->filtro['alcance']."";
		}
		
		//para filtrar por fecha_estado
		if ( $this->filtro['fecha_estado'] != '' )
		{
			$filtro .= " AND fecha_estado = '".$this->filtro['fecha_estado']."'";// $this->formatearFechaMySQL()
		}
		
		//para filtrar por orden_estado
		if ( $this->filtro['orden_estado'] != '' )
		{
			$filtro .= " AND orden_estado = '".$this->filtro['orden_estado']."'";
		}
							
		$sql = "SELECT Est.*, 
					   CodEst.codigo_estado AS codigo_estado, CodEst.nombre_estado AS nombre_estado,
					   U.codigo_usuario
				FROM (SELECT * FROM ".$this->tabla_estados."
					  ".$filtro."
					 )Est
				LEFT JOIN ".$this->tabla_codestados." CodEst ON CodEst.id_codestado = Est.id_codestado
				LEFT JOIN ".$this->tabla_usuarios." U ON (U.id_usuario = Est.id_usuario)
				ORDER BY Est.fecha_estado ASC, Est.orden_estado ASC 
			   ";
		
		$resultado = $this->ejecutarQuery($sql);
		
		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerCantidad()
	{	
		$conexion = $this->conectar();
				
		$filtro = "";
		
		//para filtrar por anio
		if ( $this->filtro['anio'] != '' )
		{
			$filtro .= " WHERE anio = ".$this->filtro['anio']."";
		}
		
		//para filtrar por tipo
		if ( $this->filtro['tipo'] != '' )
		{
			$filtro .= " AND tipo = '".$this->filtro['tipo']."'";
		}
		
		//para filtrar por numero
		if ( $this->filtro['numero'] != '' )
		{
			$filtro .= " AND numero = ".$this->filtro['numero']."";
		}
		
		//para filtrar por cuerpo
		if ( $this->filtro['cuerpo'] != '' )
		{
			$filtro .= " AND cuerpo = ".$this->filtro['cuerpo']."";
		}
		
		//para filtrar por alcance
		if ( $this->filtro['alcance'] != '' )
		{
			$filtro .= " AND alcance = ".$this->filtro['alcance']."";
		}
		
		$sql = "SELECT COUNT(*) AS cantidad 
				FROM ".$this->tabla_estados."
				".$filtro."
		       ";
		 
		$resultado = $this->ejecutarQuery($sql);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->obtenerFila($resultado);// SE CREA UN VECTOR CON EL RESULTADO
		
		$this->desconectar($conexion);
		
		return $dato['cantidad'];
	}
	
	// SE OBTIENE EL ULTIMO ORDEN DEL Estado PARA UN EXPEDIENTE DETERMINADO
	public function obtenerUltimoOrden($filtro)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT MAX(orden_estado) AS ultimoOrden 
				FROM ".$this->tabla_estados."
				WHERE anio = ".$filtro['anio']."
				AND tipo = '".$filtro['tipo']."'
				AND numero = ".$filtro['numero']."
				AND cuerpo = ".$filtro['cuerpo']."
				AND alcance = ".$filtro['alcance']."
		       ";
		$resultado = $this->ejecutarQuery($sql);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->obtenerFila($resultado);// SE CREA UN VECTOR CON EL RESULTADO
		
		$this->desconectar($conexion);
		
		return $dato['ultimoOrden'];
	}
	
	//	SE VERIFICA LA EXISTENCIA DE UN EXPEDIENTE DETERMINADO  
	public function existe($clave)
	{	
		$conexion = $this->conectar();
		
		$query = "SELECT tipo
				  FROM ".$this->tabla_estados."
				  WHERE anio = '".$clave['anio']."'
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND fecha_estado = '".$clave['fecha_estado']."'
				  AND orden_estado = '".$clave['orden_estado']."'
				 ";
		
		$resultado = $this->ejecutarQuery($query);
			
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return ($dato['tipo']);		
	}	
	
	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function verificarRegistroEntero()
	{	
		$conexion = $this->conectar();
		
		$filtro_observaciones_estado = $this->adaptarValorStringParaFiltro('observaciones_estado');
				
		$query = "SELECT anio
				  FROM ".$this->tabla_estados." 
				  WHERE anio = ".$_SESSION['anio_original']."
				  AND tipo = '".$_SESSION['tipo_original']."'
				  AND numero = ".$_SESSION['numero_original']."
				  AND cuerpo = ".$_SESSION['cuerpo_original']."
				  AND alcance = ".$_SESSION['alcance_original']."
				  AND fecha_estado = '".$_SESSION['fecha_estado_original']."'
				  AND orden_estado = '".$_SESSION['orden_estado_original']."'
				  AND id_codestado = ".$_SESSION['id_codestado_original']."
				  ".$filtro_observaciones_estado."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return ( $dato['anio'] );	
	}	
		
	public function insertar($datos)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos['observaciones_estado'] = $this->revisarValorAtributo($datos['observaciones_estado']);
		
		$query = "INSERT INTO ".$this->tabla_estados." (anio, tipo, numero, cuerpo, alcance, fecha_estado, orden_estado, id_codestado, observaciones_estado, id_usuario)
				  VALUES ( ".$datos['anio'].",
						  '".$datos['tipo']."',
						   ".$datos['numero'].",
						   ".$datos['cuerpo'].",
						   ".$datos['alcance'].",
						  '".$datos['fecha_estado']."',
						  '".$datos['orden_estado']."',
						   ".$datos['id_codestado'].",
						   ".$datos['observaciones_estado'].",
						   ".$datos['id_usuario']."
						 ) 
				 ";
		
		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			// LA CONSULTA FUE EXITOSA

			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
		
			$this->desconectar($conexion);
		
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log']     = "ALTA";
			$datos_log['tabla_log']         = $this->tabla_estados;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = "'".$datos['fecha_estado']."'";
			$datos_log['orden_log']         = $datos['orden_estado'];
			$datos_log['observaciones_log'] = "Estado: ".$datos['id_codestado']."";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;
	}
	
	public function modificar($datos)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos['observaciones_estado'] = $this->revisarValorAtributo($datos['observaciones_estado']);
		
		// SE OBTIENEN LOS DATOS ACTUALES PARA AUDITAR
		$queryA = " SELECT id_codestado
					FROM ".$this->tabla_estados."
					WHERE anio = ".$datos['anio']."
					AND tipo = '".$datos['tipo']."'
					AND numero = '".$datos['numero']."'
					AND cuerpo = ".$datos['cuerpo']."
					AND alcance = ".$datos['alcance']."
					AND fecha_estado = '".$datos['fecha_estado']."'
					AND orden_estado = '".$datos['orden_estado']."'
				  ";
		$resultadoA = $this->ejecutarQuery($queryA, $conexion);
		
		$codigo_modificado = $this->crearVector($resultadoA);
		
		// SE MODIFICA
		$query = "UPDATE ".$this->tabla_estados."
				  SET id_codestado = ".$datos['id_codestado'].",
					  observaciones_estado = ".$datos['observaciones_estado'].",
					  id_usuario = ".$datos['id_usuario']."
				  WHERE anio = ".$datos['anio']."
				  AND tipo = '".$datos['tipo']."'
				  AND numero = '".$datos['numero']."'
				  AND cuerpo = ".$datos['cuerpo']."
				  AND alcance = ".$datos['alcance']."
				  AND fecha_estado = '".$datos['fecha_estado']."'
				  AND orden_estado = '".$datos['orden_estado']."'
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
			$datos_log['tabla_log']         = $this->tabla_estados;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = "'".$datos['fecha_estado']."'";
			$datos_log['orden_log']         = $datos['orden_estado'];
			$datos_log['observaciones_log'] = "Estado: ".$codigo_modificado[0]['id_codestado'];
		    
		    //SE CARGA EN auditoria EL MOVIMIENTO
		    $modelo->registrarMovimiento($datos_log);
		}
		
		return true;		
	}
	
	public function eliminar($clave)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE OBTIENEN LOS DATOS ACTUALES PARA AUDITAR
		$queryA = " SELECT id_codestado
					FROM ".$this->tabla_estados."
					WHERE anio = ".$clave['anio']."
					AND tipo = '".$clave['tipo']."'
					AND numero = '".$clave['numero']."'
					AND cuerpo = ".$clave['cuerpo']."
					AND alcance = ".$clave['alcance']."
					AND fecha_estado = '".$clave['fecha_estado']."'
					AND orden_estado = '".$clave['orden_estado']."'
				  ";
		$resultadoA = $this->ejecutarQuery($queryA, $conexion);
		
		$codigo_eliminado = $this->crearVector($resultadoA);
		
		// SE ELIMINA
		$query = "DELETE FROM ".$this->tabla_estados."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND fecha_estado = '".$clave['fecha_estado']."'
				  AND orden_estado = '".$clave['orden_estado']."'
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
			$datos_log['operacion_log']          = "BAJA";
			$datos_log['tabla_log']              = $this->tabla_estados;
			$datos_log['anio_log']               = $clave['anio'];
			$datos_log['tipo_log']               = $clave['tipo'];
			$datos_log['numero_log']             = $clave['numero'];
			$datos_log['digito_log']             = "null";
			$datos_log['cuerpo_log']             = $clave['cuerpo'];
			$datos_log['alcance_log']            = $clave['alcance'];
			$datos_log['cuerpoalcance_log']      = "null";
			$datos_log['anexoalcance_log']       = "null";
			$datos_log['cuerpoanexoalcance_log'] = "null";
			$datos_log['anexo_log']              = "null";
			$datos_log['cuerpoanexo_log']        = "null";
			$datos_log['fecha_log']              = "'".$clave['fecha_estado']."'";
			$datos_log['orden_log']              = $clave['orden_estado'];
			$datos_log['observaciones_log']      = "Se ingresa el Estado ".$clave['anio']."-".$clave['tipo']."-".$clave['numero']."-".$clave['cuerpo']." con Fecha ".$clave['alcance'].$clave['fecha_estado']." y Orden ".$clave['orden_estado'];
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;		
	}
}
?>