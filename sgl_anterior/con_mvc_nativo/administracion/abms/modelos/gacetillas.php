<?php
if (!isset($_SESSION)) {
	session_start();
}

class gacetillasModel extends ModeloBaseMySQLi {

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

	/**
	 * Se obtiene el listado, en base a un criterio determinado en la query
	 * @return array $datos
	 */
	public function listar() {
		$conexion = $this->conectar();

		$filtro = "";
		$limite = "";

		// Para filtrar por Tipo
		if ($this->filtro['f_tipo'] != '' && $this->filtro['f_tipo'] != '0') {
			$filtro .= " AND g_tipo = '" . $this->filtro['f_tipo'] . "'";
		}

		// Para filtrar por Acto
		if ($this->filtro['f_acto'] != '' && $this->filtro['f_acto'] != '0') {
			$filtro .= " AND g_acto = '" . $this->filtro['f_acto'] . "'";
		}

		// Para filtrar por Fecha
		if ($this->filtro['f_fecha'] != '') {
			$filtro .= " AND g_fecha = '" . $this->filtro['f_fecha'] . "'";
		}

		// Para filtrar por Título
		if (isset($this->filtro['f_titulo']) && $this->filtro['f_titulo'] != '') {

			$filtro .= " AND g_titulo LIKE '%" . str_replace(" ", "%", $this->filtro['f_titulo']) . "%'";
		}

		// PARA LIMITAR EL LISTADO
		if ($this->filtro['rango'] != 0) {
			$limite = " LIMIT " . $this->filtro['inicio'] . ", " . $this->filtro['rango'];
		}

		$sql = "SELECT * FROM " . $this->tabla_gacetillas . "
				WHERE g_titulo IS NOT NULL
				" . $filtro . "
				ORDER BY " . $_SESSION['ultimo_campo'] . " " . $_SESSION['ultimo_sentido'] . "
				" . $limite;

		//LibreriaGeneral::registrarLog("sql_listar_gacetillas", $sql);

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se obtiene la cantidad de registros encontrados, en base a un criterio determinado en la query
	 */
	public function obtenerCantidad() {
		$conexion = $this->conectar();

		$filtro = "";

		// Para filtrar por Tipo
		if ($this->filtro['f_tipo'] != '' && $this->filtro['f_tipo'] != '0') {
			$filtro .= " AND g_tipo = '" . $this->filtro['f_tipo'] . "'";
		}

		// Para filtrar por Acto
		if ($this->filtro['f_acto'] != '' && $this->filtro['f_acto'] != '0') {
			$filtro .= " AND g_acto = '" . $this->filtro['f_acto'] . "'";
		}

		// Para filtrar por Fecha
		if ($this->filtro['f_fecha'] != '') {
			$filtro .= " AND g_fecha = '" . $this->filtro['f_fecha'] . "'";
		}

		// Para filtrar por Título
		if (isset($this->filtro['f_titulo']) && $this->filtro['f_titulo'] != '') {

			$filtro .= " AND g_titulo LIKE '%" . str_replace(" ", "%", $this->filtro['f_titulo']) . "%'";
		}

		$query = "SELECT COUNT(g_codigo) AS cantidad
				  FROM " . $this->tabla_gacetillas . "
				  WHERE g_titulo IS NOT NULL " . $filtro;

		//LibreriaGeneral::registrarLog("query_obtenerCantidad_gacetillas", $query);

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		return $dato['cantidad'];
	}

	/**
	 * Se obtiene el ultimo Id registrado en la DB
	 *
	 * @see ModelBase::obtenerUltimoCodigo()
	 */
	public function obtenerUltimoId() {
		return parent::obtenerUltimoCodigo($this->tabla_gacetillas, 'g_codigo');
	}

	/**
	 * Se obtiene la informacion de un registro determinado por su Id
	 *
	 * @param integer $id
	 * @return array $registro
	 */
	public function obtenerRegistro($id = 0) {

		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_gacetillas . " WHERE g_codigo = " . $id;

		$resultado = $this->ejecutarQuery($sql);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
	}

	public function existe($fecha, $titulo) {
		$conexion = $this->conectar();

		$query = "SELECT g_codigo
				  FROM " . $this->tabla_gacetillas . "
				  WHERE g_fecha = '" . $this->formatearFechaMySQL($fecha) . "'
				  AND g_titulo = '" . $titulo . "'";

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ($dato['g_codigo'] != '');
	}

	/**
	 * Se existe un código determinado
	 * @param  [integer] $codigo Código de la Gacetilla
	 * @return [boolean]         true|False
	 */
	public function existeCodigo($codigo) {
		
		$conexion = $this->conectar();

		$query = "SELECT g_codigo FROM " . $this->tabla_gacetillas . " WHERE g_codigo = " . $codigo;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		// Si existe o no
		return ($dato['g_codigo'] != '');
	}

	public function validarDatos($datos) {

		$datos['g_fecha'] = $this->revisarValorFechaAtributo($datos['g_fecha']);

		$datos['g_titulo'] = $this->revisarValorAtributo(strip_tags($datos['g_titulo']));

		$datos['g_texto'] = $this->revisarValorAtributo($datos['g_texto']);

		$datos['g_foto'] = $this->revisarValorAtributo($datos['g_foto']);

		$datos['g_tipo'] = $this->revisarValorAtributo($datos['g_tipo'], 'P');

		$datos['g_acto'] = $this->revisarValorAtributo($datos['g_acto']);

		$datos['g_enviar_por_mail'] = $this->revisarValorAtributo($datos['g_enviar_por_mail'], 0);

		$datos['g_id_mail'] = $this->revisarValorAtributo($datos['g_id_mail'], 0);

		return $datos;
	}

	/**
	 * Se verifica si el registro no ha sido modificado por otro usuario
	 *
	 * @return boolean
	 */
	public function noLoModificoOtroUsuario() {

		$conexion = $this->conectar();

		$query = "SELECT g_codigo
				  FROM " . $this->tabla_gacetillas . "
				  WHERE g_codigo = '" . $_SESSION['g_codigo_original'] . "'
				  " . $this->adaptarValorStringParaFiltro('g_fecha') . "
				  " . $this->adaptarValorStringParaFiltro('g_titulo') . "
				  " . $this->adaptarValorStringParaFiltro('g_texto') . "
				  " . $this->adaptarValorStringParaFiltro('g_foto') . "
				  AND g_tipo = '" . $_SESSION['g_tipo_original'] . "'
				  " . $this->adaptarValorStringParaFiltro('g_acto') . "
				  " . $this->adaptarValorStringParaFiltro('g_enviar_por_mail') . "
				  " . $this->adaptarValorStringParaFiltro('g_id_mail') . "
				   AND g_habilitada = " . $_SESSION['g_habilitada_original'];

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return ($dato['g_codigo']);
	}

	/**
	 * Se ingresa la informacion de un Plato nuevo
	 *
	 * @param array $datos
	 * @return boolean
	 */
	public function insertar($datos) {

		$datos['g_codigo'] = $this->obtenerUltimoId() + 1;

		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "INSERT INTO " . $this->tabla_gacetillas . "
						(g_codigo, g_fecha, g_titulo, g_texto, g_foto, g_tipo, g_acto, g_habilitada, g_enviar_por_mail, g_id_mail)
				  VALUES(" . $datos['g_codigo'] . ",
				  		 " . $datos['g_fecha'] . ",
				  		 " . $datos['g_titulo'] . ",
				  		 " . $datos['g_texto'] . ",
				  		 " . $datos['g_foto'] . ",
				  		 " . $datos['g_tipo'] . ",
				  		 " . $datos['g_acto'] . ",
				  		 1,
				  		 " . $datos['g_enviar_por_mail'] . ",
				  		 " . $datos['g_id_mail'] . ");";

		//LibreriaGeneral::registrarLog("query_insertar", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("ALTA", $this->tabla_gacetillas, "Se ingresa la Gacetilla " . $datos['g_titulo']);
		}

		return true;
	}

