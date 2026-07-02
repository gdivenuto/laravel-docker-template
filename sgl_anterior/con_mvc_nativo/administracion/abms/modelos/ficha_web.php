<?php
if (!isset($_SESSION)) {
	session_start();
}

class fichaWebModel extends ModeloBaseMySQLi
{
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Se obtiene la conexion con la DB, según un perfil determinado
	 */
	public function conectar() {
		// SE CONECTA SEGUN EL ID DEL SISTEMA
		return parent::conectarDB(1);
	}

    public function listar()
	{    
		$conexion = $this->conectar();
		
		$filtro = "";
		$limite = "";
		
		// PARA FILTRAR POR LEGAJO
		if ( ($this->filtro['f_legajo'] != 0) && ($this->filtro['f_legajo'] != '') )
			$filtro .= " AND P.p_legajo = ".$this->filtro['f_legajo'];
	
		// PARA FILTRAR POR APELLIDO Ó NOMBRE
		if ( $this->filtro['f_apellido_y_nombre'] != '' )
			$filtro .= " AND ( P.p_apellido LIKE '%".addslashes($this->filtro['f_apellido_y_nombre'])."%' OR P.p_nombre LIKE '%".addslashes($this->filtro['f_apellido_y_nombre'])."%')";
	
		// PARA LISTAR SÓLO LOS ACTIVOS
		if ( $this->filtro['f_activos']  == '1' )
		{
			$filtro .= " AND P.p_legajo IN (SELECT c_legajo
											FROM ".$this->tabla_cargos."
											WHERE c_fecha_alta = ( SELECT MAX( c_fecha_alta )
																   FROM ".$this->tabla_cargos."
																   WHERE c_legajo = P.p_legajo
															     )
											AND (c_fecha_baja IS NULL OR c_fecha_baja > CURDATE()) 
										   )";
		}
	
		// PARA LIMITAR EL LISTADO
		if ( $this->filtro['rango'] != 0 )
			$limite = " LIMIT ".$this->filtro['inicio'].", ".$this->filtro['rango'];
		
		// QUERY A EJECUTAR
		//***********************************************************************************************
		$sql = "SELECT P.*
				FROM ".$this->tabla_personal." AS P
				WHERE P.p_apellido IS NOT NULL
				AND P.p_legajo IN (
						SELECT C.c_legajo 
						FROM ".$this->tabla_cargos." AS C
						WHERE (
							C.c_nomenclador = '".$this->id_cargo_concejal."' 
							OR 
							C.c_nomenclador = '".$this->id_cargo_secretario_hcd."'
						)
						AND C.c_fecha_alta = (
							SELECT MAX( c_fecha_alta )
							FROM ".$this->tabla_cargos."
							WHERE c_legajo = C.c_legajo 
						)
				)
				".$filtro."
				ORDER BY ".$_SESSION['ultimo_campo']." ".$_SESSION['ultimo_sentido']."
			   	" . $limite;

