<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// CLASE ABSTRACTA PARA MySQL
abstract class ModelBase 
{
	private $conexion;
    private $usuario;
    private $password;
    
	private $sql;
    private $resultado;
    private $privilegios;
    
	protected $filtro;
	
	// ID DE PLANTA POLITICA
	protected $id_planta_politica;
	
	// ID DEL CARGO DE CONCEJAL
	protected $id_cargo_concejal;
	
	// ID DEL CARGO DE PRESIDENTE DEL HCD
	protected $id_cargo_presidente_hcd;
	
	// ID DEL CARGO DE SECRETARIO DEL HCD
	protected $id_cargo_secretario_hcd;
	
	// ID DEL CARGO DE SUBSECRETARIO DEL HCD
	protected $id_cargo_subsecretario_hcd;
						
	/************************************************************
		 SGL :: ADMINISTRACION
	************************************************************/
	// NOMBRE DE LA BASE DE DATOS
	private $base_datos_administracion;
	
	// NOMBRE DE LAS TABLAS
	//protected $tabla_accesos;
	//public $tabla_acciones;
	//protected $tabla_acciones_por_controlador;
	protected $tabla_actividades;
	protected $tabla_auditoria_administracion;
	protected $tabla_cod_links;
	protected $tabla_comisiones_internas;
	protected $tabla_compras;
	protected $tabla_concejales_historico;
	//public $tabla_controladores;
	//protected $tabla_controladoresxsistema;
	protected $tabla_distinciones;
	//protected $tabla_encuestas;
	protected $tabla_gacetillas;
	protected $tabla_links;
	protected $tabla_miembros_comision_interna;
	//protected $tabla_opciones_pregunta_encuesta;
	protected $tabla_perfiles;
	protected $tabla_pc_en_red;
	//protected $tabla_preguntas_encuesta;
	protected $tabla_sistemas;
	protected $tabla_usuarios;
	
	// PARA LAS ORDENES DEL DIA DE SESION
	protected $tabla_od_sesion;
	protected $tabla_od_sesion_seccion;
	protected $tabla_od_sesion_items;
	
	/************************************************************
		 SGL :: EXPEDIENTES
	************************************************************/
	// NOMBRE DE LA BASE DE DATOS
	private $base_datos_expedientes;
	
	// NOMBRE DE LAS TABLAS
	protected $tabla_auditoria_expedientes;
	protected $tabla_antecedentes;
	protected $tabla_autores;
	protected $tabla_codcategoria;
	protected $tabla_codestados;
	protected $tabla_codproyectos;
	protected $tabla_codtemas;
	protected $tabla_estados;
	protected $tabla_expedientes;
	protected $tabla_expedientes_externos;
	protected $tabla_giros;
	protected $tabla_informes;
	protected $tabla_lugares;
	protected $tabla_prestamos;
	protected $tabla_proyectos;
	protected $tabla_ruta;
	protected $tabla_sanciones;
	protected $tabla_temas;
	
	/************************************************************
		 SGL :: PERSONAL
	************************************************************/
	// NOMBRE DE LA BASE DE DATOS
	private $base_datos_personal;
	
	// NOMBRE DE LAS TABLAS
	protected $tabla_antecedentes_laborales;
	protected $tabla_areas;
	protected $tabla_auditoria_personal;
	protected $tabla_cargos;
	protected $tabla_codareas;
	protected $tabla_codcargos;
	protected $tabla_contactos_publicos;
	protected $tabla_estudios;
	protected $tabla_familiares;
	protected $tabla_personal;
	
	/************************************************************
		 SGL :: AUDITORIA WEB
	************************************************************/
	// BASE DE DATOS
	//private $base_datos_auditoria_web;
	// NOMBRE DE TABLA
	//protected $tabla_auditoria_web;
	/************************************************************/
	