	/**
	 * Se modifica la informacion de un Plato determinado
	 *
	 * @param array $datos
	 * @return boolean
	 */
	public function modificar($datos) {
		$conexion = $this->conectar();

		$datos = $this->validarDatos($datos);

		$query = "UPDATE " . $this->tabla_gacetillas . "
				  SET g_fecha = " . $datos['g_fecha'] . ",
					  g_titulo = " . $datos['g_titulo'] . ",
					  g_texto = " . $datos['g_texto'] . ",
					  g_foto = " . $datos['g_foto'] . ",
					  g_tipo = " . $datos['g_tipo'] . ",
					  g_acto = " . $datos['g_acto'] . ",
					  g_enviar_por_mail = " . $datos['g_enviar_por_mail'] . ",
					  g_id_mail = " . $datos['g_id_mail'] . "
				  WHERE g_codigo = " . $datos['g_codigo'];

		//LibreriaGeneral::registrarLog("query_modificar", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_gacetillas, "Se modifica la Gacetilla " . $datos['g_titulo']);
		}

		return true;
	}

	/**
	 * Se obtiene el nombre de la foto
	 * @param  [type] $id [description]
	 * @return [type]           [description]
	 */
	public function obtenerNombreFoto($id) {
		$conexion = $this->conectar();

		$query = "SELECT g_foto FROM " . $this->tabla_gacetillas . " WHERE g_codigo = " . $id;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['g_foto'];
	}

	/**
	 * Se elimina la foto principal del registro
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function eliminarFoto($id) {

		$conexion = $this->conectar();

		$query = "UPDATE " . $this->tabla_gacetillas . " SET g_foto = null WHERE g_codigo = " . $id;
		//LibreriaGeneral::registrarLog("query_eliminarFoto", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("MODIFICA", $this->tabla_gacetillas, "Se elimina la foto principal de la gacetilla.");
		}

		return true;
	}

	/**
	 * Se elimina un registro
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public function eliminar($id) {

		// Previamente se obtiene la info para auditar
		$info = $this->obtenerRegistro($id);

		$conexion = $this->conectar();

		$query = "DELETE FROM " . $this->tabla_gacetillas . " WHERE g_codigo = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {

			$this->desconectar($conexion);
			// Se audita
			$this->auditarEnAdministracion("BAJA", $this->tabla_gacetillas, "Se elimina la Gacetilla " . $info['g_titulo']);
		}

		return true;
	}

	/**
	 * Se obtiene el listado de habilitados
	 *
	 * @return array $datos
	 */
	public function listarHabilitados() {
		$conexion = $this->conectar();

		$sql = "SELECT * FROM " . $this->tabla_gacetillas . " WHERE g_habilitada = 1 ORDER BY g_titulo";

		$resultado = $this->ejecutarQuery($sql);

		$datos = $this->crearVector($resultado);

		$this->desconectar($conexion);

		return $datos;
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 *
	 * @param  integer $id
	 * @param  integer $habilitado
	 * @return boolean true|false
	 */
	public function modificarEstado($id, $habilitado) {

		$conexion = $this->conectar();

		$valor_habilitado = ($habilitado == 1) ? 0 : 1;

		$query = "UPDATE " . $this->tabla_gacetillas . " SET g_habilitada = $valor_habilitado WHERE g_codigo = " . $id;

		if (!$this->ejecutarQuery($query)) {
			return false;
		}

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

		$query = "UPDATE " . $this->tabla_gacetillas . "
				  SET g_foto = '" . $nombre_archivo . "'
				  WHERE g_codigo = " . $id;

		//LibreriaGeneral::registrarLog("query_ingresarNombreFoto", $query);

		if (!$this->ejecutarQuery($query)) {
			return false;
		} else {
			$this->desconectar($conexion);
		}
		return true;
	}

	/**
	 * Se registra el nombre de la foto de una Gacetilla respectiva
	 * 
	 * @param integer $codigo
	 * @return boolean
	 */
	public function registrarNombreFoto($codigo, $nombre_foto)
	{
		$conexion = $this->conectar();
	
		// Si EXISTE la Gacetilla
		if ( $this->existeCodigo($codigo) )
			// Se registra el nombre de la foto
			$query = "UPDATE ".$this->tabla_gacetillas." SET g_foto = '".$nombre_foto."' WHERE g_codigo = ".$codigo;
		else // Si no existe
			// Se ingresa la info de la Gacetilla
			$query = "INSERT INTO ".$this->tabla_gacetillas." (g_codigo, g_foto) VALUES(".$codigo.", '".$nombre_foto."')";
		
		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectar($conexion);
	
		return true;
	}

    /**
     * Se obtiene el Id del Mail de una Gacetilla determinada
     * @param  [integer] $codigo  	Identificador de la Gacetilla
     * @return [integer]       		Identificador del mail enviado
     */
    public function obtenerIdMail($codigo)
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT g_id_mail FROM ".$this->tabla_gacetillas." WHERE g_codigo = ".$codigo;
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['g_id_mail'];
    }
    
    /**
     * Se obtiene el Título del Mail de una Gacetilla determinada
     * @param  [integer] $g_id_mail  	Identificador de la campaña de la Gacetilla
     * @return [string]       			Título del mail enviado
     */
    public function obtenerAsuntoCampania($g_id_mail)
	{    
		$conexion = $this->conectar();
		
		$query = "SELECT g_titulo FROM ".$this->tabla_gacetillas." WHERE g_id_mail = ".$g_id_mail;
		
		$resultado = $this->ejecutarQuery($query);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['g_titulo'];
    }
    
	/**
	 * Se obtienen las fotos de la Gacetilla respectiva
	 * 
	 * @param  integer $codigo Código de la Gacetilla
	 * @return array   $listado_fotos  Colección de fotos de la Gacetilla
	 */
	public function obtenerFotos($codigo)
	{
		$conexion = $this->conectar();

		$query = "SELECT * FROM ".$this->tabla_fotos_secundarias_gacetillas." WHERE fsg_id_gacetilla = ".$codigo;
		
		$resultado = $this->ejecutarQuery($query);
		
		$listado_fotos = $this->crearVector($resultado);
			
		$this->desconectar($conexion);
		
		return $listado_fotos;
	}
	
    /**
     * Se ingresa el nombre del archivo de una foto en la DB para una Gacetilla determinada
     * 
     * @param integer $id_gacetilla
     * @param string $nombre_archivo
     * @return boolean
     */
    public function ingresarNombreImagenEnDB($id_gacetilla, $nombre_archivo)
    {
    	$conexion = $this->conectar();
    
    	$query = "INSERT INTO ".$this->tabla_fotos_secundarias_gacetillas." (fsg_id_gacetilla, fsg_nombre_foto, fsg_publicada)
				  VALUES(".$id_gacetilla.", '".$nombre_archivo."', 1);";
    	
    	if ( !$this->ejecutarQuery($query) )
    		return false;
    	   
    	$this->desconectar($conexion);
    
    	return true;
    }
    
    /**
     * Se obtiene la información de una imagen determinada
     * @param integer $id_imagen
     * @return array $datos
     */
    public function obtenerDatosImagen($id_imagen)
    {
    	$conexion = $this->conectar();
    
    	$query = "SELECT * FROM ".$this->tabla_fotos_secundarias_gacetillas." WHERE fsg_id = ".$id_imagen;
    	
    	$resultado = $this->ejecutarQuery($query);
    
    	$registro = $this->obtenerFila($resultado);
    
    	$this->desconectar($conexion);
    
    	return $registro;
    }
    
    /**
     * Se elimina una imagen determinada por su id
     * @param integer $id_imagen
     * @return boolean
     */
    public function eliminarFotoSecundaria($id_imagen)
	{	
		$conexion = $this->conectar();
		
		$query = "DELETE FROM ".$this->tabla_fotos_secundarias_gacetillas." WHERE fsg_id = ".$id_imagen;
		
		if ( !$this->ejecutarQuery($query) )
			return false;
		
		$this->desconectar($conexion);
		
		return true;	
    }
    
    public function auditarEnvioGacetilla($g_codigo, $g_titulo)
    {
		// Se obtiene el Id de la campaña (PHPList)
		$id_mail = $this->obtenerIdMail($g_codigo);

		// Se audita el ENVIO de la Gacetilla
		$this->auditarEnAdministracion("ENVIO", $this->tabla_gacetillas, "Se envia la Gacetilla: ".LibreriaGeneral::eliminarComillaSimple($g_titulo)." (Id Camp.".$id_mail.")");
    }

    public function auditarVerSuscriptores($g_id_mail)
    {
    	// Se obtiene el Asunto del Mail de una Gacetilla determinada
    	$g_titulo = $this->obtenerAsuntoCampania($g_id_mail);

		// Se audita la visualización de los Suscriptores de la Gacetilla
		$this->auditarEnAdministracion("VER SUSCRIPTORES", $this->tabla_gacetillas, "Se visualizan los Suscriptores de la Gacetilla ".LibreriaGeneral::eliminarComillaSimple($g_titulo)." (Id Camp.".$g_id_mail.")");
    }

}
?>
