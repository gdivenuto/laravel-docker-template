<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

class personalModel extends ModeloBaseMySQLi
{
    public function conectar() {
		// Se conecta según el Id del sistema
		return parent::conectarDB(3);
	}

    public function listar()
	{
		$conexion = $this->conectar();

		$filtro = "";
		$limite = "";
		$id_area = $this->filtro['id_area'];
		$nomenclador = $this->filtro['nomenclador'];
		$concejal = $this->filtro['concejal'];

		// PARA FILTRAR POR LEGAJO
		//************************************************************************************************
		if ( ($this->filtro['legajo'] != 0) && ($this->filtro['legajo'] != '') )
			$filtro .= " AND P.p_legajo = ".$this->filtro['legajo'];

		// PARA FILTRAR POR APELLIDO Ó NOMBRE
		//***********************************************************************************************
		if ( $this->filtro['apellido_y_nombre'] != '' )
			$filtro .= " AND ( P.p_apellido LIKE '%".addslashes($this->filtro['apellido_y_nombre'])."%' OR P.p_nombre LIKE '%".addslashes($this->filtro['apellido_y_nombre'])."%')";

		// PARA FILTRAR POR UN AREA DETERMINADA
		//***********************************************************************************************
		if ( ($id_area != 0) && ($id_area != '') )
		{
			// Y TODAS LAS DEMÁS AREAS QUE DEPENDEN DE ELLA
			$filtro .= " AND P.p_legajo IN ( SELECT A.a_legajo
											 FROM ".$this->tabla_areas." AS A
											 WHERE A.a_id_area = '".$id_area."'
											 AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
											 					    FROM ".$this->tabla_areas."
																    WHERE a_legajo = A.a_legajo
																  )
											 UNION
											 SELECT A.a_legajo
											 FROM ".$this->tabla_codareas." AS CA
											 INNER JOIN ".$this->tabla_areas." AS A
											 ON A.a_id_area = CA.ca_id
											 WHERE ( CA.ca_depende_de = '".$id_area."' OR CA.ca_depende_de IN ( SELECT ca_id
																										        FROM ".$this->tabla_codareas." AS CA
																										        INNER JOIN ".$this->tabla_areas." AS A
																										        ON A.a_id_area = CA.ca_id
																										        WHERE CA.ca_depende_de = '".$id_area."'
																										        AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
																																       FROM ".$this->tabla_areas."
																																       WHERE a_legajo = A.a_legajo
																															         )
																										      )
												   )
											 AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
																    FROM ".$this->tabla_areas."
																    WHERE a_legajo = A.a_legajo
																  )
										   )
					   ";
		}

		// PARA FILTRAR POR CARGO (EL ACTUAL)
		//***********************************************************************************************
		if ( ($nomenclador != 0) && ($nomenclador != '') )
		{
			$filtro .= " AND P.p_legajo IN ( SELECT C.c_legajo
										     FROM ".$this->tabla_cargos." AS C
										     WHERE C.c_nomenclador = '".$nomenclador."'
										     AND C.c_fecha_alta = ( SELECT MAX( c_fecha_alta )
																    FROM ".$this->tabla_cargos."
																    WHERE c_legajo = C.c_legajo
															      )
     									   )
					   ";
		}

		// PARA FILTRAR POR Concejal (siendo el actual o el último del cual dependió)
		//***********************************************************************************************
		if ( ($concejal != 0) && ($concejal != '') )
		{
			$filtro .= " AND P.p_legajo IN (SELECT c_legajo
											FROM ".$this->tabla_cargos."
											WHERE c_depende_de = ".$concejal."
											AND c_fecha_baja IS NULL
										   )
					   ";
		}

		// PARA LISTAR SÓLO LOS ACTIVOS
		//***********************************************************************************************
		if ( $this->filtro['f_activos']  == '1' )
		{
			$filtro .= " AND P.p_legajo IN (SELECT c_legajo
											FROM ".$this->tabla_cargos."
											WHERE c_fecha_alta = ( SELECT MAX( c_fecha_alta )
																   FROM ".$this->tabla_cargos."
																   WHERE c_legajo = P.p_legajo
															     )
											AND (c_fecha_baja IS NULL OR c_fecha_baja > CURDATE())
										   )
					   ";
		}

		// PARA LIMITAR EL LISTADO
		//***********************************************************************************************
		if ( $this->filtro['rango'] != 0 )
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];

		// QUERY A EJECUTAR
		//***********************************************************************************************
		$sql = "SELECT P.*
				FROM ".$this->tabla_personal." AS P
				WHERE P.p_apellido IS NOT NULL
				".$filtro."
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
			   ";

		// Se agrega el límite a la query
		$sql .= $limite;

		$resultado = $this->ejecutarQuery($sql);

		// Si devuelve algún registro
		if ( $resultado )
			$datos = $this->crearVector($resultado);
		else
			return false;

		$this->desconectar($conexion);

		return $datos;
    }

    public function obtenerCantidad()
    {
    	$conexion = $this->conectar();

    	$filtro = "";

    	$id_area = $this->filtro['id_area'];
    	$nomenclador = $this->filtro['nomenclador'];
    	$concejal = $this->filtro['concejal'];

    	// PARA FILTRAR POR LEGAJO
		//***********************************************************************************************
    	if ( ($this->filtro['legajo'] != 0) && ($this->filtro['legajo'] != '') )
    		$filtro .= " AND P.p_legajo = ".$this->filtro['legajo'];

    	// PARA FILTRAR POR APELLIDO Ó NOMBRE
		//***********************************************************************************************
    	if ( $this->filtro['apellido_y_nombre'] != '' )
    		$filtro .= " AND ( P.p_apellido LIKE '%".addslashes($this->filtro['apellido_y_nombre'])."%' OR P.p_nombre LIKE '%".addslashes($this->filtro['apellido_y_nombre'])."%')";

    	// PARA FILTRAR POR UN AREA DETERMINADA
		//***********************************************************************************************
    	if ( ($id_area != 0) && ($id_area != '') )
    	{
    		// Y TODAS LAS DEMÁS AREAS QUE DEPENDEN DE ELLA
    		$filtro .= " AND P.p_legajo IN ( SELECT A.a_legajo
											 FROM ".$this->tabla_areas." AS A
											 WHERE A.a_id_area = '".$id_area."'
											 AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
											 					    FROM ".$this->tabla_areas."
																    WHERE a_legajo = A.a_legajo
																  )
											 UNION
											 SELECT A.a_legajo
											 FROM ".$this->tabla_codareas." AS CA
											 INNER JOIN ".$this->tabla_areas." AS A
											 ON A.a_id_area = CA.ca_id
											 WHERE ( CA.ca_depende_de = '".$id_area."' OR CA.ca_depende_de IN ( SELECT ca_id
																										        FROM ".$this->tabla_codareas." AS CA
																										        INNER JOIN ".$this->tabla_areas." AS A
																										        ON A.a_id_area = CA.ca_id
																										        WHERE CA.ca_depende_de = '".$id_area."'
																										        AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
																																       FROM ".$this->tabla_areas."
																																       WHERE a_legajo = A.a_legajo
																															         )
																										      )
												   )
											 AND A.a_fecha_alta = ( SELECT MAX( a_fecha_alta )
																    FROM ".$this->tabla_areas."
																    WHERE a_legajo = A.a_legajo
																  )
										   )
					   ";
    	}

		// PARA FILTRAR POR CARGO (EL ACTUAL)
		//***********************************************************************************************
		if ( ($nomenclador != 0) && ($nomenclador != '') )
		{
			$filtro .= " AND P.p_legajo IN ( SELECT C.c_legajo
										     FROM ".$this->tabla_cargos." AS C
										     WHERE C.c_nomenclador = '".$nomenclador."'
										     AND C.c_fecha_alta = ( SELECT MAX( c_fecha_alta )
																    FROM ".$this->tabla_cargos."
																    WHERE c_legajo = C.c_legajo
															      )
										   )
					   ";
		}

		// PARA FILTRAR POR Concejal (siendo el actual o el último del cual dependió)
		//***********************************************************************************************
		if ( ($concejal != 0) && ($concejal != '') )
		{
			$filtro .= " AND P.p_legajo IN (SELECT c_legajo
											FROM ".$this->tabla_cargos."
											WHERE c_depende_de = ".$concejal."
											AND c_fecha_baja IS NULL
										   )
					   ";
		}

    	// PARA LISTAR SÓLO LOS ACTIVOS
		//***********************************************************************************************
    	if ( $this->filtro['f_activos']  == '1' )
    	{
    		$filtro .= " AND P.p_legajo IN (SELECT c_legajo
											FROM ".$this->tabla_cargos."
											WHERE c_fecha_alta = ( SELECT MAX( c_fecha_alta )
																   FROM ".$this->tabla_cargos."
																   WHERE c_legajo = P.p_legajo
															     )
											AND (c_fecha_baja IS NULL OR c_fecha_baja > CURDATE())
										   )
					   ";
    	}

    	// QUERY A EJECUTAR
		//***********************************************************************************************
    	$sql = "SELECT COUNT(*) AS cantidad
				FROM ".$this->tabla_personal." AS P
				WHERE P.p_apellido IS NOT NULL
				".$filtro."
			   ";

    	$resultado = $this->ejecutarQuery($sql);

    	$dato = $this->obtenerFila($resultado);

    	$this->desconectar($conexion);

    	return $dato['cantidad'];
    }

    /**
     * Se verifica si está activo el legajo, en base a su fecha de baja en su último Cargo
     *
     * @param integer $legajo
     * @return boolean
     */
	public function estaActivo($legajo)
	{
		$conexion = $this->conectar();

		$sql = "SELECT c_fecha_baja
				FROM pers_cargos
				WHERE c_legajo = '$legajo'
				AND c_fecha_alta = ( SELECT MAX( c_fecha_alta ) FROM pers_cargos WHERE c_legajo = '$legajo' )";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		// Si no posee Fecha de baja en su último Cargo o si no llegó aún a la fecha de baja asignada
		return ( is_null($dato['c_fecha_baja']) || $dato['c_fecha_baja'] > date("Y-m-d") );
	}

    /**
     * Se obtiene la información de un legajo determinado
     *
     * @param integer $legajo
     */
    public function obtenerRegistro($legajo)
	{
		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM ".$this->tabla_personal."
				WHERE p_legajo = ".$legajo."
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
    }

    public function existe($legajo)
	{
		$conexion = $this->conectar();

		$query = "SELECT p_legajo
				  FROM ".$this->tabla_personal."
				  WHERE p_legajo = ".$legajo."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ( $dato['p_legajo'] != '' );
    }

    public function validarDatos($datos)
	{
		$datos['p_apellido']           = $this->revisarValorAtributo($datos['p_apellido']);

		$datos['p_nombre']             = $this->revisarValorAtributo($datos['p_nombre']);

		$datos['p_sexo']               = $this->revisarValorAtributo($datos['p_sexo']);

		$datos['p_grupo_sanguineo']    = $this->revisarValorAtributo($datos['p_grupo_sanguineo']);

		$datos['p_factor_sanguineo']   = $this->revisarValorAtributo($datos['p_factor_sanguineo']);

		$datos['p_tipo_documento']     = $this->revisarValorAtributo($datos['p_tipo_documento']);

		$datos['p_nro_documento']      = $this->revisarValorAtributo($datos['p_nro_documento']);

		$datos['p_cuil']      		   = $this->revisarValorAtributo($datos['p_cuil']);

		$datos['p_foto']               = $this->revisarValorAtributo($datos['p_foto']);

		$datos['p_fecha_nac']          = $this->revisarValorFechaAtributo($datos['p_fecha_nac']);

		$datos['p_lugar_nac']          = $this->revisarValorAtributo($datos['p_lugar_nac']);

		$datos['p_provincia']          = $this->revisarValorAtributo($datos['p_provincia']);

		$datos['p_pais']               = $this->revisarValorAtributo($datos['p_pais']);

		$datos['p_nacionalidad']       = $this->revisarValorAtributo($datos['p_nacionalidad']);

		$datos['p_estado_civil']       = $this->revisarValorAtributo($datos['p_estado_civil']);

		$datos['p_calle_legal']        = $this->revisarValorAtributo($datos['p_calle_legal']);

		$datos['p_numero_legal']       = $this->revisarValorAtributo($datos['p_numero_legal']);

		$datos['p_piso_legal']         = $this->revisarValorAtributo($datos['p_piso_legal']);

		$datos['p_depto_legal']        = $this->revisarValorAtributo($datos['p_depto_legal']);

		$datos['p_entre_calles_legal'] = $this->revisarValorAtributo($datos['p_entre_calles_legal']);

		$datos['p_zona_barrio_legal']  = $this->revisarValorAtributo($datos['p_zona_barrio_legal']);

		$datos['p_pais_legal']         = $this->revisarValorAtributo($datos['p_pais_legal']);

		$datos['p_provincia_legal']    = $this->revisarValorAtributo($datos['p_provincia_legal']);

		$datos['p_localidad_legal']    = $this->revisarValorAtributo($datos['p_localidad_legal']);

		$datos['p_telefono_legal']     = $this->revisarValorAtributo($datos['p_telefono_legal']);

		$datos['p_calle_real']         = $this->revisarValorAtributo($datos['p_calle_real']);

		$datos['p_numero_real']        = $this->revisarValorAtributo($datos['p_numero_real']);

		$datos['p_piso_real']          = $this->revisarValorAtributo($datos['p_piso_real']);

		$datos['p_depto_real']         = $this->revisarValorAtributo($datos['p_depto_real']);

		$datos['p_entre_calles_real']  = $this->revisarValorAtributo($datos['p_entre_calles_real']);

		$datos['p_zona_barrio_real']   = $this->revisarValorAtributo($datos['p_zona_barrio_real']);

		$datos['p_pais_real']          = $this->revisarValorAtributo($datos['p_pais_real']);

		$datos['p_provincia_real']     = $this->revisarValorAtributo($datos['p_provincia_real']);

		$datos['p_localidad_real']     = $this->revisarValorAtributo($datos['p_localidad_real']);

		$datos['p_telefono_real']      = $this->revisarValorAtributo($datos['p_telefono_real']);

		$datos['p_celular_real']       = $this->revisarValorAtributo($datos['p_celular_real']);

		$datos['p_tel_mensajes_real']  = $this->revisarValorAtributo($datos['p_tel_mensajes_real']);

		$datos['p_mail']  			   = $this->revisarValorAtributo($datos['p_mail']);

		$datos['p_fecha_ingreso_planta_politica'] = $this->revisarValorFechaAtributo($datos['p_fecha_ingreso_planta_politica']);

		$datos['p_fecha_ingreso_planta_permanente'] = $this->revisarValorFechaAtributo($datos['p_fecha_ingreso_planta_permanente']);

		return $datos;
    }

    //	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
    public function verificarRegistroEntero($p_foto = '')
    {
		$filtro_p_apellido           = $this->adaptarValorStringParaFiltro('p_apellido');

		$filtro_p_nombre             = $this->adaptarValorStringParaFiltro('p_nombre');

		$filtro_p_sexo               = $this->adaptarValorStringParaFiltro('p_sexo');

		$filtro_p_grupo_sanguineo    = $this->adaptarValorStringParaFiltro('p_grupo_sanguineo');

		$filtro_p_factor_sanguineo   = $this->adaptarValorStringParaFiltro('p_factor_sanguineo');

		$filtro_p_tipo_documento     = $this->adaptarValorStringParaFiltro('p_tipo_documento');

		$filtro_p_nro_documento      = $this->adaptarValorStringParaFiltro('p_nro_documento');

		$filtro_p_cuil      		 = $this->adaptarValorStringParaFiltro('p_cuil');

		$filtro_p_foto               = $this->adaptarValorStringParaFiltro('p_foto');

		$filtro_p_fecha_nac          = $this->adaptarValorStringParaFiltro('p_fecha_nac');

		$filtro_p_lugar_nac          = $this->adaptarValorStringParaFiltro('p_lugar_nac');

		$filtro_p_provincia          = $this->adaptarValorStringParaFiltro('p_provincia');

		$filtro_p_pais               = $this->adaptarValorStringParaFiltro('p_pais');

		$filtro_p_nacionalidad       = $this->adaptarValorStringParaFiltro('p_nacionalidad');

		$filtro_p_estado_civil       = $this->adaptarValorStringParaFiltro('p_estado_civil');

		$filtro_p_calle_legal        = $this->adaptarValorStringParaFiltro('p_calle_legal');

		$filtro_p_numero_legal       = $this->adaptarValorNumericoParaFiltro('p_numero_legal');

		$filtro_p_piso_legal         = $this->adaptarValorStringParaFiltro('p_piso_legal');

		$filtro_p_depto_legal        = $this->adaptarValorStringParaFiltro('p_depto_legal');

		$filtro_p_entre_calles_legal = $this->adaptarValorStringParaFiltro('p_entre_calles_legal');

		$filtro_p_zona_barrio_legal  = $this->adaptarValorStringParaFiltro('p_zona_barrio_legal');

		$filtro_p_pais_legal         = $this->adaptarValorStringParaFiltro('p_pais_legal');

		$filtro_p_provincia_legal    = $this->adaptarValorStringParaFiltro('p_provincia_legal');

		$filtro_p_localidad_legal    = $this->adaptarValorStringParaFiltro('p_localidad_legal');

		$filtro_p_telefono_legal     = $this->adaptarValorStringParaFiltro('p_telefono_legal');

		$filtro_p_calle_real         = $this->adaptarValorStringParaFiltro('p_calle_real');

		$filtro_p_numero_real        = $this->adaptarValorNumericoParaFiltro('p_numero_real');

		$filtro_p_piso_real          = $this->adaptarValorStringParaFiltro('p_piso_real');

		$filtro_p_depto_real         = $this->adaptarValorStringParaFiltro('p_depto_real');

		$filtro_p_entre_calles_real  = $this->adaptarValorStringParaFiltro('p_entre_calles_real');

		$filtro_p_zona_barrio_real   = $this->adaptarValorStringParaFiltro('p_zona_barrio_real');

		$filtro_p_pais_real          = $this->adaptarValorStringParaFiltro('p_pais_real');

		$filtro_p_provincia_real     = $this->adaptarValorStringParaFiltro('p_provincia_real');

		$filtro_p_localidad_real     = $this->adaptarValorStringParaFiltro('p_localidad_real');

		$filtro_p_telefono_real      = $this->adaptarValorStringParaFiltro('p_telefono_real');

		$filtro_p_celular_real       = $this->adaptarValorStringParaFiltro('p_celular_real');

		$filtro_p_tel_mensajes_real  = $this->adaptarValorStringParaFiltro('p_tel_mensajes_real');

		$filtro_p_mail  			 = $this->adaptarValorStringParaFiltro('p_mail');

		$filtro_p_fecha_ingreso_planta_politica = $this->adaptarValorStringParaFiltro('p_fecha_ingreso_planta_politica');

		$filtro_p_fecha_ingreso_planta_permanente = $this->adaptarValorStringParaFiltro('p_fecha_ingreso_planta_permanente');

		$conexion = $this->conectar();

		$query = "SELECT p_legajo
				  FROM ".$this->tabla_personal."
				  WHERE p_legajo = ".$_SESSION['p_legajo_original']."
				  ".$filtro_p_apellido."
				  ".$filtro_p_nombre."
				  ".$filtro_p_sexo."
				  ".$filtro_p_grupo_sanguineo."
				  ".$filtro_p_factor_sanguineo."
				  ".$filtro_p_tipo_documento."
				  ".$filtro_p_nro_documento."
				  ".$filtro_p_cuil."
				  ".$filtro_p_foto."
				  ".$filtro_p_fecha_nac."
				  ".$filtro_p_lugar_nac."
				  ".$filtro_p_provincia."
				  ".$filtro_p_pais."
				  ".$filtro_p_nacionalidad."
				  ".$filtro_p_estado_civil."
				  ".$filtro_p_calle_legal."
				  ".$filtro_p_numero_legal."
				  ".$filtro_p_piso_legal."
				  ".$filtro_p_depto_legal."
				  ".$filtro_p_entre_calles_legal."
				  ".$filtro_p_zona_barrio_legal."
				  ".$filtro_p_pais_legal."
				  ".$filtro_p_provincia_legal."
				  ".$filtro_p_localidad_legal."
				  ".$filtro_p_telefono_legal."
				  ".$filtro_p_calle_real."
				  ".$filtro_p_numero_real."
				  ".$filtro_p_piso_real."
				  ".$filtro_p_depto_real."
				  ".$filtro_p_entre_calles_real."
				  ".$filtro_p_zona_barrio_real."
				  ".$filtro_p_pais_real."
				  ".$filtro_p_provincia_real."
				  ".$filtro_p_localidad_real."
				  ".$filtro_p_telefono_real."
				  ".$filtro_p_celular_real."
				  ".$filtro_p_tel_mensajes_real."
				  ".$filtro_p_mail."
				  ".$filtro_p_fecha_ingreso_planta_politica."
				  ".$filtro_p_fecha_ingreso_planta_permanente."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( $dato['p_legajo'] );
    }

    public function insertar($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO ".$this->tabla_personal." (p_legajo, p_apellido, p_nombre, p_sexo, p_grupo_sanguineo, p_factor_sanguineo, p_tipo_documento, p_nro_documento, p_cuil, p_foto, p_fecha_nac, p_lugar_nac, p_provincia, p_pais, p_nacionalidad, p_estado_civil, p_calle_legal, p_numero_legal, p_piso_legal, p_depto_legal, p_entre_calles_legal, p_zona_barrio_legal, p_pais_legal, p_provincia_legal, p_localidad_legal, p_telefono_legal, p_calle_real, p_numero_real, p_piso_real, p_depto_real, p_entre_calles_real, p_zona_barrio_real, p_pais_real, p_provincia_real, p_localidad_real, p_telefono_real, p_celular_real, p_tel_mensajes_real, p_fecha_ingreso_planta_politica, p_fecha_ingreso_planta_permanente, p_mail)
				  VALUES(".$datos['p_legajo'].",
						 ".$datos['p_apellido'].",
						 ".$datos['p_nombre'].",
						 ".$datos['p_sexo'].",
						 ".$datos['p_grupo_sanguineo'].",
						 ".$datos['p_factor_sanguineo'].",
						 ".$datos['p_tipo_documento'].",
						 ".$datos['p_nro_documento'].",
						 ".$datos['p_cuil'].",
						 ".$datos['p_foto'].",
						 ".$datos['p_fecha_nac'].",
						 ".$datos['p_lugar_nac'].",
						 ".$datos['p_provincia'].",
						 ".$datos['p_pais'].",
						 ".$datos['p_nacionalidad'].",
						 ".$datos['p_estado_civil'].",
						 ".$datos['p_calle_legal'].",
						 ".$datos['p_numero_legal'].",
						 ".$datos['p_piso_legal'].",
						 ".$datos['p_depto_legal'].",
						 ".$datos['p_entre_calles_legal'].",
						 ".$datos['p_zona_barrio_legal'].",
						 ".$datos['p_pais_legal'].",
						 ".$datos['p_provincia_legal'].",
						 ".$datos['p_localidad_legal'].",
						 ".$datos['p_telefono_legal'].",
						 ".$datos['p_calle_real'].",
						 ".$datos['p_numero_real'].",
						 ".$datos['p_piso_real'].",
						 ".$datos['p_depto_real'].",
						 ".$datos['p_entre_calles_real'].",
						 ".$datos['p_zona_barrio_real'].",
						 ".$datos['p_pais_real'].",
						 ".$datos['p_provincia_real'].",
						 ".$datos['p_localidad_real'].",
						 ".$datos['p_telefono_real'].",
						 ".$datos['p_celular_real'].",
						 ".$datos['p_tel_mensajes_real'].",
						 ".$datos['p_fecha_ingreso_planta_politica'].",
						 ".$datos['p_fecha_ingreso_planta_permanente'].",
						 ".$datos['p_mail']."
						)";

		if (!$this->ejecutarQuery($query))
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			// SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "ALTA";
			$datos_log['tabla'] = $this->tabla_personal;
			$datos_log['legajo'] = $datos['p_legajo'];
			$datos_log['observaciones'] = "Se ingresa un Empleado con legajo ".$datos['p_legajo'].".";

			// SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function modificar($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$datos = $this->validarDatos($datos);

		$query = "UPDATE ".$this->tabla_personal."
				  SET p_apellido = ".$datos['p_apellido'].",
					  p_nombre = ".$datos['p_nombre'].",
					  p_sexo = ".$datos['p_sexo'].",
					  p_grupo_sanguineo = ".$datos['p_grupo_sanguineo'].",
					  p_factor_sanguineo = ".$datos['p_factor_sanguineo'].",
					  p_tipo_documento = ".$datos['p_tipo_documento'].",
					  p_nro_documento = ".$datos['p_nro_documento'].",
					  p_cuil = ".$datos['p_cuil'].",
					  p_foto = ".$datos['p_foto'].",
					  p_fecha_nac = ".$datos['p_fecha_nac'].",
					  p_lugar_nac = ".$datos['p_lugar_nac'].",
					  p_provincia = ".$datos['p_provincia'].",
					  p_pais = ".$datos['p_pais'].",
					  p_nacionalidad = ".$datos['p_nacionalidad'].",
					  p_estado_civil = ".$datos['p_estado_civil'].",
					  p_calle_legal = ".$datos['p_calle_legal'].",
					  p_numero_legal = ".$datos['p_numero_legal'].",
					  p_piso_legal = ".$datos['p_piso_legal'].",
					  p_depto_legal = ".$datos['p_depto_legal'].",
					  p_entre_calles_legal = ".$datos['p_entre_calles_legal'].",
					  p_zona_barrio_legal = ".$datos['p_zona_barrio_legal'].",
					  p_pais_legal = ".$datos['p_pais_legal'].",
					  p_provincia_legal = ".$datos['p_provincia_legal'].",
					  p_localidad_legal = ".$datos['p_localidad_legal'].",
					  p_telefono_legal = ".$datos['p_telefono_legal'].",
					  p_calle_real = ".$datos['p_calle_real'].",
					  p_numero_real = ".$datos['p_numero_real'].",
					  p_piso_real = ".$datos['p_piso_real'].",
					  p_depto_real = ".$datos['p_depto_real'].",
					  p_entre_calles_real = ".$datos['p_entre_calles_real'].",
					  p_zona_barrio_real = ".$datos['p_zona_barrio_real'].",
					  p_pais_real = ".$datos['p_pais_real'].",
					  p_provincia_real = ".$datos['p_provincia_real'].",
					  p_localidad_real = ".$datos['p_localidad_real'].",
					  p_telefono_real = ".$datos['p_telefono_real'].",
					  p_celular_real = ".$datos['p_celular_real'].",
					  p_tel_mensajes_real = ".$datos['p_tel_mensajes_real'].",
					  p_fecha_ingreso_planta_politica = ".$datos['p_fecha_ingreso_planta_politica'].",
					  p_fecha_ingreso_planta_permanente = ".$datos['p_fecha_ingreso_planta_permanente'].",
					  p_mail = ".$datos['p_mail']."
				  WHERE p_legajo = ".$datos['p_legajo']."
				";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion']     = "MODIFICA";
			$datos_log['tabla']         = $this->tabla_personal;
			$datos_log['legajo']        = $datos['p_legajo'];
			$datos_log['observaciones'] = "Se modifica un Empleado con legajo ".$datos['p_legajo'];

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function eliminar($legajo)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		// SE ELIMINAN LOS ANTECEDENTES LABORALES DEL LEGAJO
		$queryAL = "DELETE FROM ".$this->tabla_antecedentes_laborales." WHERE al_legajo = ".$legajo."";

		if ( !$this->ejecutarQuery($queryAL, $conexion) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			// SE ELIMINAN LAS AREAS DONDE ESTUVO EL LEGAJO
			$queryA = "DELETE FROM ".$this->tabla_areas." WHERE a_legajo = ".$legajo."";

			if ( !$this->ejecutarQuery($queryA, $conexion) )
			{
				$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
				return false;
			}
			else
			{
				// SE ELIMINAN LOS CARGOS QUE OCUPÓ EL LEGAJO
				$queryC = "DELETE FROM ".$this->tabla_cargos." WHERE c_legajo = ".$legajo."";

				if ( !$this->ejecutarQuery($queryC, $conexion) )
				{
					$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
					return false;
				}
				else
				{
					// SE ELIMINAN LAS ESTUDIOS DEL LEGAJO
					$queryE = "DELETE FROM ".$this->tabla_estudios." WHERE e_legajo = ".$legajo."";

					if ( !$this->ejecutarQuery($queryE, $conexion) )
					{
						$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
						return false;
					}
					else
					{
						// SE ELIMINA EL GRUPO FAMILIAR DEL LEGAJO
						$queryF = "DELETE FROM ".$this->tabla_familiares." WHERE f_legajo_emp = ".$legajo."";

						if ( !$this->ejecutarQuery($queryF, $conexion) )
						{
							$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
							return false;
						}
						else
						{
							// SE ELIMINA EL LEGAJO
							$query = "DELETE FROM ".$this->tabla_personal." WHERE p_legajo = ".$legajo."";

							if ( !$this->ejecutarQuery($query) )
							{
								$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
								return false;
							}
							else
							{
								$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

								$this->desconectar($conexion);

								// SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
								$modelo = new auditoriaPersonalModel();

								$datos_log = Array();
								$datos_log['operacion'] = "BAJA";
								$datos_log['tabla'] = $this->tabla_personal;
								$datos_log['legajo'] = $legajo;
								$datos_log['observaciones'] = "Se da de baja el Legajo ".$legajo;

								// SE CARGA EN auditoria EL MOVIMIENTO
								$modelo->registrarMovimiento($datos_log);
							}
						}
					}
				}
			}
		}

		return true;
    }

    public function obtenerAreas($plegajo)
	{
		$conexion = $this->conectar();

		$sql = "SELECT CA.ca_id, CA.ca_nombre, A.a_fecha_alta, A.a_fecha_baja, A.a_observaciones
				FROM ".$this->tabla_codareas." AS CA
				INNER JOIN ".$this->tabla_areas." AS A
				ON A.a_id_area = CA.ca_id
				AND A.a_legajo = ".$plegajo."
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

    public function obtenerCargos($plegajo)
	{
		$conexion = $this->conectar();

		$sql = "SELECT CC.cc_nomenclador, CC.cc_nombre, C.*
				FROM ".$this->tabla_codcargos." AS CC
				INNER JOIN ".$this->tabla_cargos." AS C
				ON C.c_nomenclador = CC.cc_nomenclador
				AND C.c_legajo = ".$plegajo."
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

    public function obtenerCargosPorTipo($tipo_cargo)
    {
    	$conexion = $this->conectar();

    	$sql = "SELECT *
				FROM ".$this->tabla_codcargos."
				WHERE cc_tipo = '".$tipo_cargo."'
			   ";

    	$resultado = $this->ejecutarQuery($sql);

    	$datos = $this->crearVector($resultado);

    	$this->desconectar($conexion);

    	return $datos;
    }

    public function obtenerConcejales($cod_bloque = 0, $activos = 0)
	{
		$conexion = $this->conectar();

		$filtro = "";

		// SE FILTRA POR BLOQUE
		if ( $cod_bloque != 0 )
		{
			$filtro .= " AND p_legajo IN ( SELECT a_legajo
										   FROM ".$this->tabla_areas."
										   WHERE a_id_area IN ( SELECT ca_id
															    FROM ".$this->tabla_codareas."
															    WHERE ca_id = '".$cod_bloque."'
															  )
										 )
					   ";
		}

		// PARA LISTAR SOLO LOS ACTIVOS
		if ( $activos  == '1' )
		{
			$filtro .= " AND p_legajo IN ( SELECT c_legajo
										   FROM ".$this->tabla_cargos."
										   WHERE c_nomenclador = ( SELECT cc_nomenclador
																   FROM ".$this->tabla_codcargos."
															       WHERE cc_nomenclador = ".$this->id_cargo_concejal."
														         )
										   AND c_fecha_alta = ( SELECT MAX( c_fecha_alta )
																FROM ".$this->tabla_cargos."
																WHERE c_legajo = p_legajo
															  )
										   AND c_fecha_baja IS NULL
										 )
					   ";
		}

		$sql = "SELECT p_legajo, p_apellido, p_nombre
				FROM ".$this->tabla_personal."
				WHERE p_legajo IN ( SELECT c_legajo
									FROM ".$this->tabla_cargos."
									WHERE c_nomenclador = ( SELECT cc_nomenclador
															FROM ".$this->tabla_codcargos."
															WHERE cc_nomenclador = ".$this->id_cargo_concejal."
														  )
								  )
				".$filtro."
				ORDER BY p_apellido, p_nombre
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

    public function validarArea($datos)
    {
		if ( isset($datos['a_fecha_alta']) && $this->esFechaValida($datos['a_fecha_alta']) )
			$datos['a_fecha_alta'] = "'".$this->formatearFechaMySQL($datos['a_fecha_alta'])."'";

		$datos['a_fecha_baja'] = $this->revisarValorFechaAtributo($datos['a_fecha_baja']);

		$datos['a_observaciones'] = $this->revisarValorAtributo($datos['a_observaciones']);

		return $datos;
    }

    /**
     * Se ingresa un legajo (y sus dependientes si es Cjal.) a un área determinada
     *
     * @param array $datos
     * @return boolean
     */
    public function insertarArea($datos)
    {
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		// SE OBTIENE LA FECHA ANTERIOR A LA FECHA DE ALTA RECIBIDA, EN FORMATO yyyy-mm-dd
		$fecha_ayer = $this->obtenerFechaAyer($this->formatearFechaMySQL($datos['a_fecha_alta']));

		// SE VALIDAN LOS DATOS RECIBIDOS
		$datos = $this->validarArea($datos);

		// PRIMERO SE VERIFICA SI YA TRABAJÓ EN DICHA FECHA
		$query = "SELECT a_legajo
				  FROM ".$this->tabla_areas."
				  WHERE a_legajo = ".$datos['a_legajo']."
				  AND a_fecha_alta = ".$datos['a_fecha_alta']."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$verificacion = $this->crearVector($resultado);

		// SI NO TRABAJÓ EN DICHA FECHA
		if ( $verificacion[0]['a_legajo'] == '' )
		{
			// SE ASIGNA LA FECHA DE BAJA EN EL AREA QUE DEJA DE POSEER,
			// CON UN DIA ANTERIOR A LA FECHA DE ALTA DEL NUEVO AREA, SIEMPRE QUE SEA MAYOR A LA FECHA DE ALTA DEL AREA ANTERIOR
			$query = "UPDATE ".$this->tabla_areas."
					  SET a_fecha_baja = '".$fecha_ayer."'
					  WHERE a_legajo = ".$datos['a_legajo']."
					  AND a_fecha_baja IS NULL
					  AND a_fecha_alta < '".$fecha_ayer."'
					 ";

			if ( !$this->ejecutarQuery($query) )
			{
				$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
				return false;
			}
			else
			{
				// SE REGISTRA EL Area EN DICHA FECHA
				$query = "INSERT INTO ".$this->tabla_areas." (a_legajo, a_fecha_alta, a_fecha_baja, a_id_area, a_observaciones)
						  VALUES(".$datos['a_legajo'].",
								 ".$datos['a_fecha_alta'].",
								 ".$datos['a_fecha_baja'].",
								'".$datos['a_id_area']."',
								 ".$datos['a_observaciones']."
								)
						 ";

				if ( !$this->ejecutarQuery($query) )
				{
					$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
					return false;
				}
			}
		}
		else
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}

		$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

		$this->desconectar($conexion);

		//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
		$modelo = new auditoriaPersonalModel();

		$datos_log = Array();
		$datos_log['operacion'] = "ALTA";
		$datos_log['tabla'] = $this->tabla_areas;
		$datos_log['legajo'] = $datos['a_legajo'];
		$datos_log['observaciones'] = "Se ingresa un Area para el Legajo ".$datos['a_legajo'];

		//SE CARGA EN auditoria EL MOVIMIENTO
		$modelo->registrarMovimiento($datos_log);

		return true;
    }

    /**
     * Se modifica el área asignada a un legajo determinado
     *
     * @param array $datos
     * @return boolean
     */
    public function modificarArea($datos)
    {
		$conexion = $this->conectar();

		$datos = $this->validarArea($datos);

		$query = "UPDATE ".$this->tabla_areas."
				  SET a_fecha_baja = ".$datos['a_fecha_baja'].",
					  a_id_area = '".$datos['a_id_area']."',
					  a_observaciones = ".$datos['a_observaciones']."
				  WHERE a_legajo = ".$datos['a_legajo']."
				  AND a_fecha_alta = ".$datos['a_fecha_alta']."
				 ";

		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {
			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "MODIFICA";
			$datos_log['tabla'] = $this->tabla_areas;
			$datos_log['legajo'] = $datos['a_legajo'];
			$datos_log['observaciones'] = "Se modifica un Area para el Legajo ".$datos['a_legajo'];

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function eliminarArea()
    {
	   	$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		if ( isset($this->filtro['fecha_alta']) )
		{
			$fecha_alta = "'".$this->filtro['fecha_alta']."'";
		}

		// SE ELIMINA EL AREA DE UN LEGAJO PARA UNA FECHA DE ALTA DETERMINADA
		$query = "DELETE FROM ".$this->tabla_areas."
				  WHERE a_legajo = ".$this->filtro['legajo']."
				  AND a_fecha_alta = ".$fecha_alta."
				  AND a_id_area = '".$this->filtro['id_area']."'
				 ";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "ALTA";
			$datos_log['tabla'] = $this->tabla_areas;
			$datos_log['legajo'] = $this->filtro['legajo'];
			$datos_log['observaciones'] = "Se elimina un Area para el Legajo ".$this->filtro['legajo'];

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

	public function esValidaFechaAlta($datos)
	{
		$c_fecha_alta = $this->formatearFechaMySQL($datos['c_fecha_alta']);

		$conexion = $this->conectar();

		// SE VERIFICA SI LA FECHA DE ALTA ES MAYOR O IGUAL A LA FECHA DE INGRESO AL MUNICIPIO
		$query = "SELECT p_legajo
				  FROM ".$this->tabla_personal."
				  WHERE p_legajo = ".$datos['c_legajo']."
				  AND ( p_fecha_ingreso_planta_politica <= '".$c_fecha_alta."' OR p_fecha_ingreso_planta_permanente <= '".$c_fecha_alta."' )
				 ";

		$resultado = $this->ejecutarQuery($query);

		$verificacion = $this->obtenerFila($resultado);

		// SI ES MAYOR O IGUAL A LA FECHA DE INGRESO AL MUNICIPIO
		if ( $verificacion['p_legajo'] )
		{
			// SE VERIFICA SI YA POSEE UN CARGO
			$query = "SELECT c_legajo
					  FROM ".$this->tabla_cargos."
					  WHERE c_legajo = ".$datos['c_legajo']."
					 ";

			$resultado = $this->ejecutarQuery($query);

			$verificacion = $this->obtenerFila($resultado);
			// SI POSEE UN CARGO
			if ( $verificacion['c_legajo'] )
			{
				// SE VERIFICA SI LA FECHA DE ALTA AL NUEVO CARGO ES MAYOR A LA FECHA DE BAJA DEL CARGO ANTERIOR (SI TIENE)
				$query = "SELECT C.c_legajo
						  FROM ".$this->tabla_cargos." AS C
						  WHERE C.c_legajo = ".$datos['c_legajo']."
						  AND C.c_fecha_alta = ( SELECT MAX( c_fecha_alta )
												 FROM ".$this->tabla_cargos."
												 WHERE c_legajo = C.c_legajo
											   )
						  AND ( C.c_fecha_baja IS NULL OR C.c_fecha_baja < '".$c_fecha_alta."' )
						 ";

				$resultado = $this->ejecutarQuery($query);

				$verificacion = $this->obtenerFila($resultado);

				// SI LA FECHA DE ALTA ES VALIDA O NO
				return ( $verificacion['c_legajo'] );
			}
			else // SI NO TENIA UN CARGO, LA FECHA DE ALTA ES VALIDA
				return true;
		}
		else // NO ES VALIDA LA FECHA DE ALTA
			return false;
	}

    public function validarCargo($datos)
	{
		// FECHA DE ALTA
		$datos['c_fecha_alta'] = $this->revisarValorFechaAtributo($datos['c_fecha_alta']);

		// DECRETO DE ALTA
		$datos['c_nro_decreto_alta'] = $this->revisarValorAtributo($datos['c_nro_decreto_alta']);

		// FECHA DE BAJA
		$datos['c_fecha_baja'] = $this->revisarValorFechaAtributo($datos['c_fecha_baja']);

		// LIQUIDACION PENDIENTE
		$datos['c_liquidacion_pendiente'] = $this->revisarValorAtributo($datos['c_liquidacion_pendiente'], "0");

		// DECRETO DE BAJA
		$datos['c_nro_decreto_baja'] = $this->revisarValorAtributo($datos['c_nro_decreto_baja']);

		// NOMENCLADOR
		$datos['c_nomenclador'] = $this->revisarValorAtributo($datos['c_nomenclador']);

		// DIGITO
		$datos['c_digito'] = $this->revisarValorAtributo($datos['c_digito'], "1");

		// DEPENDE DE
		$datos['c_depende_de'] = $this->revisarValorAtributo($datos['c_depende_de']);

		// PERTENECE A SECRETARIA BLOQUE
		$datos['c_pertenece_secretaria_bloque'] = $this->revisarValorAtributo($datos['c_pertenece_secretaria_bloque'], "0");

		// OBSERVACIONES
		$datos['c_observaciones'] = $this->revisarValorAtributo($datos['c_observaciones']);

		return $datos;
    }

    public function insertarCargo($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		// 14/08/2012: SE OBTIENE LA FECHA ANTERIOR A LA FECHA DE ALTA RECIBIDA, EN FORMATO yyyy-mm-dd
		$fecha_ayer = $this->obtenerFechaAyer($this->formatearFechaMySQL($datos['c_fecha_alta']));

		// SE VALIDAN LOS DATOS RECIBIDOS
		$datos = $this->validarCargo($datos);

		// SE VERIFICA SI EL EMPLEADO NO POSEE UN CARGO EN LA FECHA RESPECTIVA
		$query = "SELECT c_legajo
				  FROM ".$this->tabla_cargos."
				  WHERE c_legajo = ".$datos['c_legajo']."
				  AND c_fecha_alta = ".$datos['c_fecha_alta']."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$verificacion = $this->obtenerFila($resultado);

		// SI EL EMPLEADO NO POSEE UN CARGO EN DICHA FECHA
		if ( !$verificacion['c_legajo'] )
		{
			// SE ASIGNA LA FECHA DE BAJA EN EL CARGO QUE DEJA DE POSEER,
			// CON UN DIA ANTERIOR A LA FECHA DE ALTA DEL NUEVO CARGO,
			// SIEMPRE QUE SEA MAYOR O IGUAL A LA FECHA DE ALTA DEL CARGO ANTERIOR
			$query = "UPDATE ".$this->tabla_cargos."
					  SET c_fecha_baja = '".$fecha_ayer."'
					  WHERE c_legajo = ".$datos['c_legajo']."
					  AND c_fecha_baja IS NULL
					  AND c_fecha_alta <= '".$fecha_ayer."'
					 ";

			if ( !$this->ejecutarQuery($query) )
			{
				$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
				return false;
			}
			else
			{
				// SE REGISTRA EL NUEVO CARGO
				$query = "INSERT INTO ".$this->tabla_cargos." (c_legajo, c_fecha_alta, c_nro_decreto_alta, c_fecha_baja, c_liquidacion_pendiente, c_nro_decreto_baja, c_nomenclador, c_digito, c_depende_de, c_pertenece_secretaria_bloque, c_observaciones, c_modificado_por)
						  VALUES(".$datos['c_legajo'].",
								 ".$datos['c_fecha_alta'].",
								 ".$datos['c_nro_decreto_alta'].",
								 ".$datos['c_fecha_baja'].",
								 ".$datos['c_liquidacion_pendiente'].",
								 ".$datos['c_nro_decreto_baja'].",
								 ".$datos['c_nomenclador'].",
								 ".$datos['c_digito'].",
								 ".$datos['c_depende_de'].",
								 ".$datos['c_pertenece_secretaria_bloque'].",
								 ".$datos['c_observaciones'].",
								 ".$_SESSION['id_usuario']."
								)
						 ";

				if ( !$this->ejecutarQuery($query) )
				{
					$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
					return false;
				}
			}
		}
		else // SI POSEE UN CARGO EN ESA FECHA SE EVITA EL INSERT
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}

		$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

		$this->desconectar($conexion);

		//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
		$modelo = new auditoriaPersonalModel();

		$datos_log = Array();
		$datos_log['operacion'] = "ALTA";
		$datos_log['tabla'] = $this->tabla_cargos;
		$datos_log['legajo'] = $datos['c_legajo'];
		$datos_log['observaciones'] = "Se ingresa un Cargo para el Legajo ".$datos['c_legajo'];

		//SE CARGA EN auditoria EL MOVIMIENTO
		$modelo->registrarMovimiento($datos_log);

		return true;
    }

    public function modificarCargo($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$datos = $this->validarCargo($datos);

		$query = "UPDATE ".$this->tabla_cargos."
				  SET c_nro_decreto_alta = ".$datos['c_nro_decreto_alta'].",
					  c_fecha_baja = ".$datos['c_fecha_baja'].",
					  c_liquidacion_pendiente = ".$datos['c_liquidacion_pendiente'].",
					  c_nro_decreto_baja = ".$datos['c_nro_decreto_baja'].",
					  c_nomenclador = ".$datos['c_nomenclador'].",
					  c_digito = ".$datos['c_digito'].",
					  c_depende_de = ".$datos['c_depende_de'].",
					  c_pertenece_secretaria_bloque = ".$datos['c_pertenece_secretaria_bloque'].",
					  c_observaciones = ".$datos['c_observaciones'].",
					  c_modificado_por = ".$_SESSION['id_usuario']."
				  WHERE c_legajo = ".$datos['c_legajo']."
				  AND c_fecha_alta = ".$datos['c_fecha_alta']."
				 ";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "MODIFICA";
			$datos_log['tabla'] = $this->tabla_cargos;
			$datos_log['legajo'] = $datos['c_legajo'];
			$datos_log['observaciones'] = "Se modifica un Cargo para el Legajo ".$datos['c_legajo'];

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function eliminarCargo()
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		// SE ELIMINA EL CARGO ESPECIFICO DEL EMPLEADO
		$query = "DELETE FROM ".$this->tabla_cargos."
				  WHERE c_legajo = ".$this->filtro['legajo']."
				  AND c_fecha_alta = '".$this->filtro['fecha_alta']."'
				  AND c_nomenclador = '".$this->filtro['nomenclador']."'
			 ";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "ALTA";
			$datos_log['tabla'] = $this->tabla_cargos;
			$datos_log['legajo'] = $this->filtro['legajo'];
			$datos_log['observaciones'] = "Se elimina un Cargo para el Legajo ".$this->filtro['legajo'];

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function buscarApellidoNombre($c_depende_de)
	{
		$conexion = $this->conectar();

		$sql = "SELECT p_apellido, p_nombre
				FROM ".$this->tabla_personal."
				WHERE p_legajo = ".$c_depende_de."
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
    }

    public function obtenerRegistroArea($legajo, $fecha_alta, $id_area)
	{
		$conexion = $this->conectar();

		$sql = "SELECT * FROM ".$this->tabla_areas."
				WHERE a_legajo = ".$legajo."
				AND a_fecha_alta = '".$fecha_alta."'
				AND a_id_area = ".$id_area."
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
    }

    public function obtenerRegistroCargo($legajo, $fecha_alta, $id_cargo)
	{
		$conexion = $this->conectar();

		$sql = "SELECT * FROM ".$this->tabla_cargos."
				WHERE c_legajo = ".$legajo."
				AND c_fecha_alta = '".$fecha_alta."'
				AND c_nomenclador = ".$id_cargo."
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
    }

    public function obtenerEstudios($plegajo, $pfecha = '')
	{
		$conexion = $this->conectar();

		// SI SE DESEA MODIFICAR UN Estudio, SE LO BUSCA POR e_fecha PARA EDITARLO
		$filtro_por_fecha = ( $pfecha != '' ) ? " AND e_fecha = '".$pfecha."'" : "";

		$sql = "SELECT *
				FROM ".$this->tabla_estudios."
				WHERE e_legajo = ".$plegajo."
				".$filtro_por_fecha."
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

    public function insertarEstudio($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		// SE VALIDAN LOS DATOS RECIBIDOS
		$datos = $this->validarEstudio($datos);

		// SE VERIFICA SI YA POSEE DICHO Estudio EN LA FECHA
		$query = "SELECT e_legajo
				  FROM ".$this->tabla_estudios."
				  WHERE e_legajo = ".$datos['e_legajo']."
				  AND e_fecha = ".$datos['e_fecha']."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$verificacion = $this->crearVector($resultado);

		// SI NO POSEE DICHO Estudio EN LA FECHA
		if (!$verificacion[0]['e_legajo']){

			// SE REGISTRA EL Area PARA DICHA FECHA
			$query = "INSERT INTO ".$this->tabla_estudios." (e_legajo, e_fecha, e_titulo, e_organismo, e_tipo_estudio, e_observaciones)
					  VALUES(".$datos['e_legajo'].",
							 ".$datos['e_fecha'].",
							 ".$datos['e_titulo'].",
							 ".$datos['e_organismo'].",
							 ".$datos['e_tipo_estudio'].",
							 ".$datos['e_observaciones']."
							)
					 ";

			if ( !$this->ejecutarQuery($query) )
			{
				$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
				return false;
			}
		}

		$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

		$this->desconectar($conexion);

		//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
		$modelo = new auditoriaPersonalModel();

		$datos_log = Array();
		$datos_log['operacion'] = "ALTA";
		$datos_log['tabla'] = $this->tabla_estudios;
		$datos_log['legajo'] = $datos['e_legajo'];
		$datos_log['observaciones'] = "Se ingresa un Estudio para el Legajo ".$datos['e_legajo'];

		//SE CARGA EN auditoria EL MOVIMIENTO
		$modelo->registrarMovimiento($datos_log);

		return true;
    }

    public function modificarEstudio($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$datos = $this->validarEstudio($datos);

		$query = "UPDATE ".$this->tabla_estudios."
				  SET e_titulo = ".$datos['e_titulo'].",
					  e_organismo = ".$datos['e_organismo'].",
					  e_tipo_estudio = ".$datos['e_tipo_estudio'].",
					  e_observaciones = ".$datos['e_observaciones']."
				  WHERE e_legajo = ".$datos['e_legajo']."
				  AND e_fecha = ".$datos['e_fecha']."
				 ";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "MODIFICA";
			$datos_log['tabla'] = $this->tabla_estudios;
			$datos_log['legajo'] = $datos['e_legajo'];
			$datos_log['observaciones'] = "Se modifica un Estudio para el Legajo ".$datos['e_legajo'];

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function eliminarEstudio($plegajo, $pfecha)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		// SE REGISTRA LA FECHA DE EGRESO DEL EMPLEADO EN EL ULTIMO PUESTO QUE HA TENIDO
		$query = "DELETE FROM ".$this->tabla_estudios."
				  WHERE e_legajo = ".$plegajo."
				  AND e_fecha = '".$pfecha."'
				 ";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "ALTA";
			$datos_log['tabla'] = $this->tabla_estudios;
			$datos_log['legajo'] = $plegajo;
			$datos_log['observaciones'] = "Se elimina un Estudio para el Legajo ".$plegajo;

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);

		}

		return true;
    }

    public function validarEstudio($datos)
	{
		// TITULO
		$datos['e_titulo'] = $this->revisarValorAtributo($datos['e_titulo']);

		// FECHA
		$datos['e_fecha'] = $this->revisarValorFechaAtributo($datos['e_fecha']);

		// ORGANISMO
		$datos['e_organismo'] = $this->revisarValorAtributo($datos['e_organismo']);

		// TIPO DE ESTUDIO
		$datos['e_tipo_estudio'] = $this->revisarValorAtributo($datos['e_tipo_estudio'], '6');

		// OBSERVACIONES
		$datos['e_observaciones'] = $this->revisarValorAtributo($datos['e_observaciones']);

		return $datos;
    }

    //	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
    public function verificarEstudioEntero()
	{
		$filtro_e_fecha = $this->adaptarValorStringParaFiltro('e_fecha');

		$filtro_e_titulo = $this->adaptarValorStringParaFiltro('e_titulo');

		$filtro_e_organismo = $this->adaptarValorStringParaFiltro('e_organismo');

		$filtro_e_tipo_estudio = ( $_SESSION['e_tipo_estudio_original'] != '' ) ? " AND e_tipo_estudio = ".$_SESSION['e_tipo_estudio_original']."" : " AND e_tipo_estudio = 6";

		$filtro_e_observaciones = $this->adaptarValorStringParaFiltro('e_observaciones');

		$conexion = $this->conectar();

		$query = "SELECT e_legajo
				  FROM ".$this->tabla_estudios."
				  WHERE e_legajo = ".$_SESSION['e_legajo_original']."
				  ".$filtro_e_fecha."
				  ".$filtro_e_titulo."
				  ".$filtro_e_organismo."
				  ".$filtro_e_tipo_estudio."
				  ".$filtro_e_observaciones."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( $dato['e_legajo'] );
    }

    public function obtenerFamilia($legajo, $id = 0)
	{
		$conexion = $this->conectar();

		// SI SE DESEA MODIFICAR UN INTEGRANTE DEL GRUPO FAMILIAR DETERMINADO, SE LO BUSCA POR f_id PARA EDITARLO
		$filtro_por_id = ( $id != 0 ) ? " AND f_id = ".$id : "";

		$sql = "SELECT *
				FROM ".$this->tabla_familiares."
				WHERE f_legajo_emp = ".$legajo."
				".$filtro_por_id."
				AND f_anulado = 0
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

    public function yaPoseeParentesco($legajo, $parentesco)
	{
		$conexion = $this->conectar();

		$sql = "SELECT COUNT(f_legajo_emp) AS cantidad
				FROM ".$this->tabla_familiares."
				WHERE f_legajo_emp = ".$legajo."
				AND f_parentesco = '".$parentesco."'
				AND f_parentesco <> 'Hijo'
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		// SI NO POSEE DICHO PARENTESCO
		$this->desconectar($conexion);

		return ( $dato['cantidad'] );
    }

    public function validarFamiliar($datos)
	{
		// NRO DOCUMENTO
		$datos['f_nro_documento']            = $this->revisarValorAtributo($datos['f_nro_documento']);

		// PARENTESCO
		$datos['f_parentesco']               = $this->revisarValorAtributo($datos['f_parentesco']);

		// APELLIDO
		$datos['f_apellido_familiar']        = $this->revisarValorAtributo($datos['f_apellido_familiar']);

		// NOMBRE
		$datos['f_nombre_familiar']          = $this->revisarValorAtributo($datos['f_nombre_familiar']);

		// VIVE
		$datos['f_vive']                     = $this->revisarValorAtributo($datos['f_vive'], 'si');

		// FECHA NACIMIENTO
		$datos['f_fecha_nac']                = $this->revisarValorFechaAtributo($datos['f_fecha_nac']);

		// NACIONALIDAD
		$datos['f_nacionalidad']             = $this->revisarValorAtributo($datos['f_nacionalidad']);

		// SEXO
		$datos['f_sexo']                     = $this->revisarValorAtributo($datos['f_sexo']);

		// FECHA INICIO CONVIVENCIA
		$datos['f_fecha_inicio_convivencia'] = $this->revisarValorFechaAtributo($datos['f_fecha_inicio_convivencia']);

		// DISCAPACITADO
		$datos['f_discapacitado']            = $this->revisarValorAtributo($datos['f_discapacitado']);

		// ESTUDIOS
		$datos['f_estudios']                 = $this->revisarValorAtributo($datos['f_estudios']);

		// OBSERVACIONES
		$datos['f_observaciones']            = $this->revisarValorAtributo($datos['f_observaciones']);

		return $datos;
    }

    public function insertarFamiliar($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$datos = $this->validarFamiliar($datos);

		$query = "INSERT INTO ".$this->tabla_familiares." (f_legajo_emp, f_id, f_nro_documento, f_parentesco, f_apellido, f_nombre, f_vive, f_fecha_nac, f_nacionalidad, f_sexo, f_fecha_inicio_convivencia, f_discapacitado, f_estudios, f_observaciones, f_anulado)
				  VALUES(".$datos['f_legajo_emp'].",
						 null,
						 ".$datos['f_nro_documento'].",
						 ".$datos['f_parentesco'].",
						 ".$datos['f_apellido_familiar'].",
						 ".$datos['f_nombre_familiar'].",
						 ".$datos['f_vive'].",
						 ".$datos['f_fecha_nac'].",
						 ".$datos['f_nacionalidad'].",
						 ".$datos['f_sexo'].",
						 ".$datos['f_fecha_inicio_convivencia'].",
						 ".$datos['f_discapacitado'].",
						 ".$datos['f_estudios'].",
						 ".$datos['f_observaciones'].",
						 0
						)
				  ";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "ALTA";
			$datos_log['tabla'] = $this->tabla_familiares;
			$datos_log['legajo'] = $datos['f_legajo_emp'];
			$datos_log['observaciones'] = "Se ingresa un Familiar para el Legajo ".$datos['f_legajo_emp'];

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function modificarFamiliar($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$datos = $this->validarFamiliar($datos);

		$query = "UPDATE ".$this->tabla_familiares."
				  SET f_nro_documento = ".$datos['f_nro_documento'].",
					  f_parentesco = ".$datos['f_parentesco'].",
					  f_apellido = ".$datos['f_apellido_familiar'].",
					  f_nombre = ".$datos['f_nombre_familiar'].",
					  f_vive = ".$datos['f_vive'].",
					  f_fecha_nac = ".$datos['f_fecha_nac'].",
					  f_nacionalidad = ".$datos['f_nacionalidad'].",
					  f_sexo = ".$datos['f_sexo'].",
					  f_fecha_inicio_convivencia = ".$datos['f_fecha_inicio_convivencia'].",
					  f_discapacitado = ".$datos['f_discapacitado'].",
					  f_estudios = ".$datos['f_estudios'].",
					  f_observaciones = ".$datos['f_observaciones']."
				  WHERE f_legajo_emp = ".$datos['f_legajo_emp']."
				  AND f_id = ".$datos['f_id']."
				 ";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "MODIFICA";
			$datos_log['tabla'] = $this->tabla_familiares;
			$datos_log['legajo'] = $datos['f_legajo_emp'];
			$datos_log['observaciones'] = "Se modifica el Familiar del Legajo ".$datos['f_legajo_emp'];

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function eliminarFamiliar($legajo, $id)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$query = "DELETE FROM ".$this->tabla_familiares."
				  WHERE f_legajo_emp = ".$legajo."
				  AND f_id = ".$id."
				 ";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "BAJA";
			$datos_log['tabla'] = $this->tabla_familiares;
			$datos_log['legajo'] = $legajo;
			$datos_log['observaciones'] = "Se elimina un integrante del grupo familiar del Legajo ".$legajo;

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function verificarFamiliarEntero()
    {
		$filtro_f_nro_documento            = $this->adaptarValorNumericoParaFiltro('f_nro_documento');

		$filtro_f_parentesco               = $this->adaptarValorStringParaFiltro('f_parentesco');

		$filtro_f_apellido                 = $this->adaptarValorStringParaFiltro('f_apellido');

		$filtro_f_nombre                   = $this->adaptarValorStringParaFiltro('f_nombre');

		$filtro_f_vive                     = $this->adaptarValorStringParaFiltro('f_vive');

		$filtro_f_fecha_nac                = $this->adaptarValorStringParaFiltro('f_fecha_nac');

		$filtro_f_nacionalidad             = $this->adaptarValorStringParaFiltro('f_nacionalidad');

		$filtro_f_sexo                     = $this->adaptarValorStringParaFiltro('f_sexo');

		$filtro_f_fecha_inicio_convivencia = $this->adaptarValorStringParaFiltro('f_fecha_inicio_convivencia');

		$filtro_f_discapacitado            = $this->adaptarValorStringParaFiltro('f_discapacitado');

		$filtro_f_estudios                 = $this->adaptarValorStringParaFiltro('f_estudios');

		$conexion = $this->conectar();

		$query = "SELECT f_legajo_emp
				  FROM ".$this->tabla_familiares."
				  WHERE f_legajo_emp = ".$_SESSION['f_legajo_emp_original']."
				  AND  f_id = ".$_SESSION['f_id_original']."
				  ".$filtro_f_nro_documento."
				  ".$filtro_f_parentesco."
				  ".$filtro_f_apellido."
				  ".$filtro_f_nombre."
				  ".$filtro_f_vive."
				  ".$filtro_f_fecha_nac."
				  ".$filtro_f_nacionalidad."
				  ".$filtro_f_sexo."
				  ".$filtro_f_fecha_inicio_convivencia."
				  ".$filtro_f_discapacitado."
				  ".$filtro_f_estudios."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( $dato['f_legajo_emp'] );
    }

    public function listarAntecedentesLaborales($legajo, $id = 0)
    {
		$conexion = $this->conectar();

		// SI SE DESEA MODIFICAR UN TRABAJO DETERMINADO, SE LO BUSCA POR al_id PARA EDITARLO
		$filtro_por_id = ( $id != 0 ) ? " AND al_id = ".$id : "";

		$sql = "SELECT *
				FROM ".$this->tabla_antecedentes_laborales."
				WHERE al_legajo = ".$legajo."
				".$filtro_por_id."
				AND al_anulado = 0
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

    public function validarAntecedenteLaboral($datos)
    {
		// AMBITO
		$datos['al_ambito'] = $this->revisarValorAtributo($datos['al_ambito']);

		// EMPRESA
		$datos['al_empresa'] = $this->revisarValorAtributo($datos['al_empresa']);

		// CARGO
		$datos['al_cargo'] = $this->revisarValorAtributo($datos['al_cargo']);

		// FECHA DESDE
		$datos['al_fecha_desde'] = $this->revisarValorFechaAtributo($datos['al_fecha_desde']);

		// FECHA HASTA
		$datos['al_fecha_hasta'] = $this->revisarValorFechaAtributo($datos['al_fecha_hasta']);

		// MOTIVOS DEL CESE
		$datos['al_motivos_cese'] = $this->revisarValorAtributo($datos['al_motivos_cese']);

		// OBSERVACIONES
		$datos['al_observaciones'] = $this->revisarValorAtributo($datos['al_observaciones']);

		return $datos;
    }

    public function insertarAntecedenteLaboral($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$datos = $this->validarAntecedenteLaboral($datos);

		$query = "INSERT INTO ".$this->tabla_antecedentes_laborales." (al_legajo, al_id, al_ambito, al_empresa, al_cargo, al_fecha_desde, al_fecha_hasta, al_motivos_cese, al_observaciones, al_anulado)
				  VALUES(".$datos['al_legajo'].",
						 null,
						 ".$datos['al_ambito'].",
						 ".$datos['al_empresa'].",
						 ".$datos['al_cargo'].",
						 ".$datos['al_fecha_desde'].",
						 ".$datos['al_fecha_hasta'].",
						 ".$datos['al_motivos_cese'].",
						 ".$datos['al_observaciones'].",
						 0
						)
				  ";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "ALTA";
			$datos_log['tabla'] = $this->tabla_antecedentes_laborales;
			$datos_log['legajo'] = $datos['al_legajo'];
			$datos_log['observaciones'] = "Se ingresa un Antecedente Laboral para el Legajo ".$datos['al_legajo'];

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function modificarAntecedenteLaboral($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$datos = $this->validarAntecedenteLaboral($datos);

		$query = "UPDATE ".$this->tabla_antecedentes_laborales."
				  SET al_ambito = ".$datos['al_ambito'].",
					  al_empresa = ".$datos['al_empresa'].",
					  al_cargo = ".$datos['al_cargo'].",
					  al_fecha_desde = ".$datos['al_fecha_desde'].",
					  al_fecha_hasta = ".$datos['al_fecha_hasta'].",
					  al_motivos_cese = ".$datos['al_motivos_cese'].",
					  al_observaciones = ".$datos['al_observaciones']."
				  WHERE al_legajo = ".$datos['al_legajo']."
				  AND al_id = ".$datos['al_id']."
				 ";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "MODIFICA";
			$datos_log['tabla'] = $this->tabla_antecedentes_laborales;
			$datos_log['legajo'] = $datos['al_legajo'];
			$datos_log['observaciones'] = "Se modifica el Antecedente Laboral del Legajo ".$datos['al_legajo'];

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function eliminarAntecedenteLaboral($legajo, $id)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$query = "DELETE FROM ".$this->tabla_antecedentes_laborales."
				  WHERE al_legajo = ".$legajo."
				  AND al_id = ".$id."
				 ";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
			$modelo = new auditoriaPersonalModel();

			$datos_log = Array();
			$datos_log['operacion'] = "BAJA";
			$datos_log['tabla'] = $this->tabla_antecedentes_laborales;
			$datos_log['legajo'] = $legajo;
			$datos_log['observaciones'] = "Se elimina un Antecedente Laboral del Legajo ".$legajo;

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
    }

    public function verificarAntecedenteLaboralEntero()
    {
		$filtro_al_ambito        = $this->adaptarValorStringParaFiltro('al_ambito');

		$filtro_al_empresa       = $this->adaptarValorStringParaFiltro('al_empresa');

		$filtro_al_cargo         = $this->adaptarValorStringParaFiltro('al_cargo');

		$filtro_al_fecha_desde   = $this->adaptarValorStringParaFiltro('al_fecha_desde');

		$filtro_al_fecha_hasta   = $this->adaptarValorStringParaFiltro('al_fecha_hasta');

		$filtro_al_motivos_cese  = $this->adaptarValorStringParaFiltro('al_motivos_cese');

		$filtro_al_observaciones = $this->adaptarValorStringParaFiltro('al_observaciones');

		$conexion = $this->conectar();

		$query = "SELECT al_legajo
				  FROM ".$this->tabla_antecedentes_laborales."
				  WHERE al_legajo = ".$_SESSION['al_legajo_original']."
				  AND  al_id = ".$_SESSION['al_id_original']."
				  ".$filtro_al_ambito."
				  ".$filtro_al_empresa."
				  ".$filtro_al_cargo."
				  ".$filtro_al_fecha_desde."
				  ".$filtro_al_fecha_hasta."
				  ".$filtro_al_motivos_cese."
				  ".$filtro_al_observaciones."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( $dato['al_legajo'] );
    }

    public function obtenerNombreUltimaArea($legajo)
    {
		$conexion = $this->conectar();

		$query = "SELECT a_id_area, ( SELECT ca_nombre
								      FROM ".$this->tabla_codareas."
								      WHERE ca_id = a_id_area
								    ) AS area
				  FROM ".$this->tabla_areas."
				  WHERE a_legajo = ".$legajo."
				  ORDER BY a_fecha_alta DESC
				  LIMIT 1
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato;
	}

	/**
	 * Se obtiene información del cargo actual de un legajo determinado
	 *
	 * @param integer $legajo
	 * @return multitype:
	 */
    public function obtenerNombreUltimoCargo($legajo)
    {
		$conexion = $this->conectar();

		$query = "SELECT c_nomenclador, c_digito, c_pertenece_secretaria_bloque, c_fecha_baja,
						 ( SELECT cc_nombre
						   FROM ".$this->tabla_codcargos."
						   WHERE cc_nomenclador = c_nomenclador
						 ) AS cargo
				  FROM ".$this->tabla_cargos."
				  WHERE c_legajo = ".$legajo."
				  ORDER BY c_fecha_alta DESC
				  LIMIT 1";

		// 20/01/2020 XXXX
		// AND cc_habilitado = 1

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato;
	}

    public function obtenerDigitoActual($legajo)
    {
		$conexion = $this->conectar();

		$query = "SELECT c_digito
				  FROM ".$this->tabla_cargos."
				  WHERE c_legajo = ".$legajo."
				  ORDER BY c_fecha_alta DESC
				  LIMIT 1
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		$digito_actual = ( $dato['c_digito'] ) ? $dato['c_digito'] : 1;

		return $digito_actual;
	}

	public function verificarAreaAsignada($legajo)
	{
		$conexion = $this->conectar();

		$query = "SELECT a_legajo
				  FROM ".$this->tabla_areas."
				  WHERE a_legajo = ".$legajo."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato;
	}

	public function buscarTipoArea($c_nomenclador)
	{
		$conexion = $this->conectar();

		$query = "SELECT cc_tipo
				  FROM ".$this->tabla_codcargos."
				  WHERE cc_nomenclador = '".$c_nomenclador."'
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato;
	}

	// PERSONAL DEL MISMO AREA, SOLO PARA LOS BLOQUES
	public function listarModalDependientes($legajo)
	{
		$conexion = $this->conectar();
		/**
		quiero los empleados del mismo area que el empleado $legajo
		y el area sea de tipo BLOQUE
		y distinto a $legajo
		/**/
		$query = "SELECT *
				  FROM ".$this->tabla_personal."
				  WHERE p_habilitado = 1
				  AND p_legajo IN ( SELECT a_legajo
									FROM ".$this->tabla_areas."
									WHERE a_id_area IN ( SELECT a_id_area
														 FROM ".$this->tabla_areas."
														 WHERE a_legajo = ".$legajo."
													   )
									AND a_id_area IN ( SELECT ca_id
													   FROM ".$this->tabla_codareas."
													   WHERE ca_tipo = 'B'
													 )

								  )
				  AND p_legajo IN ( SELECT c_legajo
									FROM ".$this->tabla_cargos."
									WHERE c_nomenclador IN ( SELECT cc_nomenclador
															 FROM ".$this->tabla_codcargos."
														     WHERE cc_gente_a_cargo = 1
														   )
								  )
				  AND p_legajo <> ".$legajo."
				  ORDER BY p_apellido
				 ";

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

    public function obtenerDependeDe($legajo)
	{
		$conexion = $this->conectar();

		$sql = "SELECT P.p_apellido, P.p_nombre
				FROM ".$this->tabla_personal." AS P
				INNER JOIN
				( SELECT c_depende_de
				  FROM ".$this->tabla_cargos."
				  WHERE c_legajo = ".$legajo."
				  AND c_fecha_alta = ( SELECT MAX(c_fecha_alta)
									   FROM ".$this->tabla_cargos."
									   WHERE c_legajo = ".$legajo."
									 )
			    ) AS C
			    ON P.p_legajo = C.c_depende_de
			   ";

		$resultado = $this->ejecutarQuery($sql);

		if ( $resultado )
			$datos = $this->crearVector($resultado);
		else
			return false;

		$this->desconectar($conexion);

		return $datos;
    }

	// SE VERIFICA SI ES EL ULTIMO CARGO DE UN LEGAJO DETERMINADO
	public function esElUltimoCargo($datos)
	{
		$conexion = $this->conectar();

		$sql = "SELECT c_legajo
				FROM ".$this->tabla_cargos."
				WHERE c_legajo = '".$datos['c_legajo']."'
				AND c_fecha_alta = '".$this->formatearFechaMySQL($datos['c_fecha_alta'])."'
				AND c_fecha_alta = ( SELECT MAX( c_fecha_alta )
									 FROM ".$this->tabla_cargos."
									 WHERE c_legajo = '".$datos['c_legajo']."'
								   )
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos_sql = $this->crearVector($resultado);

		$this->desconectar($conexion);

		// SI ES EL ULTIMO CARGO
		return ( $datos_sql[0]['c_legajo'] != '' );
	}

	/**
	 * Se obtienen los legajos dependientes de un Concejal determinado
	 * @param integer $concejal
	 * @return Ambigous <NULL, array:>
	 */
	public function obtenerDependientesPorConcejal($concejal)
	{
		$conexion = $this->conectar();

		$sql = "SELECT AUX.c_legajo
				FROM ".$this->tabla_cargos." AS AUX
				WHERE AUX.c_depende_de = '".$concejal."'
				AND AUX.c_fecha_alta = ( SELECT MAX( c_fecha_alta )
										 FROM ".$this->tabla_cargos."
									     WHERE c_legajo = AUX.c_legajo
								       )
				AND AUX.c_fecha_baja IS NULL
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	// SE OBTIENE EL CONCEJAL DEL CUAL DEPENDE UN LEGAJO DETERMINADO, SI YA ESTUVIESE REGISTRADO POR UN CARGO ANTERIOR
	public function obtenerConcejalQueDepende($legajo)
	{
		$conexion = $this->conectar();

		$sql = "SELECT c_depende_de
				FROM ".$this->tabla_cargos."
				WHERE c_legajo = '".$legajo."'
				AND c_fecha_alta = ( SELECT MAX( c_fecha_alta )
									 FROM ".$this->tabla_cargos."
									 WHERE c_legajo = '".$legajo."'
								   )
				AND c_fecha_baja IS NULL
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['c_depende_de'];
	}

    public function obtenerAreasCombo()
	{
		$conexion = $this->conectar();

		$sql = "SELECT * FROM ".$this->tabla_codareas."
				WHERE ca_habilitado = 1
				ORDER BY ca_nombre
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
			// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
			$datos = $this->crearVector($resultado);
		else
			return false;

		$this->desconectar($conexion);

		return $datos;
    }

    public function obtenerCargosCombo()
	{
		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM ".$this->tabla_codcargos."
				WHERE cc_habilitado = 1
				ORDER BY cc_nombre ASC
			   ";

		// SE EJECUTA LA QUERY
		$resultado = $this->ejecutarQuery($sql);

		$total_devueltos = $this->obtenerNumeroFilas($resultado);
		// SI DEVUELVE ALGUN REGISTRO
		if ($total_devueltos != 0)
			// SE CREA UN VECTOR CON EL RESULTADO PAGINADO
			$datos = $this->crearVector($resultado);
		else
			return false;

		// SE LIBERA LA MEMORIA USADA POR LA QUERY
		$this->liberarMemoria($resultado);

		// SE CIERRA LA CONEXION
		$this->desconectar($conexion);

		return $datos;
    }

    public function obtenerNombreUsuario($id)
    {
		$conexion = $this->conectar();

		$query = "SELECT codigo_usuario
				  FROM ".$this->tabla_usuarios."
				  WHERE id_usuario = ".$id."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['codigo_usuario'];
	}

	/**
	 * Se verifica si el legajo actualmente es Concejal
	 *
	 * @param integer $legajo
	 * @return boolean
	 */
	public function esConcejal($legajo)
	{
		$conexion = $this->conectar();

		$query = "SELECT c_legajo
			      FROM ".$this->tabla_cargos."
			      WHERE c_legajo = ".$legajo."
			      AND c_nomenclador =  ".$this->id_cargo_concejal."
			      AND c_fecha_alta = ( SELECT MAX( c_fecha_alta )
									   FROM ".$this->tabla_cargos."
									   WHERE c_legajo = ".$legajo."
									   AND (c_fecha_baja IS NULL OR c_fecha_baja > CURDATE())
								     )
			      AND (c_fecha_baja IS NULL OR c_fecha_baja > CURDATE())
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		// Si es Concejal o no
		return ($dato['c_legajo'] != '');
	}

	/**
	 * Se obtiene el nombre de la foto de un legajo respectivo
	 *
	 * @param integer $p_legajo
	 * @return string $dato['g_foto']
	 */
	public function obtenerNombreFoto($p_legajo)
	{
		$conexion = $this->conectar();

		$query = "SELECT p_foto FROM ".$this->tabla_personal." WHERE p_legajo = ".$p_legajo;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['p_foto'];
	}

	/**
	 * Se registra el nombre de la foto de un legajo respectivo
	 *
	 * @param integer $p_legajo
	 * @return boolean
	 */
	public function registrarNombreFoto($p_legajo, $nombre_foto_carnet)
	{
		$conexion = $this->conectar();

		// Si EXISTE el legajo
		if ( $this->existe($p_legajo) )
		{
			// Se registra el nombre de la foto carnet
			$query = "UPDATE ".$this->tabla_personal."
					  SET p_foto = '".$nombre_foto_carnet."'
					  WHERE p_legajo = ".$p_legajo;
		}
		else // Si no existe
		{
			// Se ingresa el legajo, con su nombre de foto carnet y por defecto se define como agente Activo
			$query = "INSERT INTO ".$this->tabla_personal." (p_legajo, p_foto, p_habilitado)
				  	  VALUES(".$p_legajo.", '".$nombre_foto_carnet."', 1)";
		}

		if ( !$this->ejecutarQuery($query) )
			return false;

		$this->desconectar($conexion);

		return true;
	}

	/**
	 * Se elimina el nombre de la foto de un legajo respectivo
	 *
	 * @param integer $p_legajo
	 * @return boolean
	 */
	public function eliminar_foto($p_legajo)
	{
		$conexion = $this->conectar();

		$query = "UPDATE ".$this->tabla_personal."
				  SET p_foto = ''
				  WHERE p_legajo = ".$p_legajo."
				 ";

		if ( !$this->ejecutarQuery($query) )
			return false;

		$this->desconectar($conexion);

		return true;
	}
}
?>