    public function __construct()
    {
		$this->privilegios = Array(
								   "0_user" => "hcd_user", // SE UTILIZA EN EL LOGIN PARA VERIFICAR EXISTENCIA DEL USUARIO Y SUS PERFILES EN CADA SISTEMA
								   "0_pass" => "user7654",
								   "1_user" => "hcd_nivel1", // PERFIL 1: ADMINISTRADOR
								   "1_pass" => "nivel1_3478",
								   "2_user" => "hcd_nivel2", // PERFIL 2: ALTAS-MODIFICACIONES
								   "2_pass" => "nivel2_4590",
								   "3_user" => "hcd_nivel3", // PERFIL 3: CONSULTAS
								   "3_pass" => "nivel3_2367",
								   "4_user" => "hcd_nivel3", // PERFIL 4: CONSULTAS WEB
								   "4_pass" => "nivel3_2367",
								   "5_user" => "hcd_nivel5", // PERFIL 5: GESTION GIROS (SECRETARIO HCD)
								   "5_pass" => "nivel5_8130",
								   "10_user" => "hcd_nivel1", // PERFIL 10: AREA ADMINISTRACION
								   "10_pass" => "nivel1_3478",
								   "11_user" => "hcd_nivel1", // PERFIL 11: AREA BIBLIOTECA
								   "11_pass" => "nivel1_3478",
								   "12_user" => "hcd_nivel1", // PERFIL 12: AREA COMISIONES
								   "12_pass" => "nivel1_3478",
								   "14_user" => "hcd_nivel1", // PERFIL 14: AREA INFORMATICA
								   "14_pass" => "nivel1_3478",
								   "15_user" => "hcd_nivel1", // PERFIL 15: AREA PRENSA
								   "15_pass" => "nivel1_3478"
								  );
		
		// ID DEL CARGO DE CONCEJAL
		$this->id_cargo_concejal = '12089901';
		
		// ID DE PLANTA POLITICA
		$this->id_planta_politica = '02000000';
		
		// ID DEL CARGO DE PRESIDENTE DEL HCD
		$this->id_cargo_presidente_hcd = '00300000';
		
		// ID DEL CARGO DE SECRETARIO DEL HCD
		$this->id_cargo_secretario_hcd = '00659909';
				
		// ID DEL CARGO DE SUBSECRETARIO DEL HCD
		$this->id_cargo_subsecretario_hcd = '00709901';
		
		$this->filtro = '';
			
		/*************************************************************************************
				PARA EL SISTEMA DE ADMINISTRACION
		**************************************************************************************/
		// NOMBRE DE LA BASE DE DATOS DE ADMINISTRACION DE SGL
		$this->base_datos_administracion = 'hcd';// REEMPLAZAR LUEGO POR hcd
								
		// NOMBRE DE LAS TABLAS DE ADMINISTRACION DE SGL
		//$this->tabla_accesos                    = $this->base_datos_administracion.'.admin_accesos';
		//$this->tabla_acciones                   = $this->base_datos_administracion.'.admin_acciones';
		//$this->tabla_acciones_por_controlador   = $this->base_datos_administracion.'.admin_acciones_por_controlador';
		$this->tabla_actividades                  = $this->base_datos_administracion.'.admin_actividades';
		$this->tabla_auditoria_administracion     = $this->base_datos_administracion.'.admin_auditoria';
		$this->tabla_cod_links                    = $this->base_datos_administracion.'.admin_cod_links';
		$this->tabla_comisiones_internas          = $this->base_datos_administracion.'.admin_comisiones_internas';
		$this->tabla_compras                      = $this->base_datos_administracion.'.admin_compras';
		$this->tabla_concejales_historico         = $this->base_datos_administracion.'.admin_concejales_historico';
		//$this->tabla_controladores              = $this->base_datos_administracion.'.admin_controladores';
		//$this->tabla_controladoresxsistema      = $this->base_datos_administracion.'.admin_controladoresxsistema';
		$this->tabla_distinciones                 = $this->base_datos_administracion.'.admin_distinciones';
		//$this->tabla_encuestas                  = $this->base_datos_administracion.'.admin_encuestas';
		$this->tabla_gacetillas                   = $this->base_datos_administracion.'.admin_gacetillas_prensa';
		$this->tabla_links                        = $this->base_datos_administracion.'.admin_links';
		$this->tabla_miembros_comision_interna    = $this->base_datos_administracion.'.admin_miembros_comision_interna';
		//$this->tabla_opciones_pregunta_encuesta = $this->base_datos_administracion.'.admin_opciones_pregunta_encuesta';
		$this->tabla_pc_en_red                    = $this->base_datos_administracion.'.admin_pc_en_red';
		$this->tabla_perfiles                     = $this->base_datos_administracion.'.admin_perfiles';
		//$this->tabla_preguntas_encuesta         = $this->base_datos_administracion.'.admin_preguntas_encuesta';
		$this->tabla_sistemas                     = $this->base_datos_administracion.'.admin_sistemas';
		$this->tabla_usuarios                     = $this->base_datos_administracion.'.admin_usuarios';
		
		// PARA LAS ORDENES DEL DIA DE SESION
		$this->tabla_od_sesion                    = $this->base_datos_administracion.'.od_sesion';
		$this->tabla_od_sesion_seccion            = $this->base_datos_administracion.'.od_sesion_seccion';
		$this->tabla_od_sesion_items              = $this->base_datos_administracion.'.od_sesion_items';
		
		/*************************************************************************************
				PARA EL SISTEMA DE EXPEDIENTES
		**************************************************************************************/
		// NOMBRE DE LA BASE DE DATOS DE EXPEDIENTES
		$this->base_datos_expedientes = 'hcd';// REEMPLAZAR LUEGO POR hcd
		
		// NOMBRE DE LAS TABLAS DE SGL::EXPEDIENTES
		$this->tabla_auditoria_expedientes = $this->base_datos_expedientes.'.expe_auditoria';
		$this->tabla_antecedentes          = $this->base_datos_expedientes.'.expe_antecedentes';
		$this->tabla_autores               = $this->base_datos_expedientes.'.expe_autores';
		$this->tabla_codcategoria          = $this->base_datos_expedientes.'.expe_codcategoria';
		$this->tabla_codestados            = $this->base_datos_expedientes.'.expe_codestados';
		$this->tabla_codproyectos          = $this->base_datos_expedientes.'.expe_codproyectos';
		$this->tabla_codtemas              = $this->base_datos_expedientes.'.expe_codtemas';
		$this->tabla_estados               = $this->base_datos_expedientes.'.expe_estados';
		$this->tabla_expedientes           = $this->base_datos_expedientes.'.expe_expedientes';
		$this->tabla_expedientes_externos  = $this->base_datos_expedientes.'.expe_expedientes_externos';
		$this->tabla_giros                 = $this->base_datos_expedientes.'.expe_giros';
		$this->tabla_informes              = $this->base_datos_expedientes.'.expe_informes';
		$this->tabla_lugares               = $this->base_datos_expedientes.'.expe_lugares';
		$this->tabla_prestamos             = $this->base_datos_expedientes.'.expe_prestamos';
		$this->tabla_proyectos             = $this->base_datos_expedientes.'.expe_proyectos';
		$this->tabla_ruta                  = $this->base_datos_expedientes.'.expe_ruta';
		$this->tabla_sanciones             = $this->base_datos_expedientes.'.expe_sanciones';
		$this->tabla_temas                 = $this->base_datos_expedientes.'.expe_temas';
		
		/*************************************************************************************
				PARA EL SISTEMA DE PERSONAL
		**************************************************************************************/
		// NOMBRE DE LA BASE DE DATOS DE PERSONAL
		$this->base_datos_personal = 'hcd';
		
		// NOMBRE DE LAS TABLAS DEL SISTEMA DE PERSONAL
		$this->tabla_antecedentes_laborales = $this->base_datos_personal.'.pers_antecedentes_laborales';
		$this->tabla_areas                  = $this->base_datos_personal.'.pers_areas';
		$this->tabla_auditoria_personal     = $this->base_datos_personal.'.pers_auditoria';
		$this->tabla_autoridades            = $this->base_datos_personal.'.pers_autoridades';
		$this->tabla_cargos                 = $this->base_datos_personal.'.pers_cargos';
		$this->tabla_codareas               = $this->base_datos_personal.'.pers_codareas';
		$this->tabla_codcargos              = $this->base_datos_personal.'.pers_codcargos';
		$this->tabla_contactos_publicos     = $this->base_datos_personal.'.pers_contactos_publicos';
		$this->tabla_estudios               = $this->base_datos_personal.'.pers_estudios';
		$this->tabla_familiares             = $this->base_datos_personal.'.pers_familiares';
		$this->tabla_personal               = $this->base_datos_personal.'.pers_personal';
		
		/****************************************************************************************
				PARA LA AUDITORIA WEB
		****************************************************************************************/
		// NOMBRE DE LA BASE DE DATOS DE AUDITORIA WEB
		//$this->base_datos_auditoria_web = 'hcd_auditoria_web';
		
		//$this->tabla_auditoria_web = $this->base_datos_auditoria_web.'.web_auditoria';
		
		/**************************************************************************************/
	}
	
