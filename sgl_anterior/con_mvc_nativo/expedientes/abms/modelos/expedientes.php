<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/temas.php';

class expedientesModel extends ModeloBaseMySQLi
{
	private $filtroSql  = "";
	private $sentido    = "";
	private $orden      = "";

	public function conectar()
	{
		return parent::conectarDB(2);
	}

	public function listadoTotal($accion = '')
	{
		$conexion = $this->conectar();

		//SEGÚN COMO SE DESEA PAGINAR
		switch ($this->filtro['sentido'])
		{
		    case 'primero':
			    $this->sentido = " <= ";
			    $this->orden = " ASC ";
			    break;
		    case 'anterior':
			    $this->sentido = " <= ";
			    $this->orden = " DESC ";
			    break;
		    case 'siguiente':
			    $this->sentido = " >= ";
			    $this->orden = " ASC ";
			    break;
		    case 'ultimo':
			    $this->sentido = " >= ";
			    $this->orden = " DESC ";
			    break;
		    case 'para_edicion':
			    $this->sentido = " = ";
			    $this->orden = " ASC ";
			    break;
		    default: // AL COMENZAR
			    $this->sentido = " >= ";
			    $this->orden = " DESC ";
			    break;
		}

		if ( $this->filtro['agregado'] == true )
		{
			$this->orden = 'ASC';
		}

		$cadena_valores = "";
		$cadena_campos = "";
		$cadena_campos = "CONCAT(fecha_entrada_expe, CONVERT(anio,CHAR(4)),tipo,RIGHT(CONVERT(numero+100000,CHAR(6)),5),CONVERT(cuerpo,CHAR(1)),CONVERT(alcance,CHAR(1)))";

		$fecha_entrada_expe = ( $this->filtro['numero'] != '' ) ? $this->obtenerFechaEntradaExpe($this->filtro) : '';

		$aux_numero = 100000 + $this->filtro['numero'];
		$numero = substr($aux_numero, -5);

		$cadena_valores = "'".$fecha_entrada_expe.$this->filtro['anio'].$this->filtro['tipo'].$numero.$this->filtro['cuerpo'].$this->filtro['alcance']."'";// COLLATE utf8_unicode_ci

		$this->filtroSql .= " WHERE ".$cadena_campos.$this->sentido.$cadena_valores;

		$limite = "LIMIT 0, 11";
		//SI SE Edita NO SE NECESITA EL Limite EN LA CONSULTA
		if ($accion == 'editar'){
			$limite = "";
		}
		/* OPCION A */
		$sql = "SELECT E.*,
				L.descripcion_grp AS iniciador_descripcion,
				Cat.id_codcategoria AS codigo_categoria, Cat.descripcion_categoria AS descripcion_categoria
				FROM (SELECT * FROM ".$this->tabla_expedientes."
					  ".$this->filtroSql."
					  ORDER BY fecha_entrada_expe ".$this->orden.", anio ".$this->orden.", tipo ".$this->orden.", numero ".$this->orden.", cuerpo ".$this->orden.", alcance ".$this->orden."
					 ) E
				LEFT JOIN ".$this->tabla_lugares." L ON (L.tipo_grp = E.iniciador_tipo AND L.codigo_grp = E.iniciador_codigo)
				LEFT JOIN ".$this->tabla_codcategoria." Cat ON (Cat.id_codcategoria = E.id_codcategoria)
				ORDER BY E.fecha_entrada_expe ".$this->orden.", E.anio ".$this->orden.", E.tipo ".$this->orden.", E.numero ".$this->orden.", E.cuerpo ".$this->orden.", E.alcance ".$this->orden."
				".$limite;

		/* OPCION B *
		$sql = "SELECT E.*,
					   (SELECT descripcion_grp FROM hcd.expe_lugares WHERE tipo_grp = E.iniciador_tipo AND codigo_grp = E.iniciador_codigo) AS iniciador_descripcion,
					   (SELECT id_codcategoria FROM hcd.expe_codcategoria WHERE id_codcategoria = E.id_codcategoria) AS codigo_categoria,
					   (SELECT descripcion_categoria FROM hcd.expe_codcategoria WHERE id_codcategoria = E.id_codcategoria) AS descripcion_categoria
				FROM (SELECT * FROM ".$this->tabla_expedientes."
					  ".$this->filtroSql."
					 ) E
				ORDER BY E.fecha_entrada_expe ".$this->orden.", E.anio ".$this->orden.", E.tipo ".$this->orden.", E.numero ".$this->orden.", E.cuerpo ".$this->orden.", E.alcance ".$this->orden."
				".$limite."
			   ";
		/**/

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		switch ( $this->filtro['sentido'] )
		{
		    case '':
		    case 'anterior':
		    case 'ultimo':
			    return array_reverse($datos);
			    break;
		    default:
			    return $datos;
			    break;
		}
	}

