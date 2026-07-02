<?php
if (!isset($_SESSION)) {
	session_start();
}

class secciones_orden_sesionModel extends ModeloBaseMySQLi {

	public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}

	public function listar() {
		$conexion = $this->conectar();

		$filtro = "";

		// PARA FILTRAR POR CODIGO
		if ($this->filtro['f_codigo'] != '') { // && $this->filtro['f_seccion_padre'] == '0'
			$filtro .= " AND codigo LIKE '" . $this->filtro['f_codigo'] . "%'";
		}

		// PARA FILTRAR POR NOMBRE
		if ($this->filtro['f_nombre'] != '') {
			$filtro .= " AND nombre LIKE '%" . $this->filtro['f_nombre'] . "%'";
		}

		// PARA FILTRAR POR SECCION PADRE
		if ($this->filtro['f_seccion_padre'] != '0') {
			// SE TOMA EL PRIMER PAR DE DIGITOS DE LA SECCION PADRE
			$primer_par_digitos = substr($this->filtro['f_seccion_padre'], 0, 2);

			$filtro .= " AND codigo LIKE '" . $primer_par_digitos . "%'
						 AND codigo <> '" . $primer_par_digitos . "000000'";
		}

		// para limitar el listado
		$registro_inicial = (isset($this->filtro['inicio']) && $this->filtro['inicio'] != '') ? $this->filtro['inicio'] : 0;

		$sql = "SELECT * FROM " . $this->tabla_od_sesion_seccion . "
				WHERE habilitado <> 3
				" . $filtro . "
			    ORDER BY " . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
			    LIMIT " . $registro_inicial . ", " . $this->filtro['rango'];

		//LibreriaGeneral::registrarLog("sql_listar_secciones", $sql);

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerCantidad() {

		$conexion = $this->conectar();

		$filtro = "";
		
		// PARA FILTRAR POR CODIGO
		if ($this->filtro['f_codigo'] != '') { // && $this->filtro['f_seccion_padre'] == '0'
			$filtro .= " AND codigo LIKE '" . $this->filtro['f_codigo'] . "%'";
		}

		// PARA FILTRAR POR NOMBRE
		if ($this->filtro['f_nombre'] != '') {
			$filtro .= " AND nombre LIKE '%" . $this->filtro['f_nombre'] . "%'";
		}

		// PARA FILTRAR POR SECCION PADRE
		if ($this->filtro['f_seccion_padre'] != '0') {
			// SE TOMA EL PRIMER PAR DE DIGITOS DE LA SECCION PADRE
			$primer_par_digitos = substr($this->filtro['f_seccion_padre'], 0, 2);

			$filtro .= " AND codigo LIKE '" . $primer_par_digitos . "%'
						 AND codigo <> '" . $primer_par_digitos . "000000'";
		}

		$query = "SELECT COUNT(codigo) AS cantidad
				  FROM " . $this->tabla_od_sesion_seccion . "
				  WHERE habilitado <> 3
				  " . $filtro;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	public function obtenerRegistro($codigo) {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_od_sesion_seccion . " WHERE codigo = '" . $codigo . "'";

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	public function existe($codigo) {

		$conexion = $this->conectar();

		$query = "SELECT codigo FROM " . $this->tabla_od_sesion_seccion . " WHERE codigo = '" . $codigo . "'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ($dato['codigo'] != '');
	}

	public function existeNombre($codigo, $nombre) {

		$conexion = $this->conectar();

		$query = "SELECT nombre
				  FROM " . $this->tabla_od_sesion_seccion . "
				  WHERE nombre = '" . $nombre . "'
				  AND codigo <> '" . $codigo . "'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ($dato['nombre'] != '');
	}

	public function validarDatos($datos) {

		$datos['nombre'] = isset($datos['nombre']) ? $this->revisarValorAtributo(strip_tags($datos['nombre'])) : '';

		$datos['mostrar_iniciador'] = isset($datos['mostrar_iniciador']) ? $this->revisarValorAtributo(strip_tags($datos['mostrar_iniciador']), 0) : 0;

		$datos['mostrar_autor'] = (isset($datos['mostrar_autor'])) ? $this->revisarValorAtributo(strip_tags($datos['mostrar_autor']), 0) : 0;

		$datos['mostrar_caratula_en_exped'] = isset($datos['mostrar_caratula_en_exped']) ? $this->revisarValorAtributo(strip_tags($datos['mostrar_caratula_en_exped']), 0) : 0;

		$datos['mostrar_caratula_en_nota'] = isset($datos['mostrar_caratula_en_nota']) ? $this->revisarValorAtributo(strip_tags($datos['mostrar_caratula_en_nota']), 0) : 0;

		$datos['mostrar_comisiones'] = isset($datos['mostrar_comisiones']) ? $this->revisarValorAtributo(strip_tags($datos['mostrar_comisiones']), 0) : 0;

		$datos['mostrar_con_salto_pagina'] = isset($datos['mostrar_con_salto_pagina']) ? $this->revisarValorAtributo(strip_tags($datos['mostrar_con_salto_pagina']), 0) : 0;
		
		$datos['permite_carga_grupal'] = isset($datos['permite_carga_grupal']) ? $this->revisarValorAtributo(strip_tags($datos['permite_carga_grupal']), 0) : 0;

		return $datos;
	}

	//	SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO POR OTRO USUARIO
	public function noLoModificoOtroUsuario() {

		$conexion = $this->conectar();

		$query = "SELECT codigo
				  FROM " . $this->tabla_od_sesion_seccion . "
				  WHERE codigo = '" . $_SESSION['codigo_original'] . "'
				  " . $this->adaptarValorStringParaFiltro('nombre') . "
				  AND mostrar_iniciador = " . $_SESSION['mostrar_iniciador_original'] . "
				  AND mostrar_autor = " . $_SESSION['mostrar_autor_original'] . "
				  AND mostrar_caratula_en_exped = " . $_SESSION['mostrar_caratula_en_exped_original'] . "
				  AND mostrar_caratula_en_nota = " . $_SESSION['mostrar_caratula_en_nota_original'] . "
				  AND mostrar_comisiones = " . $_SESSION['mostrar_comisiones_original'] . "
				  AND mostrar_con_salto_pagina = " . $_SESSION['mostrar_con_salto_pagina_original'] . "
				  AND permite_carga_grupal = " . $_SESSION['permite_carga_grupal_original'] . "
				  AND habilitado = " . $_SESSION['habilitado_original'];

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($dato['codigo']);
	}

	public function insertar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO " . $this->tabla_od_sesion_seccion . " (codigo, nombre, mostrar_iniciador, mostrar_autor, mostrar_caratula_en_exped, mostrar_caratula_en_nota, mostrar_comisiones, mostrar_con_salto_pagina, permite_carga_grupal, habilitado)
				  VALUES('" . $datos['codigo'] . "',
				  		  " . $datos['nombre'] . ",
				  		  " . $datos['mostrar_iniciador'] . ",
				  		  " . $datos['mostrar_autor'] . ",
				  		  " . $datos['mostrar_caratula_en_exped'] . ",
				  		  " . $datos['mostrar_caratula_en_nota'] . ",
				  		  " . $datos['mostrar_comisiones'] . ",
				  		  " . $datos['mostrar_con_salto_pagina'] . ",
				  		  " . $datos['permite_carga_grupal'] . ",
				  		  1);";

		//LibreriaGeneral::registrarLog("query_insertar_seccion", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);

			// Se audita el ingreso de la Sección
			$this->auditarEnAdministracion("ALTA", $this->tabla_od_sesion_seccion, "Se ingresa la Sección: " . LibreriaGeneral::eliminarComillaSimple($datos['nombre']));
		}

		return true;
	}

	public function modificar($datos) {

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "UPDATE " . $this->tabla_od_sesion_seccion . "
				  SET nombre = " . $datos['nombre'] . ",
					  mostrar_iniciador = " . $datos['mostrar_iniciador'] . ",
					  mostrar_autor = " . $datos['mostrar_autor'] . ",
					  mostrar_caratula_en_exped = " . $datos['mostrar_caratula_en_exped'] . ",
					  mostrar_caratula_en_nota = " . $datos['mostrar_caratula_en_nota'] . ",
					  mostrar_comisiones = " . $datos['mostrar_comisiones'] . ",
					  mostrar_con_salto_pagina = " . $datos['mostrar_con_salto_pagina'] . ",
					  permite_carga_grupal = " . $datos['permite_carga_grupal'] . "
				  WHERE codigo = '" . $datos['codigo'] . "'";

		//LibreriaGeneral::registrarLog("query_modificar", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);

			// Se audita el ingreso de la Sección
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_od_sesion_seccion, "Se modifica la Sección: " . LibreriaGeneral::eliminarComillaSimple($datos['nombre']));
		}

		return true;
	}

	public function eliminar($codigo)
	{
		// Previamente se obtiene el nombre
		$nombre = $this->obtenerNombre($codigo);

		$conexion = $this->conectar();

		// SE VERIFICA SI LA SECCION ESTA ASIGNADA A UN ITEM DE UNA ORDEN DEL DIA EN SESION
		$query = "SELECT cod_seccion FROM " . $this->tabla_od_sesion_items . " WHERE cod_seccion = '" . $codigo . "'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// SI ESTA ASIGNADA NO SE DEBE ELIMINAR
		if ($dato['cod_seccion']) {
			return false;
		} else {
			// SI NO ESTA ASIGNADA PUEDE ELIMINARSE
			$query = "DELETE FROM " . $this->tabla_od_sesion_seccion . " WHERE codigo = '" . $codigo . "'";

			if (!$this->ejecutarQuery($query)) {
				return false;
			} else {
				$this->desconectar($conexion);

				// Se audita el ingreso de la Sección
				$this->auditarEnAdministracion("BAJA", $this->tabla_od_sesion_seccion, "Se elimina la Sección: " . $nombre);
			}
		}

		return true;
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 *
	 * @param  integer $codigo
	 * @param  integer $habilitado
	 * @return boolean true|false
	 */
	public function modificarEstado($codigo, $habilitado) {

		$conexion = $this->conectar();

		$valor_habilitado = ($habilitado == 1) ? 0 : 1;

		$query = "UPDATE " . $this->tabla_od_sesion_seccion . " SET habilitado = ".$valor_habilitado." WHERE codigo = '" . $codigo . "'";

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

		$this->desconectar($conexion);

		return true;
	}

	public function obtenerNombre($codigo) {

		$conexion = $this->conectar();

		$sql = "SELECT nombre FROM " . $this->tabla_od_sesion_seccion . " WHERE codigo = '" . $codigo . "'";

		$resultado = $this->ejecutarQuery($sql);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['nombre'];
	}

	public function obtenerPadres() {

		$conexion = $this->conectar();

		$sql = "SELECT *
				FROM " . $this->tabla_od_sesion_seccion . "
				WHERE habilitado = 1
				AND codigo LIKE '%000000'
				ORDER BY codigo";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	public function obtenerDatosSeccionPadre($codigo) {

		$conexion = $this->conectar();

		// PRIMER PAR DE DIGITOS DEL CODIGO DE LA SECCION
		$primer_par_digitos = substr($codigo, 0, 2);

		$sql = "SELECT * FROM " . $this->tabla_od_sesion_seccion . "
				WHERE habilitado = 1
				AND codigo = '" . $primer_par_digitos . "000000'";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * A UTILIZAR A FUTURO
	 *****************************************************************/

	/**
	 * [marcarEnEdicion description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 *
	public function marcarEnEdicion($id) {
	$conexion = $this->conectar();

	$query = "UPDATE " . $this->tabla_od_sesion_seccion . "
	SET editando = '1'
	WHERE id = " . $id;

	if (!$this->ejecutarQuery($query)) {
	return false;
	}

	$this->desconectar($conexion);

	return true;
	}

	/**
	 * [desmarcarEnEdicion description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 *
	public function desmarcarEnEdicion($id) {
	$conexion = $this->conectar();

	$query = "UPDATE " . $this->tabla_od_sesion_seccion . "
	SET editando = '0'
	WHERE id = " . $id;

	if (!$this->ejecutarQuery($query)) {
	return false;
	}

	$this->desconectar($conexion);

	return true;
	}

	/**
	 * [estaEnEdicion description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 *
	public function estaEnEdicion($id) {
	$conexion = $this->conectar();

	$query = "SELECT id
	FROM " . $this->tabla_od_sesion_seccion . "
	WHERE editando = '1'
	AND id = " . $id;

	$resultado = $this->ejecutarQuery($query);

	$dato = $this->obtenerFila($resultado);

	// distinto de vacío = se encuentra en edición
	return ($dato['id'] != '');
	}

	/**
	 * Se audita la consulta de un registro determinado
	 * @param  [array] $registro Info del registro
	 *
	public function auditarConsultaRegistro($registro) {

	$modelo = new auditoriaModel();

	$datos_log = Array();
	$datos_log['au_operacion'] = "CONSULTA";
	$datos_log['au_tabla'] = $this->tabla_od_sesion_seccion;
	$datos_log['au_id_registro'] = $registro['id'];
	$datos_log['au_observaciones'] = addslashes("Se consulta la Secci&oacute;n " . $registro['nombre'] . ".");

	// SE CARGA EN auditoria EL MOVIMIENTO
	$modelo->registrarMovimiento($datos_log);
	}
	/********************************************************************/
}
?>
