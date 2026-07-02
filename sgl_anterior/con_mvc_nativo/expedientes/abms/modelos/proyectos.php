<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class proyectosModel extends ModeloBaseMySQLi
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
		if (!empty($this->filtro['anio']))
		{
			$filtro .= " WHERE anio = ".$this->filtro['anio']."";
		}
		
		//para filtrar por tipo
		if (!empty($this->filtro['tipo']))
		{
			$filtro .= " AND tipo = '".$this->filtro['tipo']."'";
		}
		
		//para filtrar por numero
		if (!empty($this->filtro['numero'])){
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
		
		//para filtrar por alcance
		if (!empty($this->filtro['orden_proyecto'])){
			$filtro .= " AND orden_proyecto = ".$this->filtro['orden_proyecto']."";
		}
						
		$sql = "SELECT P.*,
					   CP.descripcion_proyecto,
					   U.codigo_usuario,
					   S.numero_promulga, S.fecha_promulga, S.decreto_promulga
				FROM (SELECT * FROM ".$this->tabla_proyectos."
					  ".$filtro."
					 ) P
				LEFT JOIN ".$this->tabla_codproyectos." CP ON (CP.id_codproyecto = P.id_codproyecto)	 
			    LEFT JOIN ".$this->tabla_usuarios." U ON (U.id_usuario = P.id_usuario)
				LEFT JOIN ".$this->tabla_sanciones." S ON (S.anio = P.anio AND S.tipo = P.tipo AND S.numero = P.numero AND S.cuerpo = P.cuerpo AND S.alcance = P.alcance AND S.orden_proyecto = P.orden_proyecto)
			    ORDER BY P.anio ASC, P.tipo ASC, P.numero ASC, P.cuerpo ASC, P.alcance ASC, P.orden_proyecto ASC
			   ";

		$resultado = $this->ejecutarQuery($sql);
		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function listadoRelacionados()
	{
		$conexion = $this->conectar();
						
		$sql = "SELECT P.*, CP.descripcion_proyecto 
				FROM (SELECT * FROM ".$this->tabla_proyectos."
					  WHERE anio = ".$this->filtro['anio']."
					  AND tipo = '".$this->filtro['tipo']."'
					  AND numero = ".$this->filtro['numero']."
					  AND cuerpo = ".$this->filtro['cuerpo']."
					  AND alcance = ".$this->filtro['alcance']."
					  ) P
				LEFT JOIN ".$this->tabla_codproyectos." CP 
				ON CP.id_codproyecto = P.id_codproyecto  
		       ";
		
		$resultado = $this->ejecutarQuery($sql);
		
		if ( !$resultado )
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
		if (!empty($this->filtro['anio']))
		{
			$filtro .= " WHERE anio = ".$this->filtro['anio']."";
		}
		
		//para filtrar por tipo
		if (!empty($this->filtro['tipo']))
		{
			$filtro .= " AND tipo = '".$this->filtro['tipo']."'";
		}
		
		//para filtrar por numero
		if (!empty($this->filtro['numero']))
		{
			$filtro .= " AND numero = ".$this->filtro['numero']."";
		}
		
		//para filtrar por cuerpo
		if ($this->filtro['cuerpo'] != '')
		{
			$filtro .= " AND cuerpo = ".$this->filtro['cuerpo']."";
		}
		
		//para filtrar por alcance
		if ($this->filtro['alcance'] != '')
		{
			$filtro .= " AND alcance = ".$this->filtro['alcance']."";
		}
		
		$sql = "SELECT COUNT(*) AS cantidad 
				FROM ".$this->tabla_proyectos."
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
	
	public function obtenerUltimoOrden($filtro)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT MAX(orden_proyecto) AS ultimoOrden 
				FROM ".$this->tabla_proyectos."
				WHERE anio = ".$filtro['anio']."
				AND tipo = '".$filtro['tipo']."'
				AND numero = ".$filtro['numero']."
				AND cuerpo = ".$filtro['cuerpo']."
				AND alcance = ".$filtro['alcance']."
			   ";
			   
		$resultado = $this->ejecutarQuery($sql);
		
		if (!$resultado)
		{
			return false;
		}
		else
		{
			$dato = $this->obtenerFila($resultado);
		}
		
		$this->desconectar($conexion);
		
		return $dato['ultimoOrden'];
	}
	
	//	SE VERIFICA LA EXISTENCIA DE UN EXPEDIENTE DETERMINADO  
	public function existe($clave)
	{	
		$conexion = $this->conectar();
		
		$query = "SELECT tipo
				  FROM ".$this->tabla_proyectos." 
				  WHERE anio = '".$clave['anio']."'
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND orden_proyecto = '".$clave['orden_proyecto']."'
				 ";
		
		$resultado = $this->ejecutarQuery($query);
			
		if (!$resultado)
		{
			return false;
		}
		else
		{
			$dato = $this->obtenerFila($resultado);
			if (!$dato['tipo'])
			{
				return false;
			}
		}
		
		$this->desconectar($conexion);
		
		return true;	
	}
	
	public function validar($datos)
	{
		$datos['extracto'] = $this->revisarValorAtributo($datos['extracto']);
		
		$datos['observaciones_proyecto'] = $this->revisarValorAtributo($datos['observaciones_proyecto']);
		
		return $datos;
	}
	
	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function verificarRegistroEntero()
	{	
		$conexion = $this->conectar();
		
		$filtro_extracto = $this->adaptarValorStringParaFiltro('extracto');
		
		$filtro_observaciones_proyecto = $this->adaptarValorStringParaFiltro('observaciones_proyecto');
						
		$query = "SELECT anio
				  FROM ".$this->tabla_proyectos." 
				  WHERE anio = ".$_SESSION['anio_original']."
				  AND tipo = '".$_SESSION['tipo_original']."'
				  AND numero = ".$_SESSION['numero_original']."
				  AND cuerpo = ".$_SESSION['cuerpo_original']."
				  AND alcance = ".$_SESSION['alcance_original']."
				  AND orden_proyecto = '".$_SESSION['orden_proyecto_original']."'
				  AND id_codproyecto = ".$_SESSION['id_codproyecto_original']."
				  ".$filtro_extracto."
				  ".$filtro_observaciones_proyecto."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		if (!$dato['anio'])
		{
			return false;
		}
		
		$this->desconectar($conexion);
		
		return true;
	}	
	
	public function insertar($datos)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validar($datos);
		
		$query = "INSERT INTO ".$this->tabla_proyectos." (anio, tipo, numero, cuerpo, alcance, orden_proyecto, id_codproyecto, extracto, observaciones_proyecto, id_usuario)
				  VALUES ( ".$datos['anio'].",
						  '".$datos['tipo']."',
						   ".$datos['numero'].",
						   ".$datos['cuerpo'].",
						   ".$datos['alcance'].",
						  '".$datos['orden_proyecto']."',
						   ".$datos['id_codproyecto'].",
						   ".$datos['extracto'].",
						   ".$datos['observaciones_proyecto'].",
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
		
			$descripcion_proyecto = $this->obtenerDescripcionProyecto($datos);
			
			$extracto_proyecto = $this->obtenerExtractoProyecto($datos);
			
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log']     = "ALTA";
			$datos_log['tabla_log']         = $this->tabla_proyectos;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = "null";
			$datos_log['orden_log']         = $datos['orden_proyecto'];
			$datos_log['observaciones_log'] = "Proyecto ".$datos['orden_proyecto'].": ".$descripcion_proyecto.", Extracto: ".$extracto_proyecto."";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;
	}
	
	public function modificar($datos)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validar($datos);
			
		$query = "UPDATE ".$this->tabla_proyectos."
				  SET id_codproyecto = ".$datos['id_codproyecto'].",
					  extracto = ".$datos['extracto'].",
					  observaciones_proyecto = ".$datos['observaciones_proyecto'].",
					  id_usuario = ".$datos['id_usuario']."
				  WHERE anio = ".$datos['anio']."
				  AND tipo = '".$datos['tipo']."'
				  AND numero = '".$datos['numero']."'
				  AND cuerpo = ".$datos['cuerpo']."
				  AND alcance = ".$datos['alcance']."
				  AND orden_proyecto = '".$datos['orden_proyecto']."'
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
		
			$descripcion_proyecto = $this->obtenerDescripcionProyecto($datos);
			
			$extracto_proyecto = $this->obtenerExtractoProyecto($datos);
								
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log']     = "MODIFICA";
			$datos_log['tabla_log']         = $this->tabla_proyectos;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = "null";
			$datos_log['orden_log']         = $datos['orden_proyecto'];
			$datos_log['observaciones_log'] = "Proyecto ".$datos['orden_proyecto'].": ".$descripcion_proyecto.", Extracto: ".$extracto_proyecto."";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;		
	}
	
	public function eliminar($clave)
	{
		$descripcion_proyecto = $this->obtenerDescripcionProyecto($clave);
		
		$extracto_proyecto = $this->obtenerExtractoProyecto($clave);
		
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE ELIMINAN LAS SANCIONES RELACIONADAS AL PROYECTO
		$querySanciones = "DELETE FROM ".$this->tabla_sanciones."
						   WHERE anio = ".$clave['anio']."
						   AND tipo = '".$clave['tipo']."'
						   AND numero = ".$clave['numero']."
						   AND cuerpo = ".$clave['cuerpo']."
						   AND alcance = ".$clave['alcance']."
						   AND orden_proyecto = '".$clave['orden_proyecto']."'
						  ";
		
 		if ( !$this->ejecutarQuery($querySanciones) )
 		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}	
							
		// SE ELIMINA EL PROYECTO
		$query = "DELETE FROM ".$this->tabla_proyectos."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND orden_proyecto = '".$clave['orden_proyecto']."'
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
			$datos_log['operacion_log']     = "BAJA";
			$datos_log['tabla_log']         = $this->tabla_proyectos;
			$datos_log['anio_log']          = $clave['anio'];
			$datos_log['tipo_log']          = $clave['tipo'];
			$datos_log['numero_log']        = $clave['numero'];
			$datos_log['cuerpo_log']        = $clave['cuerpo'];
			$datos_log['alcance_log']       = $clave['alcance'];
			$datos_log['fecha_log']         = "null";
			$datos_log['orden_log']         = $clave['orden_proyecto'];
			$datos_log['observaciones_log'] = "Proyecto ".$clave['orden_proyecto'].": ".$descripcion_proyecto.", Extracto: ".$extracto_proyecto."";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;		
	}
	
	public function listadoModal($clave)
	{	
		$conexion = $this->conectar();
				
		$sql = "SELECT P.orden_proyecto, CP.descripcion_proyecto
				FROM 
				( SELECT id_codproyecto, orden_proyecto
				  FROM ".$this->tabla_proyectos."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				) AS P
				LEFT JOIN ".$this->tabla_codproyectos." AS CP
				ON P.id_codproyecto = CP.id_codproyecto
			   ";
		
		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerDescripcionProyecto($clave)
	{
		$conexion = $this->conectar();
				
		$sql = "SELECT descripcion_proyecto
				FROM ".$this->tabla_codproyectos."
				WHERE id_codproyecto = ( SELECT id_codproyecto
									     FROM ".$this->tabla_proyectos."
									     WHERE anio = ".$clave['anio']."
									     AND tipo = '".$clave['tipo']."'
									     AND numero = ".$clave['numero']."
									     AND cuerpo = ".$clave['cuerpo']."
									     AND alcance = ".$clave['alcance']."
									     AND orden_proyecto = '".$clave['orden_proyecto']."'
									   )
			   ";
		
		$resultado = $this->ejecutarQuery($sql);
		
		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro['descripcion_proyecto'];
	}
	
	public function obtenerExtractoProyecto($clave)
	{
		$conexion = $this->conectar();
				
		$sql = "SELECT extracto
				FROM ".$this->tabla_proyectos."
				WHERE anio = ".$clave['anio']."
				AND tipo = '".$clave['tipo']."'
				AND numero = ".$clave['numero']."
				AND cuerpo = ".$clave['cuerpo']."
				AND alcance = ".$clave['alcance']."
				AND orden_proyecto = '".$clave['orden_proyecto']."'
			   ";
		
		$resultado = $this->ejecutarQuery($sql);
		
		$registro = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $registro['extracto'];
	}
	
}
?>