	/**
	 * Estable la conexión con la base de datos
	 * utilizando un perfil determinado de usuario en la DB
	 * @param integer $perfil
	 * @return resource $conexion
	 */
	public function establecerConexion($perfil)
	{
		$this->usuario = $this->privilegios["".$perfil."_user"];
		$this->password = $this->privilegios["".$perfil."_pass"];
		
		$this->conexion = mysql_connect('localhost', $this->usuario, $this->password, true, 65536) or die ("Fallo la conexion al Servidor<br>Error: ".mysql_error()."<br>\n");
		
		// SE ESTABLECE LA CODIFICACION UTF-8
		$this->ejecutarQuery("SET CHARACTER SET utf8");
		$this->ejecutarQuery("SET NAMES utf8");
		
		$_SESSION['usuario_actual'] = $this->usuario;
		$_SESSION['password_actual'] = $this->password;
		
		return $this->conexion;	
	}
	
	/**
	 * Se obtiene el perfil del usuario según el sistema que haya elegido
	 * @param integer $id_sistema
	 * @param integer $id_usuario
	 * @return integer Id del perfil de usuario
	 */ 		
	public function obtenerPerfilSegunSistema($id_sistema, $id_usuario)
	{	    
		// SE ESTABLECE LA CONEXION CON PERFIL CERO PARA VERIFICAR LA EXISTENCIA DEL USUARIO Y SUS PERFILES
		$this->conexion = $this->establecerConexion(0);
		
		$this->sql = "SELECT perfil
					  FROM ".$this->tabla_perfiles."
					  WHERE id_sistema = ".$id_sistema."
					  AND id_usuario = ".$id_usuario."
				     ";
			   
		$this->resultado = $this->ejecutarQuery($this->sql, $this->conexion);
		
		$dato = $this->obtenerFila($this->resultado);
		
		$this->desconectar($this->conexion);
		
		// 21/10/2011: SE CAMBIO id_perfil POR perfil EN LA BD hcd TABLA admin_perfiles
		return $dato['perfil'];
	}	
	
