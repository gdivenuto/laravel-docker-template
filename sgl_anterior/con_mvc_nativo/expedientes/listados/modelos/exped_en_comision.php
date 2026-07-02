<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class expedEnComisionModel extends ModeloBaseMySQLi
{
    public function conectar()
	{
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(2);
	}
	
    public function listar()
	{    
		$conexion = $this->conectar();

		// FILTRA POR Fecha Desde y Fecha Hasta POR DEFECTO
		$filtro = "";
		
		// PARA Orden del Día SE BUSCAN LOS EXPEDIENTES QUE POSEAN MARCA COMISIÓN
		if ($this->filtro['l_tipo_listado'] == "orden_del_dia")
			$filtro .= " AND E.marca_comision <> 0";
		
		// POR Fecha Comisión PARA Expedientes en Comisión y Detalle de Giros
		$por_fecha_comision = "";
		if ( $this->filtro['l_fecha_comision'] != '' )
			$por_fecha_comision = " AND fecha_entrada_giro <= '".$this->filtro['l_fecha_comision']."'";
		
		if ( $this->filtro['l_estado'] != '' && $this->filtro['l_estado'] != '0' ) {
			$filtro .= " AND ( SELECT id_codestado FROM ".$this->tabla_estados."
							   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
							   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
							   LIMIT 1
							 ) IN (".$this->filtro['l_estado'].")
					   ";
		}
		
		// 21/06/2012	PARA EL LISTADO DE INFORMES
		$filtro_informes_A = "";
		$filtro_informes_B = "";
		$filtro_informes_C = "";
		$filtro_informes_D = "";
		if ( $this->filtro['l_tipo_listado'] == "informes" ) {
			$filtro_informes_A = ", I.fecha_pedido_informe, I.detalle_informe";
			
			$filtro_informes_B = " INNER JOIN ".$this->tabla_informes." AS I
								   ON I.anio = E.anio AND I.tipo = E.tipo AND I.numero = E.numero AND I.cuerpo = E.cuerpo AND I.alcance = E.alcance
								 ";
			$filtro_informes_C = " AND I.fecha_vuelta_informe IS NULL";	
			
			$filtro_informes_D = " AND orden_giro = I.orden_giro";			 
		}
		
		// SI SE FILTRA POR UNA Comision:
		if ( $this->filtro['l_comision_codigo'] != '' ) {
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
							   ".$por_fecha_comision."
							   ".$filtro_informes_D."
							   AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
							   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC LIMIT 1
							 ) IN ('".$this->filtro['l_comision_codigo']."')
					   ";
		} else {
			// SI SE DESEA FILTRAR UTILIZANDO UN LISTADO DE COMISIONES
			if ( ( $this->filtro['l_tipo_listado'] == 'exped_en_comision' ) && ( $this->filtro['l_comisiones_modal'] ) ) {
				$listado_comisiones = "(";
				
				$cant_listado_comisiones = count($this->filtro['l_comisiones_modal']);
				for ($i=0; $i < $cant_listado_comisiones; $i++) {
					$codigo_comision = &$this->filtro['l_comisiones_modal'][$i];
					
					$listado_comisiones .= "'".$codigo_comision."'";
					
					$anteultimo = $cant_listado_comisiones-1;
					if ( $i != $anteultimo )
						$listado_comisiones .= ", ";
				}
				
				$listado_comisiones .= ")";
				
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
								   ".$por_fecha_comision."
								   ".$filtro_informes_D."
								   AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
								   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC LIMIT 1
								 ) IN ".$listado_comisiones."
						   ";
			} else {
				// SI NO SE FILTRA POR COMISION NI POR ESTADO
				if ( $this->filtro['l_estado'] == '' ) {
					// AGREGADO EL 02/08/2012
					if ( $this->filtro['l_tipo_listado'] != "asuntos_entrados" ) {
						$filtro .= " AND ( SELECT id_codestado FROM ".$this->tabla_estados."
										   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
										   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
										   LIMIT 1
										 ) IN ((SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 3),
											   (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 16),
											   (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 79)
											  )
									 AND ( SELECT orden_giro FROM ".$this->tabla_giros."
										   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance 
										   AND fecha_entrada_giro > '0000-00-00'
										   ".$por_fecha_comision."
										   ".$filtro_informes_D."
										   AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
										   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC LIMIT 1
										 ) > 0
								   ";
					}
				}
			}
		}
		
		// SE DEFINE EL ORDEN SEGÚN EL LISTADO
		if ( $this->filtro['l_tipo_listado'] == "orden_del_dia" )
		   $orden = "ORDER BY E.anio ASC, E.tipo ASC, E.numero ASC, E.cuerpo ASC, E.alcance ASC";
		else
		   $orden = "ORDER BY E.anio, E.tipo, E.numero, E.cuerpo, E.alcance";
		/***************************************************************************************************
			  QUERY GENERAL:
		***************************************************************************************************/
		// Se utiliza la opción SQL_CALC_FOUND_ROWS en conjunto con FOUND_ROWS() 
		// para calcular el número de resultados de la query sin la cláusula LIMIT
		$sql = "SELECT SQL_CALC_FOUND_ROWS * 
				FROM ( SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision,
					   (SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria)AS categoria, 
					   (SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = iniciador_tipo AND Ini.codigo_grp = iniciador_codigo)AS iniciador
					   ".$filtro_informes_A."
					   FROM ".$this->tabla_expedientes." AS E 
					   ".$filtro_informes_B."
					   WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['l_fecha_desde']."' AND '".$this->filtro['l_fecha_hasta']."'
					   ".$filtro."
					   ".$filtro_informes_C."
					   ".$orden."
					 )AS AUX
			   ";
		
		// SI SE DESEAN VER TODOS, EN Exped. en Comision Y EN Informes
		if ( $this->filtro['l_vencidos'] != 1 ) {
			// SE AGREGA EL LIMITE A LA QUERY
			$limite = "LIMIT ".$this->filtro['l_inicio'].", ".$this->filtro['l_rango']."";
			$sql .= $limite;	
		}
		
		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);
		
		// Sólo para verificar que haya devuelto algo, es el total parcial, ya que se utilizó la claúsula LIMIT
		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0) {
			// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
			$listado = $this->crearVector($resultado);

			// SE AVERIGUA EL NUMERO TOTAL DE EXPEDIENTES 
			// Con FOUND_ROWS() se obtiene la cantidad de registros que hubiera devuelto la query 
			// si no se hubiese usado la cláusula LIMIT
			$sqlTotal = "SELECT FOUND_ROWS() as total"; 

			$resultadoTotal = $this->ejecutarQuery($sqlTotal); 
			
			$dato = $this->obtenerFila($resultadoTotal);
			// Total de registros sin limit 
			$_SESSION['total'] = $dato["total"];
		} else {
			$_SESSION['total'] = 0;
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);
			
		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);
		
		return $listado;
    }
	
	/**
	 * Se obtienen los Informes de una Comisión determinada, en un rango de fechas específico
	 * @return [type] [description]
	 */
	public function listarInformes() {
		$conexion = $this->conectar();
		
		$pfecha_desde = $this->filtro['l_fecha_desde'];
		$pfecha_hasta = $this->filtro['l_fecha_hasta'];
		$filtro_por_comision = ( $this->filtro['l_comision_codigo'] != '' ) ? "AND G.comision_codigo = '".$this->filtro['l_comision_codigo']."'" : "";

		// Devuelve los Informes de una Comisión determinada, en un rango de fechas específico
		$sql_informes = "SELECT
		    I.anio,
		    I.tipo,
		    I.numero,
		    I.cuerpo,
		    I.alcance,
		    I.orden_giro,
		    I.orden_informe,
		    I.fecha_pedido_informe,
		    I.fecha_vuelta_informe,
		    I.detalle_informe,
		    I.observaciones_informe,
		    I.id_usuario,
		    G.comision_codigo AS ro_codigo_comision,
		    LugParaComision.descripcion_grp AS ro_nombre_comision,
		    G.fecha_entrada_giro AS ro_fecha_comision,
		    LugParaIniciador.descripcion_grp AS iniciador,
		    E.caratula,
		    E.fecha_entrada_expe,
		    E.id_codcategoria, 
		    E.iniciador_tipo, 
		    E.iniciador_codigo, 
		    E.marca_comision
		FROM expe_informes AS I
		INNER JOIN expe_giros G
		ON (I.anio = G.anio AND 
		    I.tipo = G.tipo AND 
		    I.numero = G.numero AND 
		    I.cuerpo = G.cuerpo AND 
		    I.alcance = G.alcance AND 
		    I.orden_giro = G.orden_giro)
		INNER JOIN expe_lugares AS LugParaComision
		ON (LugParaComision.tipo_grp = G.comision_tipo AND LugParaComision.codigo_grp = G.comision_codigo)
		INNER JOIN expe_expedientes AS E
		ON (E.anio = I.anio AND 
		    E.tipo = I.tipo AND 
		    E.numero = I.numero AND 
		    E.cuerpo = I.cuerpo AND 
		    E.alcance = I.alcance)
		INNER JOIN expe_lugares AS LugParaIniciador
		ON (LugParaIniciador.tipo_grp = E.iniciador_tipo AND LugParaIniciador.codigo_grp = E.iniciador_codigo)
		WHERE G.fecha_entrada_giro IS NOT NULL
		AND   G.fecha_salida_giro IS NULL
		AND   G.fecha_entrada_giro BETWEEN '".$pfecha_desde."' AND '".$pfecha_hasta."'
		AND   I.fecha_vuelta_informe IS NULL
		".$filtro_por_comision."
		ORDER BY I.anio, I.tipo, I.numero, I.cuerpo, I.alcance, I.orden_informe";

		// Primero se obtiene la cantidad total de registros (sin tener en cuenta el limite)
		// ---------------------------------------------------------------------------------
		$resultado       = $this->ejecutarQuery($sql_informes);
		$datos           = $this->crearVector($resultado);
		$total_devueltos = count($datos);

		// Si NO se desea filtrar por los informes Vencidos
		// (si se limita y se filtra por los vencidos se pierden registros)
		if ( $this->filtro['l_vencidos'] != '1' ) {
			// SE AGREGA EL LIMITE A LA QUERY
			$sql_informes .= " LIMIT ".$this->filtro['l_inicio'].", ".$this->filtro['l_rango'];
		}
		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql_informes);
		$datos = $this->crearVector($resultado);
	
		// Si hay registros
		if ($total_devueltos > 0)
			$_SESSION['total'] = $total_devueltos;
		else {
			$_SESSION['total'] = 0;
			return false;
		}
		// Se libera la memoria usada por la query
		$this->liberarMemoria($resultado);
			
		// Se cierra la conexión
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	/**
	 * Se arma el listado de INFORMES para ser utilizado en los REPORTES
	 * @return [type] [description]
	 */
	public function armarListadoInformesReporte() {   
		$conexion = $this->conectar();
		
		$pfecha_desde = $this->filtro['l_fecha_desde'];
		$pfecha_hasta = $this->filtro['l_fecha_hasta'];
		$filtro_por_comision = ( $this->filtro['l_comision_codigo'] != '' ) ? "AND G.comision_codigo = '".$this->filtro['l_comision_codigo']."'" : "";

		// Devuelve los Informes de una Comisión determinada, en un rango de fechas específico
		$sql_informes = "SELECT
		    I.anio,
		    I.tipo,
		    I.numero,
		    I.cuerpo,
		    I.alcance,
		    I.orden_giro,
		    I.orden_informe,
		    I.fecha_pedido_informe,
		    I.fecha_vuelta_informe,
		    I.detalle_informe,
		    I.observaciones_informe,
		    I.id_usuario,
		    G.comision_codigo AS ro_codigo_comision,
		    LugParaComision.descripcion_grp AS ro_nombre_comision,
		    G.fecha_entrada_giro AS ro_fecha_comision,
		    LugParaIniciador.descripcion_grp AS iniciador,
		    E.caratula,
		    E.fecha_entrada_expe,
		    E.id_codcategoria, 
		    E.iniciador_tipo, 
		    E.iniciador_codigo, 
		    E.marca_comision
		FROM expe_informes AS I
		INNER JOIN expe_giros G
		ON (I.anio = G.anio AND 
		    I.tipo = G.tipo AND 
		    I.numero = G.numero AND 
		    I.cuerpo = G.cuerpo AND 
		    I.alcance = G.alcance AND 
		    I.orden_giro = G.orden_giro)
		INNER JOIN expe_lugares AS LugParaComision
		ON (LugParaComision.tipo_grp = G.comision_tipo AND LugParaComision.codigo_grp = G.comision_codigo)
		INNER JOIN expe_expedientes AS E
		ON (E.anio = I.anio AND 
		    E.tipo = I.tipo AND 
		    E.numero = I.numero AND 
		    E.cuerpo = I.cuerpo AND 
		    E.alcance = I.alcance)
		INNER JOIN expe_lugares AS LugParaIniciador
		ON (LugParaIniciador.tipo_grp = E.iniciador_tipo AND LugParaIniciador.codigo_grp = E.iniciador_codigo)
		WHERE G.fecha_entrada_giro IS NOT NULL
		AND   G.fecha_salida_giro IS NULL
		AND   G.fecha_entrada_giro BETWEEN '".$pfecha_desde."' AND '".$pfecha_hasta."'
		AND   I.fecha_vuelta_informe IS NULL
		".$filtro_por_comision."
		ORDER BY I.anio, I.tipo, I.numero, I.cuerpo, I.alcance, I.orden_informe";
	
		// Se ejecuta la query sin el LIMIT
		$resultado = $this->ejecutarQuery($sql_informes);
		// Se obtienen los datos sin tener en cuenta el LIMIT
		$datos = $this->crearVector($resultado);
		// Se obtiene la cantidad total de registros obtenidos
		$cantidad_TOTAL = count($datos);
					
		// SE UTILIZA UNA AUXILIAR PARA CALCULAR EL TOTAL DE REGISTROS
		$sql_auxiliar  = $sql_informes;  
		// SE AGREGA EL LIMITE A LA QUERY
		$sql_auxiliar .= " LIMIT 0, 100"; 	   
		
		// SE EJECUTA LA QUERY PARA LOS PRIMEROS 100 REGISTROS	
		$this->ejecutarQuery($sql_auxiliar);

		$rango = 100;
		$listado_para_reporte = Array();

		// SE EJECUTA LA QUERY CADA CIEN REGISTROS, MIENTRAS EXISTAN EXPEDIENTES/NOTAS
		$corte = false;	
		$r = 0;
		while ( $r < $cantidad_TOTAL && !$corte ) {
			// SE CALCULA EL INICIO DEL LIMITE A PEDIR
			$inicio = $r;
			
			// SE DEFINE LA QUERY AUXILIAR PARA CADA CICLO
			$sql_auxiliar = $sql_informes;
			
			// SE DEFINE EL LIMITE
			$limite_para_documento = " LIMIT ".$inicio.", ".$rango;
			
			// SE AGREGA EL LIMITE A LA QUERY AUXILIAR
			$sql_auxiliar .= $limite_para_documento;
				
			// SE EJECUTA LA QUERY PARCIAL PARA EL REPORTE	
			$resultado_parcial = $this->ejecutarQuery($sql_auxiliar);
			
			// SE LIMPIA PARA ASIGNARLE EL NUEVO LIMITE EN EL SIGUIENTE CICLO
			$sql_auxiliar = null;
			
			$total_devueltos = $this->obtenerNumeroFilas($resultado_parcial);
			// SI DEVUELVE ALGUN REGISTRO
			if ( $total_devueltos != 0 ) {
				// SE UTILIZA UN VECTOR AUXILIAR PARA EL RESULTADO PARCIAL
				$vector_auxiliar = $this->crearVector($resultado_parcial);
				
				$cant_auxiliar = count($vector_auxiliar);
				
				// SI SE LLEGÓ AL ÚLTIMO CICLO DEL RECORRIDO
				if ( $cant_auxiliar < $rango )
					$corte = 1;// PARA CORTAR EL CICLO DEL WHILE
				
				for ($a=0; $a < $cant_auxiliar; $a++) {
					$auxiliar = &$vector_auxiliar[$a];
					
					//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
					$auxiliar['temas']     = $this->obtenerTemasFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);
					//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
					$auxiliar['autores']   = $this->obtenerAutoresFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);
					//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE
					$auxiliar['proyectos'] = $this->obtenerProyectosFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);				
					
					// SE VA CARGANDO UN VECTOR AUXILIAR CON LOS REGISTROS DEVUELTOS
					$listado_aux_para_reporte[$a] = $auxiliar;
				}
				
				// SE UNEN LOS RESULTADOS PARCIALES
				$listado_para_reporte = array_merge($listado_para_reporte, $listado_aux_para_reporte);
				
				$listado_aux_para_reporte = null;
				
				// SE LIBERA LA MEMORIA USADA POR LA QUERY
				$this->liberarMemoria($resultado_parcial);
			} else
				return false;

			// SE INCREMENTA EL CONTADOR DE A 100, QUE ES EL VALOR DEL RANGO
			$r += $rango;
		}
		
		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);
		
		return $listado_para_reporte;
	}

    /**
    PARA LOS LISTADOS (PARA EL FORMATO DE IMPRESION Y EL DOCUMENTO DE TEXTO):
    SE VUELVE A EJECUTAR LA QUERY EN PARTES, CON LA CANTIDAD TOTAL DE EXPEDIENTES RESULTANTES SE CALCULA 
    LA CANTIDAD DE PAGINAS USANDO UN RANGO ESPECIFICO (100 POR DEFECTO)
    /**/
    public function armar_listado_para_reporte()
	{   
		$conexion = $this->conectar();
		
		// FILTRA POR Fecha Desde y Fecha Hasta POR DEFECTO 
		$filtro = "";
		
		// SE DEFINE EL ORDEN SEGÚN EL LISTADO CONSULTADO
		if ( $this->filtro['l_tipo_listado'] == "orden_del_dia" )
			$filtro .= " AND E.marca_comision <> 0";
		
		// SE FILTRA POR Estado
		if ( $this->filtro['l_estado'] != '' && $this->filtro['l_estado'] != '0' ) {
			$filtro .= " AND ( SELECT id_codestado FROM ".$this->tabla_estados."
							   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
							   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
							   LIMIT 1
							 ) IN (".$this->filtro['l_estado'].")
					   ";
		}
		
		// POR Fecha Comisión PARA Expedientes en Comisión y Detalle de Giros
		$por_fecha_comision = "";
		if ( !empty($this->filtro['l_fecha_comision']) )
			$por_fecha_comision = " AND fecha_entrada_giro <= '".$this->filtro['l_fecha_comision']."'";
		
		// 21/06/2012	PARA EL LISTADO DE INFORMES
		$filtro_informes_A = "";
		$filtro_informes_B = "";
		$filtro_informes_C = "";
		$filtro_informes_D = "";
		
		if ( $this->filtro['l_tipo_listado'] == "informes" ) {
			$filtro_informes_A = ", I.fecha_pedido_informe, I.detalle_informe";
			
			$filtro_informes_B = " INNER JOIN ".$this->tabla_informes." AS I
								   ON I.anio = E.anio AND I.tipo = E.tipo AND I.numero = E.numero AND I.cuerpo = E.cuerpo AND I.alcance = E.alcance
								 ";
			$filtro_informes_C = " AND I.fecha_vuelta_informe IS NULL";
			
			$filtro_informes_D = " AND orden_giro = I.orden_giro";		 
		}
		
		// SE FILTRA POR Comisión:
		if ( !empty($this->filtro['l_comision_codigo']) ) {
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
							   ".$por_fecha_comision."
							   ".$filtro_informes_D."	  
							   AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
							   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC LIMIT 1
							 ) IN ('".$this->filtro['l_comision_codigo']."')
					   ";
		} else {
			// SI SE DESEA FILTRAR UTILIZANDO UN LISTADO DE COMISIONES
			if ( ( $this->filtro['l_tipo_listado'] == 'exped_en_comision' ) && ( $this->filtro['l_comisiones_modal'] ) ) {
				$listado_comisiones = "(";
				
				$cant_listado_comisiones = count($this->filtro['l_comisiones_modal']);
				for ($i=0; $i < $cant_listado_comisiones; $i++) {
					$codigo_comision = &$this->filtro['l_comisiones_modal'][$i];
					
					$listado_comisiones .= "'".$codigo_comision."'";
					
					$anteultimo = $cant_listado_comisiones-1;
					if ( $i != $anteultimo )
						$listado_comisiones .= ", ";
				}
				
				$listado_comisiones .= ")";
				
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
								   ".$por_fecha_comision."
								   ".$filtro_informes_D."
								   AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
								   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC LIMIT 1
								 ) IN ".$listado_comisiones."
						   ";
			} else {
				// SI NO SE FILTRA POR COMISION NI POR ESTADO
				if ( empty($this->filtro['l_estado']) ) {
					// AGREGADO EL 02/08/2012
					if ( $this->filtro['l_tipo_listado'] != "asuntos_entrados" ) {
						$filtro .= " AND ( SELECT id_codestado FROM ".$this->tabla_estados."
										   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
										   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
										   LIMIT 1
										 ) IN ((SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 3),
											   (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 16),
											   (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 79)
											  )
									 AND ( SELECT orden_giro FROM ".$this->tabla_giros."
										   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance 
										   AND fecha_entrada_giro > '0000-00-00'
										   ".$por_fecha_comision."
										   ".$filtro_informes_D."
										   AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
										   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC LIMIT 1
										 ) > 0 
								   ";
					}			   
				}
			}			   		 	
		}
		
		// SE DEFINE EL ORDEN SEGÚN EL LISTADO CONSULTADO
		if ( $this->filtro['l_tipo_listado'] == "orden_del_dia" )
		   $orden = "ORDER BY E.marca_comision, E.anio, E.tipo, E.numero, E.cuerpo, E.alcance";
		else
		   $orden = "ORDER BY E.anio, E.tipo, E.numero, E.cuerpo, E.alcance";
		/***************************************************************************************************
			  QUERY GENERAL:
		***************************************************************************************************/
		// SQL_CALC_FOUND_ROWS: calcula el número de resultados de la query sin el LIMIT
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM 
					(SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision,
							(SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria) AS categoria, 
							(SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = E.iniciador_tipo AND Ini.codigo_grp = E.iniciador_codigo) AS iniciador
							".$filtro_informes_A."
					 FROM ".$this->tabla_expedientes." AS E 
					 ".$filtro_informes_B."
					 WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['l_fecha_desde']."' AND '".$this->filtro['l_fecha_hasta']."'
					 ".$filtro."
					 ".$filtro_informes_C."
					 ".$orden."
					)AS AUX
			   ";

		// SE AGREGA EL LIMITE A LA QUERY	
		$limite_inicial = " LIMIT 0, 100";

		$sql_auxiliar  = $sql;// SE UTILIZA UNA AUXILIAR PARA CALCULAR EL TOTAL DE REGISTROS	  
		$sql_auxiliar .= $limite_inicial; 	   
		
		// SE EJECUTA LA QUERY PARA LOS PRIMEROS 100 REGISTROS	
		$this->ejecutarQuery($sql_auxiliar);

		// SE AVERIGUA EL NUMERO TOTAL DE EXPEDIENTES CON FOUND_ROWS 
		// (obtiene el resultado del último SQL_CALC_FOUND_ROWS ejecutado sin la claúsula LIMIT)
		$resultadoTotal = $this->ejecutarQuery("SELECT FOUND_ROWS() AS total");

		$dato = $this->obtenerFila($resultadoTotal); 
		
		// SE GUARDA LA CANTIDAD TOTAL DE EXPEDIENTES
		$cantidad_TOTAL = $dato["total"];
		
		$rango = 100;
		$listado_para_reporte = Array();

		// SE EJECUTA LA QUERY CADA CIEN REGISTROS, MIENTRAS EXISTAN EXPEDIENTES/NOTAS
		$corte = false;	
		$r = 0;
		while ( $r < $cantidad_TOTAL && !$corte ) {
			// SE CALCULA EL INICIO DEL LIMITE A PEDIR
			$inicio = $r;
			
			// SE DEFINE LA QUERY AUXILIAR PARA CADA CICLO
			$sql_auxiliar = $sql;
			
			// SE DEFINE EL LIMITE
			$limite_para_documento = " LIMIT ".$inicio.", ".$rango;
			
			// SE AGREGA EL LIMITE A LA QUERY AUXILIAR
			$sql_auxiliar .= $limite_para_documento;
				
			// SE EJECUTA LA QUERY PARCIAL PARA EL REPORTE	
			$resultado_parcial = $this->ejecutarQuery($sql_auxiliar);
			
			// SE LIMPIA PARA ASIGNARLE EL NUEVO LIMITE EN EL SIGUIENTE CICLO
			$sql_auxiliar = null;
			
			$total_devueltos = $this->obtenerNumeroFilas($resultado_parcial);
			// SI DEVUELVE ALGUN REGISTRO
			if ( $total_devueltos != 0 ) {
				// SE UTILIZA UN VECTOR AUXILIAR PARA EL RESULTADO PARCIAL
				$vector_auxiliar = $this->crearVector($resultado_parcial);
				
				$cant_auxiliar = count($vector_auxiliar);
				
				// SI SE LLEGÓ AL ÚLTIMO CICLO DEL RECORRIDO
				if ( $cant_auxiliar < $rango )
					$corte = 1;// PARA CORTAR EL CICLO DEL WHILE
				
				for ($a=0; $a < $cant_auxiliar; $a++) {
					$auxiliar = &$vector_auxiliar[$a];
					
					//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
					$auxiliar['temas']     = $this->obtenerTemasFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);
					//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
					$auxiliar['autores']   = $this->obtenerAutoresFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);
					//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE
					$auxiliar['proyectos'] = $this->obtenerProyectosFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);				
					
					// SE VA CARGANDO UN VECTOR AUXILIAR CON LOS REGISTROS DEVUELTOS
					$listado_aux_para_reporte[$a] = $auxiliar;
				}
				
				// SE UNEN LOS RESULTADOS PARCIALES
				$listado_para_reporte = array_merge($listado_para_reporte, $listado_aux_para_reporte);
				
				$listado_aux_para_reporte = null;
				
				// SE LIBERA LA MEMORIA USADA POR LA QUERY
				$this->liberarMemoria($resultado_parcial);
			} else
				return false;

			// SE INCREMENTA EL CONTADOR DE A 100, QUE ES EL VALOR DEL RANGO
			$r += $rango;
		}
		
		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);
		
		return $listado_para_reporte;
    }
	    
    public function obtenerGiros($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT G.*, L.descripcion_grp 
				FROM ".$this->tabla_giros." G 
				LEFT JOIN ".$this->tabla_lugares." L 
				ON G.comision_tipo = L.tipo_grp AND G.comision_codigo = L.codigo_grp
				WHERE G.anio = ".$anio."
				AND G.tipo = '".$tipo."'
				AND G.numero = ".$numero."
				AND G.cuerpo = ".$cuerpo."
				AND G.alcance = ".$alcance."
			   ";
	    $resultado = $this->ejecutarQuery($sql);

	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
    }
    
    public function obtenerIniciadores()
	{
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT * FROM ".$this->tabla_lugares." WHERE habilitado_grp = 1";
	      
	    $resultado = $this->ejecutarQuery($sql);
	
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
    }
    
    public function obtenerAutores()
	{
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT tipo_grp AS autor_tipo, codigo_grp AS autor_codigo, descripcion_grp AS autor_descripcion 
			    FROM ".$this->tabla_lugares." 
			    WHERE habilitado_grp = 1
			   ";
	      
	    $resultado = $this->ejecutarQuery($sql);
	
	    $datos = $this->crearVector($resultado);
	    
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
	    $resultado = $this->ejecutarQuery($sql);
	   
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
    }
    
    public function obtenerCategorias()
	{
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT * FROM ".$this->tabla_codcategoria." WHERE habilitado_categoria = 1";
	      
	    $resultado = $this->ejecutarQuery($sql);
	    
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
    }
    
    public function obtenerTemas()
	{
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT * FROM ".$this->tabla_codtemas." WHERE habilitado_tema = 1";
	      
	    $resultado = $this->ejecutarQuery($sql);

	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
    }
    
    public function obtenerEstados($habilitado = 1)
	{
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT * FROM ".$this->tabla_codestados."
				WHERE habilitado_codestado = ".$habilitado."
			   ";
	      
	    $resultado = $this->ejecutarQuery($sql);
	   
	    $datos = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $datos;
    }
    
    public function obtenerProyectosFicha($anio, $tipo, $numero, $cuerpo, $alcance)
	{
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT DISTINCT CP.codigo_proyecto, CP.descripcion_proyecto, P.orden_proyecto, P.extracto 
				FROM (SELECT * FROM ".$this->tabla_proyectos."
					  WHERE anio = ".$anio."
					  AND tipo = '".$tipo."'
					  AND numero = ".$numero."
					  AND cuerpo = ".$cuerpo."
					  AND alcance = ".$alcance."
					 )P
				LEFT JOIN ".$this->tabla_codproyectos." CP ON P.id_codproyecto = CP.id_codproyecto 
				LEFT JOIN ".$this->tabla_expedientes." E ON ( E.anio = P.anio AND E.tipo = P.tipo AND E.numero = P.numero AND E.cuerpo = P.cuerpo AND E.alcance = P.alcance )
			   ";
	    
	    $resultado = $this->ejecutarQuery($sql);

	    $datos = $this->crearVector($resultado);
	    
	    //$this->desconectar($conexion);
	    
	    return $datos;
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
	    
	    $resultado = $this->ejecutarQuery($sql);
	    
	    $datos = $this->crearVector($resultado);
	  
	    //$this->desconectar($conexion);
	    
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
	     
	    $resultado = $this->ejecutarQuery($sql);
	    
	    $datos = $this->crearVector($resultado);
			    
	    //$this->desconectar($conexion);
	    
	    return $datos;
    }
    
    public function obtenerEstadoFicha($anio, $tipo, $numero, $cuerpo, $alcance, $id_codestado = 0)
	{
	    $conexion = $this->conectar();
	    
	    $filtro_id_codestado = "";
	    if ( $id_codestado != 0 )
	    { 
			$filtro_id_codestado = " AND id_codestado = ".$id_codestado." "; 
	    }
	    
	    $sql = "SELECT Est.id_codestado, Est.fecha_estado AS fecha_estado, Est.observaciones_estado AS observaciones_estado,
					   CEst.nombre_estado AS nombre_estado
				FROM ( SELECT * FROM ".$this->tabla_estados."
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
	    
	    $resultado = $this->ejecutarQuery($sql);
	    
	    $datos = $this->crearVector($resultado);
	    
	    //$this->desconectar($conexion);

	    return $datos;
    }
    
    public function obtenerComisionFicha($anio, $tipo, $numero, $cuerpo, $alcance)
	{
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT L.descripcion_grp AS comision, G.fecha_entrada_giro AS fecha_giro, G.orden_giro AS orden_giro 
				FROM (SELECT * FROM ".$this->tabla_giros."
					  WHERE anio = ".$anio."
					  AND tipo = '".$tipo."'
					  AND numero = ".$numero."
					  AND cuerpo = ".$cuerpo."
					  AND alcance = ".$alcance."
					  AND fecha_entrada_giro > '0000-00-00'
					  AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
					  ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC 
					  LIMIT 1
					 ) G
				LEFT JOIN ".$this->tabla_lugares." L 
				ON (L.tipo_grp = G.comision_tipo AND L.codigo_grp = G.comision_codigo)
			   ";
	    
	    $resultado = $this->ejecutarQuery($sql);

	    $datos = $this->crearVector($resultado);
	    
	    //$this->desconectar($conexion);
	    
	    return $datos;
    }
    
    public function obtenerCategoria($id_codcategoria)
	{
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT descripcion_categoria
			    FROM ".$this->tabla_codcategoria."
			    WHERE id_codcategoria = ".$id_codcategoria."
			   ";
			   
	    $resultado = $this->ejecutarQuery($sql);
	    
	    if (!$resultado)
		    return false;
	    else
		    $dato = $this->obtenerFila($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $dato['descripcion_categoria'];	
    }
    
    public function obtenerIniciador($iniciador_tipo, $iniciador_codigo)
	{
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT descripcion_grp
			    FROM ".$this->tabla_lugares."
			    WHERE tipo_grp = '".$iniciador_tipo."'
			    AND codigo_grp = '".$iniciador_codigo."'
			";
			
	    $resultado = $this->ejecutarQuery($sql);
	    
	    if (!$resultado)
		    return false;
	    else
		    $dato = $this->obtenerFila($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $dato['descripcion_grp'];	
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

		$resultado = $this->ejecutarQuery($sql);

	    $dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['descripcion_grp'];
    }
    
    public function obtenerNombreComision($anio, $tipo, $numero, $cuerpo, $alcance, $comision_tipo, $comision_codigo)
	{    
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT L.descripcion_grp, G.fecha_entrada_giro
				FROM ".$this->tabla_lugares." AS L
				INNER JOIN
				(SELECT comision_tipo, comision_codigo, fecha_entrada_giro 
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
				
	    $resultado = $this->ejecutarQuery($sql);
	    
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
	      
	    $resultado = $this->ejecutarQuery($sql);
	   
	    $valor_marca = $this->crearVector($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $valor_marca[0]['marca_comision'];
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
	    
	    $resultado = $this->ejecutarQuery($sql);
	    
	    if (!$resultado)
		    return false;
	    else
		    $datos = $this->crearVector($resultado);
		    
	    $this->desconectar($conexion);
	    
	    return $datos;	
    }
    
    public function obtenerNombreEstado($id_codestado)
	{    
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT nombre_estado
				FROM ".$this->tabla_codestados."
				WHERE id_codestado = ".$id_codestado." 
			   "; 
			    
	    $resultado = $this->ejecutarQuery($sql);
	    
	    $dato = $this->obtenerFila($resultado);
	    
	    $this->desconectar($conexion);
	    
	    return $dato['nombre_estado'];
    }
    
    // PARA Expedientes en Comision
    public function obtenerUltimaComision($anio, $tipo, $numero, $cuerpo, $alcance)
	{    
	    $conexion = $this->conectar();
	    
	    $sql = "SELECT comision_codigo
				FROM ".$this->tabla_giros."
				WHERE anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero."
				AND cuerpo = ".$cuerpo."
				AND alcance = ".$alcance."
				AND fecha_entrada_giro > '0000-00-00'
				AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
				ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC 
				LIMIT 1
			   ";
	    
	    $resultado = $this->ejecutarQuery($sql);

	    $dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['comision_codigo'];
    }
    	
    public function listar_expedientes_en_comision()
	{    
		$conexion = $this->conectar();

		// FILTRA POR Fecha Desde y Fecha Hasta POR DEFECTO
		$filtro = "";
		
		// POR Fecha Comisión
		$por_fecha_comision = "";
		if ( !empty($this->filtro['l_fecha_comision']) )
		{
			$por_fecha_comision = " AND fecha_entrada_giro <= '".$this->filtro['l_fecha_comision']."'";
		}

		if ( !empty($this->filtro['l_estado']) )
		{
			$filtro .= " AND ( SELECT id_codestado FROM ".$this->tabla_estados."
							   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
							   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
							   LIMIT 1
							 ) IN (".$this->filtro['l_estado'].")
					   ";
		}
		
		// SI SE FILTRA POR UNA Comision:
		if ( $this->filtro['l_comision_codigo'] != '' )
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
							   ".$por_fecha_comision."
							   AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
							   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC LIMIT 1
							 ) IN ('".$this->filtro['l_comision_codigo']."')
					   ";
		}
		else
		{
			// SI SE DESEA FILTRAR UTILIZANDO UN LISTADO DE COMISIONES
			if ( $this->filtro['l_comisiones_modal'] )
			{
				$listado_comisiones = "(";
				
				$cant_listado_comisiones = count($this->filtro['l_comisiones_modal']);
				for ($i=0; $i < $cant_listado_comisiones; $i++)
				{
					$codigo_comision = &$this->filtro['l_comisiones_modal'][$i];
					
					$listado_comisiones .= "'".$codigo_comision."'";
					
					$anteultimo = $cant_listado_comisiones-1;
					if ( $i != $anteultimo )
					{
						$listado_comisiones .= ", ";
					}
				}
				
				$listado_comisiones .= ")";
				
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
								   ".$por_fecha_comision."
								   AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
								   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC LIMIT 1
								 ) IN ".$listado_comisiones."
						   ";
			}
			else
			{
				// SI NO SE FILTRA POR COMISION NI POR ESTADO
				if ( $this->filtro['l_estado'] == '' )
				{
					// AGREGADO EL 02/08/2012
					$filtro .= " AND ( SELECT id_codestado FROM ".$this->tabla_estados."
									   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
									   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
									   LIMIT 1
									 ) IN ((SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 3),
										   (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 16),
										   (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 79)
										  )
								 AND ( SELECT orden_giro FROM ".$this->tabla_giros."
									   WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance 
									   AND fecha_entrada_giro > '0000-00-00'
									   ".$por_fecha_comision."
									   AND (fecha_salida_giro IS NULL OR fecha_salida_giro = '0000-00-00')
									   ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, orden_giro DESC LIMIT 1
									 ) > 0
							   ";
				}
			}
		}
		
		/***************************************************************************************************
			  QUERY GENERAL:
		***************************************************************************************************/
		// SQL_CALC_FOUND_ROWS: calcula el número de resultados de la query sin el LIMIT
		$sql = "SELECT SQL_CALC_FOUND_ROWS * FROM 
					(SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision,
							(SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria)AS categoria, 
							(SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = iniciador_tipo AND Ini.codigo_grp = iniciador_codigo)AS iniciador
					 FROM ".$this->tabla_expedientes." AS E 
					 WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['l_fecha_desde']."' AND '".$this->filtro['l_fecha_hasta']."'
					 ".$filtro."
					 ORDER BY E.anio, E.tipo, E.numero, E.cuerpo, E.alcance
					)AS AUX
			   ";
		
		// SI SE DESEAN VER TODOS
		if ( $this->filtro['l_vencidos'] != 1 )
		{
			// SE AGREGA EL LIMITE A LA QUERY
			$limite = "LIMIT ".$this->filtro['l_inicio'].", ".$this->filtro['l_rango']."";
			$sql .= $limite;	
		}
		
		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);
		
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
			
			$dato = $this->obtenerFila($resultadoTotal);
			// SE GUARDA LA CANTIDAD TOTAL DE EXPEDIENTES
			$_SESSION['total'] = $dato["total"];
		}
		else
		{
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
	 * 21/05/2013 PARA EL LISTADO DE Expedientes en Préstamo
	 */ 
    public function listar_exped_en_prestamo()
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS * 
				FROM ( SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision,
					   (SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria)AS categoria, 
					   (SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = iniciador_tipo AND Ini.codigo_grp = iniciador_codigo)AS iniciador
					   FROM ".$this->tabla_expedientes." AS E 
					   WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['l_fecha_desde']."' AND '".$this->filtro['l_fecha_hasta']."'
					   AND ( SELECT id_codestado FROM ".$this->tabla_estados."
						     WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
						     ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
						     LIMIT 1
						   ) IN (".$this->filtro['l_estado'].")
					   AND ( SELECT observaciones_estado FROM ".$this->tabla_estados."
						     WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
						     ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
						     LIMIT 1
						   ) LIKE '%".addslashes($this->filtro['l_observacion_estado'])."%'
					   ORDER BY E.anio, E.tipo, E.numero, E.cuerpo, E.alcance
					 )AS AUX
			 ";

		// SE AGREGA EL LIMITE A LA QUERY
		$limite = " LIMIT ".$this->filtro['l_inicio'].", ".$this->filtro['l_rango']."";
		$sql .= $limite;
		
		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);
		
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

			$dato = $this->obtenerFila($resultadoTotal);
			// SE GUARDA LA CANTIDAD TOTAL DE EXPEDIENTES
			$_SESSION['total'] = $dato["total"];
		}
		else
		{
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
	 * PARA IMPRIMIR Y PROCESAR TEXTO DE Expedientes en Préstamo
	 * @return boolean|multitype:
	 */
	public function armar_listado_para_reporte_exped_en_prestamo()
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS * 
				FROM ( SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision,
					   (SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria)AS categoria, 
					   (SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = iniciador_tipo AND Ini.codigo_grp = iniciador_codigo)AS iniciador
					   FROM ".$this->tabla_expedientes." AS E 
					   WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['l_fecha_desde']."' AND '".$this->filtro['l_fecha_hasta']."'
					   AND ( SELECT id_codestado FROM ".$this->tabla_estados."
						     WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
						     ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
						     LIMIT 1
						   ) IN (".$this->filtro['l_estado'].")
					   AND ( SELECT observaciones_estado FROM ".$this->tabla_estados."
						     WHERE anio = E.anio AND tipo = E.tipo AND numero = E.numero AND cuerpo = E.cuerpo AND alcance = E.alcance
						     ORDER BY anio DESC, tipo DESC, numero DESC, cuerpo DESC, alcance DESC, fecha_estado DESC, orden_estado DESC
						     LIMIT 1
						   ) LIKE '%".addslashes($this->filtro['l_observacion_estado'])."%'
					   ORDER BY E.anio, E.tipo, E.numero, E.cuerpo, E.alcance
					 )AS AUX
			   ";
		
		// SE AGREGA EL LIMITE A LA QUERY	
		$limite_inicial = " LIMIT 0, 100";
		$sql_auxiliar = $sql;// SE UTILIZA UNA AUXILIAR PARA CALCULAR EL TOTAL DE REGISTROS	  
		$sql_auxiliar .= $limite_inicial; 	   
		
		// SE EJECUTA LA QUERY PARA LOS PRIMEROS 100 REGISTROS	
		$this->ejecutarQuery($sql_auxiliar);

		// SE AVERIGUA EL NUMERO TOTAL DE EXPEDIENTES CON FOUND_ROWS (obtiene el resultado del último SQL_CALC_FOUND_ROWS ejecutado sin LIMIT)
		$resultadoTotal = $this->ejecutarQuery("SELECT FOUND_ROWS() AS total");
		
		$dato = $this->obtenerFila($resultadoTotal);
		// SE GUARDA LA CANTIDAD TOTAL DE EXPEDIENTES
		$cantidad_TOTAL = $dato["total"];
		
		$rango = 100;
		$listado_para_reporte = Array();

		// SE EJECUTA LA QUERY CADA CIEN REGISTROS, MIENTRAS EXISTAN EXPEDIENTES/NOTAS
		$corte = false;	
		$r = 0;
		while ( $r < $cantidad_TOTAL && !$corte )
		{
			// SE CALCULA EL INICIO DEL LIMITE A PEDIR
			$inicio = $r;
			
			// SE DEFINE LA QUERY AUXILIAR PARA CADA CICLO
			$sql_auxiliar = $sql;
			
			// SE DEFINE EL LIMITE
			$limite_para_documento = " LIMIT ".$inicio.", ".$rango."";
			
			// SE AGREGA EL LIMITE A LA QUERY AUXILIAR
			$sql_auxiliar .= $limite_para_documento;
				
			// SE EJECUTA LA QUERY PARCIAL PARA EL REPORTE	
			$resultado_parcial = $this->ejecutarQuery($sql_auxiliar);
			
			// SE LIMPIA PARA ASIGNARLE EL NUEVO LIMITE EN EL SIGUIENTE CICLO
			$sql_auxiliar = null;
			
			$total_devueltos = $this->obtenerNumeroFilas($resultado_parcial);
			// SI DEVUELVE ALGUN REGISTRO
			if ( $total_devueltos != 0 )
			{
				// SE UTILIZA UN VECTOR AUXILIAR PARA EL RESULTADO PARCIAL
				$vector_auxiliar = $this->crearVector($resultado_parcial);
				
				$cant_auxiliar = count($vector_auxiliar);
				
				// SI SE LLEGÓ AL ÚLTIMO CICLO DEL RECORRIDO
				if ( $cant_auxiliar < $rango )
				{
					$corte = 1;// PARA CORTAR EL CICLO DEL WHILE
				}
				
				for ($a=0; $a < $cant_auxiliar; $a++)
				{
					$auxiliar = &$vector_auxiliar[$a];
					
					//SE OBTIENEN LOS Temas DEL Expediente RESULTANTE
					$auxiliar['temas']     = $this->obtenerTemasFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);
					
					//SE OBTIENEN LOS Autores DEL Expediente RESULTANTE
					$auxiliar['autores']   = $this->obtenerAutoresFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);
					
					//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE
					$auxiliar['proyectos'] = $this->obtenerProyectosFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);				
					
					// SE VA CARGANDO UN VECTOR AUXILIAR CON LOS REGISTROS DEVUELTOS
					$listado_aux_para_reporte[$a] = $auxiliar;
				}
				
				// SE UNEN LOS RESULTADOS PARCIALES
				$listado_para_reporte = array_merge($listado_para_reporte, $listado_aux_para_reporte);
				
				$listado_aux_para_reporte = null;
				
				// SE LIBERA LA MEMORIA USADA POR LA QUERY
				$this->liberarMemoria($resultado_parcial);
			}
			else
			{
				return false;
			}
			// SE INCREMENTA EL CONTADOR DE A 100, QUE ES EL VALOR DEL RANGO
			$r += $rango;
		}
		
		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);
		
		return $listado_para_reporte;
    }
	
	// 20/04/2015, SE OBTIENEN DATOS DEL ULTIMO INFORME DE UN GIRO DETERMINADO
	public function obtenerUltimoInforme($filtro)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT *
				FROM ".$this->tabla_informes."
				WHERE anio = ".$filtro['anio']."
				AND tipo = '".$filtro['tipo']."'
				AND numero = ".$filtro['numero']."
				AND orden_giro = '".$filtro['orden_giro']."'
				AND orden_informe = ( SELECT MAX(orden_informe)
									  FROM ".$this->tabla_informes."
									  WHERE anio = ".$filtro['anio']."
									  AND tipo = '".$filtro['tipo']."'
									  AND numero = ".$filtro['numero']."
									  AND orden_giro = '".$filtro['orden_giro']."'
									  AND fecha_vuelta_informe IS NULL
									)
				AND fecha_vuelta_informe IS NULL
			   ";
		   
		$resultado = $this->ejecutarQuery($sql);
				
	    $datos = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
	}
	
	public function armar_listado_exped_sin_cargar()
    {
		$conexion = $this->conectar();
		
		/***************************************************************************************************
			  QUERY GENERAL:
		***************************************************************************************************/
		// SQL_CALC_FOUND_ROWS: calcula el número de resultados de la query sin el LIMIT
		$sql = "SELECT SQL_CALC_FOUND_ROWS * 
				FROM ( SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision,
							  (SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria)AS categoria, 
							  (SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = iniciador_tipo AND Ini.codigo_grp = iniciador_codigo)AS iniciador
					   FROM ".$this->tabla_expedientes." E 
					   WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['l_fecha_desde']."' AND '".$this->filtro['l_fecha_hasta']."'
					   AND E.tipo = 'E'
					   ORDER BY E.anio ASC, E.tipo ASC, E.numero ASC, E.cuerpo ASC, E.alcance ASC
					 )AS AUX
			   ";
		
		// SE AGREGA EL LIMITE A LA QUERY
		$limite_inicial = " LIMIT 0, 100";
		$sql_auxiliar = $sql;// SE UTILIZA UNA AUXILIAR PARA CALCULAR EL TOTAL DE REGISTROS	  
		$sql_auxiliar .= $limite_inicial; 	   
		
		// SE EJECUTA LA QUERY PARA LOS PRIMEROS 100 REGISTROS	
		$this->ejecutarQuery($sql_auxiliar);

		// SE AVERIGUA EL NUMERO TOTAL DE EXPEDIENTES CON FOUND_ROWS (obtiene el resultado del último SQL_CALC_FOUND_ROWS ejecutado sin LIMIT)
		$resultadoTotal = $this->ejecutarQuery("SELECT FOUND_ROWS() AS total"); 
		
		$dato = $this->obtenerFila($resultadoTotal);
		// SE GUARDA LA CANTIDAD TOTAL DE EXPEDIENTES
		$cantidad_TOTAL = $dato["total"];
		
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
			
			// SE EJECUTA LA QUERY PARCIAL 	
			$resultado_parcial = $this->ejecutarQuery($sql_auxiliar);

			// SE LIMPIA PARA ASIGNARLE EL NUEVO LIMITE EN EL SIGUIENTE CICLO
			$sql_auxiliar = null;
			
			$total_devueltos = $this->obtenerNumeroFilas($resultado_parcial);
			// SI DEVUELVE ALGUN REGISTRO
			if ( $total_devueltos != 0 )
			{
				// SE UTILIZA UN VECTOR AUXILIAR 
				$vector_auxiliar = $this->crearVector($resultado_parcial);
				
				$cant_auxiliar = count($vector_auxiliar);
				
				// SI SE LLEGÓ AL ÚLTIMO CICLO DEL RECORRIDO
				if ( $cant_auxiliar < $rango )
				{
					$corte = 1;// PARA CORTAR EL CICLO DEL WHILE
				}
				
				for ($a=0; $a < $cant_auxiliar; $a++)
				{
					$auxiliar = &$vector_auxiliar[$a];
					
					//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE
					$auxiliar['proyectos'] = $this->obtenerProyectosFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);				
					
					// SE VA CARGANDO UN LISTADO AUXILIAR 
					$listado_aux_para_reporte[$a] = $auxiliar;
				}
				
				// SE UNEN LOS RESULTADOS PARCIALES
				$listado_para_reporte = array_merge($listado_para_reporte, $listado_aux_para_reporte);
				
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
		
		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);
		
		return $listado_para_reporte; 
    }

    /**
     * [armar_listado_exped_sin_digitalizar description]
     * @return [type] [description]
     */
	public function armar_listado_exped_sin_digitalizar()
    {
		$conexion = $this->conectar();
		
		/***************************************************************************************************
			  QUERY GENERAL:
		***************************************************************************************************/
		// SQL_CALC_FOUND_ROWS: calcula el número de resultados de la query sin el LIMIT
		$sql = "SELECT SQL_CALC_FOUND_ROWS * 
				FROM ( SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.fecha_entrada_expe, E.id_codcategoria, E.iniciador_tipo, E.iniciador_codigo, E.marca_comision,
							  (SELECT CCat.descripcion_categoria FROM ".$this->tabla_codcategoria." CCat WHERE CCat.codigo_categoria = E.id_codcategoria)AS categoria, 
							  (SELECT Ini.descripcion_grp FROM ".$this->tabla_lugares." Ini WHERE Ini.tipo_grp = iniciador_tipo AND Ini.codigo_grp = iniciador_codigo)AS iniciador
					   FROM ".$this->tabla_expedientes." E 
					   WHERE E.fecha_entrada_expe BETWEEN '".$this->filtro['l_fecha_desde']."' AND '".$this->filtro['l_fecha_hasta']."'
					   ORDER BY E.anio ASC, E.tipo ASC, E.numero ASC, E.cuerpo ASC, E.alcance ASC
					 )AS AUX
			   ";
		
		// SE AGREGA EL LIMITE A LA QUERY
		$limite_inicial = " LIMIT 0, 100";
		$sql_auxiliar = $sql;// SE UTILIZA UNA AUXILIAR PARA CALCULAR EL TOTAL DE REGISTROS	  
		$sql_auxiliar .= $limite_inicial; 	   
		
		// SE EJECUTA LA QUERY PARA LOS PRIMEROS 100 REGISTROS	
		$this->ejecutarQuery($sql_auxiliar);

		// SE AVERIGUA EL NUMERO TOTAL DE EXPEDIENTES CON FOUND_ROWS (obtiene el resultado del último SQL_CALC_FOUND_ROWS ejecutado sin LIMIT)
		$resultadoTotal = $this->ejecutarQuery("SELECT FOUND_ROWS() AS total"); 
		
		$dato = $this->obtenerFila($resultadoTotal);
		// SE GUARDA LA CANTIDAD TOTAL DE EXPEDIENTES
		$cantidad_TOTAL = $dato["total"];
		
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
			
			// SE EJECUTA LA QUERY PARCIAL 	
			$resultado_parcial = $this->ejecutarQuery($sql_auxiliar);

			// SE LIMPIA PARA ASIGNARLE EL NUEVO LIMITE EN EL SIGUIENTE CICLO
			$sql_auxiliar = null;
			
			$total_devueltos = $this->obtenerNumeroFilas($resultado_parcial);
			// SI DEVUELVE ALGUN REGISTRO
			if ( $total_devueltos != 0 )
			{
				// SE UTILIZA UN VECTOR AUXILIAR 
				$vector_auxiliar = $this->crearVector($resultado_parcial);
				
				$cant_auxiliar = count($vector_auxiliar);
				
				// SI SE LLEGÓ AL ÚLTIMO CICLO DEL RECORRIDO
				if ( $cant_auxiliar < $rango )
				{
					$corte = 1;// PARA CORTAR EL CICLO DEL WHILE
				}
				
				for ($a=0; $a < $cant_auxiliar; $a++)
				{
					$auxiliar = &$vector_auxiliar[$a];
					
					//SE OBTIENEN LOS Proyectos DEL Expediente RESULTANTE
					$auxiliar['proyectos'] = $this->obtenerProyectosFicha($auxiliar['anio'], $auxiliar['tipo'], $auxiliar['numero'], $auxiliar['cuerpo'], $auxiliar['alcance']);				
					
					// SE VA CARGANDO UN LISTADO AUXILIAR 
					$listado_aux_para_reporte[$a] = $auxiliar;
				}
				
				// SE UNEN LOS RESULTADOS PARCIALES
				$listado_para_reporte = array_merge($listado_para_reporte, $listado_aux_para_reporte);
				
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
		
		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);
		
		return $listado_para_reporte; 
    }
}
?>