	//	SE VERIFICA LA EXISTENCIA DE UN EXPEDIENTE DETERMINADO
	public function obtenerRegistroExpediente($clave)
	{
		$conexion = $this->conectar();

		$query = "SELECT *
				  FROM ".$this->tabla_expedientes."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->crearVector($resultado);

		if (!$datos[0]['tipo']){ return false; }

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerCantidad()
	{
		$conexion = $this->conectar();

		$sql = "SELECT COUNT(*) AS cantidad FROM ".$this->tabla_expedientes;

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['cantidad'];
	}

	//	SE VERIFICA LA EXISTENCIA DE UN EXPEDIENTE DETERMINADO
	public function existe($clave)
	{
		$conexion = $this->conectar();

		if (empty($clave['numero'])){ $clave['numero'] = 0; }
		if (empty($clave['cuerpo'])){ $clave['cuerpo'] = 0; }
		if (empty($clave['alcance'])){ $clave['alcance'] = 0; }

		$query = "SELECT tipo
				  FROM ".$this->tabla_expedientes."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($dato['tipo']);
	}

	public function verificar_sin_cpo_y_alc($clave)
	{
		$conexion = $this->conectar();

		$query = "SELECT tipo
				  FROM ".$this->tabla_expedientes."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				  AND cuerpo = 0
				  AND alcance = 0
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($dato['tipo']);
	}

	//	SE VERIFICA LA EXISTENCIA DE UN EXPEDIENTE DETERMINADO
	public function verificar_con_clave_completa($clave)
	{
		$conexion = $this->conectar();

		$query = "SELECT tipo
				  FROM ".$this->tabla_expedientes."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
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

		$filtro_iniciador_bloque_tipo   = $this->adaptarValorStringParaFiltro('iniciador_bloque_tipo');

		$filtro_iniciador_bloque_codigo = $this->adaptarValorStringParaFiltro('iniciador_bloque_codigo');

		$filtro_agregado_anio           = $this->adaptarValorStringParaFiltro('agregado_anio');

		$filtro_agregado_tipo           = $this->adaptarValorStringParaFiltro('agregado_tipo');

		$filtro_agregado_numero         = $this->adaptarValorStringParaFiltro('agregado_numero');

		$filtro_agregado_cuerpo         = $this->adaptarValorStringParaFiltro('agregado_cuerpo');

		$filtro_agregado_alcance        = $this->adaptarValorStringParaFiltro('agregado_alcance');

		$filtro_caratula                = $this->adaptarValorStringParaFiltro('caratula');

		$filtro_observaciones_expe      = $this->adaptarValorStringParaFiltro('observaciones_expe');

		$filtro_marca_comision          = $this->adaptarValorStringParaFiltro('marca_comision');

		$query = "SELECT anio
				  FROM ".$this->tabla_expedientes."
				  WHERE anio = ".$_SESSION['anio_original']."
				  AND tipo = '".$_SESSION['tipo_original']."'
				  AND numero = '".$_SESSION['numero_original']."'
				  AND cuerpo = ".$_SESSION['cuerpo_original']."
				  AND alcance = ".$_SESSION['alcance_original']."
				  AND iniciador_tipo = '".$_SESSION['iniciador_tipo_original']."'
				  AND iniciador_codigo = '".$_SESSION['iniciador_codigo_original']."'
				  ".$filtro_iniciador_bloque_tipo."
				  ".$filtro_iniciador_bloque_codigo."
				  ".$filtro_agregado_anio."
				  ".$filtro_agregado_tipo."
				  ".$filtro_agregado_numero."
				  ".$filtro_agregado_cuerpo."
				  ".$filtro_agregado_alcance."
				  AND id_codcategoria = ".$_SESSION['id_codcategoria_original']."
				  AND fecha_entrada_expe = '".$_SESSION['fecha_entrada_expe_original']."'
				  ".$filtro_caratula."
				  ".$filtro_observaciones_expe."
				  ".$filtro_marca_comision."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( $dato['anio'] );
	}

	public function validarDatos($datos)
	{
		if ( $datos['cuerpo'] == '' )
		{
			$datos['cuerpo'] = 0;
		}

		if ( $datos['alcance'] == '' )
		{
			$datos['alcance'] = 0;
		}

		if (empty($datos['iniciador_tipo']))
		{
			$datos['iniciador_tipo'] = "C";
		}

		if (empty($datos['iniciador_codigo']))
		{
			$datos['iniciador_codigo'] = "001";
		}

		if (empty($datos['id_codcategoria']))
		{
			$datos['id_codcategoria'] = "1";
		}

		if (empty($datos['fecha_entrada_expe']))
		{
			$datos['fecha_entrada_expe'] = "'".date("Y-m-d")."'";
		}
		else
		{
			$datos['fecha_entrada_expe'] = "'".$datos['fecha_entrada_expe']."'";
		}

		if (empty($datos['agregado_anio']))
		{
			$datos['agregado_anio'] = "null";
		}
		else
		{
			$datos['agregado_anio'] = $datos['agregado_anio'];
		}

		if ($datos['agregado_tipo'] == '0' || ($datos['agregado_tipo'] == '' ))
		{
			$datos['agregado_tipo'] = "null";
		}
		else
		{
			$datos['agregado_tipo'] = "'".$datos['agregado_tipo']."'";
		}

		if (empty($datos['agregado_numero']))
		{
			$datos['agregado_numero'] = "null";
		}
		else
		{
			$datos['agregado_numero'] = $datos['agregado_numero'];
		}

	// 02/01/2013
		if ( $datos['agregado_cuerpo'] == '' )
		{
			$datos['agregado_cuerpo'] = "null";
		}
		elseif ( $datos['agregado_cuerpo'] == 0 )
		{
			$datos['agregado_cuerpo'] = 0;
		}
		else
		{
			$datos['agregado_cuerpo'] = $datos['agregado_cuerpo'];
		}

	// 02/01/2013
		if ( $datos['agregado_alcance'] == '' )
		{
			$datos['agregado_alcance'] = "null";
		}
		elseif ( $datos['agregado_alcance'] == 0 )
		{
			$datos['agregado_alcance'] = 0;
		}
		else
		{
			$datos['agregado_alcance'] = $datos['agregado_alcance'];
		}

		$datos['caratula'] = $this->revisarValorAtributo($datos['caratula']);

		$datos['observaciones_expe'] = $this->revisarValorAtributo($datos['observaciones_expe']);

		$datos['marca_comision'] = $this->revisarValorAtributo($datos['marca_comision']);

		// 2020/05/07 XXXX
		if ( $datos['digi_completa'] == 'on' )
			$datos['digi_completa'] = 1;
		else
			$datos['digi_completa'] = 0;

		if (empty($datos['id_usuario']))
		{
			$datos['id_usuario'] = $_SESSION['id_usuario'];
		}
		else
		{
			$datos['id_usuario'] = "".$datos['id_usuario']."";
		}

		return $datos;
	}

	public function obtenerBloque($tipo, $codigo)
	{
		$query = "SELECT bloque_tipo, bloque_codigo
						 FROM ".$this->tabla_lugares."
						 WHERE tipo_grp = '".$tipo."'
						 AND codigo_grp = '".$codigo."'
						";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato;
	}

	public function validarBloque($datos_bloque)
	{
		if ( $datos_bloque['bloque_tipo'] )
		{
			$datos_bloque['bloque_tipo'] = "'".$datos_bloque['bloque_tipo']."'";
		}
		else
		{
			$datos_bloque['bloque_tipo'] = 'null';
		}

		if ( $datos_bloque['bloque_codigo'] )
		{
			$datos_bloque['bloque_codigo'] = "'".$datos_bloque['bloque_codigo']."'";
		}
		else
		{
			$datos_bloque['bloque_codigo'] = 'null';
		}

		return $datos_bloque;
	}

	public function insertar($datos)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		$datos = $this->validarDatos($datos);

		// SE OBTIENE EL BLOQUE DEL INICIADOR (SI POSEE)
		$datos_bloque = $this->obtenerBloque($datos['iniciador_tipo'], $datos['iniciador_codigo']);

		// SE VALIDA EL BLOQUE PARA LA QUERY
		$datos_bloque = $this->validarBloque($datos_bloque);

		$query = "INSERT INTO ".$this->tabla_expedientes." (anio, tipo, numero, cuerpo, alcance, iniciador_tipo, iniciador_codigo, iniciador_bloque_tipo, iniciador_bloque_codigo, agregado_anio, agregado_tipo, agregado_numero, agregado_cuerpo, agregado_alcance, id_codcategoria, fecha_entrada_expe, caratula, observaciones_expe, marca_comision, id_usuario)
				  VALUES ( ".$datos['anio'].",
						  '".$datos['tipo']."',
						  '".$datos['numero']."',
						   ".$datos['cuerpo'].",
						   ".$datos['alcance'].",
						  '".$datos['iniciador_tipo']."',
						  '".$datos['iniciador_codigo']."',
						   ".$datos_bloque['bloque_tipo'].",
						   ".$datos_bloque['bloque_codigo'].",
						   ".$datos['agregado_anio'].",
						   ".$datos['agregado_tipo'].",
						   ".$datos['agregado_numero'].",
						   ".$datos['agregado_cuerpo'].",
						   ".$datos['agregado_alcance'].",
						   ".$datos['id_codcategoria'].",
						   ".$datos['fecha_entrada_expe'].",
						   ".$datos['caratula'].",
						   ".$datos['observaciones_expe'].",
						   ".$datos['marca_comision'].",
						   ".$datos['id_usuario']."
						 )
				";

		if ( !$this->ejecutarQuery($query) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		else
		{
			//SE CARGA EL estado '1 Registrado' PARA DICHO EXPEDIENTE
			$query_estado = "INSERT INTO ".$this->tabla_estados." (anio, tipo, numero, cuerpo, alcance, fecha_estado, orden_estado, id_codestado, observaciones_estado, id_usuario)
							 VALUES ( ".$datos['anio'].",
									 '".$datos['tipo']."',
									  ".$datos['numero'].",
									  ".$datos['cuerpo'].",
									  ".$datos['alcance'].",
									  ".$datos['fecha_entrada_expe'].",
									  '1',
									  1,
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
				// PARA CADA TEMA ELEGIDO
			    for ($t = 0; $t < $datos['contador_temas']; $t++)
			    {
				    // SE VERIFICA SU EXISTENCIA EN EL EXPEDIENTE
				    $query = "SELECT id_codtema
							  FROM ".$this->tabla_temas."
							  WHERE anio = ".$datos['anio']."
							  AND tipo = '".$datos['tipo']."'
							  AND numero = ".$datos['numero']."
							  AND cuerpo = ".$datos['cuerpo']."
							  AND alcance = ".$datos['alcance']."
							  AND id_codtema = ".$datos['i_codigo_tema'][$t]."
							 ";

				    $resultado = $this->ejecutarQuery($query);

				    $verificacion = $this->crearVector($resultado);

				    // SI NO EXISTE EL TEMA EN EL EXPEDIENTE
				    if ( !$verificacion[0]['id_codtema'] )
				    {
					    //SE CARGA EL tema PARA DICHO EXPEDIENTE
					    $query = "INSERT INTO ".$this->tabla_temas." (anio, tipo, numero, cuerpo, alcance, id_codtema, id_usuario)
								  VALUES ( ".$datos['anio'].",
										  '".$datos['tipo']."',
										   ".$datos['numero'].",
										   ".$datos['cuerpo'].",
										   ".$datos['alcance'].",
										   ".$datos['i_codigo_tema'][$t].",
										   ".$datos['id_usuario']."
										 )
								 ";

					    if ( !$this->ejecutarQuery($query) )
					    {
						    $this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
						    return false;
					    }
				    }

			    }//FIN DEL for

			    // PARA CADA AUTOR ELEGIDO
			    for ($a = 0; $a < $datos['contador_autores']; $a++)
			    {
					// SE VERIFICA SU EXISTENCIA EN EL EXPEDIENTE
					$query = "SELECT autor_tipo
							  FROM ".$this->tabla_autores."
							  WHERE anio = ".$datos['anio']."
							  AND tipo = '".$datos['tipo']."'
							  AND numero = ".$datos['numero']."
							  AND cuerpo = ".$datos['cuerpo']."
							  AND alcance = ".$datos['alcance']."
							  AND autor_tipo = '".$datos['i_autor_tipo'][$a]."'
							  AND autor_codigo = '".$datos['i_autor_codigo'][$a]."'
							 ";

					$resultado = $this->ejecutarQuery($query);

					$verificacionAutor = $this->crearVector($resultado);

					// SI NO EXISTE EL AUTOR EN EL EXPEDIENTE
					if ( !$verificacionAutor[0]['autor_tipo'] )
					{
						// SE OBTIENE EL BLOQUE DEL CONCEJAL
						$datos_bloque = $this->obtenerBloque($datos['i_autor_tipo'][$a], $datos['i_autor_codigo'][$a]);

						// SE VALIDA EL BLOQUE PARA LA QUERY
						$datos_bloque = $this->validarBloque($datos_bloque);

						//SE CARGA EL autor PARA DICHO EXPEDIENTE
						$query = "INSERT INTO ".$this->tabla_autores." (anio, tipo, numero, cuerpo, alcance, autor_tipo, autor_codigo, autor_bloque_tipo, autor_bloque_codigo, id_usuario)
								  VALUES ( ".$datos['anio'].",
										  '".$datos['tipo']."',
										   ".$datos['numero'].",
										   ".$datos['cuerpo'].",
										   ".$datos['alcance'].",
										  '".$datos['i_autor_tipo'][$a]."',
										  '".$datos['i_autor_codigo'][$a]."',
										   ".$datos_bloque['bloque_tipo'].",
										   ".$datos_bloque['bloque_codigo'].",
										   ".$datos['id_usuario']."
										 )
								";

						if ( !$this->ejecutarQuery($query) )
						{
							$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
							return false;
						}
					}

			    } //FIN DEL for DE AUTORES

			    // SI SE CARGO UN Agregado SE INSERTA COMO Antecedente
			    if ( $datos['agregado_anio'] != 'null' )
				{
					$query = "INSERT INTO ".$this->tabla_antecedentes." (anio, tipo, numero, cuerpo, alcance,
																		 anio_a, tipo_a, numero_a, digito_a, cuerpo_a, alcance_a,
																		 cuerpoalcance_a, anexoalcance_a,
																		 cuerpoanexoalcance_a, anexo_a, cuerpoanexo_a,
																		 observaciones_antecedentes, id_usuario
																	    )
							  VALUES ( ".$datos['agregado_anio'].",
									   ".$datos['agregado_tipo'].",
									   ".$datos['agregado_numero'].",
									   ".$datos['agregado_cuerpo'].",
									   ".$datos['agregado_alcance'].",
									   ".$datos['anio'].",
									  '".$datos['tipo']."',
									   ".$datos['numero'].",
									   0,
									   ".$datos['cuerpo'].",
									   ".$datos['alcance'].",
									   0,
									   0,
									   0,
									   0,
									   0,
									   'AUTOMATICO',
									   ".$datos['id_usuario']."
									 )
							";

					if ( !$this->ejecutarQuery($query) )
					{
						$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
						return false;
					}
					else	// 06/01/2012
					{
						//SE CARGA EL estado '19 - AGREGADO A OTRO EXP. Y/O NOTA' PARA DICHO EXPEDIENTE
						$query = " INSERT INTO ".$this->tabla_estados." (anio, tipo, numero, cuerpo, alcance, fecha_estado, orden_estado, id_codestado, observaciones_estado, id_usuario)
											 VALUES ( ".$datos['anio'].",
													 '".$datos['tipo']."',
													  ".$datos['numero'].",
													  ".$datos['cuerpo'].",
													  ".$datos['alcance'].",
													  '".date("Y-m-d")."',
													  '2',
													  19,
													  null,
													  ".$datos['id_usuario']."
													)
											";

						if ( !$this->ejecutarQuery($query) )
						{
							$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
							return false;
						}
					}
			    }
			}

			$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

			$this->desconectar($conexion);

			// ****** Se registran los movimientos en la DB en la tabla de Auditoría
			$modelo = new auditoriaExpedientesModel();
			$datos_log = Array();

			// ****** PRIMERO se audita el alta del Expediente
			$datos_log['operacion_log']     = "ALTA";
			$datos_log['tabla_log']         = $this->tabla_expedientes;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = $datos['fecha_entrada_expe'];
			$datos_log['orden_log']         = "null";
			$datos_log['observaciones_log'] = "";

			// Se carga en auditoria el movimiento
			$modelo->registrarMovimiento($datos_log);


			// ****** LUEGO se audita el alta del Estado 1 'automático' de dicho expediente
			$datos_log['operacion_log']     = "ALTA";
			$datos_log['tabla_log']         = $this->tabla_estados;
			$datos_log['anio_log']          = $datos['anio'];
			$datos_log['tipo_log']          = $datos['tipo'];
			$datos_log['numero_log']        = $datos['numero'];
			$datos_log['cuerpo_log']        = $datos['cuerpo'];
			$datos_log['alcance_log']       = $datos['alcance'];
			$datos_log['fecha_log']         = $datos['fecha_entrada_expe'];
			$datos_log['orden_log']         = "null";
			$datos_log['observaciones_log'] = "Estado: 1 AUTOMATICO";

			// Se carga en auditoria el movimiento
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
	}

	public function modificar($datos)
	{
		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		// SE OBTIENE EL BLOQUE DEL INICIADOR (SI POSEE)
		$datos_bloque = $this->obtenerBloque($datos['iniciador_tipo'], $datos['iniciador_codigo']);

		// SE VALIDA EL BLOQUE PARA LA QUERY
		$datos_bloque = $this->validarBloque($datos_bloque);

		$query = "UPDATE ".$this->tabla_expedientes."
				  SET iniciador_tipo = '".$datos['iniciador_tipo']."',
					  iniciador_codigo = '".$datos['iniciador_codigo']."',
					  iniciador_bloque_tipo = ".$datos_bloque['bloque_tipo'].",
					  iniciador_bloque_codigo = ".$datos_bloque['bloque_codigo'].",
					  agregado_anio = ".$datos['agregado_anio'].",
					  agregado_tipo = ".$datos['agregado_tipo'].",
					  agregado_numero = ".$datos['agregado_numero'].",
					  agregado_cuerpo = ".$datos['agregado_cuerpo'].",
					  agregado_alcance = ".$datos['agregado_alcance'].",
					  id_codcategoria = ".$datos['id_codcategoria'].",
					  fecha_entrada_expe = ".$datos['fecha_entrada_expe'].",
					  caratula = ".$datos['caratula'].",
					  observaciones_expe = ".$datos['observaciones_expe'].",
					  marca_comision = ".$datos['marca_comision'].",
					  digi_completa = ".$datos['digi_completa'].",
					  id_usuario = ".$datos['id_usuario']."
				  WHERE anio = ".$datos['anio']."
				  AND tipo = '".$datos['tipo']."'
				  AND numero = ".$datos['numero']."
				  AND cuerpo = ".$datos['cuerpo']."
				  AND alcance = ".$datos['alcance']."
				 ";

		//fputs(fopen("query_modificar_expediente.txt", 'w'), print_r($query, true));

		// SI SURGIÓ UN ERROR AL MODIFICAR EL EXPEDIENTE:
		if ( !$this->ejecutarQuery($query) )
		{
		    $this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
		    return false;

		// SI SE GUARDÓ SATISFACTORIAMENTE EL EXPEDIENTE (TENIENDO EN CUENTA LA TRANSACCIÓN ACTUAL)
		}
		else
		{
		    // 30/11/2011
		    // SE ELIMINAN LOS TEMAS DEL EXPEDIENTE PARA INSERTAR NUEVAMENTE LOS QUE QUEDARON
		    $query = "DELETE FROM ".$this->tabla_temas."
						  WHERE anio = ".$datos['anio']."
						  AND tipo = '".$datos['tipo']."'
						  AND numero = ".$datos['numero']."
						  AND cuerpo = ".$datos['cuerpo']."
						  AND alcance = ".$datos['alcance']."
						 ";

		    if (!$this->ejecutarQuery($query))
		    {
				$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
				return false;
		    }
		    else
		    {
				// PARA CADA TEMA ELEGIDO
				for ($t = 0; $t < $datos['contador_temas']; $t++)
				{
					// SE VERIFICA SU EXISTENCIA EN EL EXPEDIENTE
					$query = "SELECT id_codtema
							  FROM ".$this->tabla_temas."
							  WHERE anio = ".$datos['anio']."
							  AND tipo = '".$datos['tipo']."'
							  AND numero = ".$datos['numero']."
							  AND cuerpo = ".$datos['cuerpo']."
							  AND alcance = ".$datos['alcance']."
							  AND id_codtema = ".$datos['i_codigo_tema'][$t]."
							 ";

					$resultado = $this->ejecutarQuery($query);

					$verificacion = $this->crearVector($resultado);

					// SI NO EXISTE EL TEMA EN EL EXPEDIENTE
					if ( !$verificacion[0]['id_codtema'] )
					{
						//SE CARGA EL tema PARA DICHO EXPEDIENTE
						$query = "INSERT INTO ".$this->tabla_temas." (anio, tipo, numero, cuerpo, alcance, id_codtema, id_usuario)
								  VALUES ( ".$datos['anio'].",
										  '".$datos['tipo']."',
										   ".$datos['numero'].",
										   ".$datos['cuerpo'].",
										   ".$datos['alcance'].",
										   ".$datos['i_codigo_tema'][$t].",
										   ".$datos['id_usuario']."
										 )
								 ";

						if ( !$this->ejecutarQuery($query) )
						{
							$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
							return false;
						}
					}

				}//FIN DEL for DE TEMAS
		    }

		    // 30/11/2011
		    // SE ELIMINAN LOS AUTORES DEL EXPEDIENTE PARA CARGAR NUEVAMENTE LOS QUE QUEDARON
		    $query = "DELETE FROM ".$this->tabla_autores."
						   WHERE anio = ".$datos['anio']."
						   AND tipo = '".$datos['tipo']."'
						   AND numero = ".$datos['numero']."
						   AND cuerpo = ".$datos['cuerpo']."
						   AND alcance = ".$datos['alcance']."
						  ";

		    if (!$this->ejecutarQuery($query))
		    {
				$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
				return false;
		    }
		    else
		    {
				// PARA CADA AUTOR ELEGIDO
				for ($a = 0; $a < $datos['contador_autores']; $a++)
				{
					// SE VERIFICA SU EXISTENCIA EN EL EXPEDIENTE
					$query = "SELECT autor_tipo
							  FROM ".$this->tabla_autores."
							  WHERE anio = ".$datos['anio']."
							  AND tipo = '".$datos['tipo']."'
							  AND numero = ".$datos['numero']."
							  AND cuerpo = ".$datos['cuerpo']."
							  AND alcance = ".$datos['alcance']."
							  AND autor_tipo = '".$datos['i_autor_tipo'][$a]."'
							  AND autor_codigo = '".$datos['i_autor_codigo'][$a]."'
							 ";

					$resultado = $this->ejecutarQuery($query);

					$verificacionAutor = $this->crearVector($resultado);

					// SI NO EXISTE EL AUTOR EN EL EXPEDIENTE
					if ( !$verificacionAutor[0]['autor_tipo'] )
					{
						// SE OBTIENE EL BLOQUE DEL AUTOR, EN CASO QUE SEA CONCEJAL
						$datos_bloque = $this->obtenerBloque($datos['i_autor_tipo'][$a], $datos['i_autor_codigo'][$a]);

						// SE VALIDA EL BLOQUE PARA LA QUERY
						$datos_bloque = $this->validarBloque($datos_bloque);

						// SE CARGA EL autor PARA DICHO EXPEDIENTE
						$query = "INSERT INTO ".$this->tabla_autores." (anio, tipo, numero, cuerpo, alcance, autor_tipo, autor_codigo, autor_bloque_tipo, autor_bloque_codigo, id_usuario)
								  VALUES ( ".$datos['anio'].",
										  '".$datos['tipo']."',
										   ".$datos['numero'].",
										   ".$datos['cuerpo'].",
										   ".$datos['alcance'].",
										  '".$datos['i_autor_tipo'][$a]."',
										  '".$datos['i_autor_codigo'][$a]."',
										   ".$datos_bloque['bloque_tipo'].",
										   ".$datos_bloque['bloque_codigo'].",
										   ".$datos['id_usuario']."
										 )
								 ";

						if ( !$this->ejecutarQuery($query) )
						{
							$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
							return false;
						}
					}
				}//FIN DEL for DE AUTORES
		    }

		    // SI SE CARGO UN Agregado SE INSERTA COMO Antecedente
		    if ( $datos['agregado_anio'] != 0 )
		    {
				// SE VERIFICA LA EXISTENCIA DEL ANTECEDENTE
				$query_verificar_antecedente = "SELECT anio
												FROM ".$this->tabla_antecedentes."
												WHERE anio = ".$datos['agregado_anio']."
											    AND tipo = ".$datos['agregado_tipo']."
											    AND numero = ".$datos['agregado_numero']."
											    AND cuerpo = ".$datos['agregado_cuerpo']."
											    AND alcance = ".$datos['agregado_alcance']."
											    AND anio_a = '".$datos['anio']."'
											    AND tipo_a = '".$datos['tipo']."'
											    AND numero_a = ".$datos['numero']."
											    AND digito_a = 0
											    AND cuerpo_a = ".$datos['cuerpo']."
											    AND alcance_a = ".$datos['alcance']."
											    AND cuerpoalcance_a = 0
											    AND anexoalcance_a = 0
											    AND cuerpoanexoalcance_a = 0
											    AND anexo_a = 0
											    AND cuerpoanexo_a = 0
											   ";

				$resultado = $this->ejecutarQuery($query_verificar_antecedente);

				$verifica_antecedente = $this->obtenerFila($resultado);

				// SI NO EXISTE COMO ANTECEDENTE
				if ( !$verifica_antecedente['anio'] )
				{
					$query = "INSERT INTO ".$this->tabla_antecedentes." (anio, tipo, numero, cuerpo, alcance,
																		 anio_a, tipo_a, numero_a, digito_a, cuerpo_a, alcance_a,
																		 cuerpoalcance_a, anexoalcance_a,
																		 cuerpoanexoalcance_a, anexo_a, cuerpoanexo_a,
																		 observaciones_antecedentes, id_usuario
																	    )
							  VALUES ( ".$datos['agregado_anio'].",
									   ".$datos['agregado_tipo'].",
									   ".$datos['agregado_numero'].",
									   ".$datos['agregado_cuerpo'].",
									   ".$datos['agregado_alcance'].",
									   ".$datos['anio'].",
									  '".$datos['tipo']."',
									   ".$datos['numero'].",
									   0,
									   ".$datos['cuerpo'].",
									   ".$datos['alcance'].",
									   0,
									   0,
									   0,
									   0,
									   0,
									  'AUTOMATICO',
									   ".$datos['id_usuario']."
									 )
							";

					if ( !$this->ejecutarQuery($query) )
					{
						$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
						return false;
					}
					else
					{
						// SE VERIFICA PRIMERO SI ESTA EL ESTADO 19 PARA DICHO EXPEDIENTE
						$query_verifica_estado_19 = " SELECT id_codestado
													  FROM ".$this->tabla_estados."
													  WHERE anio = ".$datos['anio']."
													  AND tipo = '".$datos['tipo']."'
													  AND numero = ".$datos['numero']."
													  AND cuerpo = ".$datos['cuerpo']."
													  AND alcance = ".$datos['alcance']."
													  AND id_codestado = 19
													";

						$resultado = $this->ejecutarQuery($query_verifica_estado_19);

						$verifica_estado_19 = $this->obtenerFila($resultado);

						// SI NO EXISTE EL ESTADO 19 EN EL EXPEDIENTE
						if ( !$verifica_estado_19['id_codestado'] )
						{
							// SE OBTIENE EL ULTIMO ORDEN DEL ESTADO PARA DICHO EXPEDIENTE
							$query_ultimo_orden_estado = "SELECT MAX(orden_estado) AS ultimo_orden_estado
														  FROM ".$this->tabla_estados."
														  WHERE anio = ".$datos['anio']."
														  AND tipo = '".$datos['tipo']."'
														  AND numero = ".$datos['numero']."
														  AND cuerpo = ".$datos['cuerpo']."
														  AND alcance = ".$datos['alcance']."
														";

							$resultado = $this->ejecutarQuery($query_ultimo_orden_estado);

							$orden_estado = $this->obtenerFila($resultado);

							// SI SE OBTUVO EL ULTIMO ORDEN DEL ESTADO EN DICHO EXPEDIENTE
							if ( $orden_estado['ultimo_orden_estado'] )
							{
								$orden_estado_19 = $orden_estado['ultimo_orden_estado'] + 1;

								//SE CARGA EL estado '19 - AGREGADO A OTRO EXP. Y/O NOTA' PARA DICHO EXPEDIENTE
								$query_estado_19 = " INSERT INTO ".$this->tabla_estados." (anio, tipo, numero, cuerpo, alcance, fecha_estado, orden_estado, id_codestado, observaciones_estado, id_usuario)
													 VALUES ( ".$datos['anio'].",
															 '".$datos['tipo']."',
															  ".$datos['numero'].",
															  ".$datos['cuerpo'].",
															  ".$datos['alcance'].",
															 '".date("Y-m-d")."',
															 '".$orden_estado_19."',
															  19,
															  null,
															  ".$datos['id_usuario']."
															)
													";

								if ( !$this->ejecutarQuery($query_estado_19) )
								{
									$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
									return false;
								}
							}
						}
					}
				}
		    }

		}//FIN DEL else

		$this->ejecutarQuery("COMMIT");// SE EJECUTA LA TRANSACCION

		$this->desconectar($conexion);

		//SE OBTIENEN LOS DATOS A REGISTRAR EN auditoria
		$modelo = new auditoriaExpedientesModel();

		$datos_log = Array();
		$datos_log['operacion_log'] = "MODIFICA";
		$datos_log['tabla_log']     = $this->tabla_expedientes;
		$datos_log['anio_log']      = $datos['anio'];
		$datos_log['tipo_log']      = $datos['tipo'];
		$datos_log['numero_log']    = $datos['numero'];
		$datos_log['cuerpo_log']    = $datos['cuerpo'];
		$datos_log['alcance_log']   = $datos['alcance'];
		$datos_log['fecha_log']     = $datos['fecha_entrada_expe'];
		$datos_log['orden_log']     = "null";

		// 06/01/2012
		if ( isset($orden_estado_19) )
		{
			$datos_log['observaciones_log'] = "Estado: 19 AUTOMATICO";
		}
		else
		{
			$datos_log['observaciones_log'] = "null";
		}

		//SE CARGA EN auditoria EL MOVIMIENTO
		$modelo->registrarMovimiento($datos_log);

		return true;
	}

