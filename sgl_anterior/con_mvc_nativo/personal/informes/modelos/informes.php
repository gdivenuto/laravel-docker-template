<?php
// Script de control de variables de sesion
require_once $_SERVER['DOCUMENT_ROOT'] . '/sgl/librerias/control_sesion.php';

class informesModel extends ModeloBaseMySQLi {

	public function conectar() {
		// Se conecta según el Id del sistema
		return parent::conectarDB(3);
	}

	/**
	 * Se listan los legajos en base a un criterio de búsqueda determinado:
	 *
	 * - En una fecha específica
	 * - Por fecha de baja
	 * - Por un rango de fechas
	 * - Por Area
	 * - Por Cargo
	 * - Por Concejal
	 *
	 * @param integer $con_limite
	 * @return boolean|Ambigous <NULL, array:>
	 */
	public function listar($con_limite = 0) {

		$conexion = $this->conectar();

		$filtro = "";
		$filtro_x_cargo = "";
		$filtro_x_concejal = "";

		// Si se eligió un Area
		if ($this->filtro['i_area'] != '' && $this->filtro['i_area'] != 0) {

			// Se filtra por dicha Area y por las que dependen de ella (en caso que sea una dirección)
			$filtro .= " AND p_legajo IN ( SELECT A.a_legajo
										   FROM " . $this->tabla_areas . " AS A
										   WHERE A.a_id_area = '" . $this->filtro['i_area'] . "'
										   UNION
										   SELECT A.a_legajo
										   FROM " . $this->tabla_codareas . " AS CA
										   INNER JOIN " . $this->tabla_areas . " AS A
										   ON A.a_id_area = CA.ca_id
										   WHERE ( CA.ca_depende_de = '" . $this->filtro['i_area'] . "'
										   		   OR
										   		   CA.ca_depende_de IN ( SELECT ca_id
																		 FROM " . $this->tabla_codareas . " AS CA
																		 INNER JOIN " . $this->tabla_areas . " AS A
																		 ON A.a_id_area = CA.ca_id
																		 WHERE CA.ca_depende_de = '" . $this->filtro['i_area'] . "'
																	   )
											    )
										)";
		}

		// Si se filtra por Cargo
		if ($this->filtro['i_cargo'] != '' && $this->filtro['i_cargo'] != 0) {

			$filtro_x_cargo = " AND c_nomenclador = '" . $this->filtro['i_cargo'] . "'";
		}

		// Se filtra por Concejal
		if ($this->filtro['i_concejal'] != '' && $this->filtro['i_concejal'] != 0) {

			$filtro_x_concejal = " AND c_depende_de = " . $this->filtro['i_concejal'];
		}

