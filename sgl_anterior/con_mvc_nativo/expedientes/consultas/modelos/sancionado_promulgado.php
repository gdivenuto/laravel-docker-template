<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class sancionadoPromulgadoModel extends ModeloBaseMySQLi
{
	public function conectar()
	{
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(2);
	}
		
	public function listar()
	{	
	    $conexion = $this->conectar();
	    
	    // FILTRA POR Fecha POR DEFECTO 
	    $filtro = "";
	    
	    //SE FILTRA POR categoria
	    if (!empty($this->filtro['c_categoria'])){
			$filtro .= " AND E.id_codcategoria = '".$this->filtro['c_categoria']."'";
	    }
	    
	    //SE FILTRA POR iniciador
	    if (!empty($this->filtro['c_iniciado_tipo']) && !empty($this->filtro['c_iniciado_codigo']))
	    {
			$filtro .= " AND E.iniciador_tipo = '".$this->filtro['c_iniciado_tipo']."' AND E.iniciador_codigo = '".$this->filtro['c_iniciado_codigo']."'";
	    }
	    
	    // SE FILTRA POR Tema
	    $filtro_tema = "";
	    if (!empty($this->filtro['c_tema'])){
	    
			$filtro_tema = " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																					  FROM ".$this->tabla_temas."
																					  WHERE id_codtema = ".$this->filtro['c_tema']."
																					)
						   ";
	    }
		
	    // SE FILTRA POR Autor
	    $filtro_autor = "";
	    if (!empty($this->filtro['c_autor_tipo']) && !empty($this->filtro['c_autor_codigo']))
		{
			$filtro_autor = " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																					   FROM ".$this->tabla_autores."
																					   WHERE autor_tipo = '".$this->filtro['c_autor_tipo']."'
																					   AND autor_codigo = '".$this->filtro['c_autor_codigo']."'
																					 )
							";
	    }
				
	    // SE FILTRA POR Sancionado o Promulgado
	    $filtro_sancion = "";
	    if ( !empty($this->filtro['c_opcionsp']) )
	    {  	    
			// SI SE BUSCAN LOS PROMULGADOS
			if ($this->filtro['c_opcionsp'] == '2')
			{
				$filtro_sancion = " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																						     FROM ".$this->tabla_sanciones." 
																						     WHERE fecha_promulga BETWEEN '".$this->filtro['c_fecha_desde']."' AND '".$this->filtro['c_fecha_hasta']."' 
																						   ) 
								  ";
			}else{
				// SE BUSCAN LOS SANCIONADOS
				$filtro_sancion = " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																						     FROM ".$this->tabla_sanciones." 
																						     WHERE fecha_sancion BETWEEN '".$this->filtro['c_fecha_desde']."' AND '".$this->filtro['c_fecha_hasta']."' 
																						     AND (fecha_promulga IS NULL OR fecha_promulga = '0000-00-00')
																						   ) 
								  ";  
			}
	    }
		
	    /***************************************************************************************************
		  QUERY GENERAL:
	    ***************************************************************************************************/
	    // SQL_CALC_FOUND_ROWS: calcula el número de resultados de la query sin el LIMIT
	    $sql = "SELECT SQL_CALC_FOUND_ROWS * 
				FROM (SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision,
					 (SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria)AS categoria, 
					 (SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = iniciador_tipo AND Ini.codigo_grp = iniciador_codigo)AS iniciador
					  FROM ".$this->tabla_expedientes." AS E
					  WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['c_fecha_desde']."' AND '".$this->filtro['c_fecha_hasta']."'
					  ".$filtro."
					  ".$filtro_tema."
					  ".$filtro_autor."
					  ".$filtro_sancion."
					  ORDER BY E.anio ASC, E.tipo ASC, E.numero ASC, E.cuerpo ASC, E.alcance ASC
					 )AS AUX
			   ";
	    //fputs(fopen('sql_sancionado_promulgado.txt', 'w'), print_r($sql, true));

	    // SE AGREGA EL LIMITE A LA QUERY	
	    $limite = " LIMIT ".$this->filtro['c_inicio'].", ".$this->filtro['c_rango']."";		
	    $sql .= $limite; 	   
	    //fputs(fopen('sql_sancionado_promulgado.txt', 'w'),print_r($sql, true));
	    
	    // SE EJECUTA LA QUERY	
	    $resultado = $this->ejecutarQuery($sql, $conexion);
	    
	    $total_devueltos = $this->obtenerNumeroFilas($resultado);
	    // SI DEVUELVE ALGUN REGISTRO
	    if ($total_devueltos != 0) 
		{	
			// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
			$datos = $this->crearVector($resultado);
			
			// SE AVERIGUA EL NUMERO TOTAL DE EXPEDIENTES 
			// FOUND_ROWS: obtiene el resultado del último SQL_CALC_FOUND_ROWS ejecutado
			$sqlTotal = "SELECT FOUND_ROWS() as total"; 
			$resultadoTotal = $this->ejecutarQuery($sqlTotal); 
			
			$rowTotal = mysql_fetch_assoc($resultadoTotal); 
			// Total de registros sin limit 
			$_SESSION['total'] = $rowTotal["total"]; 
				
	    }else{
			$_SESSION['total'] = 0;
			return false;
	    }
	    
	    // SE LIBERA LA MEMORIA USADA POR LA QUERY
	    $this->liberarMemoria($resultado);
		    
	    // SE CIERRA LA CONEXION
	    $this->desconectar($conexion);
	    
	    return $datos;
    }	
	
    /**
    PARA LOS LISTADOS (FORMATO A IMPRIMIR Y EL DOC. DE TEXTO):
    SE VUELVE A EJECUTAR LA QUERY EN PARTES, CON LA CANTIDAD TOTAL DE EXPEDIENTES RESULTANTES SE CALCULA 
    LA CANTIDAD DE PAGINAS USANDO UN RANGO ESPECIFICO (100 POR DEFECTO)
    /**/
    public function armar_listado_para_reporte()
	{    
		$conexion = $this->conectar();
			
		// FILTRA POR Fecha POR DEFECTO 
		$filtro = "";
		
		//SE FILTRA POR categoria
		if ( !empty($this->filtro['c_categoria']) )
		{
			$filtro .= " AND E.id_codcategoria = '".$this->filtro['c_categoria']."'";
		}
		
		//SE FILTRA POR iniciador
		if ( !empty($this->filtro['c_iniciado_tipo']) && !empty($this->filtro['c_iniciado_codigo']) )
		{
			$filtro .= " AND E.iniciador_tipo = '".$this->filtro['c_iniciado_tipo']."' AND E.iniciador_codigo = '".$this->filtro['c_iniciado_codigo']."'";
		}
		
		// SE FILTRA POR Tema
		$filtro_tema = "";
		if ( !empty($this->filtro['c_tema']) )
		{
			$filtro_tema = " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																					  FROM ".$this->tabla_temas."
																					  WHERE id_codtema = ".$this->filtro['c_tema']."
																					)
																				  ";
		}
		
		// SE FILTRA POR Autor
		$filtro_autor = "";
		if ( !empty($this->filtro['c_autor_tipo']) && !empty($this->filtro['c_autor_codigo']) )
		{
			$filtro_autor = " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																					   FROM ".$this->tabla_autores."
																					   WHERE autor_tipo = '".$this->filtro['c_autor_tipo']."'
																					   AND autor_codigo = '".$this->filtro['c_autor_codigo']."'
																					 )";
		}
				
		// SE FILTRA POR Sancionado o Promulgado
		$filtro_sancion = "";
		if ( !empty($this->filtro['c_opcionsp']) )
		{	
			// SI SE BUSCAN LOS PROMULGADOS
			if ($this->filtro['c_opcionsp'] == '2')
			{
				$filtro_sancion = " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																							 FROM ".$this->tabla_sanciones." 
																							 WHERE fecha_promulga BETWEEN '".$this->filtro['c_fecha_desde']."' AND '".$this->filtro['c_fecha_hasta']."' 
																						   )";
			}
			else
			{
				// SE BUSCAN LOS SANCIONADOS
				$filtro_sancion = " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																							 FROM ".$this->tabla_sanciones." 
																							 WHERE fecha_sancion BETWEEN '".$this->filtro['c_fecha_desde']."' AND '".$this->filtro['c_fecha_hasta']."' 
																							 AND (fecha_promulga IS NULL OR fecha_promulga = '0000-00-00')
																						   )";  
			}
		}
		
		/***************************************************************************************************
			  QUERY GENERAL:
		***************************************************************************************************/
		// SQL_CALC_FOUND_ROWS: calcula el número de resultados de la query sin el LIMIT
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM (
				  SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision,
				  (SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria)AS categoria, 
				  (SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = iniciador_tipo AND Ini.codigo_grp = iniciador_codigo)AS iniciador
				  FROM ".$this->tabla_expedientes." E
				  WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['c_fecha_desde']."' AND '".$this->filtro['c_fecha_hasta']."'
				  ".$filtro."
				  ".$filtro_tema."
				  ".$filtro_autor."
				  ".$filtro_sancion."
				  ORDER BY E.anio ASC, E.tipo ASC, E.numero ASC, E.cuerpo ASC, E.alcance ASC
				)AS AUX
				";
		
		// SE AGREGA EL LIMITE A LA QUERY	
		$limite_inicial = " LIMIT 0, 100";
		$sql_auxiliar = $sql;// SE UTILIZA UNA AUXILIAR PARA CALCULAR EL TOTAL DE REGISTROS	  
		$sql_auxiliar .= $limite_inicial; 	   
		//fputs(fopen('sql_consulta_general.txt', 'w'),print_r($sql_auxiliar, true));

		// SE EJECUTA LA QUERY PARA LOS PRIMEROS 100 REGISTROS	
		$this->ejecutarQuery($sql_auxiliar, $conexion);

		// SE AVERIGUA EL NUMERO TOTAL DE EXPEDIENTES CON FOUND_ROWS (obtiene el resultado del último SQL_CALC_FOUND_ROWS ejecutado sin LIMIT)
		$resultadoTotal = $this->ejecutarQuery("SELECT FOUND_ROWS() AS total"); 
		$rowTotal = mysql_fetch_assoc($resultadoTotal); 

		// SE GUARDA LA CANTIDAD TOTAL DE EXPEDIENTES
		$cantidad_TOTAL = $rowTotal["total"];
		//fputs(fopen('cantidad_armar_listado_para_reporte_M.txt', 'w'), print_r($cantidad_TOTAL, true));

		$rango = 100;
		$listado_para_reporte = Array();

		// SE EJECUTA LA QUERY CADA CIEN REGISTROS, MIENTRAS EXISTAN EXPEDIENTES/NOTAS
		$corte = false;	
		$r = 0;
		while ( $r < $cantidad_TOTAL && !$corte )
		{
			// SE DEFINE EL INICIO DEL LIMITE A PEDIR
			$inicio = $r;
			// SE DEFINE DICHO LIMITE
			$limite_para_documento = " LIMIT ".$inicio.", ".$rango."";

			// SE UTILIZA LA QUERY YA ARMADA, COMO AUXILIAR PARA CADA CICLO
			$sql_auxiliar = $sql;
			// SE AGREGA EL LIMITE A LA QUERY AUXILIAR
			$sql_auxiliar .= $limite_para_documento;
			//fputs(fopen('sql_auxiliar_'.$r.'_armar_listado_para_reporte.txt', 'w'), print_r($sql_auxiliar, true));
			
			// SE EJECUTA LA QUERY PARCIAL 	
			$resultado_parcial = $this->ejecutarQuery($sql_auxiliar, $conexion);

			// SE LIMPIA PARA ASIGNARLE EL NUEVO LIMITE EN EL SIGUIENTE CICLO
			$sql_auxiliar = "";
			
			$total_devueltos = $this->obtenerNumeroFilas($resultado_parcial);
			// SI DEVUELVE ALGUN REGISTRO
			if ( $total_devueltos != 0 )
			{
				// SE UTILIZA UN VECTOR AUXILIAR 
				$vector_auxiliar = $this->crearVector($resultado_parcial);
				//fputs(fopen('vector_auxiliar_'.$r.'_armar_listado_para_reporte_M.txt', 'w'), print_r($vector_auxiliar, true));
				
				$cant_auxiliar = count($vector_auxiliar);
				//fputs(fopen('cant_auxiliar_vector_'.$r.'_armar_listado_para_reporte.txt', 'w'), print_r($cant_auxiliar, true));
				
				for ($a=0; $a < $cant_auxiliar; $a++)
				{
					$auxiliar = &$vector_auxiliar[$a];
					
					//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
					$auxiliar['temas'] = $this->obtenerTemasFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);
					//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
					$auxiliar['autores'] = $this->obtenerAutoresFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);
					//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE
					$auxiliar['proyectos'] = $this->obtenerProyectosFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);				
					
					// SE VA CARGANDO UN LISTADO AUXILIAR 
					$listado_aux_para_reporte[$a] = $auxiliar;
				}
				// SE VA ARMANDO EL LISTADO A MEDIDA QUE SE OBTIENEN LOS DATOS
				$listado_para_reporte = array_merge($listado_para_reporte, $listado_aux_para_reporte);
				//fputs(fopen('LISTADO_REPORTE_AUX_'.$r.'__armar_listado_para_reporte.txt', 'w'),print_r($listado_para_reporte, true));
				$listado_aux_para_reporte = null;
					
				// SE LIBERA LA MEMORIA USADA POR LA QUERY PARCIAL
				$this->liberarMemoria($resultado_parcial);
			}
			else
			{
				return false;
			}
			// SE INCREMENTA EL CONTADOR DE A 100, QUE ES EL VALOR DEL RANGO
			$r += $rango;
		}
		//fputs(fopen('listado_para_imprimir_y_procesar_COMPLETO.txt', 'w'),print_r($listado_para_reporte, true));
		
		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);
		
		return $listado_para_reporte;
    }
	
	public function obtenerTemasFicha($anio, $tipo, $numero, $cuerpo, $alcance)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT CT.descripcion_tema 
				FROM (SELECT * FROM ".$this->tabla_temas." 
					  WHERE anio = ".$anio."
					  AND tipo = '".$tipo."'
					  AND numero = ".$numero."
					  AND cuerpo = ".$cuerpo."
					  AND alcance = ".$alcance."
					 )T
				LEFT JOIN ".$this->tabla_codtemas." CT ON CT.id_codtema = T.id_codtema
				LEFT JOIN ".$this->tabla_expedientes." E ON (E.anio = T.anio AND E.tipo = T.tipo AND E.numero = T.numero AND E.cuerpo = T.cuerpo AND E.alcance = T.alcance)
			  ";
		// 15/05/2012	WHERE CT.habilitado_tema = 1	  
		//fputs(fopen('sql_obtenerTemasFicha.txt','w'),print_r($sql, true));
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$i=0;
		while ($row = mysql_fetch_array($resultado, MYSQL_ASSOC)) {
		    $datos[$i] = $row;
		    $i++;
		}
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerAutoresFicha($anio, $tipo, $numero, $cuerpo, $alcance)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT L.descripcion_grp
				FROM (SELECT * FROM ".$this->tabla_autores."
					  WHERE anio = ".$anio."
					  AND tipo = '".$tipo."'
					  AND numero = ".$numero."
					  AND cuerpo = ".$cuerpo."
					  AND alcance = ".$alcance."
					  )A
				LEFT JOIN ".$this->tabla_lugares." L ON (L.tipo_grp = A.autor_tipo AND L.codigo_grp = A.autor_codigo)
				LEFT JOIN ".$this->tabla_expedientes." E ON (E.anio = A.anio AND E.tipo = A.tipo AND E.numero = A.numero AND E.cuerpo = A.cuerpo AND E.alcance = A.alcance)
		       ";
		// 15/05/2012	WHERE L.habilitado_grp = 1       
		//fputs(fopen('sql_obtenerAutoresFicha.txt','w'),print_r($sql, true));
		 
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
				
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function obtenerProyectosFicha($anio, $tipo, $numero, $cuerpo, $alcance)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT DISTINCT CP.descripcion_proyecto, P.extracto, P.orden_proyecto
			FROM (SELECT anio, tipo, numero, id_codproyecto, extracto, orden_proyecto
			      FROM ".$this->tabla_proyectos."
			      WHERE anio = ".$anio."
			      AND tipo = '".$tipo."'
			      AND numero = ".$numero."
			      AND cuerpo = ".$cuerpo."
			      AND alcance = ".$alcance."
			     )P
			LEFT JOIN ".$this->tabla_codproyectos." CP ON P.id_codproyecto = CP.id_codproyecto 
			LEFT JOIN ".$this->tabla_expedientes." E ON ( E.anio = P.anio AND E.tipo = P.tipo AND E.numero = P.numero )
		      ";
		//fputs(fopen('sql_obtenerProyectosFicha.txt','w'),print_r($sql, true));
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
		
	public function obtenerIniciador($tipo, $codigo)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT descripcion_grp
				FROM ".$this->tabla_lugares."
				WHERE tipo_grp = '".$tipo."'
				AND codigo_grp = '".$codigo."'
			   ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO
		
		$this->desconectar($conexion);
		
		return $dato[0]['descripcion_grp'];	   
	}	
	
	public function obtenerCategoria($id_codcategoria)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT descripcion_categoria
				FROM ".$this->tabla_codcategoria."
				WHERE id_codcategoria = ".$id_codcategoria."
			   ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO
		
		$this->desconectar($conexion);
		
		return $dato[0]['descripcion_categoria'];	   
	}	
}
?>
