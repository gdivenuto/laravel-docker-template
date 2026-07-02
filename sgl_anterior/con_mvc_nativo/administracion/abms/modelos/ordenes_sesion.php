<?php
if (!isset($_SESSION)) {
	session_start();
}

class ordenes_sesionModel extends ModeloBaseMySQLi
{
    public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}

    public function listar() {

		$conexion = $this->conectar();

		$filtro = "";
		$limite = "";

		// PARA FILTRAR POR PERIODO
		if ( $this->filtro['f_periodo'] != '' )
			$filtro .= " AND periodo = ".$this->filtro['f_periodo'];

		// PARA FILTRAR POR REUNION
		if ( $this->filtro['f_reunion'] != '' )
			$filtro .= " AND reunion = ".$this->filtro['f_reunion'];

		// PARA FILTRAR POR SESION
		if ( $this->filtro['f_sesion'] != '' )
			$filtro .= " AND sesion LIKE '%".$this->filtro['f_sesion']."%'";

		// PARA FILTRAR POR FECHA
		if ( $this->filtro['f_fecha'] != '' )
			$filtro .= " AND fecha = '".$this->filtro['f_fecha']."'";

		// PARA LIMITAR EL LISTADO
		if ( $this->filtro['rango'] != 0 )
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];

		$sql = "SELECT * FROM ".$this->tabla_od_sesion."
				WHERE fecha IS NOT NULL
				".$filtro."
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido'].", hora ASC
				".$limite;

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

	public function obtenerCantidad() {

		$conexion = $this->conectar();

		$filtro = "";

		// PARA FILTRAR POR PERIODO
		if ( $this->filtro['f_periodo'] != '' )
			$filtro .= " AND periodo = ".$this->filtro['f_periodo'];

		// PARA FILTRAR POR REUNION
		if ( $this->filtro['f_reunion'] != '' )
			$filtro .= " AND reunion = ".$this->filtro['f_reunion'];

		// PARA FILTRAR POR SESION
		if ( $this->filtro['f_sesion'] != '' )
			$filtro .= " AND sesion LIKE '".$this->filtro['f_sesion']."%'";

		// PARA FILTRAR POR FECHA
		if ( $this->filtro['f_fecha'] != '' )
			$filtro .= " AND fecha = '".$this->filtro['f_fecha']."'";

		$query = "SELECT COUNT(id) AS cantidad
				  FROM ".$this->tabla_od_sesion."
				  WHERE fecha IS NOT NULL
				  ".$filtro;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

    public function obtenerRegistro($id) {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM ".$this->tabla_od_sesion." WHERE id = ".$id;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
    }

	/**
	 * Se obtiene el ultimo Id registrado en la DB
	 *
	 * @see ModelBase::obtenerUltimoCodigo()
	 */
	public function obtenerUltimoId() {

		return parent::obtenerUltimoCodigo($this->tabla_od_sesion, 'id');
	}

	public function obtenerUltimoIdItem() {

		$conexion = $this->conectar();

		$query = "SELECT MAX(id) AS ultimo_id FROM ".$this->tabla_od_sesion_items;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$ultimo_id = ($dato['ultimo_id'] != null) ? $dato['ultimo_id'] : 0;

		$this->desconectar($conexion);

		return $ultimo_id;
	}

    public function obtenerNombreSesion($id) {

		$conexion = $this->conectar();

		$sql = "SELECT sesion FROM ".$this->tabla_od_sesion." WHERE id = ".$id;

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['sesion'];
	}

	public function obtenerRegistroItem($id) {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM ".$this->tabla_od_sesion_items." WHERE id = ".$id;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
    }

    /**
     * Se obtiene el número de Orden de un Item respectivo
     * @param  [integer] $id 					Identificador de un Item
     * @return [integer] $registro['orden']		Nro de Orden
     */
	public function obtenerNroOrdenItem($id) {

		$conexion = $this->conectar();

		$sql = "SELECT orden FROM ".$this->tabla_od_sesion_items." WHERE id = ".$id;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro['orden'];
    }

    public function obtenerIniciadorParaItem($anio, $tipo, $numero) {

		$conexion = $this->conectar();

		$query = "SELECT I.codigo_grp AS codigo_iniciador, I.descripcion_grp AS descripcion_iniciador
				  FROM ( SELECT *
					     FROM ".$this->tabla_expedientes."
					     WHERE anio = ".$anio."
					     AND tipo = '".$tipo."'
					     AND numero = ".$numero."
					   ) AS E
				  INNER JOIN ".$this->tabla_lugares." AS I
				  ON I.tipo_grp = E.iniciador_tipo
				  AND I.codigo_grp = E.iniciador_codigo
				 ";

		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

    public function existe($datos) {

		$conexion = $this->conectar();

		$query = "SELECT sesion
				  FROM ".$this->tabla_od_sesion."
				  WHERE periodo = ".$datos['periodo']."
				  AND sesion = '".$datos['sesion']."'
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ( $dato['sesion'] != '' );
    }

    public function validarDatos($datos)
    {
		$datos['periodo'] = $this->revisarValorAtributo(strip_tags($datos['periodo']));

		$datos['reunion'] = $this->revisarValorAtributo(strip_tags($datos['reunion']));

		$datos['sesion'] = $this->revisarValorAtributo(strip_tags($datos['sesion']));

		$datos['fecha'] = $this->revisarValorFechaAtributo($datos['fecha']);

		$datos['hora'] = $this->revisarValorAtributo($datos['hora']);

		$datos['decreto_y_anexo'] = $this->revisarValorAtributo($datos['decreto_y_anexo'], 0);

		$datos['texto_decreto_previo_anexo'] = $this->revisarValorContenidoTextArea($datos['texto_decreto_previo_anexo']);

		return $datos;
    }

    public function noLoModificoOtroUsuario() {

		$conexion = $this->conectar();

		$query = "SELECT sesion
				  FROM ".$this->tabla_od_sesion."
				  WHERE id = '".$_SESSION['id_original']."'
				  ".$this->adaptarValorNumericoParaFiltro('periodo')."
				  ".$this->adaptarValorNumericoParaFiltro('reunion')."
				  ".$this->adaptarValorStringParaFiltro('sesion')."
				  ".$this->adaptarValorStringParaFiltro('fecha')."
				  ".$this->adaptarValorStringParaFiltro('hora')."
				  ".$this->adaptarValorStringParaFiltro('decreto_y_anexo')."
				  ".$this->adaptarValorStringParaFiltro('texto_decreto_previo_anexo');

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( $dato['sesion'] );
    }

    public function insertar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO ".$this->tabla_od_sesion."
					(periodo, reunion, sesion, fecha, hora, texto_decreto_previo_anexo)
				  VALUES
				  	(".$datos['periodo'].",
				  	 ".$datos['reunion'].",
				  	 ".$datos['sesion'].",
				  	 ".$datos['fecha'].",
				  	 ".$datos['hora'].",
				  	 ".$datos['texto_decreto_previo_anexo']."
				  	)";

		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {

			$this->desconectar($conexion);

			$observaciones  = "Se ingresa la sesión:";
			$observaciones .= " Período: ".str_replace("'","",$datos['periodo']);
			$observaciones .= " - Reunión: ".str_replace("'","",$datos['reunion']);
			$observaciones .= " - Sesión: ".str_replace("'","",$datos['sesion']);
			$observaciones .= " - Fecha: ".str_replace("'","",$datos['fecha']);
			$observaciones .= " y Hora: ".str_replace("'","",$datos['hora']);

			$this->auditarEnAdministracion("ALTA", $this->tabla_od_sesion, $observaciones);
		}

		return true;
    }

    public function modificar($datos) {

    	$info = $this->obtenerRegistro($datos['id']);

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "UPDATE ".$this->tabla_od_sesion."
				  SET periodo = ".$datos['periodo'].",
					  reunion = ".$datos['reunion'].",
					  sesion = ".$datos['sesion'].",
					  fecha = ".$datos['fecha'].",
					  hora = ".$datos['hora'].",
					  decreto_y_anexo = ".$datos['decreto_y_anexo'].",
					  texto_decreto_previo_anexo = ".$datos['texto_decreto_previo_anexo']."
				  WHERE id = ".$datos['id'];

		//LibreriaGeneral::registrarLog("query", $query, '.sql');

		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {

			$this->desconectar($conexion);

			$observaciones  = "Se modifica la sesión:";
			$observaciones .= "\n ANTES:";
			$observaciones .= " Período: ".str_replace("'","",$info['periodo']);
			$observaciones .= " - Reunión: ".str_replace("'","",$info['reunion']);
			$observaciones .= " - Sesión: ".str_replace("'","",$info['sesion']);
			$observaciones .= " - Fecha: ".str_replace("'","",$info['fecha']);
			$observaciones .= " y Hora: ".str_replace("'","",$info['hora']);
			$observaciones .= "\n DESPUES:";
			$observaciones .= " Período: ".str_replace("'","",$datos['periodo']);
			$observaciones .= " - Reunión: ".str_replace("'","",$datos['reunion']);
			$observaciones .= " - Sesión: ".str_replace("'","",$datos['sesion']);
			$observaciones .= " - Fecha: ".str_replace("'","",$datos['fecha']);
			$observaciones .= " y Hora: ".str_replace("'","",$datos['hora']);

			$this->auditarEnAdministracion("MODIFICA", $this->tabla_od_sesion, $observaciones);
		}

		return true;
    }

    public function eliminar($id) {

    	$info = $this->obtenerRegistro($id);

		$conexion = $this->conectar();

		$this->iniciarTransaccion();

		$query = "DELETE FROM ".$this->tabla_od_sesion_items."
				  WHERE id_sesion IN ( SELECT id
									   FROM ".$this->tabla_od_sesion."
									   WHERE id = ".$id."
									 )";

		if ( !$this->ejecutarQuery($query) ) {
			$this->revertirTransaccion();
			return false;
		} else {
			$query = "DELETE FROM ".$this->tabla_od_sesion." WHERE id = ".$id;

			if ( !$this->ejecutarQuery($query) ) {
				$this->revertirTransaccion();
				return false;
			} else {
				$this->confirmarTransaccion();

				$this->desconectar($conexion);

				$observaciones  = "Se elimina la sesión:";
				$observaciones .= " Período: ".str_replace("'","",$info['periodo']);
				$observaciones .= " - Reunión: ".str_replace("'","",$info['reunion']);
				$observaciones .= " - Sesión: ".str_replace("'","",$info['sesion']);
				$observaciones .= " - Fecha: ".str_replace("'","",$info['fecha']);
				$observaciones .= " y Hora: ".str_replace("'","",$info['hora']);

				$this->auditarEnAdministracion("BAJA", $this->tabla_od_sesion, $observaciones);
			}
		}

		return true;
    }

    /**
     * Se obtienen los Items de una Orden del Día de Sesion determinada por su Id
     * @param  integer $id          	Identificador de la Orden del Día de Sesión
     * @param  string  $cod_seccion 	Código de la Sección
     * @return array   $datos
     */
    public function listarItemsOrdenDiaSesion($id, $cod_seccion = '') {

		$conexion = $this->conectar();

		$filtro_por_seccion = ( $cod_seccion != '' ) ? " AND ODSI.cod_seccion = '".$cod_seccion."'" : "";

		$sql = "SELECT ODSI.*,
					   (SELECT nombre FROM ".$this->tabla_od_sesion_seccion." WHERE codigo = ODSI.cod_seccion) AS nombre_seccion
				FROM ".$this->tabla_od_sesion_items." AS ODSI
				WHERE ODSI.id_sesion = ".$id."
				".$filtro_por_seccion."
				ORDER BY anio, tipo, numero";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

    public function obtenerItemsOrdenados($id_sesion) {

		$conexion = $this->conectar();

		$sql = "SELECT id
				FROM ".$this->tabla_od_sesion_items."
				WHERE id_sesion = ".$id_sesion."
				ORDER BY id, cod_seccion";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

    public function existeItem($datos) {

		$conexion = $this->conectar();

		$query = "SELECT numero
				  FROM ".$this->tabla_od_sesion_items."
				  WHERE id_sesion = ".$datos['id_sesion']."
				  AND cod_seccion = '".$datos['cod_seccion']."'
				  AND anio = ".$datos['anio']."
				  AND tipo = '".$datos['tipo']."'
				  AND numero = ".$datos['numero']."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ( $dato['numero'] != '' );
    }

    public function existeDocumento($clave) {

		$conexion = $this->conectar();

		$query = "SELECT numero
				  FROM ".$this->tabla_expedientes."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ( $dato['numero'] != '' );
    }

    public function validarDatosItem($datos) {

		// Si se trata de una sección padre
		if ( $this->obtenerSubSecciones($datos['seccion_padre']) )
			// Se valida la sección seleccionada para el ítem
			$datos['cod_seccion'] = $this->revisarValorAtributo($datos['cod_seccion']);
		else
			// Se asigna dicha sección para el ítem (Caso del 70000000)
			$datos['cod_seccion'] = $this->revisarValorAtributo($datos['seccion_padre']);

		$datos['orden'] = $this->revisarValorAtributo($datos['orden'], 0);

		$datos['anio'] = $this->revisarValorAtributo(strip_tags($datos['anio']));

		$datos['numero'] = $this->revisarValorAtributo(strip_tags($datos['numero']));

		$datos['autor'] = $this->revisarValorAtributo(strip_tags($datos['autor']));

		$datos['caratula'] = $this->revisarValorAtributo(strip_tags($datos['caratula']));

		$datos['orden_proyecto'] = $this->revisarValorAtributo($datos['orden_proyecto']);

		$datos['codigo_proyecto'] = $this->revisarValorAtributo($datos['codigo_proyecto']);

		$datos['anio_despacho_archivo'] = $this->revisarValorAtributo(strip_tags($datos['anio_despacho_archivo']));

		$datos['tipo_despacho_archivo'] = $this->revisarValorAtributo(strip_tags($datos['tipo_despacho_archivo']));

		$datos['numero_despacho_archivo'] = $this->revisarValorAtributo(strip_tags($datos['numero_despacho_archivo']));

		$datos['habilitado'] = $this->revisarValorAtributo($datos['habilitado'], 1);

		return $datos;
    }

    // Se ingresa un Item, la auditoría se realiza en el controlador luego de generar nuevamente los nros. de orden.
    public function insertarItem($datos) {

		$datos = $this->validarDatosItem($datos);

		// Si se recibe el texto de Detalle
		$seteo_valor_detalle = ( isset($datos['detalle']) ) ? "'".$datos['detalle']."'" : "NULL";

		// Si se recibe el texto de Giro (Nombre de la Comisión)
		$seteo_valor_giros = ( isset($datos['giros']) ) ? "'".$datos['giros']."'" : "NULL";

		// Si el check para giros está activo, se marca el campo en 1, sino en 0 (cero)
		$seteo_valor_giros_edicion_manual = ( isset($datos['chk_giros']) ) ? "1" : "0";

		// Si se trata de un expediente con despacho a archivo o no
		$seteo_con_despacho_archivo = ( isset($datos['con_despacho_archivo']) ) ? "1" : "0";

		$conexion = $this->conectar();

		// 09/05/2019 XXXX
		// --------------------
		// Se separa la validación del campo 'extracto' del método validarDatosItem()
		// para evitar el uso de trim() en su valor.
		//
		// Se utiliza mysqli_real_escape_string() para escapar los saltos de línea que se desean guardar en la DB.
		// --------------------
		$datos['extracto'] = ($datos['extracto'] == '' || $datos['extracto'] == '0')
			? "null"
			: "'".mysqli_real_escape_string($conexion, $datos['extracto'])."'";

		$query = "INSERT INTO ".$this->tabla_od_sesion_items."
					(id_sesion,
					 cod_seccion,
					 orden,
					 anio,
					 tipo,
					 numero,
					 autor,
					 caratula,
					 orden_proyecto,
					 codigo_proyecto,
					 extracto,
					 detalle,
					 giros,
					 giros_edicion_manual,
					 con_despacho_archivo,
					 anio_despacho_archivo,
					 tipo_despacho_archivo,
					 numero_despacho_archivo,
					 habilitado)
				  VALUES
				  	(".$datos['id_sesion'].",
					 ".$datos['cod_seccion'].",
					 ".$datos['orden'].",
					 ".$datos['anio'].",
					'".$datos['tipo']."',
					 ".$datos['numero'].",
					 ".$datos['autor'].",
					 ".$datos['caratula'].",
					 ".$datos['orden_proyecto'].",
					 ".$datos['codigo_proyecto'].",
					 ".$datos['extracto'].",
					 ".$seteo_valor_detalle.",
					 ".$seteo_valor_giros.",
					 ".$seteo_valor_giros_edicion_manual.",
					 ".$seteo_con_despacho_archivo.",
					 ".$datos['anio_despacho_archivo'].",
					 ".$datos['tipo_despacho_archivo'].",
					 ".$datos['numero_despacho_archivo'].",
					 ".$datos['habilitado']."
					);";

		if ( !$this->ejecutarQuery($query) )
			return false;
		else
			$this->desconectar($conexion);

		return true;
    }

    /**
     * Se modifica un Item, la auditoría se realiza en el controlador luego de generar nuevamente los nros. de orden.
     * @param  [array] 		$datos  Info del Item
     * @return [boolean]        	True o False
     */
    public function modificarItem($datos) {

		$datos = $this->validarDatosItem($datos);

		// Si se recibe el texto de Detalle
		$seteo_campo_detalle = ( isset($datos['detalle']) ) ? " detalle = '".$datos['detalle']."', " : "";

		// Si el check para giros está TILDADO
		if ( isset($datos['chk_giros']) ) {
			// Se GUARDA el texto de giros (Nombre de la Comisión)
			$seteo_campo_giros = " giros = '".$datos['giros']."', ";
			// Se setea en 1
			$seteo_campo_giros_edicion_manual = " giros_edicion_manual = '1', ";
		} else {
			// Si el check está DESTILDADO y la marca PREVIA está activa
			if ( $datos['giros_edicion_manual_marca_previa'] == '1' ) {
				// Se BORRA el texto de giros (Nombre de la Comisión)
				$seteo_campo_giros = " giros = NULL, ";
				// Se setea en 0 (cero)
				$seteo_campo_giros_edicion_manual = " giros_edicion_manual = '0', ";
			}
			else // Si la marca PREVIA está desactivada
			{
				// No se hace NADA
				$seteo_campo_giros = "";
				$seteo_campo_giros_edicion_manual = "";
			}
		}

		// Si se trata de un expediente con despacho a archivo o no
		$seteo_con_despacho_archivo = ( isset($datos['con_despacho_archivo']) ) ? 1 : 0;

		$conexion = $this->conectar();

		// Se separa la validación del campo 'extracto' del método validarDatosItem()
		// para evitar el uso de trim() en su valor.
		//
		// Se utiliza mysqli_real_escape_string() para escapar los saltos de línea que se desean guardar en la DB.
		//
		$datos['extracto'] = ($datos['extracto'] == '' || $datos['extracto'] == '0')
			? "null"
			: "'".mysqli_real_escape_string($conexion, $datos['extracto'])."'";

		$query = "UPDATE ".$this->tabla_od_sesion_items."
				  SET cod_seccion = ".$datos['cod_seccion'].",
					  orden = ".$datos['orden'].",
					  anio = ".$datos['anio'].",
					  tipo = '".$datos['tipo']."',
					  numero = ".$datos['numero'].",
					  autor = ".$datos['autor'].",
					  caratula = ".$datos['caratula'].",
					  orden_proyecto = ".$datos['orden_proyecto'].",
					  codigo_proyecto = ".$datos['codigo_proyecto'].",
					  extracto = ".$datos['extracto'].",
					  ".$seteo_campo_detalle."
					  ".$seteo_campo_giros."
					  ".$seteo_campo_giros_edicion_manual."
					  con_despacho_archivo = ".$seteo_con_despacho_archivo.",
					  anio_despacho_archivo = ".$datos['anio_despacho_archivo'].",
					  tipo_despacho_archivo = ".$datos['tipo_despacho_archivo'].",
					  numero_despacho_archivo = ".$datos['numero_despacho_archivo'].",
					  habilitado = ".$datos['habilitado']."
				  WHERE id = ".$datos['id'];

		if ( !$this->ejecutarQuery($query) )
			return false;

		$this->desconectar($conexion);

		return true;
    }

	/**
	 * Se elimina un Item
	 * @param  [integer] $id Identificador del Item
	 * @return [boolean]     True|False
	 */
    public function eliminarItem($id) {

		// Se obtiene su info antes de eliminarlo, para la auditoria.
		$info_previa = $this->obtenerRegistroItem($id);

		$conexion = $this->conectar();

		$query = "DELETE FROM ".$this->tabla_od_sesion_items." WHERE id = ".$id;

		if ( !$this->ejecutarQuery($query) )
			return false;
		else {
			$this->desconectar($conexion);
			// Se audita la BAJA del Item
			$this->auditarEnAdministracion("BAJA", $this->tabla_od_sesion_items, "Se elimina el Item: ".$this->armarVistaPreviaItem($info_previa));
		}

		return true;
    }

    // SE OBTIENEN LAS SECCIONES PRINCIPALES (XX000000)
	public function obtenerSecciones($id = '') {

		$conexion = $this->conectar();

		$filtro_por_orden = "";
		// PARA FILTRAR POR UNA ORDEN DEL DIA DE SESION
		if ( $id != '' ) {
			// CUYO CODIGO SEA EL PADRE DE LA SECCION DEL ITEM
			$filtro_por_orden = "AND codigo IN (SELECT CONCAT(SUBSTRING(cod_seccion,1,2 ),'000000')
											    FROM ".$this->tabla_od_sesion_items."
											    WHERE id_sesion = ".$id."
											   )";
		}

		$sql = "SELECT *
				FROM ".$this->tabla_od_sesion_seccion."
				WHERE habilitado = 1
				AND codigo LIKE '%000000'
				".$filtro_por_orden."
				ORDER BY codigo
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	// 14/07/2022 XXXX
    // Se obtienen todas las secciones principales (XX000000)
	public function obtenerTodasSecciones($id = '') {

		$conexion = $this->conectar();

		$filtro_por_orden = "";
		// PARA FILTRAR POR UNA ORDEN DEL DIA DE SESION
		if ( $id != '' ) {
			// CUYO CODIGO SEA EL PADRE DE LA SECCION DEL ITEM
			$filtro_por_orden = "AND codigo IN (SELECT CONCAT(SUBSTRING(cod_seccion,1,2 ),'000000')
											    FROM ".$this->tabla_od_sesion_items."
											    WHERE id_sesion = ".$id."
											   )";
		}

		$sql = "SELECT *
				FROM ".$this->tabla_od_sesion_seccion."
				WHERE codigo LIKE '%000000'
				".$filtro_por_orden."
				ORDER BY codigo";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	// SE TOMAN LOS DOS PRIMEROS DIGITOS DE LA SECCION DEL ITEM
	// PARA OBTENER LA SECCION PADRE, EN CASO DE POSEER
	public function obtenerSeccionPadre($cod_seccion) {

		$conexion = $this->conectar();

		// SE TOMA EL PRIMER PAR DE DIGITOS DE LA SECCION PADRE
		$primer_par_digitos = substr($cod_seccion, 0, 2);

		$sql = "SELECT *
				FROM ".$this->tabla_od_sesion_seccion."
				WHERE habilitado = 1
				AND codigo = '".$primer_par_digitos."000000'
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtienen las SubSecciones de una sección padre
	 * @param  [type] $seccion_padre [description]
	 * @return [type]                [description]
	 */
	public function obtenerSubSecciones($seccion_padre) {

		$conexion = $this->conectar();

		// Se toma el primer par de dígitos de la sección padre
		$primer_par_digitos = substr($seccion_padre, 0, 2);

		$sql = "SELECT *
				FROM ".$this->tabla_od_sesion_seccion."
				WHERE habilitado = 1
				AND codigo LIKE '".$primer_par_digitos."%'
				AND codigo <> '".$primer_par_digitos."000000'";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtienen todas las SubSecciones de una sección padre
	 * Habilitadas y deshabilitadas
	 * @param  [type] $seccion_padre [description]
	 * @return [type]                [description]
	 */
	public function obtenerTodasSubSecciones($seccion_padre) {

		$conexion = $this->conectar();

		// Se toma el primer par de dígitos de la sección padre
		$primer_par_digitos = substr($seccion_padre, 0, 2);

		$sql = "SELECT *
				FROM ".$this->tabla_od_sesion_seccion."
				WHERE codigo LIKE '".$primer_par_digitos."%'
				AND codigo <> '".$primer_par_digitos."000000'";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

    public function obtenerDatosExpedienteItem($clave) {

		$conexion = $this->conectar();

		$sql = "SELECT E.anio, E.tipo, E.numero, E.caratula, E.iniciador_codigo,
					   I.descripcion_grp AS descripcion_iniciador
				FROM ( SELECT *
					   FROM ".$this->tabla_expedientes."
					   WHERE anio = ".$clave['anio']."
					   AND tipo = '".$clave['tipo']."'
					   AND numero = ".$clave['numero']."
					 ) AS E
				INNER JOIN ".$this->tabla_lugares." AS I
					ON I.tipo_grp = E.iniciador_tipo
				AND I.codigo_grp = E.iniciador_codigo";

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
    }

    public function obtenerAutoresExpedienteItem($clave) {

		$conexion = $this->conectar();

		$sql = "SELECT A.autor_codigo AS codigo_autor,
					   L.descripcion_grp AS nombre_autor
				FROM ".$this->tabla_lugares." AS L
				INNER JOIN
				( SELECT autor_tipo, autor_codigo
				  FROM ".$this->tabla_autores."
				  WHERE anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				) AS A
				ON A.autor_tipo = L.tipo_grp
				AND A.autor_codigo = L.codigo_grp
			   ";

		$resultado = $this->ejecutarQuery($sql);

		$autores = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $autores;
    }

	/**
	 * 23/07/2018
	 * @param  [type] $anio   [description]
	 * @param  [type] $tipo   [description]
	 * @param  [type] $numero [description]
	 * @return [type]         [description]
	 */
    public function obtenerInfoPrimerAutor($anio, $tipo, $numero) {

		$conexion = $this->conectar();

		$sql = "SELECT A.autor_codigo AS codigo_autor,
					   L.descripcion_grp AS descripcion_autor
				FROM ".$this->tabla_lugares." AS L
				INNER JOIN
				( SELECT autor_tipo, autor_codigo
				  FROM ".$this->tabla_autores."
				  WHERE anio = ".$anio."
				  AND tipo = '".$tipo."'
				  AND numero = ".$numero."
				) AS A
				ON A.autor_tipo = L.tipo_grp
				AND A.autor_codigo = L.codigo_grp";

		$resultado = $this->ejecutarQuery($sql);

		$info = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $info[0];
    }

    public function obtenerProyectosExpedienteItem($clave) {

		$conexion = $this->conectar();

		$sql = "SELECT P.orden_proyecto, P.id_codproyecto, P.extracto,
					   ( SELECT descripcion_proyecto
						 FROM ".$this->tabla_codproyectos."
						 WHERE id_codproyecto = P.id_codproyecto
					   ) AS descripcion_proyecto
				FROM ".$this->tabla_proyectos." AS P
				WHERE P.anio = ".$clave['anio']."
				AND P.tipo = '".$clave['tipo']."'
				AND P.numero = ".$clave['numero']."
				AND P.extracto IS NOT NULL";

		$resultado = $this->ejecutarQuery($sql);

		$proyectos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $proyectos;
    }

	public function guardarOrden($id_item, $orden_a_guardar) {

		$conexion = $this->conectar();

		// SE REGISTRA EL ORDEN NUMERICO DEL ITEM
		$query = "UPDATE ".$this->tabla_od_sesion_items."
				  SET orden = ".$orden_a_guardar."
				  WHERE id = ".$id_item;

		if ( !$this->ejecutarQuery($query) )
			return false;

		$this->desconectar($conexion);

		return true;
	}

	public function tieneItems($id_sesion, $cod_seccion = '') {

		$conexion = $this->conectar();

		$filtro = ( $cod_seccion != '' ) ? " AND cod_seccion = '".$cod_seccion."'" : "";

		$query = "SELECT cod_seccion
				  FROM ".$this->tabla_od_sesion_items."
				  WHERE id_sesion = ".$id_sesion."
				  ".$filtro;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ( isset($dato['cod_seccion']) && $dato['cod_seccion'] != '' );
	}

	public function obtenerOrdenesItems($id_sesion, $cod_seccion, $para_seccion_padre = '0') {

		$conexion = $this->conectar();

		$filtro = ( $para_seccion_padre == '1' ) ? "AND cod_seccion LIKE '".substr($cod_seccion, 0, 2)."%'" : "AND cod_seccion = '".$cod_seccion."'";

		$query = "SELECT *
				  FROM ".$this->tabla_od_sesion_items."
				  WHERE id_sesion = ".$id_sesion."
				  ".$filtro."
				  ORDER BY orden";

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerItem($id_sesion, $orden = 1, $numero = '') {

		$conexion = $this->conectar();

		$filtro = "";

		// SI SE BUSCA POR NUMERO DE ITEM Ó POR ORDEN
		$filtro = ( $numero != '' ) ? " AND numero = ".$numero."" : " AND orden = ".$orden."";

		$sql = "SELECT *
				FROM ".$this->tabla_od_sesion_items."
				WHERE id_sesion = ".$id_sesion."
				".$filtro;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
    }

    public function obtenerUltimoOrden($id_sesion) {

		$conexion = $this->conectar();

		$sql = "SELECT orden AS ultimo_orden
				FROM ".$this->tabla_od_sesion_items."
				WHERE id_sesion = ".$id_sesion."
				ORDER BY orden DESC
				LIMIT 1";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['ultimo_orden'];
	}

    public function obtenerNombresComisionesItem($anio, $tipo, $numero) {

		$conexion = $this->conectar();

		$query = "SELECT DISTINCT L.abreviatura_grp AS abreviatura_comision
				  FROM ".$this->tabla_lugares." AS L
				  INNER JOIN
				  ( SELECT comision_tipo, comision_codigo
					FROM ".$this->tabla_giros."
					WHERE anio = ".$anio."
					AND tipo = '".$tipo."'
					AND numero = ".$numero."
				  ) AS G
				  ON G.comision_tipo = L.tipo_grp
				  AND G.comision_codigo = L.codigo_grp";

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
    }

    public function obtenerAntecedente($anio, $tipo, $numero) {

		$conexion = $this->conectar();

		$query = "SELECT E.agregado_anio, E.agregado_tipo, E.agregado_numero,
					     ( SELECT iniciador_codigo
					       FROM ".$this->tabla_expedientes."
						   WHERE anio = E.agregado_anio
						   AND tipo = E.agregado_tipo
						   AND numero = E.agregado_numero
						 ) AS iniciador_agregado
				  FROM ".$this->tabla_expedientes." AS E
				  WHERE E.anio = ".$anio."
				  AND E.tipo = '".$tipo."'
				  AND E.numero = ".$numero;

		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	/**
	 * Se obtienen los Expedientes y Notas ingresados dentro de un rango de fechas determinado.
	 * Las Recomendaciones NO se cargan en la Orden del Día.
	 * @param  [array] $filtro Posee la fecha Desde y Hasta para el criterio de búsqueda.
	 * @return [array] $datos  Listado de expedientes/notas devueltos.
	 */
    public function listarCargaGrupal($filtro) {

		$conexion = $this->conectar();

		$query = "SELECT E.anio, E.tipo, E.numero, E.cuerpo, E.alcance, E.caratula, E.iniciador_codigo,
						 I.descripcion_grp AS descripcion_iniciador
				  FROM ( SELECT *
					     FROM ".$this->tabla_expedientes."
					     WHERE fecha_entrada_expe BETWEEN
					     	'".$filtro['odcg_fecha_desde']."' AND '".$filtro['odcg_fecha_hasta']."'
					     AND cuerpo = 0
					     AND alcance = 0
					     AND (tipo = 'E' OR tipo = 'N')
					   ) AS E
				  INNER JOIN ".$this->tabla_lugares." AS I
				  	ON I.tipo_grp = E.iniciador_tipo
				  	AND I.codigo_grp = E.iniciador_codigo";

		$resultado = $this->ejecutarQuery($query);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

    public function estaRegistradoeEnOrdenSesion($clave, $id_sesion) {

		$conexion = $this->conectar();

		$query = "SELECT id
				  FROM ".$this->tabla_od_sesion_items."
				  WHERE id_sesion = ".$id_sesion."
				  AND anio = ".$clave['anio']."
				  AND tipo = '".$clave['tipo']."'
				  AND numero = ".$clave['numero']."
				 ";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		// Si existe o no
		return ( isset($dato['id']) && $dato['id'] != '' );
	}

	public function seMuestraIniciador($cod_seccion) {

		$conexion = $this->conectar();

		$query = "SELECT codigo
				  FROM ".$this->tabla_od_sesion_seccion."
				  WHERE codigo = '".$cod_seccion."'
				  AND mostrar_iniciador = '1'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		// Si existe o no
		return ( isset($dato['codigo']) && $dato['codigo'] != '' );
	}

	public function seMuestraAutor($cod_seccion) {

		$conexion = $this->conectar();

		$query = "SELECT codigo
				  FROM ".$this->tabla_od_sesion_seccion."
				  WHERE codigo = '".$cod_seccion."'
				  AND mostrar_autor = '1'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		// Si existe o no
		return ( isset($dato['codigo']) && $dato['codigo'] != '' );
	}

	public function seMuestraCaratulaEnExpedientes($cod_seccion) {

		$conexion = $this->conectar();

		$query = "SELECT codigo
				  FROM ".$this->tabla_od_sesion_seccion."
				  WHERE codigo = '".$cod_seccion."'
				  AND mostrar_caratula_en_exped = '1'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		// Si existe o no
		return ( isset($dato['codigo']) && $dato['codigo'] != '' );
	}

	public function seMuestraCaratulaEnNotas($cod_seccion) {

		$conexion = $this->conectar();

		$query = "SELECT codigo
				  FROM ".$this->tabla_od_sesion_seccion."
				  WHERE codigo = '".$cod_seccion."'
				  AND mostrar_caratula_en_nota = '1'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		// Si existe o no
		return ( isset($dato['codigo']) && $dato['codigo'] != '' );
	}

	public function seMuestranComisiones($cod_seccion) {

		$conexion = $this->conectar();

		$query = "SELECT codigo
				  FROM ".$this->tabla_od_sesion_seccion."
				  WHERE codigo = '".$cod_seccion."'
				  AND mostrar_comisiones = '1'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		// Si existe o no
		return ( isset($dato['codigo']) && $dato['codigo'] != '' );
	}

	public function permiteCargaGiros($cod_seccion) {

		$conexion = $this->conectar();

		$query = "SELECT codigo FROM ".$this->tabla_od_sesion_seccion." WHERE codigo = '".$cod_seccion."' AND mostrar_comisiones = '1'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		// Si existe o no
		return ( isset($dato['codigo']) && $dato['codigo'] != '' );
	}

    public function obtenerNombreSeccion($codigo) {

		$conexion = $this->conectar();

		$sql = "SELECT nombre FROM ".$this->tabla_od_sesion_seccion." WHERE codigo = '".$codigo."'";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ( isset($dato['nombre']) && $dato['nombre'] != '' );
    }

    /**
     * Se obtiene la información de una Sección determinada por su código.
     * @param  [string] $codigo Código de la sección.
     * @return [array]          Info de la sección.
     */
    public function obtenerInfoSeccion($codigo) {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM ".$this->tabla_od_sesion_seccion." WHERE codigo = '".$codigo."'";

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
    }

    public function obtenerDatosUltimaOrdenDiaSesion() {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM ".$this->tabla_od_sesion." ORDER BY periodo DESC, reunion DESC LIMIT 1";

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

    public function obtenerDatosUltimoItemOrdenDiaSesion($id_sesion, $cod_seccion) {

		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM ".$this->tabla_od_sesion_items."
				WHERE id_sesion = ".$id_sesion."
				AND cod_seccion = '".$cod_seccion."'
				ORDER BY orden DESC
				LIMIT 1";

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	public function cargarGiroEnItem($id_item, $giros_a_cargar_en_item) {

		$conexion = $this->conectar();

		// SE CARGAN LOS GIROS EN EL ITEM (LOS NOMBRES DE LAS COMISIONES POR LAS QUE PASO EL EXPED/NOTA, SEPARADOS POR COMA)
		// 22/06/2018 XXXX
		// Siempre y cuando NO se encuentre Activa la edición MANUAL del campo giros
		$query = "UPDATE ".$this->tabla_od_sesion_items."
				  SET giros = '".$giros_a_cargar_en_item."'
				  WHERE id = ".$id_item."
				  AND giros_edicion_manual = '0'";

		if ( !$this->ejecutarQuery($query) )
			return false;

		$this->desconectar($conexion);

		return true;
	}

	/**
	 * Se obtiene el Extracto de un expediente determinado
	 * @param  [integer] $anio   Año del expediente
	 * @param  [string]  $tipo   Tipo del expediente
	 * @param  [integer] $numero Número del expediente
	 * @return [string]          Extracto
	 */
	public function obtenerExtractoExpediente($anio, $tipo, $numero) {

		$conexion = $this->conectar();

		$sql = "SELECT extracto
				FROM ".$this->tabla_proyectos."
				WHERE anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro['extracto'];
	}

	private function armarDescripcionDocumento($dato) {

		// Se obtiene el nombre del Iniciador
		$iniciador_para_item = $this->obtenerIniciadorParaItem($dato['anio'], $dato['tipo'], $dato['numero']);

		switch ($dato['tipo']) {
			case 'E':
				$descripcion = "Expte ".str_replace("'", "", $dato['numero'])."-".$iniciador_para_item['codigo_iniciador']."-".substr($dato['anio'], 2, 2).': ';
				break;
			case 'N':
				$descripcion = "Nota ".str_replace("'", "", $dato['numero'])."-".$iniciador_para_item['codigo_iniciador']."-".substr($dato['anio'], 2, 2).': ';
				break;
			case 'D':
				$descripcion = "Decreto Nº ".str_replace("'", "", $dato['numero']).': ';
				break;
			case '0':
				$descripcion = "Otro Nº ".str_replace("'", "", $dato['numero']).': ';
				break;
		}

		return $descripcion;
	}

    /**
     * Se audita el ingreso de un Item
     * @param  [array] $datos Información de un Item recién ingresado
     */
	public function auditarAltaItem($datos) {

		// Se obtiene el Id del Item recién ingresado
		$id_item_ingresado = $this->obtenerUltimoIdItem();
		// Se obtiene el Nro. de Orden, una vez generado
		$datos['orden'] = $this->obtenerNroOrdenItem($id_item_ingresado);

		// Se arma la observación
		$observacion  = "Se ingresa el Item: ";
		$observacion .= $datos['anio']."-".$datos['tipo']."-".$datos['numero']."\n";
		$observacion .= $this->armarVistaPreviaItem($datos);

		// Se audita el ingreso del Item respectivo
		$this->auditarEnAdministracion("ALTA", $this->tabla_od_sesion_items, $observacion);
	}

	/**
	 * Se audita la Impresión de la Orden en formato PDF
	 * @param  [array] $datos 	Información de la Orden del Día
	 */
	public function auditarImpresionOrdenPDF($datos) {

		// Se divide la fecha para armar la observación en la auditoría
		$partes_fecha = explode('-', $datos[0]['fecha']);

		$anio = $partes_fecha[0];
		$mes  = $partes_fecha[1];
		$dia  = $partes_fecha[2];

		// Se arma la observación
		$observacion  = "Se imprime la Orden del D&iacute;a:\n";
		$observacion .= " Per&iacute;odo: ".$datos[0]['periodo'];
		$observacion .= " - Reuni&oacute;n: ".$datos[0]['reunion'];
		$observacion .= " - Sesi&oacute;n: ".strtoupper(LibreriaGeneral::quitarAcentos($datos[0]['sesion']));
		$observacion .= " - Fecha: ".$dia." de ".LibreriaGeneral::obtenerNombreMes($mes)." de ".$anio;
		$observacion .= " - Hora: ".$datos[0]['hora']." Hs.";

		// Se audita la Impresión de la Orden en formato PDF
		$this->auditarEnAdministracion("IMPRIME", $this->tabla_od_sesion_items, $observacion);
	}

    /**
     * Se audita la modificación de un Item
     * @param  [array]  $datos 					Información de un Item recién modificado
     * @param  [string] $vista_previa_antes 	Vista previa del Item antes de la modificación, para auditar
     */
	public function auditarModificacionItem($datos, $vista_previa_antes = null) {

		// Se obtiene el Nro. de Orden, una vez generado
		// el nro de Orden puede variar en caso de cambiar de Sección.
		$datos['orden'] = $this->obtenerNroOrdenItem($datos['id']);

		$vista_previa_despues = $this->armarVistaPreviaItem($datos);

		// Se arma la observación, registrando el ANTES y el DESPUES de la vista previa del Item
		$observacion  = "Se modifica el Item: ";
		$observacion .= $datos['anio']."-".$datos['tipo']."-".$datos['numero'];
		$observacion .= "\n ANTES: ".$vista_previa_antes;
		$observacion .= "\n DESPÚES: ".$vista_previa_despues;

		// Se audita la modificación del Item respectivo
		$this->auditarEnAdministracion("MODIFICA", $this->tabla_od_sesion_items, $observacion);
	}

    /**
     * Se audita la eliminación de un Item
     * @param  [array]  $datos 					Información de un Item recién eliminado
     * @param  [string] $vista_previa_antes 	Vista previa del Item antes de la eliminación, para auditar
     */
	public function auditarEliminacionItem($datos, $vista_previa_antes = null) {

		// Se arma la observación, registrando la vista previa del Item eliminado
		$observacion  = "Se elimina el Item: ";
		$observacion .= $datos['anio']."-".$datos['tipo']."-".$datos['numero'];
		$observacion .= "\n".$vista_previa_antes;

		// Se audita la eliminación del Item respectivo
		$this->auditarEnAdministracion("BAJA", $this->tabla_od_sesion_items, $observacion);
	}

    /**
	 * Se arma la vista previa de un Item respectivo.
	 * Utilizado al auditar.
	 * @param  [type] $info_item [description]
	 * @return [type]       [description]
	 */
	public function armarVistaPreviaItem($info_item) {

		// Se retiran las comillas simples en ciertos valores
		$nro_orden	    = str_replace("'", "", $info_item['orden']);
		$codigo_seccion = str_replace("'", "", $info_item['cod_seccion']);
		$extracto 		= str_replace("'", "", $info_item['extracto']);

		// Para mostrar o no el valor del campo "autor"
		$iniciador_autor = '';
		// Para mostrar o no la Carátula
		$caratula = '';
		// Para mostrar o no las Comisiones
		$texto_comisiones = '';
		// Para mostrar o no el Detalle
		$detalle_en_vista_previa = '';

		// Si la sección permite mostrar el Iniciador y/o el Autor
		if ( $this->seMuestraIniciador($codigo_seccion) || $this->seMuestraAutor($codigo_seccion) )
			$iniciador_autor = ( isset($info_item['autor']) && $info_item['autor'] != '' ) ? $info_item['autor'].': ' : '';

		// Si la sección permite mostrar la Carátula en Expedientes
		if ( $info_item['tipo'] == 'E' && $this->seMuestraCaratulaEnExpedientes($codigo_seccion) )
			$caratula = LibreriaGeneral::reemplazarPorMayusculaAcentuada(strtoupper($info_item['caratula'])).': ';

		// Si la sección permite mostrar la Carátula en Notas
		if ( $info_item['tipo'] == 'N' && $this->seMuestraCaratulaEnNotas($codigo_seccion) )
			$caratula = LibreriaGeneral::reemplazarPorMayusculaAcentuada(strtoupper($info_item['caratula'])).': ';

		// Si la sección permite mostrar las Comisiones
		if ( $this->seMuestranComisiones($codigo_seccion) ) {
			// Se arma el texto de las Comisiones
			$texto_comisiones = ' '.LibreriaGeneral::reemplazarPorMayusculaAcentuada($info_item['giros']);
		}
		// Si posee Detalle lo muestra
		if ( isset($info_item['detalle']) && $info_item['detalle'] != '' )
			$detalle_en_vista_previa = ' '.LibreriaGeneral::reemplazarPorMayusculaAcentuada($info_item['detalle']);

		// Si el tipo del documento NO es "Otro", se muestra su Descripción, Iniciador/Autor y Carátula
		$texto_previo_al_extracto = ( $info_item['tipo'] != '0' ) ? $this->armarDescripcionDocumento($info_item).$iniciador_autor.$caratula : 'Otro Nº ';

		// Retorna la Vista Previa armada
		return $nro_orden.'. '.$texto_previo_al_extracto.$extracto.$texto_comisiones.$detalle_en_vista_previa;
	}

	// Gestión de los despachos de un item de la orden del día de sesión
	// -----------------------------------------------------------------

	/**
	 * Se obtienen los X últimos Despachos de un exped. electrónico
	 * para una Orden del Día de Sesión determinada por su Id
	 *
	 * @param  integer  $id_sesion
	 * @param  integer  $anio
	 * @param  string  	$tipo
	 * @param  integer  $numero
	 * @param  integer  $limite
	 *
	 * @return array
	 */
	public function obtenerUltimosDespachos($id_sesion, $anio, $tipo, $numero, $limite = 2)
	{
		$conexion = $this->conectar();

		$sql = "SELECT documento, detalle
				FROM (  SELECT detalle, documento
						FROM ".$this->tabla_expedientes_elec."
						WHERE anio = ".$anio."
						AND tipo = '".$tipo."'
						AND numero = ".$numero."
						AND fecha_hora < (SELECT fecha FROM ".$this->tabla_od_sesion." WHERE id = ".$id_sesion.")
						ORDER BY orden DESC
						LIMIT ".$limite."
					 ) t
				WHERE detalle  LIKE '%despacho%'";

		$resultado = $this->ejecutarQuery($sql);

		$candidatos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		$datos = [];

		if ( isset($candidatos) ) {
			foreach($candidatos as $c)
				if (preg_match('/^\s*despacho(\s+#[0-9]+)?\s*$/i', $c['detalle']) == 1)
					$datos[] = $c;
		}

		return $datos;
	}

	/**
	 * Se obtienen los Documentos de un Expediente Electrónico determinado
	 *
	 * @param  integer  $anio
	 * @param  string  	$tipo
	 * @param  integer  $numero
	 *
	 * @return array    $documentos
	 */
	public function obtenerDocumentosExpedElec($anio, $tipo, $numero)
	{
		$conexion = $this->conectar();

		$sql = "SELECT anio, tipo, numero, orden, detalle, documento, dec1404, fecha_hora
				FROM ".$this->tabla_expedientes_elec."
				WHERE anio = ".$anio."
				AND tipo = '".$tipo."'
				AND numero = ".$numero;

		//LibreriaGeneral::registrarLog("sql_obtenerDocumentosExpedElec", $sql);

		$resultado = $this->ejecutarQuery($sql);

		$documentos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $documentos;
	}

	/**
	 * Se obtiene la información de un Expediente Elec y su Orden respectivo
	 * @param  integer $panio    Año del exped elec
	 * @param  string  $ptipo    Tipo del exped elec
	 * @param  integer $pnumero  Número del exped elec
	 * @param  integer $porden   Orden del exped elec
	 * @return array
	 */
	public function obtenerExpedienteElec($panio, $ptipo, $pnumero, $porden)
	{
		$conexion = $this->conectar();

		$sql = "SELECT anio, tipo, numero, orden, detalle, documento, dec1404, fecha_hora
				FROM $this->tabla_expedientes_elec
				WHERE anio = $panio
				AND tipo = '$ptipo'
				AND numero = $pnumero
				AND orden = $porden";

		//LibreriaGeneral::registrarLog("sql_obtenerExpedienteElec", $sql);

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	/**
	 * Se obtienen los Despachos de un Item determinado
	 *
	 * La lógica de la verificación si está Alcanzado por el Art 11 Decreto 1404 se tomó de
	 * la capa de Negocio NGExpedientesElec, método obtenerExpedElecUrl()
	 *
	 * @param  integer $id_item 	Identificador del item
	 * @return array   $despachos   Despachos asociados al item
	 */
	public function obtenerDespachosItem($id_item)
	{
		$despachos = [];
		$url = '';

		$info_item = $this->obtenerRegistroItem($id_item);

		// Si la sección padre del ítem es de:
		// 40: DICTAMEN DE COMISION
		// 50: EXPEDIENTES Y NOTAS CON DICTAMEN DE COMISION
		if ( substr($info_item['cod_seccion'], 0, 2) === '40' ||
			 substr($info_item['cod_seccion'], 0, 2) === '50'
		) {
			if ( ($info_item['tipo'] == '0') &&
			     ( ! is_null($info_item['anio_despacho_archivo']) ) &&
			     ( ! is_null($info_item['tipo_despacho_archivo']) ) &&
			     ( ! is_null($info_item['numero_despacho_archivo']) )
			) {
				$anio = $info_item['anio_despacho_archivo'];
				$tipo = $info_item['tipo_despacho_archivo'];
				$numero = $info_item['numero_despacho_archivo'];
			} else {
				$anio = $info_item['anio'];
				$tipo = $info_item['tipo'];
				$numero = $info_item['numero'];
			}

			$conexion = $this->conectar();

			$sql = "SELECT
						ID.id_item, ID.orden_actuacion, ID.detalle,
						EE.dec1404, EE.documento
					FROM
						(SELECT * FROM $this->tabla_od_sesion_item_despachos WHERE id_item = $id_item) AS ID
					INNER JOIN
						$this->tabla_expedientes_elec AS EE ON EE.orden = ID.orden_actuacion
					WHERE EE.anio = $anio
					AND EE.tipo = '$tipo'
					AND EE.numero = $numero";

			//LibreriaGeneral::registrarLog("sql_obtenerDespachosItem", $sql);

			$resultado = $this->ejecutarQuery($sql);

			$despachos = $this->crearVector($resultado);
			//LibreriaGeneral::registrarLog("despachos_obtenerDespachosItem", $despachos);

			$this->desconectar($conexion);

			foreach($despachos as &$d) // Se agrega el & para actualizar el array $despachos
			{
				// Se verifica la existencia del documento.
				// Casos:
				// 1. Si el doc no está alcanzado por el decreto 1404, se muestra.
				// 2. Si está alcanzado por el dec 1404 pero el documento existe en disco,
				//    se muestra porque se está en el entorno 'interno' del HCD.
				// 3. Si está alcanzado por el dec 1404 pero el documento NO existe,
				//    se muestra la plantilla (se está en la versión pública).
				$documento = RUTA_PROYECTOS . $d['documento'];
				//LibreriaGeneral::registrarLog("documento", $documento);

				if ($d['dec1404'] == 0) { // No alcanzado por el Art 11 Decreto 1404
					if (file_exists($documento)) {
						// Caso 1
						$url = URL_PROYECTOS_SITIO_WEB.$d['documento'];
					}
				} else { // Alcanzado por el Art 11 Decreto 1404
					$url = (file_exists($documento))
						? URL_PROYECTOS_SITIO_WEB.$d['documento'] // Caso 2
						: URL_SGL_DOC_FALTANTE_DEC1404;           // Caso 3
				}

				// Agrego un random al final, para evitar caches
				$url .= sprintf('?v=%s', rand());

				$d['documento'] = $url;
			}
		}

		return $despachos;
	}

	/**
	 * Se verifica si existe un Despacho en un Item determinado
	 * @param  integer $id_item         	Identificador del item
	 * @param  integer $orden_actuacion 	Orden de la actuación
	 * @return boolean
	 */
	public function existeDespachoItem($id_item, $orden_actuacion)
	{
		$conexion = $this->conectar();

		$sql = "SELECT orden_actuacion
				FROM $this->tabla_od_sesion_item_despachos
				WHERE id_item = $id_item
				AND orden_actuacion = $orden_actuacion";

		//LibreriaGeneral::registrarLog("sql_existeDespachoItem", $sql);

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($dato['orden_actuacion'] != '');
	}

	/**
	 * Se asignan un despacho en un item determinado
	 * @param  integer $id_item 			Identificador del item
	 * @param  integer $orden_actuacion		Orden de la actuación
	 * @param  string  $detalle             Texto del detalle de la actuación
	 * @return boolean
	 */
	public function asignarDespacho($id_item, $orden_actuacion, $detalle)
	{
		if ( $this->existeDespachoItem($id_item, $orden_actuacion) )
			return false;
		else
		{
			$conexion = $this->conectar();

			$query = "INSERT INTO $this->tabla_od_sesion_item_despachos
						(id_item, orden_actuacion, detalle)
					  VALUES
					  	($id_item, $orden_actuacion, '$detalle')";

			//LibreriaGeneral::registrarLog("query_asignarDespacho", $query);

			if ( !$this->ejecutarQuery($query) )
				return false;

			$this->desconectar($conexion);

			// Se audita el ALTA de un despacho en el Item
			$this->auditarEnAdministracion(
				"ALTA",
				$this->tabla_od_sesion_item_despachos,
				"Se ingresa un despacho, para el Item de Id ".$id_item);

			return true;
		}
	}

	/**
	 * Se actualiza el texto del campo detalle del despacho respectivo
	 * @param  integer $id_item      		Identificador del Item
	 * @param  integer $orden_actuacion		Orden de la actuación
	 * @param  string  $detalle  			Texto del detalle
	 * @return boolean
	 */
	public function actualizarDetalleDespacho($id_item, $orden_actuacion, $detalle) {

		$conexion = $this->conectar();

		$query = "UPDATE $this->tabla_od_sesion_item_despachos
				  	SET detalle = {$this->revisarValorAtributo(strip_tags($detalle))}
				  WHERE id_item = $id_item
				  AND orden_actuacion = $orden_actuacion";

		//LibreriaGeneral::registrarLog("query_actualizarDetalleDespacho", $query);

		if ( !$this->ejecutarQuery($query) )
			return false;

		$this->desconectar($conexion);

		// Se audita la Modificación de un despacho en el Item
		$this->auditarEnAdministracion(
			"MODIFICA",
			$this->tabla_od_sesion_item_despachos,
			"Se modifica un despacho, para el Item de Id ".$id_item);

		return true;
	}

	/**
	 * Se elimina un despacho de un item determinado
	 * @param  integer $id_item      		Identificador del Item
	 * @param  integer $orden_actuacion		Orden de la actuación
	 * @return boolean
	 */
	public function eliminarDespacho($id_item, $orden_actuacion) {

		$conexion = $this->conectar();

		$query = "DELETE FROM $this->tabla_od_sesion_item_despachos
				  WHERE id_item = $id_item
				  AND orden_actuacion = $orden_actuacion";

		//LibreriaGeneral::registrarLog("query_eliminarDespacho", $query);

		if ( !$this->ejecutarQuery($query) )
			return false;

		$this->desconectar($conexion);

		// Se audita la BAJA del despacho en el Item
		$this->auditarEnAdministracion(
			"BAJA",
			$this->tabla_od_sesion_item_despachos,
			"Se elimina un despacho, para el Item de Id ".$id_item);

		return true;
    }
}