	/**
	 * Se obtiene la conexión según el Id de un sistema determinado
	 * @param integer $id_sistema
	 * @return resource $conexion
	 */
    public function conectar($id_sistema)
	{	    
		// SE OBTIENE EL PERFIL SEGUN EL SISTEMA A ACCEDER
		$perfil = $this->obtenerPerfilSegunSistema($id_sistema, $_SESSION['id_usuario']);
		
		// SE GUARDA EL Perfil EN SESION PARA HABILITAR O NO LAS ACCIONES PARA EL USUARIO EN DICHO SISTEMA 
		$_SESSION['perfil'.$id_sistema] = $perfil;
		
		//SE ABRE LA CONEXION CON EL PERFIL DETERMINADO 
		$this->conexion = $this->establecerConexion($perfil);
					
		return $this->conexion;
    }
    
    /**
     * Se cierra la conexión
     * @param $conexion
     */
    public function desconectar($conexion)
    {
		mysql_close($conexion);	
	}
	
	/**
	 * Asigna un conjunto de valores a utilizar como filtro en las querys de cada Modelo
	 * @param array $filtro
	 */
    public function setFiltro($filtro)
    {
		$this->filtro = $filtro;
    }
    
    /**
     * Devuelve un array asociativo con la información obtenida en una query determinada,
     * en caso que la query no devuelva información retorna null
     * @param unknown $resultado
     * @return NULL|array:
     */
    public function crearVector($resultado)
	{
		$i=0;
		while ( $row = mysql_fetch_array($resultado, MYSQL_ASSOC) )
		{
			$datos[$i] = $row;
			$i++;
		}
		
		// Si no devuelve ningún resultado
		if ( ! isset($datos) )
			return null;
		
		return $datos;
    }
    