		// Si se filtra por una Fecha específica
		if ($this->filtro['i_a_la_fecha'] != '') {

			$filtro .= " AND p_legajo IN ( SELECT c_legajo
										   FROM " . $this->tabla_cargos . "
										   WHERE c_fecha_alta <= '" . $this->filtro['i_a_la_fecha'] . "'
										   AND ( c_fecha_baja IS NULL OR c_fecha_baja >= '" . $this->filtro['i_a_la_fecha'] . "' )
										   ".$filtro_x_cargo."
										   ".$filtro_x_concejal."
										 )";
		}

		// Si se filtra por una Fecha de Baja específica y el Decreto de Baja no sea nulo
		if ($this->filtro['i_por_fecha_de_baja'] != '') {

			$filtro .= " AND p_legajo IN ( SELECT c_legajo
										   FROM " . $this->tabla_cargos . "
										   WHERE c_fecha_baja = '" . $this->filtro['i_por_fecha_de_baja'] . "'
										   AND c_nro_decreto_baja IS NOT NULL
										   ".$filtro_x_cargo."
									       ".$filtro_x_concejal."
										 )";
		}

		// Si se filtra por un Rango de Fechas determinado
		if ($this->filtro['i_fecha_desde'] != '' && $this->filtro['i_fecha_hasta'] != '') {

			$filtro .= " AND p_legajo IN ( SELECT c_legajo
										   FROM " . $this->tabla_cargos . "
										   WHERE ( c_fecha_alta BETWEEN '" . $this->filtro['i_fecha_desde'] . "' AND '" . $this->filtro['i_fecha_hasta'] . "' )
										   AND ( c_fecha_baja IS NULL OR c_fecha_baja <= '" . $this->filtro['i_fecha_hasta'] . "' )
										   ".$filtro_x_cargo."
										   ".$filtro_x_concejal."
										 )";
		}

		// Para ordenar por Legajo o Apellido
		$orden = ($this->filtro['i_orden'] == 1) ? "ORDER BY p_legajo" : "ORDER BY p_apellido";

		// Para limitar el listado
		$limite = "";
		if ($con_limite != 0) {
			$limite = ($this->filtro['i_inicio'] != '' && $this->filtro['i_rango'] != '') ? " LIMIT " . $this->filtro['i_inicio'] . ", " . $this->filtro['i_rango'] : " LIMIT 0, " . $this->filtro['i_rango'];
		}

		$sql = "SELECT * FROM " . $this->tabla_personal . "
				WHERE p_habilitado = 1
				" . $filtro . "
				" . $orden . "
				" . $limite;

		//fputs(fopen("sql_listar_informe.txt", 'w'), print_r($sql, true));

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->liberarMemoria($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerPersonalAdministrativoBloque($id_bloque = 0, $concejal = 0) {
		$conexion = $this->conectar();

		$filtro_id_bloque = "";
		if ($id_bloque != 0) {
			$filtro_id_bloque = "AND ca_id = '" . $id_bloque . "'";
		}

		$filtro_por_concejal = "";
		if ($concejal != 0) {
			$filtro_por_concejal = "AND p_legajo IN ( SELECT c_legajo
													  FROM " . $this->tabla_cargos . "
													  WHERE c_depende_de = '" . $concejal . "'
													  AND c_fecha_baja IS NULL
													)";
		}

		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT a_legajo
									FROM " . $this->tabla_areas . "
									WHERE a_id_area IN ( SELECT ca_id
														 FROM " . $this->tabla_codareas . "
														 WHERE ca_tipo = 'B'
														 " . $filtro_id_bloque . "
													   )
								  )
				AND p_legajo NOT IN ( SELECT c_legajo
									  FROM " . $this->tabla_cargos . "
									  WHERE c_nomenclador IN ( SELECT cc_nomenclador
															   FROM " . $this->tabla_codcargos . "
														       WHERE cc_nomenclador = '" . $this->id_cargo_concejal . "'
														     )
									  AND c_fecha_baja IS NULL
								    )
				AND p_legajo IN ( SELECT c_legajo
								  FROM " . $this->tabla_cargos . "
								  WHERE c_fecha_baja IS NULL
								)
				" . $filtro_por_concejal . "
				ORDER BY p_apellido
			   ";

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
		{
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerPersonalAdministrativoParaLiquidaciones($id_area = 0) {
		$conexion = $this->conectar();

		// PARA FILTRAR POR UN AREA DETERMINADA Y TODAS LAS DEMÁS AREAS QUE DEPENDEN DE ELLA
		if (($id_area != 0) && ($id_area != '')) {
			$filtro .= " AND p_legajo IN ( SELECT A.a_legajo
										   FROM " . $this->tabla_areas . " AS A
										   WHERE A.a_id_area = '" . $id_area . "'
										   AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
											 					  FROM " . $this->tabla_areas . "
																  WHERE a_legajo = A.a_legajo
																)
										   UNION
										   SELECT A.a_legajo
										   FROM " . $this->tabla_codareas . " AS CA
										   INNER JOIN " . $this->tabla_areas . " AS A
										   ON A.a_id_area = CA.ca_id
										   WHERE ( CA.ca_depende_de = '" . $id_area . "' OR CA.ca_depende_de IN ( SELECT ca_id
																										      FROM " . $this->tabla_codareas . " AS CA
																										      INNER JOIN " . $this->tabla_areas . " AS A
																										      ON A.a_id_area = CA.ca_id
																										      WHERE CA.ca_depende_de = '" . $id_area . "'
																										      AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
																																     FROM " . $this->tabla_areas . "
																																     WHERE a_legajo = A.a_legajo
																															       )
																										    )
												 )
										   AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
																  FROM " . $this->tabla_areas . "
																  WHERE a_legajo = A.a_legajo
																)
										 )
					   ";
		}

		$sql = "SELECT *, ( SELECT c_digito
							FROM " . $this->tabla_cargos . "
							WHERE c_legajo = p_legajo
							ORDER BY c_fecha_alta DESC LIMIT 1
						  ) AS digito
				FROM " . $this->tabla_personal . "
				WHERE p_apellido IS NOT NULL
				AND p_legajo IN ( SELECT c_legajo
								  FROM " . $this->tabla_cargos . "
								  WHERE ( c_fecha_baja IS NULL OR c_fecha_baja > CURDATE() )
								)
				" . $filtro . "
				ORDER BY p_apellido
			   ";

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
		{
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerAreas() {
		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM " . $this->tabla_codareas . "
				ORDER BY ca_nombre ASC
			   ";

		// 20/01/2020 XXXX
		// Se retira para usar las Areas históricas
		// WHERE ca_habilitado = 1

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0) {
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtienen los Cargos de un determinado tipo (Bloque o Planta)
	 *
	 * @param string $tipo
	 * @return boolean|Ambigous <NULL, array:>
	 */
	public function obtenerCargos($tipo = '') {
		$conexion = $this->conectar();

		$filtro = ($tipo != '') ? " WHERE cc_tipo = '" . $tipo . "'" : "";

		$sql = "SELECT *
				FROM " . $this->tabla_codcargos . "
				" . $filtro . "
				ORDER BY cc_nombre ASC";

		// 20/01/2020 XXXX
		// Se retira para usar los Cargos históricos
		// WHERE cc_habilitado = 1

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
		{
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene el Id del área de un legajo determinado
	 * @param  [integer] $legajo Legajo
	 * @return [integer]         Id de la última área obtenida de dicho legajo
	 */
	public function obtenerIdUltimaArea($legajo) {
		$conexion = $this->conectar();

		$query = "SELECT a_id_area
				  FROM " . $this->tabla_areas . "
				  WHERE a_legajo = " . $legajo . "
				  ORDER BY a_fecha_alta DESC
				  LIMIT 1
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['a_id_area'];
	}

	public function obtenerNombreArea($legajo) {
		$conexion = $this->conectar();

		$sql = "SELECT ca_nombre AS area
				FROM " . $this->tabla_codareas . "
				WHERE ca_id IN ( SELECT a_id_area
							     FROM " . $this->tabla_areas . "
							     WHERE a_legajo = " . $legajo . "
							   )
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['area'];
	}

	public function obtenerNombreAreaSegunFecha($legajo, $fecha_elegida = '') {
		$conexion = $this->conectar();

		$sql = "SELECT ca_nombre AS area
				FROM " . $this->tabla_codareas . "
				WHERE ca_id IN ( SELECT a_id_area
							     FROM " . $this->tabla_areas . "
							     WHERE a_legajo = " . $legajo . "
							     AND a_fecha_alta <= '" . $fecha_elegida . "'
							     AND ( a_fecha_baja >= '" . $fecha_elegida . "' OR a_fecha_baja IS NULL )
							   )";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['area'];
	}

	public function obtenerNombreCargo($legajo, $fecha_elegida) {
		$conexion = $this->conectar();

		$sql = "SELECT CC.cc_nombre AS cargo, C.c_digito AS digito
				FROM " . $this->tabla_codcargos . " AS CC
				INNER JOIN
				( SELECT c_nomenclador, c_digito
				  FROM " . $this->tabla_cargos . "
				  WHERE c_legajo = " . $legajo . "
				  AND c_fecha_alta <= '" . $fecha_elegida . "'
				  AND ( c_fecha_baja >= '" . $fecha_elegida . "' OR c_fecha_baja IS NULL )
			    ) AS C
				ON CC.cc_nomenclador = C.c_nomenclador";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato;
	}

	public function obtenerNombreDependeDe($legajo, $fecha_elegida) {
		$conexion = $this->conectar();

		$sql = "SELECT p_apellido, p_nombre
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT c_depende_de
								    FROM " . $this->tabla_cargos . "
								    WHERE c_legajo = " . $legajo . "
								    AND c_fecha_alta <= '" . $fecha_elegida . "'
								    AND ( c_fecha_baja >= '" . $fecha_elegida . "' OR c_fecha_baja IS NULL )
								  )";

		$resultado = $this->ejecutarQuery($sql);

		if ($resultado) {
			$dato = $this->obtenerFila($resultado);
		} else {
			return false;
		}

		$this->desconectar($conexion);

		return $dato;
	}

	public function obtenerTipoArea($id_area) {
		$conexion = $this->conectar();

		$sql = "SELECT ca_tipo FROM " . $this->tabla_codareas . " WHERE ca_id = '" . $id_area . "'";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['ca_tipo'];
	}

	/**
	 * Devuelve las áreas según su tipo, Bloque o Planta Permanente
	 * @param string 	$tipo_area B=Bloque o P=Permanente
	 * @param integer 	$defensoria
	 * @param integer 	$solo_activas
	 * @return array|null $datos
	 */
	public function obtenerPorTipo($tipo_area, $defensoria = 0, $solo_activas = 0) {
		$conexion = $this->conectar();

		$filtro = ($defensoria == 1)
			? " WHERE ca_id = '01100000'"
			: " WHERE ca_tipo = '" . $tipo_area . "' AND ca_id <> '01100000'";

		if ($solo_activas == 1) {
			$filtro .= " AND ca_habilitado = 1";
		}

		$sql = "SELECT *
				FROM " . $this->tabla_codareas . "
				" . $filtro . "
				AND ca_depende_de <> 0
				ORDER BY ca_id
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	// DEVUELVE LOS BLOQUES
	public function obtenerBloques() {
		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM " . $this->tabla_codareas . "
				WHERE ca_id LIKE '02%'
				AND ca_habilitado = 1
				ORDER BY ca_nombre ASC
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Devuelve sólo los Bloques y las Direcciones
	 *
	 * @return Ambigous <NULL, array:>
	 */
	public function obtenerAreasParaLiquidaciones() {
		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM " . $this->tabla_codareas . "
				WHERE ca_habilitado = 1
				AND ca_id LIKE '02%'
				OR (ca_id LIKE '01%' AND SUBSTRING( ca_id, 5, 2 ) = '00' AND ca_id <> '01000000')
				ORDER BY ca_nombre ASC
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	// DEVUELVE LOS CONCEJALES QUE NO PERTENEZCAN A UN BLOQUE POLITICO
	public function obtenerConcejalesSinBloque() {
		$conexion = $this->conectar();

		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT c_legajo
									FROM " . $this->tabla_cargos . "
									WHERE c_nomenclador IN ( SELECT cc_nomenclador
															FROM " . $this->tabla_codcargos . "
															WHERE cc_nomenclador = '" . $this->id_cargo_concejal . "'
															OR cc_nomenclador = '" . $this->id_cargo_concejal_con_licencia . "'
														  )
									AND c_fecha_baja IS NULL
								  )
				AND p_legajo IN ( SELECT a_legajo
								  FROM " . $this->tabla_areas . "
								  WHERE a_id_area IN ( SELECT ca_id
													   FROM " . $this->tabla_codareas . "
													   WHERE ca_id = '" . $this->id_planta_politica . "'
													 )
								  AND a_fecha_baja IS NULL
								)
				ORDER BY p_apellido
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerNombreBloque($id_bloque) {
		$conexion = $this->conectar();

		$sql = "SELECT ca_nombre
				FROM " . $this->tabla_codareas . "
				WHERE ca_id = '" . $id_bloque . "'
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['ca_nombre'];
	}

	// DEVUELVE LOS CONCEJALES PARA LA CONSULTA GENERAL
	public function obtenerConcejalesConsultaGeneral($filtro) {
		$conexion = $this->conectar();

		$filtro_sql = "";

		// SE FILTRA HASTA UNA FECHA DETERMINADA
		if ($filtro['i_a_la_fecha'] != '') {
			$filtro_sql .= " AND c_fecha_alta <= '" . $filtro['i_a_la_fecha'] . "'
							 AND ( c_fecha_baja >= '" . $filtro['i_a_la_fecha'] . "' OR c_fecha_baja IS NULL )
					       ";
		}

		// SE FILTRA POR UNA FECHA DE BAJA DETERMINADA Y EL DECRETO DE BAJA NO SEA NULO
		if ($filtro['i_por_fecha_de_baja'] != '') {
			$filtro_sql .= " AND c_fecha_baja = '" . $filtro['i_por_fecha_de_baja'] . "'
					         AND c_nro_decreto_baja IS NOT NULL
						   ";
		}

		// SE FILTRA POR UN RANGO DE FECHA DETERMINADO
		if ($filtro['i_fecha_desde'] != '' && $filtro['i_fecha_hasta'] != '') {
			$filtro_sql .= " AND ( c_fecha_alta BETWEEN '" . $filtro['i_fecha_desde'] . "' AND '" . $filtro['i_fecha_hasta'] . "')
						     AND ( ( c_fecha_baja BETWEEN '" . $filtro['i_fecha_desde'] . "' AND '" . $filtro['i_fecha_hasta'] . "' ) OR c_fecha_baja IS NULL )
					       ";
		}

		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT c_legajo
									FROM " . $this->tabla_cargos . "
									WHERE c_nomenclador IN ( SELECT cc_nomenclador
															FROM " . $this->tabla_codcargos . "
															WHERE cc_nomenclador = '" . $this->id_cargo_concejal . "'
															OR cc_nomenclador = '" . $this->id_cargo_concejal_con_licencia . "'
														  )
									" . $filtro_sql . "
								  )
				ORDER BY p_apellido
			   ";

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
		{
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * 03/10/2019 XXXX
	 * @param  integer $id_bloque [description]
	 * @return [type]             [description]
	 */
	public function obtenerConcejalesPorBloque_ConsultaGral($id_bloque = 0) {
		$conexion = $this->conectar();

		$filtro = "";

		if ($id_bloque != 0) {
			// SE FILTRA POR BLOQUE
			$filtro .= "AND p_legajo IN ( SELECT a_legajo
										  FROM " . $this->tabla_areas . "
										  WHERE a_id_area IN ( SELECT ca_id
															   FROM " . $this->tabla_codareas . "
															   WHERE ca_id = '" . $id_bloque . "'
															 )
										)";
		}

		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT c_legajo
									FROM " . $this->tabla_cargos . "
									WHERE c_nomenclador IN ( SELECT cc_nomenclador
															FROM " . $this->tabla_codcargos . "
															WHERE cc_nomenclador = '" . $this->id_cargo_concejal . "'
															OR cc_nomenclador = '" . $this->id_cargo_concejal_con_licencia . "'
														  )
								  )
				" . $filtro . "
				ORDER BY p_apellido
			   ";

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
		{
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	// DEVUELVE LOS CONCEJALES, FILTRANDO POR BLOQUE O MES TRABAJADO
	public function obtenerConcejales($id_bloque = 0, $i_mes = 0, $i_anio = 0) {
		$conexion = $this->conectar();

		$filtro = "";

		if ($id_bloque != 0) {
			// SE FILTRA POR BLOQUE
			$filtro .= "AND p_legajo IN ( SELECT a_legajo
										  FROM " . $this->tabla_areas . "
										  WHERE a_id_area IN ( SELECT ca_id
															   FROM " . $this->tabla_codareas . "
															   WHERE ca_id = '" . $id_bloque . "'
															 )
										)
					   ";
		}

		if ($i_mes != 0 && $i_anio != 0) {
			// SI EL NUMERO DEL MES ES MENOR A 10 SE COMPLETA CON UN CERO A LA IZQUIERDA
			$i_mes = ($i_mes < 10) ? '0' . $i_mes : $i_mes;

			// DIA 1 DEL MES Y AÑO DETERMINADOS, FORMATO yyyy-mm-dd
			$inicio_mes = $i_anio . '-' . $i_mes . '-01';

			// SE FILTRA POR LOS QUE HAYAN TRABAJADO EN UN MES DETERMINADO, INCLUSIVE HABIENDOSE DADO DE BAJA
			$filtro .= "AND p_legajo IN ( SELECT a_legajo
										  FROM " . $this->tabla_areas . "
									      WHERE a_fecha_alta <= ( SELECT LAST_DAY( '" . $inicio_mes . "' ) )
									      AND ( a_fecha_baja >= '" . $inicio_mes . "' OR a_fecha_baja IS NULL )
									    )
					   ";
		}

		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT c_legajo
									FROM " . $this->tabla_cargos . "
									WHERE c_nomenclador IN ( SELECT cc_nomenclador
															FROM " . $this->tabla_codcargos . "
															WHERE cc_nomenclador = '" . $this->id_cargo_concejal . "'
															OR cc_nomenclador = '" . $this->id_cargo_concejal_con_licencia . "'
														  )
									AND  c_fecha_alta <= ( SELECT LAST_DAY( '" . $inicio_mes . "' ) )
									AND ( c_fecha_baja >= '" . $inicio_mes . "' OR c_fecha_baja IS NULL )
								  )
				" . $filtro . "
				ORDER BY p_apellido";

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
		{
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Devuelve el listado de Concejales de un Bloque determinado, en una fecha específica
	 *
	 * @param number $id_bloque
	 * @param number $i_a_la_fecha
	 * @return boolean|Ambigous <NULL, array:>
	 */
	public function obtenerConcejalesParaUnaFecha($id_bloque = 0, $i_a_la_fecha = '') {
		$conexion = $this->conectar();

		$filtro = "";

		if ($id_bloque != 0) {
			// SE FILTRA POR BLOQUE
			$filtro .= "AND p_legajo IN ( SELECT a_legajo
										  FROM " . $this->tabla_areas . "
										  WHERE a_id_area IN ( SELECT ca_id
															   FROM " . $this->tabla_codareas . "
															   WHERE ca_id = '" . $id_bloque . "'
															 )
										  AND  a_fecha_alta <= '" . $this->formatearFechaMySQL($i_a_la_fecha) . "'
					    				  AND (a_fecha_baja >= '" . $this->formatearFechaMySQL($i_a_la_fecha) . "' OR a_fecha_baja IS NULL)
										)
					   ";
		}

		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT c_legajo
									FROM " . $this->tabla_cargos . "
									WHERE c_nomenclador IN ( SELECT cc_nomenclador
															FROM " . $this->tabla_codcargos . "
															WHERE cc_nomenclador = '" . $this->id_cargo_concejal . "'
															OR cc_nomenclador = '" . $this->id_cargo_concejal_con_licencia . "'
														  )
									AND  c_fecha_alta <= '" . $this->formatearFechaMySQL($i_a_la_fecha) . "'
					    			AND (c_fecha_baja >= '" . $this->formatearFechaMySQL($i_a_la_fecha) . "' OR c_fecha_baja IS NULL)
								  )
				" . $filtro . "
				ORDER BY p_apellido";

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
		{
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	// DEVUELVE LOS CONCEJALES DE UN BLOQUE DETERMINADO
	public function obtenerConcejalesPorBloque($id_bloque, $fecha_informe, $i_orden) {
		$conexion = $this->conectar();

		$orden = ($i_orden == 1) ? "ORDER BY p_legajo" : "ORDER BY p_apellido";

		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT c_legajo
									FROM " . $this->tabla_cargos . "
									WHERE c_nomenclador IN ( SELECT cc_nomenclador
															FROM " . $this->tabla_codcargos . "
															WHERE cc_nomenclador = '" . $this->id_cargo_concejal . "'
															OR cc_nomenclador = '" . $this->id_cargo_concejal_con_licencia . "'
														  )
									AND ( c_fecha_alta <= '" . $fecha_informe . "' )
									AND ( c_fecha_baja >= '" . $fecha_informe . "' OR c_fecha_baja IS NULL )
								  )
				AND p_legajo IN ( SELECT a_legajo
								  FROM " . $this->tabla_areas . "
								  WHERE a_id_area IN ( SELECT ca_id
													   FROM " . $this->tabla_codareas . "
													   WHERE ca_id = '" . $id_bloque . "'
												     )
								  AND ( a_fecha_alta <= '" . $fecha_informe . "' )
								  AND ( a_fecha_baja >= '" . $fecha_informe . "' OR a_fecha_baja IS NULL )
								)
				" . $orden . "
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtienen los Concejales ACTIVOS
	 * @return Ambigous <NULL, array:>
	 */
	public function obtenerConcejalesActivos() {
		$conexion = $this->conectar();

		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT c_legajo
									FROM " . $this->tabla_cargos . "
									WHERE c_nomenclador = ( SELECT cc_nomenclador
															FROM " . $this->tabla_codcargos . "
															WHERE cc_nomenclador = " . $this->id_cargo_concejal . "
														  )
									AND c_fecha_alta = ( SELECT MAX( c_fecha_alta )
														 FROM " . $this->tabla_cargos . "
														 WHERE c_legajo = p_legajo
													   )
									AND c_fecha_baja IS NULL
								  )
				ORDER BY p_apellido, p_nombre
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtienen los dependientes de un Concejal determinado
	 * @param string $concejal
	 * @param string $fecha_informe
	 * @param integer $i_orden
	 * @return Ambigous <NULL, array:>
	 */
	public function obtenerDependientes($concejal, $fecha_informe, $i_orden) {
		$conexion = $this->conectar();

		$orden = ($i_orden == 1) ? "ORDER BY P.p_legajo" : "ORDER BY P.p_apellido";

		$sql = "SELECT P.p_legajo, P.p_apellido, P.p_nombre, C.c_digito, C.c_pertenece_secretaria_bloque
				FROM " . $this->tabla_personal . " AS P
				INNER JOIN
				( SELECT C.*
				  FROM " . $this->tabla_cargos . " AS C
				  WHERE C.c_depende_de = '" . $concejal . "'
				  AND C.c_fecha_alta <= '" . $fecha_informe . "'
				  AND ( C.c_fecha_baja >= '" . $fecha_informe . "' OR C.c_fecha_baja IS NULL )
				) AS C
				ON C.c_legajo = P.p_legajo
				" . $orden . "
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene la información de un Legajo determinado
	 *
	 * @param integer $legajo
	 * @return boolean|multitype:
	 */
	public function obtenerInformacionLegajo($legajo) {
		$conexion = $this->conectar();

		$sql = "SELECT P.p_legajo, P.p_apellido, P.p_nombre, P.p_nro_documento, P.p_foto,
					   C.c_nomenclador, C.c_digito, C.c_fecha_alta,
					   CC.cc_nombre
				FROM " . $this->tabla_personal . " AS P
				INNER JOIN " . $this->tabla_cargos . " AS C
					ON P.p_legajo = C.c_legajo
				AND P.p_legajo = " . $legajo . "
				AND C.c_fecha_alta = ( SELECT MAX( c_fecha_alta )
									   FROM " . $this->tabla_cargos . "
									   WHERE c_legajo = P.p_legajo
									 )
				INNER JOIN " . $this->tabla_codcargos . " AS CC
					ON CC.cc_nomenclador = C.c_nomenclador";

		$resultado = $this->ejecutarQuery($sql);

		if ($resultado) {
			$dato = $this->obtenerFila($resultado);
		} else {
			return false;
		}

		$this->desconectar($conexion);

		return $dato;
	}

	// NO UTILIZADO
	public function perteneceSecretaria($legajo) {
		$conexion = $this->conectar();

		$sql = "SELECT c_pertenece_secretaria_bloque
				FROM " . $this->tabla_cargos . "
				WHERE c_legajo = " . $legajo . "
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($dato['c_pertenece_secretaria_bloque'] == 1);
	}

	/**
	 * NUEVO 17/12/2020 XXXX
	 * Se listan sólo los Activos a una fecha determinada, con su Cargo respectivo
	 * @return [array] $datos
	 */
	public function listarParaSitioMGP() {

		$conexion = $this->conectar();

		$limite = "";
		if ($con_limite != 0) {
			//para limitar el listado
			$limite = " LIMIT 0, " . $this->filtro['i_rango'];
			if ($this->filtro['i_inicio'] != '' && $this->filtro['i_rango'] != '') {
				$limite = " LIMIT " . $this->filtro['i_inicio'] . ", " . $this->filtro['i_rango'];
			}

		}

		// 08/04/2021 XXXX CORREGIDO
		// Se agregó que la fecha de Alta en el Cargo sea la última
		// En los métodos:
		// 		obtenerPersonal()
		// 		obtenerPersonalDefensoria()
		// 		listarParaSitioMGP()
		// --------------------------------------------------------
		$sql = "SELECT *
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT a_legajo
								    FROM " . $this->tabla_areas . "
								    WHERE a_id_area LIKE '" . $prefijo_area . "%'
								    AND a_fecha_alta <= '" . $this->filtro['i_a_la_fecha'] . "'
								    AND ( a_fecha_baja >= '" . $this->filtro['i_a_la_fecha'] . "' OR a_fecha_baja IS NULL )
								  )
				AND p_legajo IN ( SELECT C.c_legajo
								  FROM " . $this->tabla_cargos . " AS C
								  WHERE C.c_fecha_alta <= '" . $this->filtro['i_a_la_fecha'] . "'

								  AND ( C.c_fecha_alta = (SELECT MAX( c_fecha_alta )
								  						  FROM " . $this->tabla_cargos . "
								  						  WHERE c_legajo = C.c_legajo)
							  			AND
							  	   	    C.c_fecha_baja IS NULL OR C.c_fecha_baja > CURDATE()
							  	   	  )

								  AND ( C.c_fecha_baja >= '" . $this->filtro['i_a_la_fecha'] . "' OR C.c_fecha_baja IS NULL )
								  AND C.c_nomenclador IN
								  	(SELECT cc_nomenclador FROM " . $this->tabla_codcargos . ")
								  AND C.c_fecha_baja IS NULL OR C.c_fecha_baja > CURDATE()
								)
				ORDER BY p_apellido, p_nombre
				" . $limite;

		//fputs(fopen("sql_listarParaSitioMGP.txt", 'w'), print_r($sql, true));

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerPersonal($prefijo_area, $con_limite = 0) {
		$conexion = $this->conectar();

		if ($prefijo_area == '02') {
			$tipo_de_cargo = 'B';
		} elseif ($prefijo_area == '01') {
			$tipo_de_cargo = 'P';
		}

		// Para mostrar sólo los Activos
		if ($this->filtro['i_solo_activos'] == 1) {

			// 08/04/2021 XXXX CORREGIDO
			// Se agregó que la fecha de Alta en el Cargo sea la última
			// En los métodos:
			// 		obtenerPersonal()
			// 		obtenerPersonalDefensoria()
			// 		listarParaSitioMGP()
			// --------------------------------------------------------
			$solo_activos = " AND (C.c_fecha_alta = (SELECT MAX( c_fecha_alta ) FROM " . $this->tabla_cargos . " WHERE c_legajo = C.c_legajo)
							  		AND
							  	   C.c_fecha_baja IS NULL OR C.c_fecha_baja > CURDATE())";
			//$solo_activos = " AND C.c_fecha_baja IS NULL OR C.c_fecha_baja > CURDATE()";
		} else {
			$solo_activos = ""; // PARA MOSTRARLOS A TODOS
		}

		$limite = "";
		if ($con_limite != 0) {
			//para limitar el listado
			$limite = " LIMIT 0, " . $this->filtro['i_rango'];
			if ($this->filtro['i_inicio'] != '' && $this->filtro['i_rango'] != '') {
				$limite = " LIMIT " . $this->filtro['i_inicio'] . ", " . $this->filtro['i_rango'];
			}

		}

		$orden = "ORDER BY p_apellido";
		if ($this->filtro['i_orden'] == '1') {
			$orden = "ORDER BY p_legajo";
		}

		$sql = "SELECT *
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT a_legajo
								    FROM " . $this->tabla_areas . "
								    WHERE a_id_area LIKE '" . $prefijo_area . "%'
								    AND a_id_area <> '01100000'
								    AND a_fecha_alta <= '" . $this->filtro['i_a_la_fecha'] . "'
								    AND ( a_fecha_baja >= '" . $this->filtro['i_a_la_fecha'] . "' OR a_fecha_baja IS NULL )
								  )
				AND p_legajo IN ( SELECT C.c_legajo
								  FROM " . $this->tabla_cargos . " AS C
								  WHERE C.c_fecha_alta <= '" . $this->filtro['i_a_la_fecha'] . "'
								  AND ( C.c_fecha_baja >= '" . $this->filtro['i_a_la_fecha'] . "' OR C.c_fecha_baja IS NULL )
								  AND C.c_nomenclador IN (SELECT cc_nomenclador
								  						  FROM " . $this->tabla_codcargos . "
								  						  WHERE cc_tipo = '" . $tipo_de_cargo . "')
								  " . $solo_activos . "
								)
				" . $orden . "
				" . $limite . "
			   ";

		//fputs(fopen("sql_obtenerPersonal.sql", 'w'), print_r($sql, true));

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerPersonalDefensoria($con_limite = 0) {
		$conexion = $this->conectar();

		// Para mostrar sólo los Activos
		if ($this->filtro['i_solo_activos'] == 1) {

			// 08/04/2021 XXXX CORREGIDO
			// Se agregó que la fecha de Alta en el Cargo sea la última
			// En los métodos:
			// 		obtenerPersonal()
			// 		obtenerPersonalDefensoria()
			// 		listarParaSitioMGP()
			// --------------------------------------------------------
			$solo_activos = " AND ( C.c_fecha_alta = (SELECT MAX( c_fecha_alta )
													 FROM " . $this->tabla_cargos . "
													 WHERE c_legajo = C.c_legajo)
							  		AND
							  	    C.c_fecha_baja IS NULL OR C.c_fecha_baja > CURDATE()
							  	  )";
		} else {
			$solo_activos = ""; // PARA MOSTRARLOS A TODOS
		}

		$limite = "";
		if ($con_limite != 0) {
			//para limitar el listado
			$limite = " LIMIT 0, " . $this->filtro['i_rango'];
			if ($this->filtro['i_inicio'] != '' && $this->filtro['i_rango'] != '') {
				$limite = " LIMIT " . $this->filtro['i_inicio'] . ", " . $this->filtro['i_rango'];
			}

		}

		$sql = "SELECT *
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT a_legajo
								    FROM " . $this->tabla_areas . "
								    WHERE a_id_area = '01100000'
								    AND a_fecha_alta <= '" . $this->filtro['i_a_la_fecha'] . "'
								    AND ( a_fecha_baja >= '" . $this->filtro['i_a_la_fecha'] . "' OR a_fecha_baja IS NULL )
								  )
				AND p_legajo IN ( SELECT C.c_legajo
								  FROM " . $this->tabla_cargos . " AS C
								  WHERE C.c_fecha_alta <= '" . $this->filtro['i_a_la_fecha'] . "'
								  AND ( C.c_fecha_baja >= '" . $this->filtro['i_a_la_fecha'] . "' OR C.c_fecha_baja IS NULL )
								  " . $solo_activos . "
								)
				ORDER BY p_apellido
				" . $limite;

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	// LEGAJOS DE UN AREA DETERMINADA
	public function obtenerPersonalPorArea($id_area, $fecha_informe) {
		$conexion = $this->conectar();

		// SE TOMA C.c_nomenclador PARA ORDENAR SOLAMENTE
		$sql = "SELECT P.p_legajo, P.p_apellido, P.p_nombre, C.c_nomenclador
				FROM " . $this->tabla_personal . " AS P
				INNER JOIN
				( SELECT C.c_legajo, C.c_nomenclador
				  FROM " . $this->tabla_cargos . " AS C
				  WHERE C.c_fecha_alta <= '" . $fecha_informe . "'
				  AND ( C.c_fecha_baja >= '" . $fecha_informe . "' OR C.c_fecha_baja IS NULL )
				) AS C
				ON C.c_legajo = P.p_legajo
				AND P.p_legajo IN ( SELECT a_legajo
								    FROM " . $this->tabla_areas . "
								    WHERE a_id_area = '" . $id_area . "'
									AND a_fecha_alta <= '" . $fecha_informe . "'
									AND ( a_fecha_baja >= '" . $fecha_informe . "' OR a_fecha_baja IS NULL )
								  )
				ORDER BY SUBSTRING(C.c_nomenclador, 3, 2) DESC, P.p_apellido ASC
			   ";

		//fputs(fopen("sql_obtenerPersonalPorArea.txt", 'w'), print_r($sql, true));

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene información del cargo de un Legajo en un mes y año determinados
	 *
	 * @param integer $legajo
	 * @param integer $mes
	 * @param integer $anio
	 * @return Ambigous <NULL, array:>
	 */
	public function obtenerDatosCargoSegunMes($legajo, $mes, $anio) {
		$conexion = $this->conectar();

		///////////////////// 	AGREGADO EL 12/02/2015 		//////////////////////////////////////////////

		// SI EL MES A LIQUIDAR ES ENERO
		if ($mes == 1) {
			// EL MES ANTERIOR ES DICIEMBRE
			$mes_anterior = 12;

			// Y EL AÑO RESPECTIVO ES EL ANTERIOR TAMBIEN
			$anio_mes_anterior = $anio - 1;
		} else {
			// EL MES ANTERIOR
			$mes_anterior = $mes - 1;

			// DEL MISMO AÑO
			$anio_mes_anterior = $anio;
		}

		// SI EL NUMERO DEL MES ANTERIOR ES MENOR A 10 SE COMPLETA CON UN CERO A LA IZQUIERDA
		if ($mes_anterior < 10) {
			$mes_anterior = '0' . $mes_anterior;
		}

		$inicio_mes_anterior = $anio_mes_anterior . '-' . $mes_anterior . '-01';
		/////////////////////////////////////////////////////////////////////////////////////////////

		// SI EL NUMERO DEL MES ES MENOR A 10 SE COMPLETA CON UN CERO A LA IZQUIERDA
		if ($mes < 10) {
			$mes = '0' . $mes;
		}

		$inicio_mes = $anio . '-' . $mes . '-01';

		// *** 08/09/2017
		// SE CORRIGIÓ, POR EL CASO PARTICULAR DE Leniz y Vezzi
		// PORQUE NO TRAÍA LOS DEPENDIENTES DE Vezzi,
		// POR NO CONTEMPLAR EL ÚLTIMO CARGO DEL LEGAJO: CON MAX( c_fecha_alta )
		$sql = "SELECT *
				FROM " . $this->tabla_cargos . "
				WHERE c_legajo = '" . $legajo . "'
				AND (
					  ( c_depende_de IS NOT NULL
					    AND  c_fecha_alta <= (SELECT LAST_DAY('" . $inicio_mes . "'))
				  	    AND (c_fecha_baja >= '" . $inicio_mes . "' OR c_fecha_baja IS NULL)
				      )
				  	  OR
				      ( c_liquidacion_pendiente = '1'
				      	AND c_fecha_alta <= (SELECT LAST_DAY('" . $inicio_mes_anterior . "'))
				      	AND (c_fecha_baja >= '" . $inicio_mes_anterior . "' OR c_fecha_baja IS NULL)
				      )
				    )
				/*AND  c_fecha_alta = ( SELECT MAX( c_fecha_alta )
									  FROM " . $this->tabla_cargos . "
									  WHERE c_legajo = '" . $legajo . "'
									)*/
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * CORREGIDO EL 09/11/2017 XXXX, XXXX
	 *
	 * LISTADO PARA LIQUIDACIONES EN UN MES Y AÑO DETERMINADOS,
	 * LA FECHA DE ALTA DEBE SER MENOR O IGUAL AL ULTIMO DIA DEL MES,
	 * LA FECHA DE BAJA DEBE SER MAYOR O IGUAL AL PRIMER DIA DEL MES, O NO EXISTIR (NULA)
	 *
	 * EL 07/06/2016
	 * SE QUITÓ EL PARÁMETRO $legajo_concejal PARA UTILIZAR EL HISTÓRICO AL GENERAR EL LISTADO DE LIQUIDACIÓN
	 * POR EL CASO DE Santalla Y Cano EN LOS MESES DE MAYO Y JUNIO
	 *
	 * @param integer $i_mes
	 * @param integer $i_anio
	 * @param string
	 * @return boolean|Ambigous <NULL, array:>
	 */
	public function listarParaLiquidaciones($i_mes, $i_anio, $i_area = '', $legajos_concejales = '') {
		$conexion = $this->conectar();

		// *** AGREGADO EL 12/02/2015 --------------------------------------------------
		// SI EL MES A LIQUIDAR ES ENERO
		if ($i_mes == 1) {
			// EL MES ANTERIOR ES DICIEMBRE
			$mes_anterior = 12;
			// Y EL AÑO RESPECTIVO ES EL ANTERIOR TAMBIEN
			$anio_mes_anterior = $i_anio - 1;
		} else {
			// EL MES ANTERIOR
			$mes_anterior = $i_mes - 1;
			// DEL MISMO AÑO
			$anio_mes_anterior = $i_anio;
		}

		// SI EL NUMERO DEL MES ANTERIOR ES MENOR A 10 SE COMPLETA CON UN CERO A LA IZQUIERDA
		if ($mes_anterior < 10) {
			$mes_anterior = '0' . $mes_anterior;
		}

		$inicio_mes_anterior = $anio_mes_anterior . '-' . $mes_anterior . '-01';
		// ---------------------------------------------------------------------------

		// SI EL NUMERO DEL MES ELEGIDO ES MENOR A 10 SE COMPLETA CON UN CERO A LA IZQUIERDA
		if ($i_mes < 10) {
			$i_mes = '0' . $i_mes;
		}

		// DIA 1 DEL MES Y AÑO DETERMINADOS, FORMATO yyyy-mm-dd
		$inicio_mes = $i_anio . '-' . $i_mes . '-01';

		// Se trae la info de los legajos que se encuentren
		// en un área respectiva
		// dentro del mes respectivo
		// o con liquidación pendiente para el mes anterior
		// ordenados por legajo
		$sql = "SELECT c_legajo,
				       c_digito,
				       c_depende_de,
				       p_apellido,
					   p_nombre,
					   c_fecha_alta,
				       c_fecha_baja,
				       c_pertenece_secretaria_bloque,
				       a_id_area,
				       ca_nombre,
				       a_fecha_alta,
				       a_fecha_baja

				FROM   " . $this->tabla_cargos . "

				INNER JOIN " . $this->tabla_personal . " ON
					(p_legajo = c_legajo)

				INNER JOIN " . $this->tabla_areas . " ON
					(a_legajo = c_legajo)

				INNER JOIN " . $this->tabla_codareas . " ON
					(a_id_area = ca_id)

				WHERE a_id_area = '" . $i_area . "'
				AND c_depende_de IN (" . $legajos_concejales . ")
				AND (
						( /* Caso 1: fecha de filtro */
							c_fecha_alta <= LAST_DAY( '" . $inicio_mes . "' )
							AND (c_fecha_baja IS NULL OR c_fecha_baja >= '" . $inicio_mes . "')
							AND a_fecha_alta <= LAST_DAY( '" . $inicio_mes . "' )
							AND (a_fecha_baja IS NULL OR a_fecha_baja >= '" . $inicio_mes . "')
						) OR (
						    /* Caso 2: mes anterior a fecha de filtro y liquidacion pendiente */
							c_liquidacion_pendiente = '1'
							AND c_fecha_alta <= LAST_DAY( '" . $inicio_mes_anterior . "' )
							AND c_fecha_baja >= '" . $inicio_mes_anterior . "'
							AND a_fecha_alta <= LAST_DAY( '" . $inicio_mes_anterior . "' )
							AND (a_fecha_baja IS NULL OR a_fecha_baja >= '" . $inicio_mes_anterior . "')
						)
				    )
				ORDER BY c_depende_de, c_legajo";

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
		{
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * CORREGIDO EL 09/11/2017 XXXX, XXXX
	 *
	 * Certificado Mensual, del personal dependiente de un Concejal en un mes y un año determinados,
	 * la fecha de alta debe ser menor o igual al último día del mes, y la última para dicho legajo
	 * la fecha de baja debe ser mayor o igual al primer día del mes, o no existir (= null)
	 *
	 * @return boolean|Ambigous <NULL, array:>
	 */
	public function listarParaCertificado($i_mes, $i_anio, $i_area = '', $legajos_concejales = '') {
		$conexion = $this->conectar();

		// *** AGREGADO EL 12/02/2015 --------------------------------------------------
		// SI EL MES A LIQUIDAR ES ENERO
		if ($i_mes == 1) {
			// EL MES ANTERIOR ES DICIEMBRE
			$mes_anterior = 12;
			// Y EL AÑO RESPECTIVO ES EL ANTERIOR TAMBIEN
			$anio_mes_anterior = $i_anio - 1;
		} else {
			// EL MES ANTERIOR
			$mes_anterior = $i_mes - 1;
			// DEL MISMO AÑO
			$anio_mes_anterior = $i_anio;
		}

		// SI EL NUMERO DEL MES ANTERIOR ES MENOR A 10 SE COMPLETA CON UN CERO A LA IZQUIERDA
		if ($mes_anterior < 10) {
			$mes_anterior = '0' . $mes_anterior;
		}

		$inicio_mes_anterior = $anio_mes_anterior . '-' . $mes_anterior . '-01';
		// ---------------------------------------------------------------------------

		// SI EL NUMERO DEL MES ELEGIDO ES MENOR A 10 SE COMPLETA CON UN CERO A LA IZQUIERDA
		if ($i_mes < 10) {
			$i_mes = '0' . $i_mes;
		}

		// DIA 1 DEL MES Y AÑO DETERMINADOS, FORMATO yyyy-mm-dd
		$inicio_mes = $i_anio . '-' . $i_mes . '-01';

		$sql = "SELECT c_legajo,
				       c_digito,
				       c_depende_de,
				       p_apellido,
					   p_nombre,
					   c_fecha_alta,
				       c_fecha_baja,
				       c_pertenece_secretaria_bloque,
				       a_id_area,
				       ca_nombre,
				       a_fecha_alta,
				       a_fecha_baja

				FROM   " . $this->tabla_cargos . "

				INNER JOIN " . $this->tabla_personal . " ON
					(p_legajo = c_legajo)

				INNER JOIN " . $this->tabla_areas . " ON
					(a_legajo = c_legajo)

				INNER JOIN " . $this->tabla_codareas . " ON
					(a_id_area = ca_id)

				WHERE a_id_area = '" . $i_area . "'
				AND c_depende_de IN (" . $legajos_concejales . ")
				AND (
						( /* Caso 1: fecha de filtro */
							c_fecha_alta <= LAST_DAY( '" . $inicio_mes . "' )
							AND (c_fecha_baja IS NULL OR c_fecha_baja >= '" . $inicio_mes . "')
							AND a_fecha_alta <= LAST_DAY( '" . $inicio_mes . "' )
							AND (a_fecha_baja IS NULL OR a_fecha_baja >= '" . $inicio_mes . "')
						) OR (
						    /* Caso 2: mes anterior a fecha de filtro y liquidacion pendiente */
							c_liquidacion_pendiente = '1'
							AND c_fecha_alta <= LAST_DAY( '" . $inicio_mes_anterior . "' )
							AND c_fecha_baja >= '" . $inicio_mes_anterior . "'
							AND a_fecha_alta <= LAST_DAY( '" . $inicio_mes_anterior . "' )
							AND (a_fecha_baja IS NULL OR a_fecha_baja >= '" . $inicio_mes_anterior . "')
						)
				   )
				ORDER BY c_depende_de, c_legajo";

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);

		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
		{
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 *  Se obtiene el Personal dependiente de un Concejal en una fecha específica
	 *
	 * @return boolean|Ambigous <NULL, array:>
	 */
	public function listarPorConcejal() {
		$conexion = $this->conectar();

		$filtro = "";

		$sql = "SELECT P.p_legajo, P.p_apellido, P.p_nombre, P.p_foto,
				C.c_digito, C.c_pertenece_secretaria_bloque, CC.cc_nomenclador, CC.cc_nombre, CC.cc_modulo
				FROM
				(
					" . $this->tabla_personal . " AS P
					INNER JOIN
					( SELECT C.*
					  FROM " . $this->tabla_cargos . " AS C
					  WHERE C.c_depende_de = '" . $this->filtro['i_concejal'] . "'
					  AND C.c_fecha_alta <= '" . $this->filtro['i_a_la_fecha'] . "'
					  AND ( C.c_fecha_baja > '" . $this->filtro['i_a_la_fecha'] . "' OR C.c_fecha_baja IS NULL )
					) AS C
					ON C.c_legajo = P.p_legajo
				)
				INNER JOIN
				" . $this->tabla_codcargos . " AS CC
				ON CC.cc_nomenclador = C.c_nomenclador
				ORDER BY P.p_apellido
			   ";

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
		{
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerCargoActual($legajo) {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_cargos . "
				WHERE c_legajo = " . $legajo . "
				AND c_fecha_baja IS NULL
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerUltimoCargo($legajo) {
		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM " . $this->tabla_cargos . "
				WHERE c_legajo = " . $legajo . "
				ORDER BY c_fecha_alta DESC
				LIMIT 1
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**-
		     * Se obtienen todas las fechas de Alta, en orden descendente
		     *
		     * @param integer $legajo
		     * @return Ambigous <NULL, array:> $listado
	*/
	public function obtenerFechasAlta($legajo) {
		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM " . $this->tabla_cargos . "
				WHERE c_legajo = " . $legajo . "
				ORDER BY c_fecha_alta DESC
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$listado = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $listado;
	}

	/**
	 * Devuelve la fecha de Alta más reciente de un legajo determinado, cuyo cargo anterior posea decreto de baja o NO.
	 *
	 * @param integer $legajo
	 */
	public function buscarFechaAltaReciente($legajo) {
		// Se obtienen las fechas de Alta, ordenadas de la más actual para atrás (descendentemente)
		$info_por_fecha_alta = $this->obtenerFechasAlta($legajo);

		// Cantidad de fechas a evaluar
		$cant_fechas_alta = count($info_por_fecha_alta);

		// Se toma por defecto la primer fecha de alta como posible fecha inicial
		$fecha_alta_inicial = $info_por_fecha_alta[0]['c_fecha_alta'];

		// Por cada fecha de alta
		for ($i = 0; $i < $cant_fechas_alta; $i++) {
			// Si NO posee decreto de Baja
			if ($info_por_fecha_alta[$i]['c_nro_decreto_baja'] == '')
			// Se toma la fecha de alta como posible fecha inicial
			{
				$fecha_alta_inicial = $info_por_fecha_alta[$i]['c_fecha_alta'];
			} else // si posee un decreto de baja para dicha fecha
			// devuelve la fecha que anteriormente se definió como la inicial
			{
				return $fecha_alta_inicial;
			}

		}

		// Si terminó de recorrer las fechas y no encontró un decreto de baja, devuelve la primer fecha de alta
		return $fecha_alta_inicial;
	}

	public function guardarReasignacion($datos) {
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN"); // SE INICIA LA TRANSACCION

		// Se obtiene la fecha anterior a la fecha recibida, en formato yyyy-mm-dd
		$fecha_ayer = $this->obtenerFechaAyer($datos['i_fecha_reasignacion']);

		// Se verifica si está registrado en la fecha con el CONCEJAL Destino seleccionados, para NO repetir el cargo con el mismo concejal y la misma fecha
		$query = "SELECT c_legajo
				  FROM " . $this->tabla_cargos . "
				  WHERE c_legajo = " . $datos['i_datos_cargo_actual'][0]['c_legajo'] . "
				  AND c_fecha_alta = '" . $datos['i_fecha_reasignacion'] . "'
				  AND c_fecha_baja IS NULL
				  AND c_depende_de = " . $datos['i_concejal_destino'] . "
				 ";

		$resultado = $this->ejecutarQuery($query);

		$verificacion = $this->obtenerFila($resultado);

		// SI EL EMPLEADO NO POSEE UN CARGO EN DICHA FECHA
		if (!$verificacion['c_legajo']) {
			// *** 28/08/2017 XXXX Se agregó el Id del usuario que realizó la operación (campo c_modificado_por) ***
			//
			// SE ASIGNA LA FECHA DE BAJA EN EL CARGO QUE DEJA DE POSEER,
			// CON UN DIA ANTERIOR A LA FECHA DE ALTA DEL NUEVO CARGO, SIEMPRE QUE SEA MAYOR A LA FECHA DE ALTA DEL CARGO ANTERIOR
			$query = "UPDATE " . $this->tabla_cargos . "
					  SET c_fecha_baja = '" . $fecha_ayer . "',
					  	  c_modificado_por = " . $_SESSION['id_usuario'] . "
					  WHERE c_legajo = " . $datos['i_datos_cargo_actual'][0]['c_legajo'] . "
					  AND c_fecha_baja IS NULL
					  AND c_fecha_alta < '" . $fecha_ayer . "'
					 ";

			if (!$this->ejecutarQuery($query)) {
				$this->ejecutarQuery("ROLLBACK"); // SE DESHACE LA TRANSACCION
				return false;
			} else {
				// *** 28/08/2017 XXXX Se agregó el Id del usuario que realizó la operación (campo c_modificado_por) ***
				//
				// SE REGISTRA EL NUEVO CARGO CON LA REASIGNACION AL CONCEJAL DESTINO
				$query = "INSERT INTO " . $this->tabla_cargos . " (c_legajo, c_fecha_alta, c_nro_decreto_alta, c_fecha_baja, c_nro_decreto_baja, c_nomenclador, c_digito, c_depende_de, c_pertenece_secretaria_bloque, c_observaciones, c_modificado_por)
						  VALUES(" . $datos['i_datos_cargo_actual'][0]['c_legajo'] . ",
								'" . $datos['i_fecha_reasignacion'] . "',
								 null,
								 null,
								 null,
								 " . $datos['i_datos_cargo_actual'][0]['c_nomenclador'] . ",
								 " . $datos['i_datos_cargo_actual'][0]['c_digito'] . ",
								 " . $datos['i_concejal_destino'] . ",
								 " . $datos['i_datos_cargo_actual'][0]['c_pertenece_secretaria_bloque'] . ",
								 'ASIGNADO AUTOM&Aacute;TICAMENTE al Concejal con legajo " . $datos['i_concejal_destino'] . ".',
								 " . $_SESSION['id_usuario'] . "
								)
						 ";

				if (!$this->ejecutarQuery($query)) {
					$this->ejecutarQuery("ROLLBACK"); // SE DESHACE LA TRANSACCION
					return false;
				}
			}
		} else {
			// SI POSEE UN CARGO EN ESA FECHA SE EVITA EL INSERT
			$this->ejecutarQuery("ROLLBACK"); // SE DESHACE LA TRANSACCION
			return false;
		}

		$this->ejecutarQuery("COMMIT"); // SE EJECUTA LA TRANSACCION

		//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
		$modelo = new auditoriaPersonalModel();

		$datos_log = Array();
		$datos_log['operacion'] = "ALTA";
		$datos_log['tabla'] = "pers_cargos";
		$datos_log['legajo'] = $datos['i_datos_cargo_actual'][0]['c_legajo'];
		$datos_log['observaciones'] = "Se reasigna el Legajo " . $datos['legajo'] . " como dependiente, al Concejal con legajo " . $datos['i_concejal_destino'] . ".";

		// SE CARGA EN auditoria EL MOVIMIENTO
		$modelo->registrarMovimiento($datos_log);

		$this->desconectar($conexion);

		return true;
	}

	/**
	 * Se verifica si el legajo pertenece al Bloque del Concejal destino
	 * de NO ser así, se registra en dicho bloque en la fecha de la reasignación
	 * @param  [array] $datos 	Datos para realizar la verificación
	 * @return [boolean]  	                    true o false
	 */
	public function verificarPertenenciaBloque($legajo_asesor, $fecha_reasignacion, $id_bloque) {
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN"); // SE INICIA LA TRANSACCION

		// Se verifica si el asesor pertenece actualmente al bloque
		$query = "SELECT a_id_area
				  FROM " . $this->tabla_areas . "
				  WHERE a_legajo = " . $legajo_asesor . "
				  AND a_id_area = '" . $id_bloque . "'
				  ORDER BY a_fecha_alta DESC
				  LIMIT 1
				 ";

		$resultado = $this->ejecutarQuery($query);

		$verificacion = $this->obtenerFila($resultado);

		// Si NO pertenece actualmente a dicho Bloque
		if ($verificacion['a_id_area'] == '') {
			// Se obtiene la fecha anterior a la fecha de reasignación, en formato yyyy-mm-dd
			$fecha_anterior = $this->obtenerFechaAyer($fecha_reasignacion);

			// Se asigna la fecha de baja en el área que deja de pertenecer
			// con un día anterior a la fecha de reasignación, siempre que sea mayor a la fecha de alta del área anterior
			$query = "UPDATE " . $this->tabla_areas . "
					  SET a_fecha_baja = '" . $fecha_anterior . "'
					  WHERE a_legajo = " . $legajo_asesor . "
					  AND a_fecha_baja IS NULL
					  AND a_fecha_alta < '" . $fecha_anterior . "'
					 ";

			if (!$this->ejecutarQuery($query)) {
				$this->ejecutarQuery("ROLLBACK"); // SE DESHACE LA TRANSACCION
				return false;
			} else {
				// Se registra el Area en la fecha de reasignación
				$query = "INSERT INTO " . $this->tabla_areas . " (a_legajo, a_fecha_alta, a_id_area, a_observaciones)
						  VALUES(" . $legajo_asesor . ",
								'" . $fecha_reasignacion . "',
								'" . $id_bloque . "',
								'Cambio automatico al reasignarlo a otro Concejal.'
								)
						 ";

				if (!$this->ejecutarQuery($query)) {
					$this->ejecutarQuery("ROLLBACK"); // SE DESHACE LA TRANSACCION
					return false;
				} else {
					$this->ejecutarQuery("COMMIT"); // SE EJECUTA LA TRANSACCION

					// SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
					$modelo = new auditoriaPersonalModel();

					$datos_log = Array();
					$datos_log['operacion'] = "MODIFICA";
					$datos_log['tabla'] = $this->tabla_areas;
					$datos_log['legajo'] = $legajo_asesor;
					$datos_log['observaciones'] = "Se modifica un Area para el Legajo " . $legajo_asesor . ", al reasignarlo a otro Concejal.";

					//SE CARGA EN auditoria EL MOVIMIENTO
					$modelo->registrarMovimiento($datos_log);
				}
			}
		}

		$this->desconectar($conexion);

		return true;
	}

	public function obtenerDatosLegajo($legajo) {
		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM " . $this->tabla_personal . "
				WHERE p_legajo = " . $legajo . "
			   ";

		$resultado = $this->ejecutarQuery($sql);

		if ($resultado) {
			$dato = $this->obtenerFila($resultado);
		} else {
			return false;
		}

		$this->desconectar($conexion);

		return $dato;
	}

	/**
	 * Se obtiene el histórico de Cargos de un legajo determinado
	 *
	 * @param integer $plegajo
	 * @return Ambigous <NULL, array:> $datos
	 */
	public function obtenerHistoricoCargos($plegajo) {
		$conexion = $this->conectar();

		$sql = "SELECT CC.cc_nomenclador, CC.cc_nombre, C.*
				FROM " . $this->tabla_codcargos . " AS CC
				INNER JOIN " . $this->tabla_cargos . " AS C
				ON C.c_nomenclador = CC.cc_nomenclador
				AND C.c_legajo = " . $plegajo . "
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function listarParaCredencial($con_limite = 0) {
		$conexion = $this->conectar();

		$filtro = "";

		// SE FILTRA HASTA UNA FECHA DETERMINADA
		if ($this->filtro['i_a_la_fecha'] != '') {
			$filtro .= " AND P.p_legajo IN ( SELECT a_legajo
											 FROM " . $this->tabla_areas . "
											 WHERE a_fecha_alta <= '" . $this->filtro['i_a_la_fecha'] . "'
											 AND ( a_fecha_baja IS NULL OR a_fecha_baja > '" . $this->filtro['i_a_la_fecha'] . "' )
										   )
						 AND P.p_legajo IN ( SELECT c_legajo
											 FROM " . $this->tabla_cargos . "
											 WHERE c_fecha_alta <= '" . $this->filtro['i_a_la_fecha'] . "'
											 AND ( c_fecha_baja IS NULL  OR c_fecha_baja > '" . $this->filtro['i_a_la_fecha'] . "')
										   )
					   ";
		}

		// SE FILTRA POR UN RANGO DE FECHA DETERMINADO
		if ($this->filtro['i_fecha_desde'] != '' && $this->filtro['i_fecha_hasta'] != '') {
			$filtro .= " AND P.p_legajo IN ( SELECT a_legajo
											 FROM " . $this->tabla_areas . "
											 WHERE ( a_fecha_alta BETWEEN '" . $this->filtro['i_fecha_desde'] . "' AND '" . $this->filtro['i_fecha_hasta'] . "' )
											 AND ( a_fecha_baja IS NULL OR a_fecha_baja <= '" . $this->filtro['i_fecha_hasta'] . "' )
										   )
						 AND P.p_legajo IN ( SELECT c_legajo
											 FROM " . $this->tabla_cargos . "
											 WHERE ( c_fecha_alta BETWEEN '" . $this->filtro['i_fecha_desde'] . "' AND '" . $this->filtro['i_fecha_hasta'] . "' )
											 AND ( c_fecha_baja IS NULL OR c_fecha_baja <= '" . $this->filtro['i_fecha_hasta'] . "' )
										   )
					   ";
		}

		// SE FILTRA POR Area
		if (!empty($this->filtro['i_area']) && $this->filtro['i_area'] != 0) {
			// SE VERIFICA SI DEPENDEN OTRAS AREAS DE ELLA
			$filtro .= " AND P.p_legajo IN ( SELECT A.a_legajo
											 FROM " . $this->tabla_areas . " AS A
											 WHERE A.a_id_area = '" . $this->filtro['i_area'] . "'
											 AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
											 					    FROM " . $this->tabla_areas . "
																    WHERE a_legajo = A.a_legajo
																  )
											 UNION
											 SELECT A.a_legajo
											 FROM " . $this->tabla_codareas . " AS CA
											 INNER JOIN " . $this->tabla_areas . " AS A
											 ON A.a_id_area = CA.ca_id
											 WHERE ( CA.ca_depende_de = '" . $this->filtro['i_area'] . "' OR CA.ca_depende_de IN ( SELECT ca_id
																															   FROM " . $this->tabla_codareas . " AS CA
																															   INNER JOIN " . $this->tabla_areas . " AS A
																															   ON A.a_id_area = CA.ca_id
																															   WHERE CA.ca_depende_de = '" . $this->filtro['i_area'] . "'
																															   AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
																																				      FROM " . $this->tabla_areas . "
																																				      WHERE a_legajo = A.a_legajo
																																				    )
																															 )
												   )
											 AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
																    FROM " . $this->tabla_areas . "
																    WHERE a_legajo = A.a_legajo
																  )
										   )
					   ";
		}

		// SE FILTRA POR Cargo
		if (!empty($this->filtro['i_cargo']) && $this->filtro['i_cargo'] != 0) {
			$filtro .= " AND P.p_legajo IN ( SELECT C.c_legajo
										     FROM " . $this->tabla_cargos . " AS C
										     WHERE C.c_nomenclador = '" . $this->filtro['i_cargo'] . "'
										     AND C.c_fecha_alta = ( SELECT MAX( c_fecha_alta )
																    FROM " . $this->tabla_cargos . "
																    WHERE c_legajo = C.c_legajo
																  )
											 AND C.c_fecha_baja IS NULL
										   )
				       ";
		}

		// SE FILTRA POR Concejal
		if (!empty($this->filtro['i_concejal']) && $this->filtro['i_concejal'] != 0) {
			$filtro .= " AND P.p_legajo IN (SELECT c_legajo
											FROM " . $this->tabla_cargos . "
											WHERE c_depende_de = " . $this->filtro['i_concejal'] . "
											AND c_fecha_baja IS NULL
										   )
					   ";
		}

		// PARA ORDENAR POR LEGAJO O APELLIDO
		$orden = "ORDER BY P.p_apellido";
		if ($this->filtro['i_orden'] == 1) {
			$orden = "ORDER BY P.p_legajo";
		}

		// PARA LIMITAR EL LISTADO
		$limite = "";
		if ($con_limite != 0) {
			$limite = " LIMIT 0, " . $this->filtro['i_rango'] . "";
			if ($this->filtro['i_inicio'] != '' && $this->filtro['i_rango'] != '') {
				$limite = " LIMIT " . $this->filtro['i_inicio'] . ", " . $this->filtro['i_rango'];
			}

		}

		$sql = "SELECT P.p_legajo
				FROM " . $this->tabla_personal . " AS P
				WHERE P.p_legajo IN	( SELECT C.c_legajo
									  FROM " . $this->tabla_cargos . " AS C
									  WHERE C.c_fecha_alta = ( SELECT MAX( c_fecha_alta )
															   FROM " . $this->tabla_cargos . "
															   WHERE c_legajo = C.c_legajo
															 )
									)
				" . $filtro . "
				" . $orden . "
				" . $limite . "
			   ";

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
		// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
		{
			$datos = $this->crearVector($resultado);
		} else {
			return false;
		}

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * 31/07/2019 XXXX
	 * Se obtienen los Defensores del Pueblo ACTIVOS
	 */
	public function obtenerDefensoresPuebloActivos() {
		$conexion = $this->conectar();

		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM " . $this->tabla_personal . "
				WHERE p_legajo IN ( SELECT c_legajo
									FROM " . $this->tabla_cargos . "
									WHERE c_nomenclador IN ( SELECT cc_nomenclador
															 FROM " . $this->tabla_codcargos . "
															 WHERE (cc_nomenclador = " . $this->id_cargo_defensor_pueblo . " OR cc_nomenclador = " . $this->id_cargo_defensor_pueblo_coordinador . ")
														   )
									AND c_fecha_alta = ( SELECT MAX( c_fecha_alta )
														 FROM " . $this->tabla_cargos . "
														 WHERE c_legajo = p_legajo
													   )
									AND c_fecha_baja IS NULL
								  )
				ORDER BY p_apellido, p_nombre
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * 13/02/2026 XXXX
	 * Se retira de mantenimiento, la sección de Planta Política del sitio web
	 */
	public function activarSeccionWebPlantaPolitica() {
		$conexion = $this->conectar();

		$query = "UPDATE ".$this->tabla_paginas_sitio."
				  SET en_mantenimiento = 0
		  		  WHERE id = 4";

		if ( !$this->ejecutarQuery($query) ) {
			return false;
		}
		$this->desconectar($conexion);

		return true;
    }
}
?>