	public function eliminar($clave)
	{
		$conexion = $this->conectar();

		$this->ejecutarQuery("BEGIN");// SE INICIA LA TRANSACCION

		// SE ELIMINAN PRIMERO LOS TEMAS DE DICHO EXPEDIENTE
		$queryTema = "DELETE FROM ".$this->tabla_temas."
					  WHERE anio = ".$clave['anio']."
					  AND tipo = '".$clave['tipo']."'
					  AND numero = ".$clave['numero']."
					  AND cuerpo = ".$clave['cuerpo']."
					  AND alcance = ".$clave['alcance']."
					 ";

		if ( !$this->ejecutarQuery($queryTema) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}

		// SE ELIMINAN LUEGO LOS AUTORES DE DICHO EXPEDIENTE
		$queryAutor = "DELETE FROM ".$this->tabla_autores."
					   WHERE anio = ".$clave['anio']."
					   AND tipo = '".$clave['tipo']."'
					   AND numero = ".$clave['numero']."
					   AND cuerpo = ".$clave['cuerpo']."
					   AND alcance = ".$clave['alcance']."
					  ";

 		if ( !$this->ejecutarQuery($queryAutor) )
 		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}

		// SE ELIMINAN LUEGO LAS SANCIONES DE DICHO EXPEDIENTE
		$querySanciones = "DELETE FROM ".$this->tabla_sanciones."
						   WHERE anio = ".$clave['anio']."
						   AND tipo = '".$clave['tipo']."'
						   AND numero = ".$clave['numero']."
						   AND cuerpo = ".$clave['cuerpo']."
						   AND alcance = ".$clave['alcance']."
						  ";