    /**
     * Se verifica si una fecha determinada es nula, vacía o su valor es incorrecto
     * @param string $fecha
     * @return boolean
     */
	public function esFechaValida($fecha)
	{
	    if ( $fecha != null && $fecha != '' )
	    {
		    $fec_partes = explode("/",$fecha);
		    $mes   = $fec_partes[1];
		    $dia   = $fec_partes[0];
		    $anio  = $fec_partes[2];
		    
		    return checkdate( $mes, $dia, $anio );
	    }
	    else
	    {
		    return false;
	    }	
	}
    
    /**
     * Devuelve una fecha en formato año_completo-mes-dia para MySQL
     * @param string $fecha
     * @return string|boolean
     */
    public function formatearFechaMySQL($fecha)
	{	
		if ( $fecha )
		{
			$fec_partes = explode("/",$fecha);
			$fecha_mysql = $fec_partes[2].'-'.$fec_partes[1].'-'.$fec_partes[0];
			
			return $fecha_mysql;
		}
		else
		{
			return false;
		}	
    }
    
    /**
     * Ejecuta una query determinada,
     * en caso de surgir un error escribe en un archivo de log de errores
     * la descripción de dicho error
     * @param string $query
     * @param string $conexion
     * @return resource
     */
    public function ejecutarQuery($query, $conexion = '')
    {
		if ( $conexion == '' )
			return mysql_query($query);	// PARA LAS TRANSACCIONES BEGIN, ROLLBACK Y COMMIT
		else {
			// SE EJECUTA LA QUERY
			$resultado = mysql_query($query, $conexion);
			
			// SI SURGIO UN ERROR
			if ( !$resultado ) {
				$numero_error = mysql_errno($conexion);
				$texto_error = mysql_error($conexion);
				
				$this->registrarErrorSQL($numero_error, $texto_error, $query);
			}
			
			return $resultado;
		}
	}	
   
	/**
	 * Adapta la sintáxis de una línea en una query según el valor (Texto) de un campo determinado
	 * @param string $campo
	 * @return string Línea adaptada para la query
	 */
    public function adaptarValorStringParaFiltro($campo)
    {  
		if ( $_SESSION[$campo.'_original'] != '' )
		{
			$filtro = " AND ".$campo." = '".addslashes($_SESSION[$campo.'_original'])."'";
		}
		elseif ( is_null($_SESSION[$campo.'_original']) )
		{
			$filtro = " AND ".$campo." IS NULL";
		}
		else
		{
			$filtro = " AND ".$campo." = ''";
		}
		
		return $filtro;
    }
	
    /**
     * Adapta la sintáxis de una línea en una query según el valor (Numérico) de un campo determinado
     * @param string $campo
     * @return string Línea adaptada para la query
     */
    public function adaptarValorNumericoParaFiltro($campo)
    {  
		if ( $_SESSION[''.$campo.'_original'] != '' )
		{
			$filtro = " AND ".$campo." = ".$_SESSION[''.$campo.'_original']."";
		}
		else
		{
			if ( is_null($_SESSION[''.$campo.'_original']) )
			{
				$filtro = " AND ".$campo." IS NULL";
			}
		}
		
		return $filtro;
    }
    
	/**
	 * Se libera la memoria utilizada por la query
	 * @param unknown $resultado
	 */
	public function liberarMemoria($resultado)
	{
		mysql_free_result($resultado);
	}	
	
	/**
	 * Se obtiene el número de filas del resultado de una query
	 * @param unknown $resultado
	 * @return integer Número de filas
	 */
	public function obtenerNumeroFilas($resultado)
	{
		return mysql_num_rows($resultado);
	}	
	
	/**
	 * Se obtiene la fila del resultado de una query
	 * @param unknown $resultado
	 * @return array Registro obtenido
	 */
	public function obtenerFila($resultado)
	{
		return mysql_fetch_array($resultado, MYSQL_ASSOC);
	}
	
	/**
	 * Devuelve la fecha anterior a una dada
	 * @param string $fecha, en formato yyyy-mm-dd
	 * @return string Fecha de Ayer
	 */
	public function obtenerFechaAyer($fecha)
	{
		$partes_fecha_alta = explode("-", $fecha);
		$mes = $partes_fecha_alta[1];
		$dia = $partes_fecha_alta[2];
		$anio = $partes_fecha_alta[0];

		$fecha_ayer = date("Y-m-d", mktime(0, 0, 0, $mes, $dia-1, $anio));

		return $fecha_ayer;
	}
	
