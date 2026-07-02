<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class informesModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		return parent::conectarDB(2);
	}
		
	// SE OBTIENEN LOS INFORMES DE UN GIRO DETERMINADO
	public function listar($filtro)
	{
		$conexion = $this->conectar();
		
		$filtro_orden_informe = ( $filtro['orden_informe'] ) ? "AND orden_informe = ".$filtro['orden_informe'] :  "";
		
		$sql = "SELECT *
				FROM ".$this->tabla_informes."
				WHERE anio = ".$filtro['anio']."
				AND tipo = '".$filtro['tipo']."'
				AND numero = ".$filtro['numero']."
				AND cuerpo = ".$filtro['cuerpo']."
				AND alcance = ".$filtro['alcance']."
				AND orden_giro = ".$filtro['orden_giro']."
				".$filtro_orden_informe."
			   ";
			   
		$resultado = $this->ejecutarQuery($sql);
		
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	// SE OBTIENE EL ULTIMO ORDEN DEL INFORME PARA UN GIRO DETERMINADO
	public function obtenerUltimoOrden($filtro)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT MAX(orden_informe) AS ultimoOrden 
				FROM ".$this->tabla_informes."
				WHERE anio = ".$filtro['anio']."
				AND tipo = '".$filtro['tipo']."'
				AND numero = ".$filtro['numero']."
				AND cuerpo = ".$filtro['cuerpo']."
				AND alcance = ".$filtro['alcance']."
				AND orden_giro = '".$filtro['orden_giro']."'
			   ";
			   
		$resultado = $this->ejecutarQuery($sql);
		
		if (!$resultado)
		{
			$ultimoOrden = 0;
		}
		else
		{
			$dato = $this->obtenerFila($resultado);
			$ultimoOrden = $dato['ultimoOrden'];
		}
		
		$this->desconectar($conexion);
		
		return $ultimoOrden;
	}
	
	//	SE VERIFICA LA EXISTENCIA DE UN INFORME DETERMINADO  
	public function existe($clave)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT orden_informe
				  FROM ".$this->tabla_informes." 
				  WHERE anio = '".$clave['anio']."'
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND orden_giro = '".$clave['orden_giro']."'
				  AND orden_informe = '".$clave['orden_informe']."'
				 ";
		$resultado = $this->ejecutarQuery($query);	
		
		if (!$resultado)
		{
			return false;
		}
		else
		{
			$dato = $this->obtenerFila($resultado);
			if ( !$dato['orden_informe'] )
			{
				return false;
			}
		}	
		
		$this->desconectar($conexion);
		
		return true;	
	}	
	
	public function validarDatos($datos)
	{
		$datos['fecha_pedido_informe']  = $this->revisarValorFechaAtributo($datos['fecha_pedido_informe']);
		
		$datos['fecha_vuelta_informe']  = $this->revisarValorFechaAtributo($datos['fecha_vuelta_informe']);
		
		$datos['detalle_informe']       = $this->revisarValorAtributo($datos['detalle_informe']);
		
		$datos['observaciones_informe'] = $this->revisarValorAtributo($datos['observaciones_informe']);
		
		return $datos;
	}
	
	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function verificarRegistroEntero()
	{
		$conexion = $this->conectar();
		
		$filtro_fecha_pedido_informe  = $this->adaptarValorStringParaFiltro('fecha_pedido_informe');
		
		$filtro_fecha_vuelta_informe  = $this->adaptarValorStringParaFiltro('fecha_vuelta_informe');
		
		$filtro_detalle_informe       = $this->adaptarValorStringParaFiltro('detalle_informe');
		
		$filtro_observaciones_informe = $this->adaptarValorStringParaFiltro('observaciones_informe');
						
		$query = "SELECT anio
				  FROM ".$this->tabla_informes." 
				  WHERE anio = ".$_SESSION['anio_original']."
				  AND tipo = '".$_SESSION['tipo_original']."'
				  AND numero = ".$_SESSION['numero_original']."
				  AND cuerpo = ".$_SESSION['cuerpo_original']."
				  AND alcance = ".$_SESSION['alcance_original']."
				  AND orden_giro = '".$_SESSION['orden_giro_original']."'
				  AND orden_informe = '".$_SESSION['orden_informe_original']."'
				  ".$filtro_fecha_pedido_informe."
				  ".$filtro_fecha_vuelta_informe."
				  ".$filtro_detalle_informe."
				  ".$filtro_observaciones_informe."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);

		return ( $dato['anio'] );
	}	
	
	// SE AGREGA UN INFORME
	public function insertar($datos)
	{
		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$query = "INSERT INTO ".$this->tabla_informes." (anio, tipo, numero, cuerpo, alcance, orden_giro, orden_informe, fecha_pedido_informe, fecha_vuelta_informe, detalle_informe, observaciones_informe, id_usuario)
				  VALUES ( ".$datos['anio'].",
						  '".$datos['tipo']."',
						   ".$datos['numero'].",
						   ".$datos['cuerpo'].",
						   ".$datos['alcance'].",
						  '".$datos['orden_giro']."',
						  '".$datos['orden_informe']."',
						   ".$datos['fecha_pedido_informe'].",
						   ".$datos['fecha_vuelta_informe'].",
						   ".$datos['detalle_informe'].",
						   ".$datos['observaciones_informe'].",
						   ".$datos['id_usuario']."
						  ) ";
		
		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			// LA CONSULTA FUE EXITOSA
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
						
			// SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log']     = "ALTA";
			$datos_log['tabla_log']         = $this->tabla_informes;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = $datos['fecha_pedido_informe'];// Fecha de Pedido del Informe
			$datos_log['orden_log']         = $datos['orden_giro'];
			$datos_log['observaciones_log'] = "Orden Informe: ".$datos['orden_informe'];
			
			// SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		$this->desconectar($conexion);
		
		return true;
	}
	
	// SE MODIFICA UN INFORME
	public function modificar($datos)
	{
		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE MODIFICA
		$query = "UPDATE ".$this->tabla_informes."
				  SET fecha_pedido_informe = ".$datos['fecha_pedido_informe'].",
					  fecha_vuelta_informe = ".$datos['fecha_vuelta_informe'].",
					  detalle_informe = ".$datos['detalle_informe'].",
					  observaciones_informe = ".$datos['observaciones_informe'].",
					  id_usuario = ".$datos['id_usuario']."
				  WHERE anio = ".$datos['anio']."
				  AND tipo = '".$datos['tipo']."'
				  AND numero = '".$datos['numero']."'
				  AND cuerpo = ".$datos['cuerpo']."
				  AND alcance = ".$datos['alcance']."
				  AND orden_giro = '".$datos['orden_giro']."'
				  AND orden_informe = '".$datos['orden_informe']."'
				 ";
				
		if (!$this->ejecutarQuery($query))
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{	// LA CONSULTA FUE EXITOSA
			
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
						
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log']     = "MODIFICA";
			$datos_log['tabla_log']         = $this->tabla_informes;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = $datos['fecha_pedido_informe'];// Fecha de Pedido del Informe
			$datos_log['orden_log']         = $datos['orden_giro'];
			$datos_log['observaciones_log'] = "Orden Informe: ".$datos['orden_informe'];
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		$this->desconectar($conexion);//SE CIERRA LA CONEXION
		
		return true;		
	}
			
	// SE ELIMINA UN INFORME
	public function eliminar($clave)
	{
	    $conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE ELIMINA
		$query = "DELETE FROM ".$this->tabla_informes."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND orden_giro = '".$clave['orden_giro']."'
				  AND orden_informe = '".$clave['orden_informe']."'
				 ";
		
		if (!$this->ejecutarQuery($query))
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}	
		else
		{	// LA CONSULTA FUE EXITOSA
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
						
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log']     = "BAJA";
			$datos_log['tabla_log']         = $this->tabla_informes;
			$datos_log['anio_log']          = $clave['anio'];
			$datos_log['tipo_log']          = $clave['tipo'];
			$datos_log['numero_log']        = $clave['numero'];
			$datos_log['cuerpo_log']        = $clave['cuerpo'];
			$datos_log['alcance_log']       = $clave['alcance'];
			$datos_log['fecha_log']         = "null";
			$datos_log['orden_log']         = $clave['orden_giro'];
			$datos_log['observaciones_log'] = "Orden Informe: ".$clave['orden_informe'];
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		$this->desconectar($conexion);
		
		return true;		
	}
}
?>