 		if ( !$this->ejecutarQuery($querySanciones) )
 		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}

		// SE ELIMINAN LUEGO LOS PROYECTOS DE DICHO EXPEDIENTE
		$queryProyecto = "DELETE FROM ".$this->tabla_proyectos."
						  WHERE anio = ".$clave['anio']."
						  AND tipo = '".$clave['tipo']."'
						  AND numero = ".$clave['numero']."
						  AND cuerpo = ".$clave['cuerpo']."
						  AND alcance = ".$clave['alcance']."
						 ";

 		if ( !$this->ejecutarQuery($queryProyecto) )
 		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}

		// SE ELIMINAN LUEGO LOS GIROS DE DICHO EXPEDIENTE
		$queryGiro = "DELETE FROM ".$this->tabla_giros."
					  WHERE anio = ".$clave['anio']."
					  AND tipo = '".$clave['tipo']."'
					  AND numero = ".$clave['numero']."
					  AND cuerpo = ".$clave['cuerpo']."
					  AND alcance = ".$clave['alcance']."
					 ";

 		if ( !$this->ejecutarQuery($queryGiro) )
 		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}

		// SE ELIMINAN LUEGO LOS ESTADOS DE DICHO EXPEDIENTE
		$queryEstado = "DELETE FROM ".$this->tabla_estados."
					    WHERE anio = ".$clave['anio']."
					    AND tipo = '".$clave['tipo']."'
					    AND numero = ".$clave['numero']."
					    AND cuerpo = ".$clave['cuerpo']."
					    AND alcance = ".$clave['alcance']."
					   ";

 		if ( !$this->ejecutarQuery($queryEstado) )
 		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}

		// SE ELIMINAN LUEGO LOS ANTECEDENTES DE DICHO EXPEDIENTE
		$queryAntecedente = "DELETE FROM ".$this->tabla_antecedentes."
						     WHERE anio = ".$clave['anio']."
						     AND tipo = '".$clave['tipo']."'
						     AND numero = ".$clave['numero']."
						     AND cuerpo = ".$clave['cuerpo']."
						     AND alcance = ".$clave['alcance']."
						    ";

 		if ( !$this->ejecutarQuery($queryAntecedente) )
 		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}

		$queryQuitarAgregado = "UPDATE ".$this->tabla_expedientes."
							    SET agregado_anio = NULL,
									agregado_tipo = NULL,
									agregado_numero = NULL,
									agregado_cuerpo = NULL,
									agregado_alcance = NULL
							    WHERE anio = ".$clave['anio']."
							    AND tipo = '".$clave['tipo']."'
							    AND numero = ".$clave['numero']."
							    AND cuerpo = ".$clave['cuerpo']."
							    AND alcance = ".$clave['alcance']."
							   ";

		if ( !$this->ejecutarQuery($queryQuitarAgregado) )
		{
			$this->ejecutarQuery("ROLLBACK");// SE DESHACE LA TRANSACCION
			return false;
		}
		// SE ELIMINA EL EXPEDIENTE
		$query = "DELETE FROM ".$this->tabla_expedientes."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				  AND cuerpo = ".$clave['cuerpo']."
				  AND alcance = ".$clave['alcance']."
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
			$datos_log['tabla_log']         = $this->tabla_expedientes;
			$datos_log['anio_log']          = $clave['anio'];
			$datos_log['tipo_log']          = $clave['tipo'];
			$datos_log['numero_log']        = $clave['numero'];
			$datos_log['cuerpo_log']        = $clave['cuerpo'];
			$datos_log['alcance_log']       = $clave['alcance'];
			$datos_log['fecha_log']         = "'".$clave['fecha_entrada_expe']."'";
			$datos_log['orden_log']         = "null";
			$datos_log['observaciones_log'] = "null";

			//SE CARGA EN auditoria EL MOVIMIENTO
			$modelo->registrarMovimiento($datos_log);
		}

		return true;
	}

	public function obtenerDatosExped()
	{
		$conexion = $this->conectar();

		//para filtrar por anio
		if ($this->filtro['anio'] != '')
		{
			$this->filtroSql = " WHERE anio = ".$this->filtro['anio']."";
		}

		//para filtrar por tipo
		if ($this->filtro['tipo'] != '')
		{
			$this->filtroSql .= " AND tipo = '".$this->filtro['tipo']."'";
		}

		//para filtrar por numero
		if ($this->filtro['numero'] != '')
		{
			$this->filtroSql .= " AND numero = ".$this->filtro['numero']."";
		}

		//para filtrar por cuerpo
		if ($this->filtro['cuerpo'] != '')
		{
			$this->filtroSql .= " AND cuerpo = ".$this->filtro['cuerpo']."";
		}

		//para filtrar por alcance
		if ($this->filtro['alcance'] != '')
		{
			$this->filtroSql .= " AND alcance = ".$this->filtro['alcance']."";
		}

		$sql = "SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.observaciones_expe, E.digi_completa,
					   L.codigo_grp, L.descripcion_grp,
					   CCat.id_codcategoria, CCat.descripcion_categoria,
					   U.codigo_usuario
				FROM (SELECT * FROM ".$this->tabla_expedientes."
					  ".$this->filtroSql."
					 ) E
				LEFT JOIN ".$this->tabla_lugares." L ON L.tipo_grp = E.iniciador_tipo AND L.codigo_grp = E.iniciador_codigo
				LEFT JOIN ".$this->tabla_codcategoria." CCat ON CCat.id_codcategoria = E.id_codcategoria
				LEFT JOIN ".$this->tabla_usuarios." U ON (U.id_usuario = E.id_usuario)
			   ";

		$resultado = $this->ejecutarQuery($sql);

		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerEstado()
	{
		$conexion = $this->conectar();

		//para filtrar por anio
		if ($this->filtro['anio'] != '')
		{
			$this->filtroSql = " WHERE anio = ".$this->filtro['anio']."";
		}

		//para filtrar por tipo
		if ($this->filtro['tipo'] != '')
		{
			$this->filtroSql .= " AND tipo = '".$this->filtro['tipo']."'";
		}

		//para filtrar por numero
		if ($this->filtro['numero'] != '')
		{
			$this->filtroSql .= " AND numero = ".$this->filtro['numero']."";
		}

		//para filtrar por cuerpo
		if ($this->filtro['cuerpo'] != '')
		{
			$this->filtroSql .= " AND cuerpo = ".$this->filtro['cuerpo']."";
		}

		//para filtrar por alcance
		if ($this->filtro['alcance'] != '')
		{
			$this->filtroSql .= " AND alcance = ".$this->filtro['alcance']."";
		}

		$sql = "SELECT codigo_estado, nombre_estado
				FROM ".$this->tabla_codestados."
				WHERE id_codestado = (SELECT id_codestado FROM ".$this->tabla_estados."
									  ".$this->filtroSql."
									  ORDER BY fecha_estado DESC
									  LIMIT 0,1
									 )
		       ";

		$resultado = $this->ejecutarQuery($sql);
		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerComision($datos)
	{
		$conexion = $this->conectar();

		$sql = "SELECT L.codigo_grp, L.descripcion_grp
				FROM ".$this->tabla_lugares." L
				INNER JOIN
				(SELECT *
				  FROM
				  (
					SELECT estado_comis.*, comisiones.comision_tipo, comisiones.comision_codigo
					FROM
					(
					  SELECT anio, tipo, numero, cuerpo, alcance, id_codestado
					  FROM (
							SELECT anio, tipo, numero, cuerpo, alcance, id_codestado
							FROM (
								  SELECT anio, tipo, numero, cuerpo, alcance, id_codestado
								  FROM ".$this->tabla_estados."
								  WHERE anio = ".$datos[0]['anio']."
								  AND tipo = '".$datos[0]['tipo']."'
								  AND numero = ".$datos[0]['numero']."
								  AND cuerpo = ".$datos[0]['cuerpo']."
								  AND alcance = ".$datos[0]['alcance']."
								  ORDER BY anio, tipo, numero, cuerpo, alcance, fecha_estado DESC , orden_estado DESC
								 )AS estados_ordenados
							GROUP BY anio, tipo, numero, cuerpo, alcance, id_codestado
						  )AS mayor_estado
					  WHERE (id_codestado = (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 3) OR
						     id_codestado = (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 16) OR
						     id_codestado = (SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 79)
						    )
					)AS estado_comis
					LEFT JOIN
					(
						SELECT anio, tipo, numero, cuerpo, alcance, comision_tipo, comision_codigo
						FROM ".$this->tabla_giros."
						WHERE anio = ".$datos[0]['anio']."
						AND tipo = '".$datos[0]['tipo']."'
						AND numero = ".$datos[0]['numero']."
						AND cuerpo = ".$datos[0]['cuerpo']."
						AND alcance = ".$datos[0]['alcance']."
						AND ".$this->tabla_giros.".fecha_entrada_giro IS NOT NULL
						AND ".$this->tabla_giros.".fecha_entrada_giro <> '0000-00-00'
						AND (".$this->tabla_giros.".fecha_salida_giro IS NULL OR ".$this->tabla_giros.".fecha_salida_giro = '0000-00-00')
					)AS comisiones

					ON estado_comis.anio = comisiones.anio
					AND estado_comis.tipo = comisiones.tipo
					AND estado_comis.numero = comisiones.numero
					AND estado_comis.cuerpo = comisiones.cuerpo
					AND estado_comis.alcance = comisiones.alcance
				  )AS en_comision
				) G
				ON G.comision_tipo = L.tipo_grp
				AND G.comision_codigo = L.codigo_grp
		       ";

		$resultado = $this->ejecutarQuery($sql);

		if (!$resultado)
		    return false;
		else
		    $datos = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerProyectosExped()
	{
		$conexion = $this->conectar();

		//para filtrar por anio
		if ($this->filtro['anio'] != '')
		{
			$this->filtroSql = " WHERE anio = ".$this->filtro['anio']."";
		}

		//para filtrar por tipo
		if ($this->filtro['tipo'] != '')
		{
			$this->filtroSql .= " AND tipo = '".$this->filtro['tipo']."'";
		}

		//para filtrar por numero
		if ($this->filtro['numero'] != '')
		{
			$this->filtroSql .= " AND numero = ".$this->filtro['numero']."";
		}

		//para filtrar por cuerpo
		if ($this->filtro['cuerpo'] != '')
		{
			$this->filtroSql .= " AND cuerpo = ".$this->filtro['cuerpo']."";
		}

		//para filtrar por alcance
		if ($this->filtro['alcance'] != '')
		{
			$this->filtroSql .= " AND alcance = ".$this->filtro['alcance']."";
		}

		$sql = "SELECT P.orden_proyecto, P.extracto, CP.descripcion_proyecto
				FROM (SELECT * FROM ".$this->tabla_expedientes."
					  ".$this->filtroSql."
					 ) E
				LEFT JOIN ".$this->tabla_proyectos." P ON (P.anio = E.anio AND P.tipo = E.tipo AND P.numero = E.numero AND P.cuerpo = E.cuerpo AND P.alcance = E.alcance)
				LEFT JOIN ".$this->tabla_codproyectos." CP ON CP.id_codproyecto = P.id_codproyecto
			   ";

		$resultado = $this->ejecutarQuery($sql);

		if (!$resultado)
			return false;
		else
			$datos = $this->crearVector($resultado);// SE CREA UN VECTOR CON EL RESULTADO

		$this->desconectar($conexion);

		return $datos;
	}

	public function verificarParentesco($agregado)
	{
		$conexion = $this->conectar();

		$query = "SELECT *
				  FROM ".$this->tabla_expedientes."
				  WHERE agregado_anio = ".$agregado['anio']."
				  AND agregado_tipo = '".$agregado['tipo']."'
				  AND agregado_numero = ".$agregado['numero']."
			  	  AND agregado_cuerpo = ".$agregado['cuerpo']."
			   	  AND agregado_alcance = ".$agregado['alcance']."
				 ";
		$resultado = $this->ejecutarQuery($query);

		$datos = $this->crearVector($resultado);

		if (!$datos[0]['tipo']){
			return false;
		}
		$this->desconectar($conexion);

		return true;
	}

	public function setearNumeroSgte($anio, $tipo)
	{
		$conexion = $this->conectar();

		$query = "SELECT MAX(numero) AS numero
				  FROM ".$this->tabla_expedientes."
				  WHERE anio = ".$anio."
				  AND tipo = '".$tipo."'
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		// EN CASO DE INICIAR UN NUEVO AÑO
		if ( !$dato['numero'] ) {
			if ( $tipo == 'E' )
				$nuevo_numero = 1001;// COMIENZA A PARTIR DEL 1001 PARA EXPEDIENTE
			elseif ( $tipo == 'N' )
				$nuevo_numero = 1;// COMIENZA A PARTIR DEL 1 PARA NOTA
			else // SI EL TIPO NO ES NI 'E' NI 'N', ES UNA RECOMENDACION
				$nuevo_numero = 1;// COMIENZA A PARTIR DEL 1 PARA RECOMENDACION
		} else
			$nuevo_numero = $dato['numero'] + 1;

		return $nuevo_numero;
	}

    /**
     * Devuelve el valor de un Id de un Estado, según el Código
     * @param  integer $codigo   		Código de la codificadora de estados
     * @return integer id_codestado     Identificador respectivo
     */
    public function obtenerIdCodEstadoSegunCodigo($codigo)
	{
		$conexion = $this->conectar();

		$query = "SELECT id_codestado
				  FROM ".$this->tabla_codestados."
				  WHERE codigo_estado = ".$codigo;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['id_codestado'];
    }

	public function obtenerFechaEntradaExpe($clave)
	{
		$sql = "SELECT fecha_entrada_expe
				FROM ".$this->tabla_expedientes."
			    WHERE anio = ".$clave['anio']."
			    AND tipo = '".$clave['tipo']."'
			    AND numero = ".$clave['numero']."
			    AND cuerpo = ".$clave['cuerpo']."
			    AND alcance = ".$clave['alcance']."
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		return $dato['fecha_entrada_expe'];
	}

}
?>
