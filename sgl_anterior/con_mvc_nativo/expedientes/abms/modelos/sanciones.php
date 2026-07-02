<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class sancionesModel extends ModeloBaseMySQLi
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
		if ( $this->filtro['alcance'] != '')
		{
			$filtro .= " AND alcance = ".$this->filtro['alcance']."";
		}
		
		//para filtrar por orden_proyecto
		if ( $this->filtro['orden_proyecto'] != '' )
		{
			$filtro .= " AND orden_proyecto = '".$this->filtro['orden_proyecto']."'";
		}
		
		//para filtrar por cuerpo
		if ( $this->filtro['fecha_sancion'] != '' )
		{
			$filtro .= " AND fecha_sancion = '".$this->filtro['fecha_sancion']."'";// $this->formatearFechaMySQL()
		}
		
							
		$sql = "SELECT S.*,
					   CodP.descripcion_proyecto,
					   U.codigo_usuario	
				FROM (SELECT * FROM ".$this->tabla_sanciones."
					  ".$filtro."
					 )S
				LEFT JOIN ".$this->tabla_proyectos." P ON (P.anio = S.anio AND P.tipo = S.tipo AND P.numero = S.numero AND P.cuerpo = S.cuerpo AND P.alcance = S.alcance AND P.orden_proyecto = S.orden_proyecto)
				LEFT JOIN ".$this->tabla_codproyectos." CodP ON CodP.id_codproyecto = P.id_codproyecto
				LEFT JOIN ".$this->tabla_usuarios." U ON (U.id_usuario = S.id_usuario)
				ORDER BY S.anio ASC, S.tipo ASC, S.numero ASC, S.cuerpo ASC, S.alcance ASC, S.orden_proyecto ASC, S.fecha_sancion ASC
			   ";
		
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
				FROM ".$this->tabla_sanciones."
				".$filtro."
		       ";
		 
		$resultado = $this->ejecutarQuery($sql);
		
		if (!$resultado)
		{
			return false;
		}
		else
		{
			$dato = $this->obtenerFila($resultado);// SE CREA UN VECTOR CON EL RESULTADO
		}
		
		$this->desconectar($conexion);
		
		return $dato['cantidad'];
	}
	
	public function obtenerUltimoOrden($filtro)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT MAX(orden_proyecto) AS ultimoOrden 
				FROM ".$this->tabla_sanciones."
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
				  FROM ".$this->tabla_sanciones." 
				  WHERE anio = '".$clave['anio']."'
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND orden_proyecto = '".$clave['orden_proyecto']."'
				  AND fecha_sancion = '".$clave['fecha_sancion']."'
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
				
		$filtro_numero_sancion                 = $this->adaptarValorStringParaFiltro('numero_sancion');
		
		$filtro_fecha_promulga                 = $this->adaptarValorStringParaFiltro('fecha_promulga');
		
		$filtro_numero_promulga                = $this->adaptarValorStringParaFiltro('numero_promulga');
		
		$filtro_decreto_promulga               = $this->adaptarValorStringParaFiltro('decreto_promulga');
		
		$filtro_fecha_veto                     = $this->adaptarValorStringParaFiltro('fecha_veto');
		
		$filtro_decreto_veto                   = $this->adaptarValorStringParaFiltro('decreto_veto');
		
		$filtro_decreto_presidencia            = $this->adaptarValorStringParaFiltro('decreto_presidencia');
		
		$filtro_fecha_remision_de_comunicacion = $this->adaptarValorStringParaFiltro('fecha_remision_de_comunicacion');
		
		$filtro_fecha_1er_vto_comunicacion     = $this->adaptarValorStringParaFiltro('fecha_1er_vto_comunicacion');
		
		$filtro_fecha_2do_vto_comunicacion     = $this->adaptarValorStringParaFiltro('fecha_2do_vto_comunicacion');
		
		$filtro_fecha_rta_comunicacion         = $this->adaptarValorStringParaFiltro('fecha_rta_comunicacion');
		
		$filtro_observaciones_sancion          = $this->adaptarValorStringParaFiltro('observaciones_sancion');
		
		$query = "SELECT anio
				  FROM ".$this->tabla_sanciones." 
				  WHERE anio = ".$_SESSION['anio_original']."
				  AND tipo = '".$_SESSION['tipo_original']."'
				  AND numero = ".$_SESSION['numero_original']."
				  AND cuerpo = ".$_SESSION['cuerpo_original']."
				  AND alcance = ".$_SESSION['alcance_original']."
				  AND orden_proyecto= '".$_SESSION['orden_proyecto_original']."'
				  AND fecha_sancion = '".$_SESSION['fecha_sancion_original']."'
				  ".$filtro_numero_sancion."
				  ".$filtro_fecha_promulga."
				  ".$filtro_numero_promulga."
				  ".$filtro_decreto_promulga."
				  ".$filtro_fecha_veto."
				  ".$filtro_decreto_veto."
				  ".$filtro_decreto_presidencia."
				  ".$filtro_fecha_remision_de_comunicacion."
				  ".$filtro_fecha_1er_vto_comunicacion."
				  ".$filtro_fecha_2do_vto_comunicacion."
				  ".$filtro_fecha_rta_comunicacion."
				  ".$filtro_observaciones_sancion."
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
	
	public function registrar_estado_girado_a_comision($datos, $fecha, $orden)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		//SE CARGA EL estado '1 Registrado' PARA DICHO EXPEDIENTE
		$query_estado = "INSERT INTO ".$this->tabla_estados." (anio, tipo, numero, cuerpo, alcance, fecha_estado, orden_estado, id_codestado, observaciones_estado, id_usuario)
						 VALUES ( ".$datos['anio'].",
								 '".$datos['tipo']."',
								  ".$datos['numero'].",
								  ".$datos['cuerpo'].",
								  ".$datos['alcance'].",
								 '".$fecha."',
								  ".$orden.",
								  8,
								  null,
								  ".$datos['id_usuario']."
								)";
		
		if ( !$this->ejecutarQuery($query_estado) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{	
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
			
			$this->desconectar($conexion);
		
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log']          = "ALTA";
			$datos_log['tabla_log']              = $this->tabla_estados;
			$datos_log['anio_log']               = $datos['anio'];
			$datos_log['tipo_log']               = $datos['tipo'];
			$datos_log['numero_log']             = $datos['numero'];
			$datos_log['digito_log']             = "null";
			$datos_log['cuerpo_log']             = $datos['cuerpo'];
			$datos_log['alcance_log']            = $datos['alcance'];
			$datos_log['cuerpoalcance_log']      = "null";
			$datos_log['anexoalcance_log']       = "null";
			$datos_log['cuerpoanexoalcance_log'] = "null";
			$datos_log['anexo_log']              = "null";
			$datos_log['cuerpoanexo_log']        = "null";
			$datos_log['fecha_log']              = $datos['fecha_promulga'];
			$datos_log['orden_log']              = $orden;
			$datos_log['observaciones_log']      = "AUTOMATICO";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;
	}
		
	public function registrar_estado_promulgado($datos, $fecha, $orden)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		//SE CARGA EL estado '8 Promulgado' PARA DICHO EXPEDIENTE
		$query_estado = "INSERT INTO ".$this->tabla_estados." (anio, tipo, numero, cuerpo, alcance, fecha_estado, orden_estado, id_codestado, observaciones_estado, id_usuario)
						 VALUES (".$datos['anio'].",
								'".$datos['tipo']."',
								 ".$datos['numero'].",
								 ".$datos['cuerpo'].",
								 ".$datos['alcance'].",
								 ".$fecha.",
								 ".$orden.",
								 8,
								 null,
								 ".$datos['id_usuario']."
								) ";
		
		if ( !$this->ejecutarQuery($query_estado) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{	
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
			
			$this->desconectar($conexion);
		
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log']          = "ALTA";
			$datos_log['tabla_log']              = $this->tabla_estados;
			$datos_log['anio_log']               = $datos['anio'];
			$datos_log['tipo_log']               = $datos['tipo'];
			$datos_log['numero_log']             = $datos['numero'];
			$datos_log['digito_log']             = "null";
			$datos_log['cuerpo_log']             = $datos['cuerpo'];
			$datos_log['alcance_log']            = $datos['alcance'];
			$datos_log['cuerpoalcance_log']      = "null";
			$datos_log['anexoalcance_log']       = "null";
			$datos_log['cuerpoanexoalcance_log'] = "null";
			$datos_log['anexo_log']              = "null";
			$datos_log['cuerpoanexo_log']        = "null";
			$datos_log['fecha_log']              = $fecha;
			$datos_log['orden_log']              = $orden;
			$datos_log['observaciones_log']      = "AUTOMATICO";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;
	}
	
	public function validarDatos($datos)
	{
		$datos['numero_sancion']                 = $this->revisarValorAtributo($datos['numero_sancion']);
		
		$datos['fecha_promulga']                 = $this->revisarValorFechaAtributo($datos['fecha_promulga']);
		
		$datos['numero_promulga']                = $this->revisarValorAtributo($datos['numero_promulga']);
		
		$datos['decreto_promulga']               = $this->revisarValorAtributo($datos['decreto_promulga']);
		
		$datos['fecha_veto']                     = $this->revisarValorFechaAtributo($datos['fecha_veto']);
		
		$datos['decreto_veto']                   = $this->revisarValorAtributo($datos['decreto_veto']);
		
		$datos['decreto_presidencia']            = $this->revisarValorAtributo($datos['decreto_presidencia']);
		
		$datos['fecha_remision_de_comunicacion'] = $this->revisarValorFechaAtributo($datos['fecha_remision_de_comunicacion']);
		
		$datos['fecha_1er_vto_comunicacion']     = $this->revisarValorFechaAtributo($datos['fecha_1er_vto_comunicacion']);
		
		$datos['fecha_2do_vto_comunicacion']     = $this->revisarValorFechaAtributo($datos['fecha_2do_vto_comunicacion']);
		
		$datos['fecha_rta_comunicacion']         = $this->revisarValorFechaAtributo($datos['fecha_rta_comunicacion']);
		
		$datos['observaciones_sancion']          = $this->revisarValorAtributo($datos['observaciones_sancion']);
		
		return $datos;
	}
	
	public function insertar($datos)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validarDatos($datos);
		
		$query = "INSERT INTO ".$this->tabla_sanciones." (anio, tipo, numero, cuerpo, alcance, orden_proyecto, fecha_sancion, numero_sancion, fecha_promulga, numero_promulga, decreto_promulga, fecha_veto, decreto_veto, decreto_presidencia, fecha_remision_de_comunicacion, fecha_1er_vto_comunicacion, fecha_2do_vto_comunicacion, fecha_rta_comunicacion, observaciones_sancion, id_usuario)
				  VALUES ( ".$datos['anio'].",
						  '".$datos['tipo']."',
						   ".$datos['numero'].",
						   ".$datos['cuerpo'].",
						   ".$datos['alcance'].",
						  '".$datos['orden_proyecto']."',
						  '".$datos['fecha_sancion']."',
						   ".$datos['numero_sancion'].",
						   ".$datos['fecha_promulga'].",
						   ".$datos['numero_promulga'].",
						   ".$datos['decreto_promulga'].",
						   ".$datos['fecha_veto'].",
						   ".$datos['decreto_veto'].",
						   ".$datos['decreto_presidencia'].",
						   ".$datos['fecha_remision_de_comunicacion'].",
						   ".$datos['fecha_1er_vto_comunicacion'].",
						   ".$datos['fecha_2do_vto_comunicacion'].",
						   ".$datos['fecha_rta_comunicacion'].",
						   ".$datos['observaciones_sancion'].",
						   ".$datos['id_usuario']."
						  ) ";
		
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
			$datos_log['tabla_log']         = $this->tabla_sanciones;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = "'".$datos['fecha_sancion']."'";
			$datos_log['orden_log']         = $datos['orden_proyecto'];
			$datos_log['observaciones_log'] = "null";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
			
			if ( isset($datos['fecha_promulga']) && $datos['fecha_promulga'] != 'null' && $datos['fecha_promulga'] != '' )
			    $this->registrar_estado_promulgado($datos, $datos['fecha_promulga'], $datos['orden_proyecto']);
		}
		
		return true;
	}
	
	public function modificar($datos)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validarDatos($datos);
		
		$query = "UPDATE ".$this->tabla_sanciones."
				  SET numero_sancion = ".$datos['numero_sancion'].",
					  fecha_promulga = ".$datos['fecha_promulga'].",
					  numero_promulga = ".$datos['numero_promulga'].",
					  decreto_promulga = ".$datos['decreto_promulga'].",
					  fecha_veto = ".$datos['fecha_veto'].",
					  decreto_veto = ".$datos['decreto_veto'].",
					  decreto_presidencia = ".$datos['decreto_presidencia'].",
					  fecha_remision_de_comunicacion = ".$datos['fecha_remision_de_comunicacion'].",
					  fecha_1er_vto_comunicacion = ".$datos['fecha_1er_vto_comunicacion'].",
					  fecha_2do_vto_comunicacion = ".$datos['fecha_2do_vto_comunicacion'].",
					  fecha_rta_comunicacion = ".$datos['fecha_rta_comunicacion'].",
					  observaciones_sancion = ".$datos['observaciones_sancion'].",
					  id_usuario = ".$datos['id_usuario']."
				  WHERE anio = ".$datos['anio']."
				  AND tipo = '".$datos['tipo']."'
				  AND numero = '".$datos['numero']."'
				  AND cuerpo = ".$datos['cuerpo']."
				  AND alcance = ".$datos['alcance']."
				  AND orden_proyecto = '".$datos['orden_proyecto']."'
				  AND fecha_sancion = '".$datos['fecha_sancion']."'
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
			$datos_log['tabla_log']         = $this->tabla_sanciones;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = "'".$datos['fecha_sancion']."'";
			$datos_log['orden_log']         = $datos['orden_proyecto'];
			$datos_log['observaciones_log'] = "null";
			
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
		$sqlA = "SELECT * FROM ".$this->tabla_sanciones."
				 WHERE anio = ".$clave['anio']."
				 AND tipo = '".$clave['tipo']."'
				 AND numero = '".$clave['numero']."'
				 AND cuerpo = ".$clave['cuerpo']."
				 AND alcance = ".$clave['alcance']."
				 AND orden_proyecto = '".$clave['orden_proyecto']."'
				 AND fecha_sancion = '".$clave['fecha_sancion']."'
		        ";
		
		$resultadoA = $this->ejecutarQuery($sqlA);
		
		$datos_previos = $this->crearVector($resultadoA);
		
		// SE ELIMINA
		$query = "DELETE FROM ".$this->tabla_sanciones."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND orden_proyecto = '".$clave['orden_proyecto']."'
				  AND fecha_sancion = '".$clave['fecha_sancion']."'
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
			$datos_log['tabla_log']         = $this->tabla_sanciones;
			$datos_log['anio_log']          = $clave['anio'];
			$datos_log['tipo_log']          = $clave['tipo'];
			$datos_log['numero_log']        = $clave['numero'];
			$datos_log['cuerpo_log']        = $clave['cuerpo'];
			$datos_log['alcance_log']       = $clave['alcance'];
			$datos_log['fecha_log']         = "'".$clave['fecha_sancion']."'";
			$datos_log['orden_log']         = $clave['orden_proyecto'];
			$datos_log['observaciones_log'] = "Sanción Nro.: ".$datos_previos[0]['numero_sancion']." Veto:".$datos_previos[0]['decreto_veto']; 
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;
	}
}
?>