		$resultado = $this->ejecutarQuery($sql);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }
    
    public function obtenerCantidad()
    {
    	$conexion = $this->conectar();
    
    	$filtro = "";
    
    	// PARA FILTRAR POR LEGAJO
    	if ( ($this->filtro['f_legajo'] != 0) && ($this->filtro['f_legajo'] != '') )
    		$filtro .= " AND P.p_legajo = ".$this->filtro['f_legajo'];
    
    	// PARA FILTRAR POR APELLIDO Ó NOMBRE
    	if ( $this->filtro['f_apellido_y_nombre'] != '' )
    		$filtro .= " AND ( P.p_apellido LIKE '%".addslashes($this->filtro['f_apellido_y_nombre'])."%' OR P.p_nombre LIKE '%".addslashes($this->filtro['f_apellido_y_nombre'])."%')";
    
    	// PARA LISTAR SÓLO LOS ACTIVOS
    	if ( $this->filtro['f_activos']  == '1' )
    	{
    		$filtro .= " AND P.p_legajo IN (SELECT c_legajo
											FROM ".$this->tabla_cargos."
											WHERE c_fecha_alta = ( SELECT MAX( c_fecha_alta )
																   FROM ".$this->tabla_cargos."
																   WHERE c_legajo = P.p_legajo
															     )
											AND (c_fecha_baja IS NULL OR c_fecha_baja > CURDATE())
										   )";
    	}
    
    	$sql = "SELECT COUNT(*) AS cantidad
				FROM ".$this->tabla_personal." AS P
				WHERE P.p_apellido IS NOT NULL
				AND P.p_legajo IN (
						SELECT C.c_legajo 
						FROM ".$this->tabla_cargos." AS C
						WHERE (
							C.c_nomenclador = '".$this->id_cargo_concejal."' 
							OR 
							C.c_nomenclador = '".$this->id_cargo_secretario_hcd."'
						)
						AND C.c_fecha_alta = (
							SELECT MAX( c_fecha_alta )
							FROM ".$this->tabla_cargos."
							WHERE c_legajo = C.c_legajo 
						)
				)".$filtro;
    
    	$resultado = $this->ejecutarQuery($sql);
    
    	$dato = $this->obtenerFila($resultado);
    
    	$this->desconectar($conexion);
    
    	return $dato['cantidad'];
    }
    
    /**
     * Se obtiene la info de la ficha web de un legajo determinado
     */
    public function obtenerRegistro($legajo)
    {
    	$conexion = $this->conectar();
    
    	$sql = "SELECT * FROM ".$this->tabla_fichas_web." WHERE fw_legajo = ".$legajo;
    
    	$resultado = $this->ejecutarQuery($sql);
    
    	$registro = $this->obtenerFila($resultado);
    
    	$this->desconectar($conexion);
    
    	return $registro;
    }

    /**
     * Se obtienen los Autores activos
     * @return [array] Listado de autores
     */
    public function obtenerAutores() {

    	$conexion = $this->conectar();

    	$query = "SELECT tipo_grp, codigo_grp, descripcion_grp
				  FROM ".$this->tabla_lugares."
				  WHERE tipo_grp = 'V'
				  AND habilitado_grp = 1";

		$resultado = $this->ejecutarQuery($query);
		
		$datos = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $datos;
    }

	/**
	 * Se validan los datos antes de utilizarlos en la query
	 * 
	 * @param array $datos
	 * @return array $datos (validado)
	 */
	public function validar($datos)
	{
		$datos['fw_funcion']              = $this->revisarValorAtributo($datos['fw_funcion']);
		
		$datos['fw_es_presidente_bloque'] = $this->revisarValorAtributo($datos['fw_es_presidente_bloque'], 0);
		
		$datos['fw_anio_inicio']          = $this->revisarValorAtributo($datos['fw_anio_inicio']);
		
		$datos['fw_anio_fin']             = $this->revisarValorAtributo($datos['fw_anio_fin']);
		
		$datos['fw_foto']                 = $this->revisarValorAtributo($datos['fw_foto']);
		
		$datos['fw_profesion']            = $this->revisarValorAtributo($datos['fw_profesion']);
		
		$datos['fw_mail']                 = $this->revisarValorAtributo($datos['fw_mail']);
		
		$datos['fw_telefono']             = $this->revisarValorAtributo($datos['fw_telefono']);
		
		$datos['fw_facebook']             = $this->revisarValorAtributo($datos['fw_facebook']);
		
		$datos['fw_instagram']            = $this->revisarValorAtributo($datos['fw_instagram']);
		
		$datos['fw_twitter']              = $this->revisarValorAtributo($datos['fw_twitter']);
		
		$datos['fw_sitio_web']            = $this->revisarValorAtributo($datos['fw_sitio_web']);
		
		$datos['fw_autor_codigo']         = $this->revisarValorAtributo($datos['fw_autor_codigo']);
	
		return $datos;
	}
	
	/**
	 * Se verifica que no se haya ingresado antes la misma informacion por otro usuario 
	 * @return boolean
	 */
	public function noLoModificoOtroUsuario()
	{
		$conexion = $this->conectar();
	
		$query = "SELECT fw_legajo
				  FROM ".$this->tabla_fichas_web."
				  WHERE fw_legajo = ".$_SESSION['fw_legajo_original']."
				  ".$this->adaptarValorStringParaFiltro('fw_funcion')."
				  ".$this->adaptarValorStringParaFiltro('fw_es_presidente_bloque')."
				  ".$this->adaptarValorStringParaFiltro('fw_anio_inicio')."
				  ".$this->adaptarValorStringParaFiltro('fw_anio_fin')."
				  ".$this->adaptarValorStringParaFiltro('fw_foto')."
				  ".$this->adaptarValorStringParaFiltro('fw_profesion')."
				  ".$this->adaptarValorStringParaFiltro('fw_mail')."
				  ".$this->adaptarValorStringParaFiltro('fw_telefono')."
				  ".$this->adaptarValorStringParaFiltro('fw_facebook')."
				  ".$this->adaptarValorStringParaFiltro('fw_instagram')."
				  ".$this->adaptarValorStringParaFiltro('fw_twitter')."
				  ".$this->adaptarValorStringParaFiltro('fw_sitio_web')."
				  ".$this->adaptarValorStringParaFiltro('fw_autor_codigo')."
				 ";

		$resultado = $this->ejecutarQuery($query);
		
		$registro = $this->obtenerFila($resultado);
	
		$this->desconectar($conexion);
	
		return ( $registro['fw_legajo'] );
	}

	/**
	 * Se verifica si ya existe info registrada de la ficha web de un Legajo, en un año de Inicio respectivo
	 * @param  [integer] $legajo  Legajo
	 * @return [boolean]          True/False
	 */
    public function existe($legajo)
	{
		$conexion = $this->conectar();
		
		$query = "SELECT fw_legajo FROM ".$this->tabla_fichas_web." WHERE fw_legajo = ".$legajo;
	
		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);
		
		// Si existe o no
		return ( $dato['fw_legajo'] != '' );	
    }

	public function guardar($datos)
	{
		$conexion = $this->conectar();
	
		$datos = $this->validar($datos);

		$query = "INSERT INTO ".$this->tabla_fichas_web." 
						(fw_legajo, 
						 fw_funcion, 
						 fw_es_presidente_bloque, 
						 fw_anio_inicio, 
						 fw_anio_fin, 
						 fw_foto, 
						 fw_profesion, 
						 fw_mail, 
						 fw_telefono, 
						 fw_facebook, 
						 fw_instagram, 
						 fw_twitter, 
						 fw_sitio_web, 
						 fw_autor_codigo)
				  VALUES(".$datos['fw_legajo'].",
				  		 ".$datos['fw_funcion'].",
				  		 ".$datos['fw_es_presidente_bloque'].",
				  		 ".$datos['fw_anio_inicio'].",
				  		 ".$datos['fw_anio_fin'].",
				  		 ".$datos['fw_foto'].",
				  		 ".$datos['fw_profesion'].",
						 ".$datos['fw_mail'].",
						 ".$datos['fw_telefono'].",
						 ".$datos['fw_facebook'].",
						 ".$datos['fw_instagram'].",
						 ".$datos['fw_twitter'].",
						 ".$datos['fw_sitio_web'].",
						 ".$datos['fw_autor_codigo']."
						)
				  ON DUPLICATE KEY UPDATE 
				  		fw_funcion = ".$datos['fw_funcion'].",
					    fw_es_presidente_bloque = ".$datos['fw_es_presidente_bloque'].",
				        fw_anio_inicio = ".$datos['fw_anio_inicio'].",
				        fw_anio_fin = ".$datos['fw_anio_fin'].",
				        fw_foto = ".$datos['fw_foto'].",
				        fw_profesion = ".$datos['fw_profesion'].",
				  	    fw_mail = ".$datos['fw_mail'].",
					    fw_telefono = ".$datos['fw_telefono'].",
					    fw_facebook = ".$datos['fw_facebook'].",
					    fw_instagram = ".$datos['fw_instagram'].",
					    fw_twitter = ".$datos['fw_twitter'].",
					    fw_sitio_web = ".$datos['fw_sitio_web'].",
					    fw_autor_codigo = ".$datos['fw_autor_codigo'];

		if ( !$this->ejecutarQuery($query) ) {
			return false;
		} else {	
			$this->desconectar($conexion);

			// Se audita
			$this->auditarEnAdministracion("ALTA/MODIFICA", $this->tabla_fichas_web, "Se guarda la Ficha Web del Legajo ".$datos['fw_legajo']);
		}
	
		return true;
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
	
		$query = "SELECT fw_foto FROM ".$this->tabla_fichas_web." WHERE fw_legajo = ".$p_legajo;
		
		$resultado = $this->ejecutarQuery($query);
	
		$dato = $this->obtenerFila($resultado);
	
		$this->desconectar($conexion);
	
		return $dato['fw_foto'];
	}

	/**
	 * Se registra el nombre de la foto de un legajo respectivo
	 * 
	 * @param integer $p_legajo
	 * @return boolean
	 */
	public function registrarNombreFoto($p_legajo, $nombre_foto)
	{
		$conexion = $this->conectar();
	
		// Si EXISTE el legajo
		if ( $this->existe($p_legajo) )
		{
			// Se registra el nombre de la foto
			$query = "UPDATE ".$this->tabla_fichas_web."
					  SET fw_foto = '".$nombre_foto."'
					  WHERE fw_legajo = ".$p_legajo;
		}
		else // Si no existe
		{
			// Se ingresa la info de la ficha web de un Legajo respectivo
			$query = "INSERT INTO ".$this->tabla_fichas_web." (fw_legajo, fw_foto)
				  	  VALUES(".$p_legajo.", '".$nombre_foto."')";
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
	
		$query = "UPDATE ".$this->tabla_fichas_web." SET fw_foto = '' WHERE fw_legajo = ".$p_legajo;
	
		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectar($conexion);
	
		return true;
	}

	/**
	 * Se ingresa el nombre de la foto en la DB
	 * @param  [integer] $id     [description]
	 * @param  [string] $nombre_archivo  [description]
	 * @return [boolean]                 [description]
	 */
	public function ingresarNombreFoto($id, $nombre_archivo) {

		$conexion = $this->conectar();

		$query = "UPDATE " . $this->tabla_fichas_web . "
				  SET fw_foto = '" . $nombre_archivo . "'
				  WHERE fw_legajo = " . $id;

		//LibreriaGeneral::registrarLog("query_ingresarNombreFoto", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
		}
		return true;
	}

	/**
	 * Se elimina la foto principal del registro
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function eliminarFoto($id) {

		$conexion = $this->conectar();

		$query = "UPDATE " . $this->tabla_fichas_web . " SET fw_foto = null WHERE fw_legajo = " . $id;
		//LibreriaGeneral::registrarLog("query_eliminarFoto", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_fichas_web, "Se elimina la foto del registro.");
		}

		return true;
	}

}
    
