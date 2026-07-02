<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class consultaGralModel extends ModeloBaseMySQLi
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
		if (!empty($this->filtro['c_iniciado_tipo']) && !empty($this->filtro['c_iniciado_codigo'])){
		
			$filtro .= " AND E.iniciador_tipo = '".$this->filtro['c_iniciado_tipo']."' AND E.iniciador_codigo = '".$this->filtro['c_iniciado_codigo']."'";
		}
		
		// FILTRA POR Palabra (Caratula o Extracto)
		if ( !empty($this->filtro['c_palabra']) )
		{
		  $filtro .= " AND ( E.caratula LIKE '%".addslashes($this->filtro['c_palabra'])."%' OR (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																																					FROM ".$this->tabla_proyectos."
																																					WHERE extracto LIKE '%".addslashes($this->filtro['c_palabra'])."%' 
																																				  )	
						   )
				     ";
		}
		
		// SE FILTRA POR Tema
		if ( !empty($this->filtro['c_tema']) )
		{
			$filtro .= " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																				  FROM ".$this->tabla_temas."
																				  WHERE id_codtema = ".$this->filtro['c_tema']."
																				)
				       ";
		}
			
		// SE FILTRA POR Autor
		if ( !empty($this->filtro['c_autor_tipo']) && !empty($this->filtro['c_autor_codigo']) )
		{

			$filtro .= " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																				  FROM ".$this->tabla_autores."
																				  WHERE autor_tipo = '".$this->filtro['c_autor_tipo']."'
																				  AND autor_codigo = '".$this->filtro['c_autor_codigo']."'
																				)
				       ";
		}
		
		// SE FILTRA POR Estado
		if ( !empty($this->filtro['c_estado']) )
		{
			$filtro .= " AND ( SELECT id_codestado FROM ".$this->tabla_estados."
							   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
							   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
							   LIMIT 1
							 ) IN (".$this->filtro['c_estado'].")
					   ";
		}
		
		// SE FILTRA POR Comisión:
		if ( !empty($this->filtro['c_comision_tipo']) && !empty($this->filtro['c_comision_codigo']) )
		{
		   $filtro .= " AND ( SELECT id_codestado FROM ".$this->tabla_estados."
							  WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
							  ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
							  LIMIT 1
							) IN ((SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 3),
								  (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 16),
								  (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 79)
								 )
						AND ( SELECT comision_codigo FROM ".$this->tabla_giros."
							  WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance 
							  AND fecha_entrada_giro > '0000-00-00'
							  AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
							  ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC LIMIT 1
							) = '".$this->filtro['c_comision_codigo']."'
					  ";
		}
		  
		/***************************************************************************************************
			  QUERY GENERAL:
		***************************************************************************************************/
		// SQL_CALC_FOUND_ROWS: calcula el número de resultados de la query sin el LIMIT
		$sql = "SELECT SQL_CALC_FOUND_ROWS * 
				FROM (
					   SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision,
							  (SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria)AS categoria, 
							  (SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = iniciador_tipo AND Ini.codigo_grp = iniciador_codigo)AS iniciador
					   FROM ".$this->tabla_expedientes." AS E
					   WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['c_fecha_desde']."' AND '".$this->filtro['c_fecha_hasta']."'
					   ".$filtro."
					   ORDER BY E.anio ASC, E.tipo ASC, E.numero ASC, E.cuerpo ASC, E.alcance ASC
					 )AS AUX
			   ";
		
		// SE AGREGA EL LIMITE A LA QUERY
		$limite = " LIMIT ".$this->filtro['c_inicio'].", ".$this->filtro['c_rango']."";	
		$sql .= $limite;
		
		fputs(fopen('sql_consulta_general.txt', 'w'),print_r($sql, true));
		
		// SE EJECUTA LA QUERY	
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		{
			// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
			$datos = $this->crearVector($resultado);
			
			// SE AVERIGUA EL NUMERO TOTAL DE EXPEDIENTES, FOUND_ROWS() obtiene el resultado del último SQL_CALC_FOUND_ROWS ejecutado
			$sqlTotal = "SELECT FOUND_ROWS() AS total"; 
			$resultadoTotal = $this->ejecutarQuery($sqlTotal); 
			
			$rowTotal = mysql_fetch_assoc($resultadoTotal); 
			// Total de registros sin limit 
			$_SESSION['total'] = $rowTotal["total"];
			//fputs(fopen('total_consulta_general.txt', 'w'),print_r($_SESSION['total'], true));
		}
		else
		{
			$_SESSION['total'] = 0;
			return false;
		}
		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		//fputs(fopen('datos_consulta_generalM.txt', 'w'),print_r($datos, true));

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);
		
		return $datos;
    }			
	    
    /**
    PARA EL LISTADO (FORMATO A IMPRIMIR Y EL DOCUMENTO DE TEXTO):
    SE VUELVE A EJECUTAR LA QUERY EN PARTES,CON LA CANTIDAD TOTAL DE EXPEDIENTES RESULTANTES SE CALCULA 
    LA CANTIDAD DE PAGINAS USANDO UN RANGO ESPECIFICO (100 POR DEFECTO)
    /**/
    public function armar_listado_para_reporte()
    {
		$conexion = $this->conectar();
		
		// FILTRA POR Fecha POR DEFECTO 
		$filtro = "";
		
		// FILTRA POR Categoria
		if (!empty($this->filtro['c_categoria'])){
		  $filtro .= " AND E.id_codcategoria = '".$this->filtro['c_categoria']."'";
		}

		// FILTRA POR Iniciador 
		if (!empty($this->filtro['c_iniciado_tipo']) && !empty($this->filtro['c_iniciado_codigo'])){
		  $filtro .= " AND E.iniciador_tipo = '".$this->filtro['c_iniciado_tipo']."' AND E.iniciador_codigo = '".$this->filtro['c_iniciado_codigo']."'";
		}

		// FILTRA POR Palabra (Caratula o Extracto)
		if (!empty($this->filtro['c_palabra'])){
		
		  $filtro .= " AND ( E.caratula LIKE '%".addslashes($this->filtro['c_palabra'])."%' OR (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																																					FROM ".$this->tabla_proyectos."
																																					WHERE extracto LIKE '%".addslashes($this->filtro['c_palabra'])."%' 
																																				  )	
						   ) 
				     ";
		}

		// SE FILTRA POR Tema
		if (!empty($this->filtro['c_tema'])){
		
			$filtro .= " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																				  FROM ".$this->tabla_temas."
																				  WHERE id_codtema = ".$this->filtro['c_tema']."
																				)
				       ";
		}
			
		// SE FILTRA POR Autor
		if (!empty($this->filtro['c_autor_tipo']) && !empty($this->filtro['c_autor_codigo'])){

			$filtro .= " AND (E.anio, E.tipo, E.numero, E.cuerpo, E.alcance) IN ( SELECT anio, tipo, numero, cuerpo, alcance 
																				  FROM ".$this->tabla_autores."
																				  WHERE autor_tipo = '".$this->filtro['c_autor_tipo']."'
																				  AND autor_codigo = '".$this->filtro['c_autor_codigo']."'
																				)
					   ";
		}
		
		// SE FILTRA POR Estado
		if ( !empty($this->filtro['c_estado']) )
		{
			$filtro .= " AND ( SELECT id_codestado FROM ".$this->tabla_estados."
							   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
							   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
							   LIMIT 1
							 ) IN (".$this->filtro['c_estado'].")
					   ";
		}
				
		// SE FILTRA POR Comisión:
		if ( !empty($this->filtro['c_comision_tipo']) && !empty($this->filtro['c_comision_codigo']) )
		{
		   
			$filtro .= " AND ( SELECT id_codestado FROM ".$this->tabla_estados."
							   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
							   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
							   LIMIT 1
							 ) IN ((SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 3),
								   (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 16),
								   (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 79)
								  )
						 AND ( SELECT comision_codigo FROM ".$this->tabla_giros."
							   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance 
							   AND fecha_entrada_giro > '0000-00-00'
							   AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
							   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC LIMIT 1
							 ) = '".$this->filtro['c_comision_codigo']."'
					  ";
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
							    ORDER BY E.anio ASC, E.tipo ASC, E.numero ASC, E.cuerpo ASC, E.alcance ASC
							  )AS AUX
			   ";
		//fputs(fopen('sql_armar_listado_para_reporte.txt', 'w'), print_r($sql, true));
		
		
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
			$sql_auxiliar = null;
			
			$total_devueltos = $this->obtenerNumeroFilas($resultado_parcial);
			// SI DEVUELVE ALGUN REGISTRO
			if ( $total_devueltos != 0 )
			{
				// SE UTILIZA UN VECTOR AUXILIAR 
				$vector_auxiliar = $this->crearVector($resultado_parcial);
				//fputs(fopen('vector_auxiliar_'.$r.'_armar_listado_para_reporte_M.txt', 'w'), print_r($vector_auxiliar, true));
				
				$cant_auxiliar = count($vector_auxiliar);
				//fputs(fopen('cant_auxiliar_vector_'.$r.'_armar_listado_para_reporte.txt', 'w'), print_r($cant_auxiliar, true));
				
				// SI SE LLEGÓ AL ÚLTIMO CICLO DEL RECORRIDO
				if ( $cant_auxiliar < $rango )
				{
					$corte = 1;// PARA CORTAR EL CICLO DEL WHILE
				}
				
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
				//fputs(fopen("listado_para_imprimir_y_procesar_PARCIAL_".$r.".txt", 'w'), print_r($listado_aux_para_reporte, true));
				
				// SE UNEN LOS RESULTADOS PARCIALES
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
		
    public function obtenerIniciadores($habilitado = 1)
    {
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_lugares." WHERE habilitado_grp = ".$habilitado."";
		  
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
	
    public function obtenerAutores($habilitado = 1)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT tipo_grp AS autor_tipo, codigo_grp AS autor_codigo, descripcion_grp AS autor_descripcion 
				FROM ".$this->tabla_lugares." 
				WHERE habilitado_grp = ".$habilitado."
			   ";
		  
		$resultado = $this->ejecutarQuery($sql, $conexion);
				
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
		//fputs(fopen('datosAutoresM.txt','w'),print_r($datos, true));
		
		$this->desconectar($conexion);
		
		return $datos;
    }
	
    public function obtenerComisiones($habilitado = 1)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_lugares." 
				WHERE tipo_grp = 'C' 
				AND habilitado_grp = ".$habilitado."
			   ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
    
    public function obtenerCategorias($habilitado = 1)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_codcategoria." WHERE habilitado_categoria = ".$habilitado."";
		  
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
    
    public function obtenerTemas($habilitado = 1)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_codtemas." WHERE habilitado_tema = ".$habilitado."";
		  
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
    
    public function obtenerEstados($habilitado = 1)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$this->tabla_codestados." WHERE habilitado_codestado = ".$habilitado."";
		  
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
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);

		// SE LIBERA LA MEMORIA USADA POR LA QUERY 
		$this->liberarMemoria($resultado);

		$this->desconectar($conexion);

		return $datos;
    }
    
    public function obtenerTemasFicha($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT CT.descripcion_tema 
				FROM (SELECT id_codtema, anio, tipo, numero, cuerpo, alcance 
					  FROM ".$this->tabla_temas." 
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
			
		// SE LIBERA LA MEMORIA USADA POR LA QUERY 
		$this->liberarMemoria($resultado);

		$this->desconectar($conexion);
		
		return $datos;
    }
    
    public function obtenerAutoresFicha($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT L.descripcion_grp
				FROM (SELECT autor_tipo, autor_codigo, anio, tipo, numero, cuerpo, alcance 
					  FROM ".$this->tabla_autores."
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
		
		// SE LIBERA LA MEMORIA USADA POR LA QUERY 
		$this->liberarMemoria($resultado);
			
		$this->desconectar($conexion);
		
		return $datos;
    }
    
    public function obtenerEstadoFicha($anio, $tipo, $numero, $cuerpo, $alcance, $id_codestado = 0) 
	{    
		$conexion = $this->conectar();
		
		if ($id_codestado != 0)
		{
		    $filtro_id_codestado = "AND id_codestado = ".$id_codestado."";
		}
		else
		{
		    $filtro_id_codestado = "";
		}
		
		$sql = "SELECT Est.id_codestado, Est.fecha_estado AS fecha_estado, CEst.nombre_estado AS nombre_estado
				FROM 
				( SELECT id_codestado, fecha_estado 
				  FROM ".$this->tabla_estados."
				  WHERE anio = ".$anio."
				  AND tipo = '".$tipo."'
				  AND numero = ".$numero."
				  AND cuerpo = ".$cuerpo."
				  AND alcance = ".$alcance."
				  ".$filtro_id_codestado."
				  ORDER BY fecha_estado DESC, orden_estado DESC
				  LIMIT 0,1	    
				) Est
				LEFT JOIN ".$this->tabla_codestados." CEst 
				ON CEst.id_codestado = Est.id_codestado
			   ";
		//fputs(fopen('sql_obtenerEstadoFicha.txt', 'w'),print_r($sql, true));
		  
		$resultado = $this->ejecutarQuery($sql, $conexion);
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
		
		// SE LIBERA LA MEMORIA USADA POR LA QUERY 
		$this->liberarMemoria($resultado);
			
		$this->desconectar($conexion);
		
		return $datos;
    }
    
    public function obtenerEstadoFicha_para_Listados($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT Est.id_codestado, Est.fecha_estado AS fecha_estado, CEst.nombre_estado AS nombre_estado
				FROM 
				( SELECT id_codestado, fecha_estado 
				  FROM ".$this->tabla_estados."
				  WHERE anio = ".$anio."
				  AND tipo = '".$tipo."'
				  AND numero = ".$numero."
				  AND cuerpo = ".$cuerpo."
				  AND alcance = ".$alcance."
				  ORDER BY fecha_estado DESC
				) Est
				LEFT JOIN ".$this->tabla_codestados." CEst 
				ON CEst.id_codestado = Est.id_codestado   
				WHERE (Est.id_codestado = ".$this->obtenerIdSegunCodigo($this->tabla_codestados, 'id_codestado', 'codigo_estado', 3)." OR 
					   Est.id_codestado = ".$this->obtenerIdSegunCodigo($this->tabla_codestados, 'id_codestado', 'codigo_estado', 16)." OR 
					   Est.id_codestado = ".$this->obtenerIdSegunCodigo($this->tabla_codestados, 'id_codestado', 'codigo_estado', 79)."
					  )
			   ";
		//fputs(fopen('sql_obtenerEstadoFicha.txt', 'w'),print_r($sql, true));
		  
		$resultado = $this->ejecutarQuery($sql, $conexion);
		//se crea un vector asociativo para la vista
		$datos = $this->crearVector($resultado);
		
		// SE LIBERA LA MEMORIA USADA POR LA QUERY 
		$this->liberarMemoria($resultado);
			
		$this->desconectar($conexion);
		
		return $datos;
    }
    
    public function obtenerComisionFicha($anio, $tipo, $numero, $cuerpo, $alcance, $comision_tipo = '', $comision_codigo = '')
	{    
		$conexion = $this->conectar();
		
		$filtro_comision_tipo = "";
		if ($comision_tipo != ''){
			$filtro_comision_tipo = "AND comision_tipo = '".$comision_tipo."'";
		}
		
		$filtro_comision_codigo = "";
		if ($comision_codigo != ''){
			$filtro_comision_codigo = "AND comision_codigo = '".$comision_codigo."'";
		}
		
		$sql = "SELECT L.descripcion_grp AS comision, G.fecha_entrada_giro AS fecha_giro
				FROM (SELECT comision_tipo, comision_codigo, fecha_entrada_giro 
					  FROM ".$this->tabla_giros."
					  WHERE anio = ".$anio."
					  AND tipo = '".$tipo."'
					  AND numero = ".$numero."
					  AND cuerpo = ".$cuerpo."
					  AND alcance = ".$alcance."
					  ".$filtro_comision_tipo."
					  ".$filtro_comision_codigo."
					  AND fecha_entrada_giro > '0000-00-00'
					  AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
					 ) G
				LEFT JOIN ".$this->tabla_lugares." L ON (L.tipo_grp = G.comision_tipo AND L.codigo_grp = G.comision_codigo)
			   ";
		//fputs(fopen('sql_obtenerComisionFicha.txt','w'),print_r($sql, true));	   
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$datos = $this->crearVector($resultado);
		
		// SE LIBERA LA MEMORIA USADA POR LA QUERY 
		$this->liberarMemoria($resultado);
			
		$this->desconectar($conexion);
		
		return $datos;
    }
	
    public function obtenerCategoria($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT descripcion_categoria
				FROM ".$this->tabla_codcategoria."
				WHERE EXISTS (
							  SELECT anio, tipo, numero, cuerpo, alcance
							  FROM ".$this->tabla_expedientes."
							  WHERE anio = ".$anio."
							  AND tipo = '".$tipo."'
							  AND numero = ".$numero."
							  AND cuerpo = ".$cuerpo."
							  AND alcance = ".$alcance."
							  AND id_codcategoria = ".$this->tabla_codcategoria.".id_codcategoria
							 )
				";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $dato[0]['descripcion_categoria'];	
    }
    
    public function obtenerIniciador($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT descripcion_grp
				FROM ".$this->tabla_lugares."
				WHERE EXISTS (
							  SELECT anio, tipo, numero, cuerpo, alcance
							  FROM ".$this->tabla_expedientes."
							  WHERE anio = ".$anio."
							  AND tipo = '".$tipo."'
							  AND numero = ".$numero."
							  AND cuerpo = ".$cuerpo."
							  AND alcance = ".$alcance."
							  AND iniciador_tipo = ".$this->tabla_lugares.".tipo_grp
							  AND iniciador_codigo = ".$this->tabla_lugares.".codigo_grp
							 ) 
			   ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $dato[0]['descripcion_grp'];	
    }
    
    public function obtenerNombreAutor($autor_tipo, $autor_codigo)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT L.descripcion_grp
				FROM ".$this->tabla_lugares." AS L
				INNER JOIN
				(SELECT autor_tipo, autor_codigo 
				  FROM ".$this->tabla_autores."
				  WHERE autor_tipo = '".$autor_tipo."'
				  AND autor_codigo = '".$autor_codigo."'
				) AS A
				ON A.autor_tipo = L.tipo_grp
				AND A.autor_codigo = L.codigo_grp		
			   ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $dato[0]['descripcion_grp'];	
    }
    
    /**
     * Se obtiene el nombre de una Comisión determinada
     * 
     * @param string $comision_tipo
     * @param string $comision_codigo
     * 
     * @return string $dato[0]['descripcion_grp']
     */
    public function obtenerNombreComision($comision_tipo, $comision_codigo)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT L.descripcion_grp
				FROM ".$this->tabla_lugares." AS L
				INNER JOIN
				(SELECT comision_tipo, comision_codigo, fecha_entrada_giro 
				  FROM ".$this->tabla_giros."
				  WHERE comision_tipo = '".$comision_tipo."'
				  AND comision_codigo = '".$comision_codigo."'
				) AS G
				ON G.comision_tipo = L.tipo_grp AND G.comision_codigo = L.codigo_grp		
			   ";
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $dato[0]['descripcion_grp'];
    }
    
    public function obtenerSancionados($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT *
				FROM ".$this->tabla_sanciones."
				WHERE anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero."
				AND cuerpo = ".$cuerpo."
				AND alcance = ".$alcance."
			   ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;	
    }
    
    /**
     * Se obtiene el nombre de un Estado determinado
     * 
     * @param integer $id_codestado
     */
    public function obtenerNombreEstado($id_codestado)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT nombre_estado 
				FROM ".$this->tabla_codestados." 
				WHERE id_codestado = ".$id_codestado."
			   ";
		  
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['nombre_estado'];
    }
	    
    public function obtenerNombreTema($id_codtema)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT descripcion_tema FROM ".$this->tabla_codtemas." WHERE id_codtema = ".$id_codtema."";
		  
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos[0]['descripcion_tema'];
    }
    
    public function obtenerNombreCategoria($id_codcategoria)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT descripcion_categoria FROM ".$this->tabla_codcategoria." WHERE id_codcategoria = ".$id_codcategoria."";
		  
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos[0]['descripcion_categoria'];
    }
    
    public function obtenerCaratula($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT caratula
				FROM ".$this->tabla_expedientes."
				WHERE anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero."
				AND cuerpo = ".$cuerpo."
				AND alcance = ".$alcance."
			   ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $dato[0]['caratula'];	
    }
    
    public function obtenerFechaEntradaExpe($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT fecha_entrada_expe
				FROM ".$this->tabla_expedientes."
				WHERE anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero."
				AND cuerpo = ".$cuerpo."
				AND alcance = ".$alcance."
		       ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $dato[0]['fecha_entrada_expe'];	
    }
    
    public function obtenerTipoIniciador($anio, $tipo, $numero, $cuerpo, $alcance)
	{   
		$conexion = $this->conectar();
		
		$sql = "SELECT iniciador_tipo
				FROM ".$this->tabla_expedientes."
				WHERE anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero."
				AND cuerpo = ".$cuerpo."
				AND alcance = ".$alcance."
			   ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $dato[0]['iniciador_tipo'];	
    }
    
    public function obtenerDatosSancion($anio, $tipo, $numero, $cuerpo, $alcance, $orden_proyecto)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT fecha_promulga, numero_promulga, decreto_promulga, fecha_sancion, numero_sancion 
				FROM ".$this->tabla_sanciones."
				WHERE anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero."
				AND cuerpo = ".$cuerpo."
				AND alcance = ".$alcance."
				AND orden_proyecto = ".$orden_proyecto."
			   ";
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
    
    public function obtenerCodigoProyecto($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT codigo_proyecto
				FROM ".$this->tabla_codproyectos."
				WHERE EXISTS (
							  SELECT id_codproyecto  
							  FROM ".$this->tabla_proyectos."
							  WHERE anio = ".$anio."
							  AND tipo = '".$tipo."'
							  AND numero = ".$numero."
							  AND cuerpo = ".$cuerpo."
							  AND alcance = ".$alcance."
							 )
			   ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$dato = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $dato[0]['codigo_proyecto'];
    }
    
    public function obtenerNombreComision_para_documento($anio, $tipo, $numero, $cuerpo, $alcance, $comision_tipo, $comision_codigo)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT L.descripcion_grp, G.fecha_entrada_giro
				FROM ".$this->tabla_lugares." AS L
				INNER JOIN
				( SELECT comision_tipo, comision_codigo, fecha_entrada_giro 
				  FROM ".$this->tabla_giros."
				  WHERE anio = ".$anio."
				  AND tipo = '".$tipo."'
				  AND numero = ".$numero."
				  AND cuerpo = ".$cuerpo."
				  AND alcance = ".$alcance."
				  AND comision_tipo = '".$comision_tipo."'
				  AND comision_codigo = '".$comision_codigo."'
				) AS G
				ON G.comision_tipo = L.tipo_grp AND G.comision_codigo = L.codigo_grp		
			  ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
	    
    public function obtenerMarca($dato)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT marca_comision 
				FROM ".$this->tabla_expedientes." 
				WHERE anio = ".$dato['anio']."
				AND tipo = '".$dato['tipo']."'
				AND numero = ".$dato['numero']."
				AND cuerpo = ".$dato['cuerpo']."
				AND alcance = ".$dato['alcance']."
			   ";
		  
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$valor_marca = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $valor_marca[0]['marca_comision'];
    }
    
    public function obtener_datos_para_documento_de_texto($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT *
				FROM ".$this->tabla_expedientes."
				WHERE anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero."
				AND cuerpo = ".$cuerpo."
				AND alcance = ".$alcance."
			   ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;	
    }
	    
    public function obtener_datos_por_marca_comision($anio, $tipo, $numero, $cuerpo, $alcance, $marca_comision)
	{	    
		$conexion = $this->conectar();
		
		$sql = "SELECT *
				FROM ".$this->tabla_expedientes."
				WHERE anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero."
				AND cuerpo = ".$cuerpo."
				AND alcance = ".$alcance."
				AND marca_comision = ".$marca_comision."
			   ";
		//fputs(fopen('sql_obtener_datos_por_marca_comision.txt','w'),print_r($sql,true));
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);
			
		$this->desconectar($conexion);
		
		return $datos;	
    }
	    
    public function obtener_letra_y_caratula($anio, $tipo, $numero, $cuerpo, $alcance)
	{	    
		$conexion = $this->conectar();
		
		$sql = "SELECT *
				FROM ".$this->tabla_expedientes."
				WHERE anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero."
				AND cuerpo = ".$cuerpo."
				AND alcance = ".$alcance."
			   ";
		//fputs(fopen('sql_obtener_datos_por_marca_comision.txt','w'),print_r($sql,true));
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);
			
		$this->desconectar($conexion);
		
		return $datos;	
    }
	
    public function obtenerNombreIniciador($iniciador_tipo, $iniciador_codigo)
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT Ini.descripcion_grp AS iniciador
			    FROM ".$this->tabla_lugares." Ini 
			    WHERE Ini.tipo_grp = '".$iniciador_tipo."' 
			    AND Ini.codigo_grp = '".$iniciador_codigo."' 
			   ";
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos[0]['iniciador'];
    }

    public function obtenerAntecedentesFicha($numero, $anio)
	{	
		$conexion = $this->conectar();
		
		$sql = "SELECT anio_a, tipo_a, numero_a, digito_a, cuerpo_a, alcance_a
				FROM ".$this->tabla_antecedentes."
				WHERE numero = ".$numero."
				AND anio = ".$anio."
			   ";
		//fputs(fopen('sql_obtenerProyectosFicha.txt','w'),print_r($sql, true));

		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$datos = $this->crearVector($resultado);

		// SE LIBERA LA MEMORIA USADA POR LA QUERY 
		$this->liberarMemoria($resultado);

		$this->desconectar($conexion);

		return $datos;
    }
}
?>
