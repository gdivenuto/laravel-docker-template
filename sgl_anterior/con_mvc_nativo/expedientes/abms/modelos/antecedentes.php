<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class antecedentesModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		return parent::conectarDB(2);
	}
		
	public function listado($clave = '')
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
		
		//para filtrar por anio
		if ( $this->filtro['anio_a'] != '' )
		{
			$filtro .= " AND anio_a = ".$this->filtro['anio_a']."";
		}
		
		//para filtrar por tipo
		if ( $this->filtro['tipo_a'] != '' )
		{
			$filtro .= " AND tipo_a = '".$this->filtro['tipo_a']."'";
		}
		
		//para filtrar por numero
		if ( $this->filtro['numero_a'] != '' )
		{
			$filtro .= " AND numero_a = ".$this->filtro['numero_a']."";
		}
		
		//para filtrar por cuerpo
		if ( $this->filtro['cuerpo_a'] != '' )
		{
			$filtro .= " AND cuerpo_a = ".$this->filtro['cuerpo_a']."";
		}
		
		//para filtrar por alcance
		if ( $this->filtro['alcance_a'] != '' )
		{
			$filtro .= " AND alcance_a = ".$this->filtro['alcance_a']."";
		}
		
		$sql = "SELECT *
				FROM ".$this->tabla_antecedentes."
				".$filtro."
				ORDER BY anio ASC, tipo ASC, numero ASC, cuerpo ASC, alcance ASC, anio_a ASC, tipo_a ASC, numero_a ASC, digito_a ASC, cuerpo_a ASC, alcance_a ASC, cuerpoalcance_a ASC, anexoalcance_a ASC, cuerpoanexoalcance_a ASC, anexo_a ASC, cuerpoanexo_a ASC
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
						
		$sql = "SELECT * FROM ".$this->tabla_antecedentes."
			    WHERE anio = ".$this->filtro['anio']."
				AND tipo = '".$this->filtro['tipo']."'
				AND numero = ".$this->filtro['numero']."
				AND cuerpo = ".$this->filtro['cuerpo']."
				AND alcance = ".$this->filtro['alcance']."
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
				FROM ".$this->tabla_antecedentes."
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
	
	// SE VERIFICA LA EXISTENCIA DE UN ANTECEDENTE DETERMINADO, SALVO QUE SEA UN EXPEDIENTE DEL EJECUTIVO
	public function existe($clave)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT tipo
				  FROM ".$this->tabla_expedientes."
				  WHERE anio = '".$clave['anio_a']."'
				  AND tipo = '".$clave['tipo_a']."'
				  AND numero = ".$clave['numero_a']."
				  AND cuerpo = ".$clave['cuerpo_a']."
				  AND alcance = ".$clave['alcance_a']."
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
		
		if ( $_SESSION['anexo_a_original'] != '' )
		{
			$filtro_anexo_a = " AND anexo_a = '".$_SESSION['anexo_a_original']."'";
		
		}
		elseif ( is_null($_SESSION['anexo_a_original']) )
		{
			$filtro_anexo_a = " AND anexo_a = 0";
		}
		else
		{
			$filtro_anexo_a = " AND anexo_a = 0";
		}
		
		if ( $_SESSION['digito_a_original'] != '' )
		{
			$filtro_digito_a = " AND digito_a = '".$_SESSION['digito_a_original']."'";
		}
		elseif ( is_null($_SESSION['digito_a_original']) )
		{
			$filtro_digito_a = " AND digito_a = 0";
			
		}
		else
		{
			$filtro_digito_a = " AND digito_a = 0";
		}
		
		if ( $_SESSION['cuerpoalcance_a_original'] != '' )
		{
			$filtro_cuerpoalcance_a = " AND cuerpoalcance_a = '".$_SESSION['cuerpoalcance_a_original']."'";
		
		}
		elseif ( is_null($_SESSION['cuerpoalcance_a_original']) )
		{
			$filtro_cuerpoalcance_a = " AND cuerpoalcance_a = 0";
			
		}
		else
		{
			$filtro_cuerpoalcance_a = " AND cuerpoalcance_a = 0";
		}
		
		if ( $_SESSION['anexoalcance_a_original'] != '' )
		{
			$filtro_anexoalcance_a = " AND anexoalcance_a = '".$_SESSION['anexoalcance_a_original']."'";
		
		}
		elseif ( is_null($_SESSION['anexoalcance_a_original']) )
		{
			$filtro_anexoalcance_a = " AND anexoalcance_a = 0";
			
		}
		else
		{
			$filtro_anexoalcance_a = " AND anexoalcance_a = 0";
		}
		
		if ( $_SESSION['cuerpoanexoalcance_a_original'] != '' )
		{
			$filtro_cuerpoanexoalcance_a = " AND cuerpoanexoalcance_a = '".$_SESSION['cuerpoanexoalcance_a_original']."'";
		
		}
		elseif (is_null($_SESSION['cuerpoanexoalcance_a_original']) )
		{
			$filtro_cuerpoanexoalcance_a = " AND cuerpoanexoalcance_a = 0";
			
		}
		else
		{
			$filtro_cuerpoanexoalcance_a = " AND cuerpoanexoalcance_a = 0";
		}
		
		if ( $_SESSION['cuerpoanexo_a_original'] != '' )
		{
			$filtro_cuerpoanexo_a = " AND cuerpoanexo_a = '".$_SESSION['cuerpoanexo_a_original']."'";
		
		}
		elseif ( is_null($_SESSION['cuerpoanexo_a_original']) )
		{
			$filtro_cuerpoanexo_a = " AND cuerpoanexo_a = 0";
			
		}
		else
		{
			$filtro_cuerpoanexo_a = " AND cuerpoanexo_a = 0";
		}
		
		if ( $_SESSION['observaciones_antecedentes_original'] != '' )
		{
			$filtro_observaciones_antecedentes = " AND observaciones_antecedentes = '".addslashes($_SESSION['observaciones_antecedentes_original'])."'";
		
		}
		elseif ( is_null($_SESSION['observaciones_antecedentes_original']) )
		{
			$filtro_observaciones_antecedentes = " AND observaciones_antecedentes IS NULL";
			
		}
		else
		{
			$filtro_observaciones_antecedentes = " AND observaciones_antecedentes = ''";
		}
						
		$query = "SELECT anio
				  FROM ".$this->tabla_antecedentes." 
				  WHERE anio = ".$_SESSION['anio_original']."
				  AND tipo = '".$_SESSION['tipo_original']."'
				  AND numero = ".$_SESSION['numero_original']."
				  AND cuerpo = ".$_SESSION['cuerpo_original']."
				  AND alcance = ".$_SESSION['alcance_original']."
				  AND anio_a = ".$_SESSION['anio_a_original']."
				  AND tipo_a = '".$_SESSION['tipo_a_original']."'
				  AND numero_a = ".$_SESSION['numero_a_original']."
				  AND cuerpo_a = ".$_SESSION['cuerpo_a_original']."
				  AND alcance_a = ".$_SESSION['alcance_a_original']."
				  ".$filtro_anexo_a."
				  ".$filtro_digito_a."
				  ".$filtro_cuerpoalcance_a."
				  ".$filtro_anexoalcance_a."
				  ".$filtro_cuerpoanexoalcance_a."
				  ".$filtro_cuerpoanexo_a."
				  ".$filtro_observaciones_antecedentes."
				 ";
		
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);

		return ($dato['anio']);
	}	
	
	public function insertar($datos)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos['observaciones_antecedentes'] = $this->revisarValorAtributo($datos['observaciones_antecedentes']);
		
		$query = "INSERT INTO ".$this->tabla_antecedentes." (anio, tipo, numero, cuerpo, alcance, anio_a, tipo_a, numero_a,
															 digito_a, cuerpo_a,alcance_a, cuerpoalcance_a, anexoalcance_a,
															 cuerpoanexoalcance_a, anexo_a,cuerpoanexo_a,
															 observaciones_antecedentes, id_usuario)
				  VALUES ( ".$datos['anio'].",
						  '".$datos['tipo']."',
							".$datos['numero'].",
							".$datos['cuerpo'].",
							".$datos['alcance'].",
							".$datos['anio_a'].",
						  '".$datos['tipo_a']."',
							".$datos['numero_a'].",
						   '".$datos['digito_a']."',
							".$datos['cuerpo_a'].",
							".$datos['alcance_a'].",
							".$datos['cuerpoalcance_a'].",
							".$datos['anexoalcance_a'].",
							".$datos['cuerpoanexoalcance_a'].",
							".$datos['anexo_a'].",
							".$datos['cuerpoanexo_a'].",
						    ".$datos['observaciones_antecedentes'].",
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
			$datos_log['operacion_log']          = "ALTA";
			$datos_log['tabla_log']              = $this->tabla_antecedentes;
			$datos_log['anio_log']               = $datos['anio'];
			$datos_log['tipo_log']               = $datos['tipo'];
			$datos_log['numero_log']             = $datos['numero'];
			$datos_log['digito_log']             = $datos['digito_a'];
			$datos_log['cuerpo_log']             = $datos['cuerpo'];
			$datos_log['alcance_log']            = $datos['alcance'];
			$datos_log['cuerpoalcance_log']      = $datos['cuerpoalcance_a'];
			$datos_log['anexoalcance_log']       = $datos['anexoalcance_a'];
			$datos_log['cuerpoanexoalcance_log'] = $datos['cuerpoanexoalcance_a'];
			$datos_log['anexo_log']              = $datos['anexo_a'];
			$datos_log['cuerpoanexo_log']        = $datos['cuerpoanexo_a'];
			$datos_log['fecha_log']              = "null";
			$datos_log['orden_log']              = "null";
			$datos_log['observaciones_log']      = "Se inserta un Antecedente";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;	
	}
	
	public function modificar($datos)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		//$_SESSION['observaciones_antecedentes_original'] = $this->adaptarValorStringParaFiltro($_SESSION['observaciones_antecedentes_original']);
		$filtro_observaciones_antecedentes = $this->adaptarValorStringParaFiltro('observaciones_antecedentes');
		
		// PRIMERO SE ELIMINA EL REGISTRO ORIGINAL
		$query_DELETE = "DELETE FROM ".$this->tabla_antecedentes." 
						 WHERE anio = ".$_SESSION['anio_original']."
						 AND tipo = '".$_SESSION['tipo_original']."'
						 AND numero = ".$_SESSION['numero_original']."
						 AND cuerpo = ".$_SESSION['cuerpo_original']."
						 AND alcance = ".$_SESSION['alcance_original']."
						 AND anio_a = ".$_SESSION['anio_a_original']."
						 AND tipo_a = '".$_SESSION['tipo_a_original']."'
						 AND numero_a = ".$_SESSION['numero_a_original']."
						 AND digito_a = '".$_SESSION['digito_a_original']."'
						 AND cuerpo_a = ".$_SESSION['cuerpo_a_original']."
						 AND alcance_a = ".$_SESSION['alcance_a_original']."
						 AND cuerpoalcance_a = ".$_SESSION['cuerpoalcance_a_original']."
						 AND anexoalcance_a = ".$_SESSION['anexoalcance_a_original']."
						 AND cuerpoanexoalcance_a = ".$_SESSION['cuerpoanexoalcance_a_original']."
						 AND anexo_a = ".$_SESSION['anexo_a_original']."
						 AND cuerpoanexo_a = ".$_SESSION['cuerpoanexo_a_original']."
						 ".$filtro_observaciones_antecedentes."
						 AND id_usuario = ".$_SESSION['id_usuario_original']."
						";
		
		if ( !$this->ejecutarQuery($query_DELETE, $conexion) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}	
		else
		{
		    $datos['observaciones_antecedentes'] = $this->revisarValorAtributo($datos['observaciones_antecedentes']);
		
		    // SE INGRESA EL REGISTRO CON LOS DATOS MODIFICADOS CON RESPECTO AL ORIGINAL
		    $query = "INSERT INTO ".$this->tabla_antecedentes." (anio, tipo, numero, cuerpo, alcance, anio_a, tipo_a, numero_a,
																 digito_a, cuerpo_a, alcance_a, cuerpoalcance_a, anexoalcance_a,
																 cuerpoanexoalcance_a, anexo_a, cuerpoanexo_a,
																 observaciones_antecedentes, id_usuario)
					  VALUES ( ".$datos['anio'].",
							  '".$datos['tipo']."',
							   ".$datos['numero'].",
							   ".$datos['cuerpo'].",
							   ".$datos['alcance'].",
							   ".$datos['anio_a'].",
							  '".$datos['tipo_a']."',
							   ".$datos['numero_a'].",
							  '".$datos['digito_a']."',
							   ".$datos['cuerpo_a'].",
							   ".$datos['alcance_a'].",
							   ".$datos['cuerpoalcance_a'].",
							   ".$datos['anexoalcance_a'].",
							   ".$datos['cuerpoanexoalcance_a'].",
							   ".$datos['anexo_a'].",
							   ".$datos['cuerpoanexo_a'].",
							   ".$datos['observaciones_antecedentes'].",
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
				
				$this->desconectar($conexion);
		
				//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
				$modelo = new auditoriaExpedientesModel();
				
				$datos_log = Array();
				$datos_log['operacion_log']          = "MODIFICA";
				$datos_log['tabla_log']              = $this->tabla_antecedentes;
				$datos_log['anio_log']               = $datos['anio'];
				$datos_log['tipo_log']               = $datos['tipo'];
				$datos_log['numero_log']             = $datos['numero'];
				$datos_log['digito_log']             = $datos['digito_a'];
				$datos_log['cuerpo_log']             = $datos['cuerpo'];
				$datos_log['alcance_log']            = $datos['alcance'];
				$datos_log['cuerpoalcance_log']      = $datos['cuerpoalcance_a'];
				$datos_log['anexoalcance_log']       = $datos['anexoalcance_a'];
				$datos_log['cuerpoanexoalcance_log'] = $datos['cuerpoanexoalcance_a'];
				$datos_log['anexo_log']              = $datos['anexo_a'];
				$datos_log['cuerpoanexo_log']        = $datos['cuerpoanexo_a'];
				$datos_log['fecha_log']              = "null";
				$datos_log['orden_log']              = "null";
				$datos_log['observaciones_log']      = "Se modifica un Antecedente";
				
				//SE CARGA EN auditoria EL MOVIMIENTO
				$modelo->registrarMovimiento($datos_log);
		    }
		}
		
		return true;
	}
	
	public function eliminar($clave)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$query = "DELETE FROM ".$this->tabla_antecedentes."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND anio_a = ".$clave['anio_a']."
				  AND tipo_a = '".$clave['tipo_a']."'
				  AND numero_a = ".$clave['numero_a']."
				  AND digito_a = '".$clave['digito_a']."'
				  AND cuerpo_a = ".$clave['cuerpo_a']."
				  AND alcance_a = ".$clave['alcance_a']."
				  AND cuerpoalcance_a = ".$clave['cuerpoalcance_a']."
				  AND anexoalcance_a = ".$clave['anexoalcance_a']."
				  AND cuerpoanexoalcance_a = ".$clave['cuerpoanexoalcance_a']."
				  AND anexo_a = ".$clave['anexo_a']."
				  AND cuerpoanexo_a = ".$clave['cuerpoanexo_a']."";
				  
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
			$datos_log['operacion_log']          = "BAJA";
			$datos_log['tabla_log']              = $this->tabla_antecedentes;
			$datos_log['anio_log']               = $clave['anio'];
			$datos_log['tipo_log']               = $clave['tipo'];
			$datos_log['numero_log']             = $clave['numero'];
			$datos_log['digito_log']             = $clave['digito_a'];
			$datos_log['cuerpo_log']             = $clave['cuerpo'];
			$datos_log['alcance_log']            = $clave['alcance'];
			$datos_log['cuerpoalcance_log']      = $clave['cuerpoalcance_a'];
			$datos_log['anexoalcance_log']       = $clave['anexoalcance_a'];
			$datos_log['cuerpoanexoalcance_log'] = $clave['cuerpoanexoalcance_a'];
			$datos_log['anexo_log']              = $clave['anexo_a'];
			$datos_log['cuerpoanexo_log']        = $clave['cuerpoanexo_a'];
			$datos_log['fecha_log']              = "null";
			$datos_log['orden_log']              = "null";
			$datos_log['observaciones_log']      = "Se elimina un Antecedente";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		return true;	
	}
}
?>