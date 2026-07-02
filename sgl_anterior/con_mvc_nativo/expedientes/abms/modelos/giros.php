<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class girosModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		return parent::conectarDB(2);
	}
		
	public function listar($desde_carga_giros = 0)
	{	
	    $conexion = $this->conectar();
	    
	    $filtro = "";
	    
	    //para filtrar por anio
	    if (!empty($this->filtro['anio']))
		    $filtro .= " WHERE anio = ".$this->filtro['anio']."";
	    
	    //para filtrar por tipo
	    if (!empty($this->filtro['tipo']))
		    $filtro .= " AND tipo = '".$this->filtro['tipo']."'";
	   
	    //para filtrar por numero
	    if (!empty($this->filtro['numero']))
		    $filtro .= " AND numero = ".$this->filtro['numero']."";
	    
	    //para filtrar por cuerpo
	    if ( $this->filtro['cuerpo'] != '' )
		    $filtro .= " AND cuerpo = ".$this->filtro['cuerpo']."";
	    
	    //para filtrar por alcance
	    if ( $this->filtro['alcance'] != '' )
		    $filtro .= " AND alcance = ".$this->filtro['alcance']."";
	    
	    // SI NO SE LLEGA DESDE Carga Giros (Tareas):
	    if ( $desde_carga_giros == 0 ) {
			//para filtrar por orden_giro
			if ( !empty($this->filtro['orden_giro']) )
				$filtro .= " AND orden_giro = '".$this->filtro['orden_giro']."'";
	    }
	    
	    $sql = "SELECT G.*, L.descripcion_grp AS comision_descripcion, U.codigo_usuario
				FROM (SELECT * FROM ".$this->tabla_giros."
					  ".$filtro."
					 )G
				LEFT JOIN ".$this->tabla_lugares." L ON (L.tipo_grp = G.comision_tipo AND L.codigo_grp = G.comision_codigo)
				LEFT JOIN ".$this->tabla_usuarios." U ON (U.id_usuario = G.id_usuario)
				ORDER BY G.anio ASC, G.tipo ASC, G.numero ASC, G.cuerpo ASC, G.alcance ASC, G.orden_giro ASC
			   ";
		
	    $resultado = $this->ejecutarQuery($sql);
	    
	    if ( !$resultado )
			return false;
	    else
			$datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
	}
	
	public function obtenerCantidad()
	{
		$conexion = $this->conectar();
				
		$filtro = "";
		
		//para filtrar por anio
		if (!empty($this->filtro['anio']))
			$filtro .= " WHERE anio = ".$this->filtro['anio']."";
		
		//para filtrar por tipo
		if (!empty($this->filtro['tipo']))
			$filtro .= " AND tipo = '".$this->filtro['tipo']."'";
		
		//para filtrar por numero
		if (!empty($this->filtro['numero']))
			$filtro .= " AND numero = ".$this->filtro['numero']."";
		
		//para filtrar por cuerpo
		if ($this->filtro['cuerpo'] != '')
			$filtro .= " AND cuerpo = ".$this->filtro['cuerpo']."";
		
		//para filtrar por alcance
		if ($this->filtro['alcance'] != '')
			$filtro .= " AND alcance = ".$this->filtro['alcance']."";
		
		$sql = "SELECT COUNT(*) AS cantidad 
				FROM ".$this->tabla_giros."
				".$filtro."
		       ";
		 
		$resultado = $this->ejecutarQuery($sql);
		
		if(!$resultado)
			return false;
		else
			$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['cantidad'];
	}
	
	// SE OBTIENE EL ULTIMO ORDEN DEL GIRO PARA UN EXPEDIENTE DETERMINADO
	public function obtenerUltimoOrden($filtro)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT MAX(orden_giro) AS ultimoOrden 
				FROM ".$this->tabla_giros."
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
			$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['ultimoOrden'];
	}
	
	//	SE VERIFICA LA EXISTENCIA DE UN GIRO DETERMINADO  
	public function existe($clave)
	{
		$conexion = $this->conectar();

		$query = "SELECT orden_giro
				  FROM ".$this->tabla_giros." 
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND orden_giro = '".$clave['orden_giro']."'
				 ";
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return ($dato['orden_giro']);
	}	
		
	public function establecerFechaEntradaGiroSiguiente($datos)
	{
		$posterior = $datos;
		$posterior['orden_giro'] = ($datos['orden_giro']+1);//PARA TRABAJAR CON EL GIRO SIGUIENTE(SI EXISTE)
		
		// SI EXISTE, SE ESTABLECE SU fecha_entrada_giro
		if ($this->existe($posterior))
		{
			$conexion = $this->conectar();

			$query = "UPDATE ".$this->tabla_giros."
					  SET fecha_entrada_giro = '".$this->formatearFechaMySQL($posterior['fecha_salida_giro'])."'
					  WHERE anio = ".$posterior['anio']."
					  AND tipo = '".$posterior['tipo']."'
					  AND numero = ".$posterior['numero']."
					  AND cuerpo = ".$posterior['cuerpo']."
					  AND alcance = ".$posterior['alcance']."
					  AND orden_giro = '".$posterior['orden_giro']."'
					 ";
			
			if (!$this->ejecutarQuery($query))
				return false;
			else {
				// Si el UPDATE fue exitoso
				
				$this->desconectar($conexion);//SE CIERRA LA CONEXION
		
				// SE TOMAN LOS DATOS A REGISTRAR EN auditoria
				$modelo = new auditoriaExpedientesModel();
				
				$datos_log = Array();
				$datos_log['operacion_log']          = "MODIFICA";
				$datos_log['tabla_log']              = $this->tabla_giros;
				$datos_log['anio_log']               = $posterior['anio'];
				$datos_log['tipo_log']               = $posterior['tipo'];
				$datos_log['numero_log']             = $posterior['numero'];
				$datos_log['digito_log']             = "null";
				$datos_log['cuerpo_log']             = $posterior['cuerpo'];
				$datos_log['alcance_log']            = $posterior['alcance'];
				$datos_log['cuerpoalcance_log']      = "null";
				$datos_log['anexoalcance_log']       = "null";
				$datos_log['cuerpoanexoalcance_log'] = "null";
				$datos_log['anexo_log']              = "null";
				$datos_log['cuerpoanexo_log']        = "null";
				$datos_log['fecha_log']              = $posterior['fecha_entrada_giro'];
				$datos_log['orden_log']              = $posterior['orden_giro'];
				
				$dictamen_giro = ($datos['dictamen_giro'] != 'null') ? ' - Dictamen: '.$datos['dictamen_giro'] : '';
				
				$datos_log['observaciones_log'] = "Ingreso AUTOMATICO de fecha de entrada: ".$datos['fecha_salida_giro'].", para la Comisi&oacute;n: ".$datos['comision_tipo']."-".$datos['comision_codigo'].$dictamen_giro."";
				
				//SE CARGA EN auditoria EL MOVIMIENTO
				$modelo->registrarMovimiento($datos_log);
			}
		}
		
		return true;				 
	}
	
	public function validarDatos($datos)
	{
		$datos['comision_tipo']      = $this->revisarValorAtributo($datos['comision_tipo']);
		
		$datos['comision_codigo']    = $this->revisarValorAtributo($datos['comision_codigo']);
		
		$datos['dictamen_giro']      = $this->revisarValorAtributo($datos['dictamen_giro']);
		
		$datos['observaciones_giro'] = $this->revisarValorAtributo($datos['observaciones_giro']);
		
		return $datos;
	}
	
	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function verificarRegistroEntero()
	{
		$conexion = $this->conectar();
		
		$filtro_comision_tipo      = $this->adaptarValorStringParaFiltro('comision_tipo');
		
		$filtro_comision_codigo    = $this->adaptarValorStringParaFiltro('comision_codigo');
		
		$filtro_fecha_entrada_giro = $this->adaptarValorStringParaFiltro('fecha_entrada_giro');
		
		$filtro_fecha_salida_giro  = $this->adaptarValorStringParaFiltro('fecha_salida_giro');
		
		$filtro_dictamen_giro      = $this->adaptarValorStringParaFiltro('dictamen_giro');
		
		$filtro_observaciones_giro = $this->adaptarValorStringParaFiltro('observaciones_giro');
		
		$query = "SELECT anio
				  FROM ".$this->tabla_giros." 
				  WHERE anio = ".$_SESSION['anio_original']."
				  AND tipo = '".$_SESSION['tipo_original']."'
				  AND numero = ".$_SESSION['numero_original']."
				  AND cuerpo = ".$_SESSION['cuerpo_original']."
				  AND alcance = ".$_SESSION['alcance_original']."
				  AND orden_giro = '".$_SESSION['orden_giro_original']."'
				  ".$filtro_comision_tipo."
				  ".$filtro_comision_codigo."
				  ".$filtro_fecha_entrada_giro."
				  ".$filtro_fecha_salida_giro."
				  ".$filtro_dictamen_giro."
				  ".$filtro_observaciones_giro."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return ( $dato['anio'] );		
	}
	
	public function insertar($datos)
	{
		$datos = $this->validarDatos($datos);
		
		// SI SE RECIBE UNA FECHA DE ENTRADA PARA EL GIRO
		if ( $this->esFechaValida($datos['fecha_entrada_giro']) )
			$datos['fecha_entrada_giro'] = "'".$this->formatearFechaMySQL($datos['fecha_entrada_giro'])."'";
		else {
			$anterior = $datos;// PARA UTILIZAR LA MISMA CLAVE, SALVO EL orden_giro
			$anterior['orden_giro'] = $datos['orden_giro']-1;
			
			// SE VERIFICA SI ES CORRECTO EL VALOR DE UN orden_giro PARA UN Giro ANTERIOR Y SU EXISTENCIA
			if ( ($anterior['orden_giro'] > 0) && $this->existe($anterior) ) {
				// SE VERIFICA SI POSEE fecha_salida
				$fecha_salida_giro = $this->verificarFechaSalida($anterior);
				
				// SI POSEE fecha_salida
				if ( $fecha_salida_giro != '' )
					// SE ASIGNA DICHA FECHA COMO FECHA DE ENTRADA AL NUEVO GIRO
					$datos['fecha_entrada_giro'] = $fecha_salida_giro;
				else
					$datos['fecha_entrada_giro'] = 'null';
			} else
				$datos['fecha_entrada_giro'] = 'null';
		}
		
		$datos['fecha_salida_giro'] = $this->revisarValorFechaAtributo($fecha_salida_giro);

		$conexion = $this->conectar();
		
		$query = "INSERT INTO ".$this->tabla_giros." (anio, tipo, numero, cuerpo, alcance, orden_giro, comision_tipo, comision_codigo, fecha_entrada_giro, fecha_salida_giro, dictamen_giro, observaciones_giro, id_usuario)
				  VALUES ( ".$datos['anio'].",
						  '".$datos['tipo']."',
						   ".$datos['numero'].",
						   ".$datos['cuerpo'].",
						   ".$datos['alcance'].",
						  '".$datos['orden_giro']."',
						   ".$datos['comision_tipo'].",
						   ".$datos['comision_codigo'].",
						   ".$datos['fecha_entrada_giro'].",
						   ".$datos['fecha_salida_giro'].",
						   ".$datos['dictamen_giro'].",
						   ".$datos['observaciones_giro'].",
						   ".$datos['id_usuario']."
						  ) ";
		
		if ( !$this->ejecutarQuery($query) )
		{
			return false;
		}
		else
		{
			$this->desconectar($conexion);
		
			// SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log']     = "ALTA";
			$datos_log['tabla_log']         = $this->tabla_giros;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = $datos['fecha_entrada_giro'];
			$datos_log['orden_log']         = $datos['orden_giro'];
			$datos_log['observaciones_log'] = "Comisi&oacute;n: ".$datos['comision_tipo']." ".$datos['comision_codigo']." ".$datos['fecha_entrada_giro']." ".$datos['dictamen_giro'];
			
			// SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;
	}
	
	public function modificar($datos)
	{
		$datos = $this->validarDatos($datos);
		
		$datos['fecha_entrada_giro'] = $this->revisarValorFechaAtributo($datos['fecha_entrada_giro']);
		
		// SI SE RECIBE UNA FECHA DE SALIDA
		if ( $this->esFechaValida($datos['fecha_salida_giro']) )
			// SI EXISTE UN GIRO SIGUIENTE, SE REGISTRA COMO FECHA DE ENTRADA PARA DICHO GIRO
			$this->establecerFechaEntradaGiroSiguiente($datos);
		else
			$datos['fecha_salida_giro'] = "null";

		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE OBTIENEN LOS DATOS ACTUALES PARA AUDITAR
		$sqlA = " SELECT * FROM ".$this->tabla_giros."
				  WHERE anio = ".$datos['anio']."
				  AND tipo = '".$datos['tipo']."'
				  AND numero = ".$datos['numero']."
				  AND cuerpo = ".$datos['cuerpo']."
				  AND alcance = ".$datos['alcance']."
				  AND orden_giro = '".$datos['orden_giro']."'
				";
		
		$resultadoA = $this->ejecutarQuery($sqlA);
		
		$datos_previos = $this->crearVector($resultadoA);
		
		if ( isset($datos['fecha_salida_giro']) && $datos['fecha_salida_giro'] != 'null'&& $datos['fecha_salida_giro'] != '' )
			$datos['fecha_salida_giro'] = "'".$this->formatearFechaMySQL($datos['fecha_salida_giro'])."'";
			
		// SE MODIFICA EL GIRO
		$query = "UPDATE ".$this->tabla_giros."
				  SET comision_tipo = ".$datos['comision_tipo'].",
					  comision_codigo = ".$datos['comision_codigo'].",
					  fecha_entrada_giro = ".$datos['fecha_entrada_giro'].",
					  fecha_salida_giro = ".$datos['fecha_salida_giro'].",
					  dictamen_giro = ".$datos['dictamen_giro'].",
					  observaciones_giro = ".$datos['observaciones_giro'].",
					  id_usuario = ".$datos['id_usuario']."
				  WHERE anio = ".$datos['anio']."
				  AND tipo = '".$datos['tipo']."'
				  AND numero = ".$datos['numero']."
				  AND cuerpo = ".$datos['cuerpo']."
				  AND alcance = ".$datos['alcance']."
				  AND orden_giro = '".$datos['orden_giro']."'
				 ";
		
		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{	// LA CONSULTA FUE EXITOSA
			
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
			
			$this->desconectar($conexion);//SE CIERRA LA CONEXION
		
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			
			$datos_log['operacion_log'] = "MODIFICA";
			$datos_log['tabla_log']     = $this->tabla_giros;
			$datos_log['anio_log']      = $datos['anio'];
			$datos_log['tipo_log']      = $datos['tipo'];
			$datos_log['numero_log']    = $datos['numero'];
			$datos_log['cuerpo_log']    = $datos['cuerpo'];
			$datos_log['alcance_log']   = $datos['alcance'];
			$datos_log['fecha_log']     = $datos['fecha_entrada_giro'];
			$datos_log['orden_log']     = $datos['orden_giro'];
			
			$fecha_entrada_giro = ($datos['fecha_entrada_giro'] != 'null') ? ' - Fecha Entrada: '.$datos['fecha_entrada_giro'] : '';
			
			$dictamen_giro = ($datos['dictamen_giro'] != 'null') ? ' - Dictamen: '.$datos['dictamen_giro'] : '';
				
			$datos_log['observaciones_log'] = "Comisi&oacute;n: ".$datos_previos[0]['comision_tipo']." ".$datos_previos[0]['comision_codigo'].$fecha_entrada_giro.$dictamen_giro."";
				
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;		
	}
	
	//	SE VERIFICA LA EXISTENCIA DE UN INFORME Pendiente  
	public function existeInformePendiente($clave)
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
				  AND fecha_pedido_informe IS NOT NULL
				  AND fecha_vuelta_informe IS NULL
				 ";

		$resultado = $this->ejecutarQuery($query);	
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);

		return ( $dato['orden_informe'] != '' );	
	}	
	
	public function eliminar($clave)
	{
		Logger::getinstance()->Log("clave_en_eliminar_giro", $clave, false);

	    $conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		// SE OBTIENEN LOS DATOS ACTUALES PARA AUDITAR
		$sqlA = "SELECT * FROM ".$this->tabla_giros."
				 WHERE anio = ".$clave['anio']."
				 AND tipo = '".$clave['tipo']."'
				 AND numero = '".$clave['numero']."'
				 AND cuerpo = ".$clave['cuerpo']."
				 AND alcance = ".$clave['alcance']."
				 AND orden_giro = '".$clave['orden_giro']."'
		        ";
		
		$resultadoA = $this->ejecutarQuery($sqlA);
		
		$datos_previos = $this->crearVector($resultadoA);
		
		// SE ELIMINA
		$query = "DELETE FROM ".$this->tabla_giros."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND orden_giro = '".$clave['orden_giro']."'
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
			$datos_log['operacion_log']     = "BAJA";
			$datos_log['tabla_log']         = $this->tabla_giros;
			$datos_log['anio_log']          = $clave['anio'];
			$datos_log['tipo_log']          = $clave['tipo'];
			$datos_log['numero_log']        = $clave['numero'];
			$datos_log['cuerpo_log']        = $clave['cuerpo'];
			$datos_log['alcance_log']       = $clave['alcance'];
			$datos_log['fecha_log']         = "'".$clave['fecha_entrada_giro']."'";
			$datos_log['orden_log']         = $clave['orden_giro'];
			$datos_log['observaciones_log'] = "Comisi&oacute;n: ".$datos_previos[0]['comision_tipo']."-".$datos_previos[0]['comision_codigo'];
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;		
	}
		
	//	SE VERIFICA LA EXISTENCIA DE LA FECHA DE SALIDA DE UN GIRO DETERMINADO  
	public function verificarFechaSalida($clave)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT fecha_salida_giro
				  FROM ".$this->tabla_giros." 
				  WHERE anio = '".$clave['anio']."'
				  AND tipo = '".$clave['tipo']."'
				  AND numero = '".$clave['numero']."'
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND orden_giro = '".$clave['orden_giro']."'
				  AND fecha_salida_giro IS NOT NULL
				 ";
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['fecha_salida_giro'];
	}	
}
?>