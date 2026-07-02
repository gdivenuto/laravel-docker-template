<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class prestamosModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		return parent::conectarDB(2);
	}
	
	public function listar($clave = '')
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
		
		$sql = "SELECT *, (SELECT Ini.descripcion_grp 
						   FROM ".$this->tabla_lugares." Ini 
						   WHERE Ini.tipo_grp = solicitante_tipo AND Ini.codigo_grp = solicitante_codigo
						  )AS nombre_solicitante
				FROM ".$this->tabla_prestamos."
				".$filtro."
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
		       ";
		       
		//fputs(fopen("sql_".$this->tabla_prestamos.".txt",'w'),print_r($sql,true));
		
		// LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango']."
				
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
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
				FROM ".$this->tabla_prestamos."
				".$filtro."
		       ";
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->obtenerFila($resultado);// SE CREA UN VECTOR CON EL RESULTADO
		
		$this->desconectar($conexion);
		
		return $dato['cantidad'];
	}

	public function obtenerRegistro($clave)
	{
		$conexion = $this->conectar();
		 
		$sql = "SELECT *,
					   DATE_FORMAT(fecha_prestado, '%d/%m/%Y') AS fecha_prestado,
					   DATE_FORMAT(fecha_devuelto, '%d/%m/%Y') AS fecha_devuelto,
					   DATE_FORMAT(fecha_anulado, '%d/%m/%Y') AS fecha_anulado
				FROM ".$this->tabla_prestamos."
				WHERE anio = ".$clave['anio']."
				AND tipo = '".$clave['tipo']."'
				AND numero = ".$clave['numero']."
				AND cuerpo = ".$clave['cuerpo']."
				AND alcance = ".$clave['alcance']."
				AND digito = '".$clave['digito']."'
				AND cuerpoalcance = ".$clave['cuerpoalcance']."
				AND anexoalcance = ".$clave['anexoalcance']."
				AND cuerpoanexoalcance = ".$clave['cuerpoanexoalcance']."
				AND anexo = ".$clave['anexo']."
				AND cuerpoanexo = ".$clave['cuerpoanexo']."
				AND fecha_solicitud = '".$clave['fecha_solicitud']."'
			   ";
		 
		$resultado = $this->ejecutarQuery($sql, $conexion);
		 
		$registro = $this->obtenerFila($resultado);
		 
		$this->desconectar($conexion);
		 
		return $registro;
	}
	
	// SE VERIFICA LA EXISTENCIA DE UN EXPED./NOTA DETERMINADO EN EL SISTEMA DE EXPEDIENTES
	public function existe($datos)
	{
		$conexion = $this->conectar();
		
		$datos = $this->validarDatos($datos);
		
		$query = "SELECT tipo
				  FROM ".$this->tabla_prestamos."
				  WHERE anio = ".$datos['anio']."
				  AND tipo = '".$datos['tipo']."'
				  AND numero = ".$datos['numero']."
				  AND cuerpo = ".$datos['cuerpo']."
				  AND alcance = ".$datos['alcance']."
				  AND digito = '".$datos['digito']."'
				  AND cuerpoalcance = ".$datos['cuerpoalcance']."
				  AND anexoalcance = ".$datos['anexoalcance']."
				  AND cuerpoanexoalcance = ".$datos['cuerpoanexoalcance']."
				  AND anexo = ".$datos['anexo']."
				  AND cuerpoanexo = ".$datos['cuerpoanexo']."
				  AND fecha_solicitud = ".$datos['fecha_solicitud']."
				 ";
		
		//fputs(fopen('query_existe_prestamo.txt','w'), print_r($query,true));
		
		$resultado = $this->ejecutarQuery($query, $conexion);
		
		if (!$resultado)
		{
			return false;
		}
		else
		{
			$dato = $this->obtenerFila($resultado);
			if ( !$dato['tipo'] )
			{
				return false;
			}
		}	
		
		$this->desconectar($conexion);
		
		return true;
	}
	
	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function verificarRegistroEntero()
	{	
		$conexion = $this->conectar();

		$filtro_digito = ($_SESSION['digito_original']) ? " AND digito = '".$_SESSION['digito_original']."'" : '0';
		
		$filtro_anexo = ($_SESSION['anexo_original']) ? " AND anexo = '".$_SESSION['anexo_original']."'" : 0;

		$filtro_cuerpoalcance = ($_SESSION['cuerpoalcance_original']) ? " AND cuerpoalcance = '".$_SESSION['cuerpoalcance_original']."'" : 0;

		$filtro_anexoalcance = ($_SESSION['anexoalcance_original']) ? " AND anexoalcance = '".$_SESSION['anexoalcance_original']."'" : 0;

		$filtro_cuerpoanexoalcance = ($_SESSION['cuerpoanexoalcance_original']) ? " AND cuerpoanexoalcance = '".$_SESSION['cuerpoanexoalcance_original']."'" : 0;

		$filtro_cuerpoanexo = ($_SESSION['cuerpoanexo_original']) ? " AND cuerpoanexo = '".$_SESSION['cuerpoanexo_original']."'" : 0;
		
		$filtro_fecha_solicitud = $this->adaptarValorStringParaFiltro('fecha_solicitud');

		$filtro_fecha_prestado = $this->adaptarValorStringParaFiltro('fecha_prestado');
		
		$filtro_fecha_devuelto = $this->adaptarValorStringParaFiltro('fecha_devuelto');
		
		$filtro_fecha_anulado = $this->adaptarValorStringParaFiltro('fecha_anulado');
		
		$filtro_solicitante_tipo = $this->adaptarValorStringParaFiltro('solicitante_tipo');
		
		$filtro_solicitante_codigo = $this->adaptarValorStringParaFiltro('solicitante_codigo');

		$filtro_libro_numero = $this->adaptarValorStringParaFiltro('libro_numero');

		$filtro_libro_folio = $this->adaptarValorStringParaFiltro('libro_folio');
		
		$filtro_observaciones_prestamo = $this->adaptarValorStringParaFiltro('observaciones_prestamo');
		
		$query = "SELECT anio
				  FROM ".$this->tabla_prestamos." 
				  WHERE anio = ".$_SESSION['anio_original']."
				  AND tipo = '".$_SESSION['tipo_original']."'
				  AND numero = ".$_SESSION['numero_original']."
				  AND cuerpo = ".$_SESSION['cuerpo_original']."
				  AND alcance = ".$_SESSION['alcance_original']."
				  ".$filtro_digito."
				  ".$filtro_cuerpoalcance."
				  ".$filtro_anexoalcance."
				  ".$filtro_cuerpoanexoalcance."
				  ".$filtro_anexo."
				  ".$filtro_cuerpoanexo."
				  ".$filtro_fecha_solicitud."
				  ".$filtro_fecha_prestado."
				  ".$filtro_fecha_devuelto."
				  ".$filtro_fecha_anulado."
				  ".$filtro_solicitante_tipo."
				  ".$filtro_solicitante_codigo."
				  ".$filtro_libro_numero."
				  ".$filtro_libro_folio."
				  ".$filtro_observaciones_prestamo."
				 ";
		//fputs(fopen('queryVerificarRegistroEntero_prestamoM.txt','w'),print_r($query,true));
		
		$resultado = $this->ejecutarQuery($query, $conexion);

		$dato = $this->obtenerFila($resultado);
		
		if (!$dato['anio'])
		{
			return false;
		}
		$this->desconectar($conexion);
		
		return true;		
	}	
	
	public function validarDatos($datos)
	{
		$datos['digito'] = $this->revisarValorAtributo($datos['digito'], 0);
		
		$datos['cuerpoalcance'] = $this->revisarValorNumericoAtributo($datos['cuerpoalcance'], 0);
		
		$datos['anexoalcance'] = $this->revisarValorNumericoAtributo($datos['anexoalcance'], 0);
		
		$datos['cuerpoanexoalcance'] = $this->revisarValorNumericoAtributo($datos['cuerpoanexoalcance'], 0);
		
		$datos['anexo'] = $this->revisarValorNumericoAtributo($datos['anexo'], 0);
		
		$datos['cuerpoanexo'] = $this->revisarValorNumericoAtributo($datos['cuerpoanexo'], 0);
				
		// AL EDITAR LA FECHA CUANDO SE INGRESA UN PRESTAMO
		if ( $datos['fecha_solicitud'] != '' && $datos['accion'] == 'insertar' )
		{
			// SE REVISA EL VALOR DE LA FECHA
			$datos['fecha_solicitud'] = $this->revisarValorFechaAtributo($datos['fecha_solicitud'], true);
		}
		else
		{
			// SE MANTIENE EL FORMATO DATETIME yyyy-mm-dd hh:mm:ss 
			$datos['fecha_solicitud'] = ( $datos['fecha_solicitud_con_hora'] != '' && $datos['fecha_solicitud_con_hora'] != '0000-00-00 00:00:00' ) ? "'".$datos['fecha_solicitud_con_hora']."'" : 'null';
		}
		
		// SI SE EDITÓ LA FECHA
		if ( $datos['fecha_prestado'] != '' )
		{
			// SE REVISA SU VALOR
			$datos['fecha_prestado'] = $this->revisarValorFechaAtributo($datos['fecha_prestado']);
		}
		else
		{
			// SE MANTIENE EL FORMATO DATETIME yyyy-mm-dd hh:mm:ss 
			$datos['fecha_prestado'] = ( $datos['fecha_prestado_con_hora'] != '' && $datos['fecha_prestado_con_hora'] != '0000-00-00 00:00:00' ) ? "'".$datos['fecha_prestado_con_hora']."'" : 'null';
		}

		// SI SE EDITÓ LA FECHA
		if ( $datos['fecha_devuelto'] != '' )
		{
			// SE REVISA SU VALOR
			$datos['fecha_devuelto'] = $this->revisarValorFechaAtributo($datos['fecha_devuelto']);
		}
		else
		{
			// SE MANTIENE EL FORMATO DATETIME yyyy-mm-dd hh:mm:ss 
			$datos['fecha_devuelto'] = ( $datos['fecha_devuelto_con_hora'] != '' && $datos['fecha_devuelto_con_hora'] != '0000-00-00 00:00:00' ) ? "'".$datos['fecha_devuelto_con_hora']."'" : 'null';
		}

		// SI SE EDITÓ LA FECHA
		if ( $datos['fecha_anulado'] != '' )
		{
			// SE REVISA SU VALOR
			$datos['fecha_anulado'] = $this->revisarValorFechaAtributo($datos['fecha_anulado']);
		}
		else
		{
			// SE MANTIENE EL FORMATO DATETIME yyyy-mm-dd hh:mm:ss 
			$datos['fecha_anulado'] = ( $datos['fecha_anulado_con_hora'] != '' && $datos['fecha_anulado_con_hora'] != '0000-00-00 00:00:00' ) ? "'".$datos['fecha_anulado_con_hora']."'" : 'null';
		}

		// SE SEPARA EL TIPO Y CODIGO DEL SOLICITANTE
		$solicitante = explode("-", $datos['solicitante']);
		
		$datos['solicitante_tipo'] = $this->revisarValorAtributo($solicitante[0]);
		$datos['solicitante_codigo'] = $this->revisarValorAtributo($solicitante[1]);

		$datos['libro_numero'] = $this->revisarValorAtributo($datos['libro_numero']);

		$datos['libro_folio'] = $this->revisarValorAtributo($datos['libro_folio']);

		$datos['estado'] = $this->revisarValorAtributo($datos['estado']);
		
		$datos['observaciones_prestamo'] = $this->revisarValorAtributo($datos['observaciones_prestamo']);
		
		return $datos;
	}
	
	public function insertar($datos)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validarDatos($datos);
		
		$query = "INSERT INTO ".$this->tabla_prestamos." (anio, tipo, numero, cuerpo, alcance,
														  digito, cuerpoalcance, anexoalcance, cuerpoanexoalcance, anexo, cuerpoanexo,
														  fecha_solicitud, fecha_prestado, fecha_devuelto, fecha_anulado,
														  solicitante_tipo, solicitante_codigo, libro_numero, libro_folio, 
														  estado, observaciones_prestamo, id_usuario)
				  VALUES ( ".$datos['anio'].",
						  '".$datos['tipo']."',
						   ".$datos['numero'].",
						   ".$datos['cuerpo'].",
						   ".$datos['alcance'].",
						  '".$datos['digito']."',
						   ".$datos['cuerpoalcance'].",
						   ".$datos['anexoalcance'].",
						   ".$datos['cuerpoanexoalcance'].",
						   ".$datos['anexo'].",
						   ".$datos['cuerpoanexo'].",
						   ".$datos['fecha_solicitud'].",
						   IFNULL(".$datos['fecha_prestado'].", 'NULL'),
						   IFNULL(".$datos['fecha_devuelto'].", 'NULL'),
						   IFNULL(".$datos['fecha_anulado'].", 'NULL'),
						   ".$datos['solicitante_tipo'].",
						   ".$datos['solicitante_codigo'].",
						   ".$datos['libro_numero'].",
						   ".$datos['libro_folio'].",
						   ".$datos['estado'].",
						   ".$datos['observaciones_prestamo'].",
						   ".$datos['id_usuario']."
						 ) ";
					 
		fputs(fopen("query_insertar_".$this->tabla_prestamos.".txt",'w'),print_r($query,true));
					 
		if ( !$this->ejecutarQuery($query, $conexion) )
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
			$datos_log['operacion_log'] = "ALTA";
			$datos_log['tabla_log'] = $this->tabla_prestamos;
			$datos_log['anio_log'] = $datos['anio'];
			$datos_log['tipo_log'] = $datos['tipo'];
			$datos_log['numero_log'] = $datos['numero'];
			$datos_log['cuerpo_log'] = $datos['cuerpo'];
			$datos_log['alcance_log'] = $datos['alcance'];
			$datos_log['digito_log'] = $datos['digito'];
			$datos_log['cuerpoalcance_log'] = $datos['cuerpoalcance'];
			$datos_log['anexoalcance_log'] = $datos['anexoalcance'];
			$datos_log['cuerpoanexoalcance_log'] = $datos['cuerpoanexoalcance'];
			$datos_log['anexo_log'] = $datos['anexo'];
			$datos_log['cuerpoanexo_log'] = $datos['cuerpoanexo'];
			$datos_log['fecha_log'] = "null";
			$datos_log['orden_log'] = "null";
			$datos_log['observaciones_log'] = "Se ingresa un Pr&eacute;stamo";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		$this->desconectar($conexion);
		
		return true;	
	}
	
	public function modificar($datos)
	{
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$datos = $this->validarDatos($datos);
		//fputs(fopen("datos_validados_al_modificar_prestamo.txt", 'w'), print_r($datos, true));
		
	    $query = "UPDATE ".$this->tabla_prestamos."
				  SET fecha_prestado = 	IFNULL(".$datos['fecha_prestado'].", 'NULL'),
				  	  fecha_devuelto = IFNULL(".$datos['fecha_devuelto'].", 'NULL'),
				  	  fecha_anulado = IFNULL(".$datos['fecha_anulado'].", 'NULL'),
					  solicitante_tipo = ".$datos['solicitante_tipo'].",
					  solicitante_codigo = ".$datos['solicitante_codigo'].",
					  libro_numero = ".$datos['libro_numero'].",
					  libro_folio = ".$datos['libro_folio'].",
					  estado = ".$datos['estado'].",
					  observaciones_prestamo = ".$datos['observaciones_prestamo'].",
					  id_usuario = ".$datos['id_usuario']."
				  WHERE anio = ".$datos['anio']."
				  AND tipo = '".$datos['tipo']."'
				  AND numero = ".$datos['numero']."
				  AND cuerpo = ".$datos['cuerpo']."
				  AND alcance = ".$datos['alcance']."
				  AND digito = '".$datos['digito']."'
				  AND cuerpoalcance = ".$datos['cuerpoalcance']."
				  AND anexoalcance = ".$datos['anexoalcance']."
				  AND cuerpoanexoalcance = ".$datos['cuerpoanexoalcance']."
				  AND anexo = ".$datos['anexo']."
				  AND cuerpoanexo = ".$datos['cuerpoanexo']."
				  AND fecha_solicitud = ".$datos['fecha_solicitud']."
				 ";
							 
		fputs(fopen("query_UPDATE_".$this->tabla_prestamos.".txt",'w'),print_r($query,true));
		    
	    if ( !$this->ejecutarQuery($query, $conexion) )
	    {
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
	    }
	    else
	    {
			// LA CONSULTA FUE EXITOSA
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
						
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log'] = "ALTA";
			$datos_log['tabla_log'] = $this->tabla_prestamos;
			$datos_log['anio_log'] = $datos['anio'];
			$datos_log['tipo_log'] = $datos['tipo'];
			$datos_log['numero_log'] = $datos['numero'];
			$datos_log['cuerpo_log'] = $datos['cuerpo'];
			$datos_log['alcance_log'] = $datos['alcance'];
			$datos_log['digito_log'] = $datos['digito'];
			$datos_log['cuerpoalcance_log'] = $datos['cuerpoalcance'];
			$datos_log['anexoalcance_log'] = $datos['anexoalcance'];
			$datos_log['cuerpoanexoalcance_log'] = $datos['cuerpoanexoalcance'];
			$datos_log['anexo_log'] = $datos['anexo'];
			$datos_log['cuerpoanexo_log'] = $datos['cuerpoanexo'];
			$datos_log['fecha_log'] = "null";
			$datos_log['orden_log'] = "null";
			$datos_log['observaciones_log'] = "Se modifica un Pr&eacute;stamo";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
	    }
		
		$this->desconectar($conexion);
		
		return true;
	}
	
	public function eliminar($clave)
	{	
		$conexion = $this->conectar();
		
		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION
		
		$query = "DELETE FROM ".$this->tabla_prestamos."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				  AND digito = '".$clave['digito']."'
				  AND cuerpoalcance = ".$clave['cuerpoalcance']."
				  AND anexoalcance = ".$clave['anexoalcance']."
				  AND cuerpoanexoalcance = ".$clave['cuerpoanexoalcance']."
				  AND anexo = ".$clave['anexo']."
				  AND cuerpoanexo = ".$clave['cuerpoanexo']."
				  AND fecha_solicitud = '".$clave['fecha_solicitud']."'
				";
				  
		//fputs(fopen("query_eliminar_".$this->tabla_prestamos.".txt",'w'),print_r($query,true));	  
				  
		if ( !$this->ejecutarQuery($query, $conexion) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}	
		else
		{
			// LA CONSULTA FUE EXITOSA
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION
						
			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaExpedientesModel();
			
			$datos_log = Array();
			$datos_log['operacion_log'] = "ALTA";
			$datos_log['tabla_log'] = $this->tabla_prestamos;
			$datos_log['anio_log'] = $datos['anio'];
			$datos_log['tipo_log'] = $datos['tipo'];
			$datos_log['numero_log'] = $datos['numero'];
			$datos_log['cuerpo_log'] = $datos['cuerpo'];
			$datos_log['alcance_log'] = $datos['alcance'];
			$datos_log['digito_log'] = $datos['digito'];
			$datos_log['cuerpoalcance_log'] = $datos['cuerpoalcance'];
			$datos_log['anexoalcance_log'] = $datos['anexoalcance'];
			$datos_log['cuerpoanexoalcance_log'] = $datos['cuerpoanexoalcance'];
			$datos_log['anexo_log'] = $datos['anexo'];
			$datos_log['cuerpoanexo_log'] = $datos['cuerpoanexo'];
			$datos_log['fecha_log'] = "null";
			$datos_log['orden_log'] = "null";
			$datos_log['observaciones_log'] = "Se elimina un Pr&eacute;stamo";
			
			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}
		
		$this->desconectar($conexion);
		
		return true;	
	}

	public function obtenerSolicitantes()
	{
		$conexion = $this->conectar();
	
		$sql = "SELECT * FROM ".$this->tabla_lugares." WHERE habilitado_grp = 1";
		//fputs(fopen('sql_obtenerSolicitantes.txt','w'),print_r($sql, true));
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
	
		$datos = $this->crearVector($resultado);
	
		$this->desconectar($conexion);
	
		return $datos;
	}
	
}
?>