	/**
	 * Se registra el error al ejecutar una consulta SQL determinada, en el directorio "sgl/log/"
	 * 
	 * @param integer $numero_error
	 * @param string $texto_error
	 * @param string $query
	 */
	public function registrarErrorSQL($numero_error, $texto_error, $query)
	{
		$info_del_error  = "#####################################################";
		$info_del_error .= "\n Usuario: ".$_SESSION['usuario'];
		$info_del_error .= "\n Fecha y hora: ".date("d/m/Y H:i")." hs.";
		$info_del_error .= "\n#####################################################";
		$info_del_error .= "\nError # ".$numero_error;
		$info_del_error .= "\n\nMensaje del Error: ".$texto_error;
		$info_del_error .= "\n\nEn la siguiente consulta SQL:\n\n";
		$info_del_error .= $query;
				
		Logger::GetInstance()->Log("error_al_ejecutar_query", $info_del_error);
	}
	
	/**
	 * Devuelve la cantidad de registros de una tabla determinada
	 * @param string $tabla
	 * @param string $campo
	 * @return integer cantidad de registros
	 */
    public function obtenerCantidad($tabla, $campo)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT COUNT(".$campo.") AS cantidad 
				FROM ".$tabla."
			   ";
		
		$resultado = $this->ejecutarQuery($sql, $conexion);

		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['cantidad'];
    }
	
    /**
     * Devuelve el último código o id de una tabla determinada
     * @param string $tabla
     * @param string $campo
     * @return integer código o id
     */
    public function obtenerUltimoCodigo($tabla, $campo)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT MAX(".$campo.") AS ultimo_codigo
				FROM ".$tabla."
			   ";
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$dato = $this->obtenerFila($resultado);
		
		if ( $dato['ultimo_codigo'] )
		{
			$ultimo_codigo = $dato['ultimo_codigo'];
		}
		else
		{
			$ultimo_codigo = 0;
		}
		
		$this->desconectar($conexion);
		
		return $ultimo_codigo;
    }
	
    /**
     * Devuelve el nombre o descripción de una codificadora determinada
     * @param string $tabla
     * @param string $campo_nombre
     * @param string $campo_codigo
     * @param integer $valor_codigo
     * @return string Nombre o Descripción de la codificadora
     */
    public function obtenerNombreCodificadora($tabla, $campo_nombre, $campo_codigo, $valor_codigo)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT ".$campo_nombre." AS nombre_codificadora
				FROM ".$tabla."
				WHERE ".$campo_codigo." = ".$valor_codigo."
			   ";
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['nombre_codificadora'];
    }
    
    /**
     * Se revisa un valor determinado para utilizar en una query, 
     * si no posee valor o es cero, le asigna null 
     * @param string $dato
     * @param string $valor_predeterminado
     * @return string Valor revisado
     */
	public function revisarValorAtributo($dato, $valor_predeterminado = "null")
	{
		// SI ESTA VACIO
		if ( $dato == '' || $dato == '0' )
		{
			// SU VALOR PREDETERMINADO
			$valor = $valor_predeterminado;
		}
		else
		{
			// SINO EL VALOR REVISADO 
			$valor = "'".addslashes(trim($dato))."'";
		}
		
		return $valor;
	}
	
	/**
	 * Revisa el valor de la fecha, si es válido se convierte al formato yyyy-mm-dd, sino devuelve null
	 * @param $fecha en formato dd/mm/yyyy
	 * @return Retorna la fecha en formato yyyy-mm-dd ó null.
	 */
	public function revisarValorFechaAtributo($fecha, $con_hora = false)
	{
		// SI ESTÁ DEFINIDA LA FECHA Y ES VÁLIDA
		if ( isset($fecha) && $this->esFechaValida($fecha) )
		{
			$valor = ($con_hora) ? "'".$this->formatearFechaMySQL($fecha).date(" H:m:s")."'" : "'".$this->formatearFechaMySQL($fecha)."'";
		}
		else
		{
			$valor = "null";
		}
		
		return $valor;
	}
	
	/**
	 * Se revisa un valor numérico determinado para utilizar en una query, 
     * si no posee valor, le asigna null 
	 * @param unknown $dato
	 * @param string $valor_predeterminado
	 * @return string
	 */
	public function revisarValorNumericoAtributo($dato, $valor_predeterminado = "null")
	{
		// SI ESTA VACIO
		if ( $dato == '' )
		{
			// SU VALOR PREDETERMINADO
			$valor = $valor_predeterminado;
		}
		else
		{
			// SINO EL VALOR REVISADO 
			$valor = strip_tags($dato);
		}
		
		return $valor;
	}
	
	/**
	 * Devuelve el nombre de un sistema determinado por su Id
	 * @param integer $id_sistema
	 * @return string Nombre del sistema
	 */
    public function obtenerNombreSistema($id_sistema)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT nombre_sistema
				FROM ".$this->tabla_sistemas."
				WHERE id_sistema = ".$id_sistema."
			   ";
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['nombre_sistema'];
    }
    
    /**
     * Devuelve el valor de un Id de una tabla, según el valor de otro de sus campos
     * (Ejemplo: SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 79)
     * 
     * @param string $tabla
     * @param string $nombre_campo_id
     * @param string $nombre_campo_codigo
     * @param string|integer $valor_campo_codigo
     * @return integer Id
     */
    public function obtenerIdSegunCodigo($tabla, $nombre_campo_id, $nombre_campo_codigo, $valor_campo_codigo)
	{
		$conexion = $this->conectar();
		
		$sql = "SELECT ".$nombre_campo_id." AS id
				FROM ".$tabla."
				WHERE ".$nombre_campo_codigo." = ".$valor_campo_codigo."
			   ";
		//Logger::getinstance()->Log("query_obtenerIdSegunCodigo_mysql_viejo", $sql, false);

		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$dato = $this->obtenerFila($resultado);
		
		$this->desconectar($conexion);
		
		return $dato['id'];
    }
    
    /**
     * Devuelve un registro de una tabla determinada, según el valor de su Id
     * @param string $tabla
     * @param string $nombre_campo_id
     * @param integer $valor_id
     * @return NULL|array
     */
    public function obtenerRegistro($tabla, $nombre_campo_id, $valor_id)
	{    
		$conexion = $this->conectar();
		
		$sql = "SELECT * FROM ".$tabla."
				WHERE ".$nombre_campo_id." = '".$valor_id."'
			   ";
		
		$resultado = $this->ejecutarQuery($sql, $conexion);
		
		$registro = $this->crearVector($resultado);
		
		$this->desconectar($conexion);
		
		return $registro;
    }

    /**
     * Se verifica si un perfil tiene permiso para realizar una acción determinada,
     * para un controlador y sistema respectivos 
     * @param integer $perfil
     * @param integer $sistema
     * @param string $controlador
     * @param string $accion
     * @return boolean
     */
    public function tienePermisoAcceso($perfil, $id_sistema, $nombre_controlador, $nombre_accion)
    {
    	$conexion = $this->conectar();
    
    	// Se elimina '_controller', para utilizar sólo el nombre del controlador en la query
    	$nombre_controlador = str_replace('_controller', '', $nombre_controlador);
    	
    	$sql = "SELECT id_accion
				FROM ".$this->tabla_accesos."
				WHERE id_perfil = ".$perfil."
				AND id_sistema = ".$id_sistema."
				AND id_controlador = (SELECT id_controlador
									  FROM ".$this->tabla_controladores."
									  WHERE nombre_controlador = '".$nombre_controlador."'
									 )
				AND id_accion = (SELECT id_accion
								 FROM ".$this->tabla_acciones."
								 WHERE nombre_accion = '".$nombre_accion."'
								)
			   ";
    	
    	$resultado = $this->ejecutarQuery($sql, $conexion);
    
    	$dato = $this->obtenerFila($resultado);
    
    	$this->desconectar($conexion);
    
    	// Si no tiene permiso
    	if ($dato['id_accion'] == '')
    		return false;
    	
    	return true;
    }
    
    /**
     * Devuelve el Id del cargo Concejal
     * 
     * @return string
     */
    public function obtenerIdCargoConcejal()
    {
    	return $this->id_cargo_concejal;
    }

    /**
     * Devuelve el Id del cargo Secretario del HCD
     *
     * @return string
     */
    public function obtenerIdCargoSecretarioHCD()
    {
    	return $this->id_cargo_secretario_hcd;
    }
}
?>
