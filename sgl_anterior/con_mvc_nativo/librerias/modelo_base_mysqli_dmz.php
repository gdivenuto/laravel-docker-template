<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Clase abstracta utilizando la librería mysqli de PHP
abstract class ModeloBaseMySQLiDMZ
{
	private $conexion;
    private $usuario;
    private $password;

	// Nombre de la base de datos de SGL
	private $base_datos;

	private $sql;
    private $resultado;
    private $privilegios;

	protected $filtro;

	// Id de Planta Política
	protected $id_planta_politica;

	// Id del cargo de Concejal del HCD
	protected $id_cargo_concejal;

	// 25/01/2021 XXXX
	protected $id_cargo_concejal_con_licencia;

	// Id del cargo de Presidente del HCD
	protected $id_cargo_presidente_hcd;

	// Id del cargo de Secretario del HCD
	protected $id_cargo_secretario_hcd;

	// Id del cargo de Subsecretario del HCD
	protected $id_cargo_subsecretario_hcd;

	/**********************************************************
		Nombre de las tablas del sistema de Administración
	**********************************************************/
	//protected $tabla_accesos;
	//public $tabla_acciones;
	//protected $tabla_acciones_por_controlador;
	//public $tabla_controladores;
	//protected $tabla_controladoresxsistema;
	//protected $tabla_encuestas;
	//protected $tabla_opciones_pregunta_encuesta;
	//protected $tabla_preguntas_encuesta;

	protected $tabla_actividades;
	protected $tabla_auditoria_administracion;
	protected $tabla_autoridades;
	protected $tabla_cod_links;
	protected $tabla_comisiones_internas;
	protected $tabla_compras;
	protected $tabla_concejales_historico;
	protected $tabla_contactos; // Para la sección de Contactos del sitio web
	protected $tabla_distinciones;
	protected $tabla_gacetillas;
	protected $tabla_fichas_web;// Para la ficha de los Concejales
	protected $tabla_fotos_secundarias_gacetillas;
	protected $tabla_links;
	protected $tabla_miembros_comision_interna;
	protected $tabla_perfiles;
	protected $tabla_pc_en_red;
	protected $tabla_sistemas;
	protected $tabla_usuarios;

	// 30/11/2018 XXXX
	// Para SGLv2
	protected $tabla_usuarios_x_modulo;

	// 21/08/2019 XXXX
	protected $tabla_mails_lista_prensa;
	// 21/02/2020 XXXX
	// Para el sitio web
	protected $tabla_contenidos_web;

	// PARA LAS ORDENES DEL DIA DE SESION
	protected $tabla_od_sesion;
	protected $tabla_od_sesion_seccion;
	protected $tabla_od_sesion_items;

	// 11/05/2020 XXXX
	// Notificaciones Internas
	protected $tabla_notificaciones;
	// 15/05/2020 XXXX y XXXX
	// Grupos de distribución de notificaciones
	protected $tabla_lista_notificaciones_grupos;
	// 02/09/2020 XXXX y XXXX
	// Listas de distribución de notificaciones
	protected $tabla_listas_notificaciones;

	/**********************************************************
		Nombre de las tablas del sistema de Expedientes
	**********************************************************/
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
	protected $tabla_participaciones;// 19/02/2021 XXXX
	protected $tabla_prestamos;
	protected $tabla_proyectos;
	protected $tabla_ruta;
	protected $tabla_sanciones;
	protected $tabla_temas;

	// DMZ
	// --------------------
	protected $tabla_estadisticas_consulta_digesto;
	protected $tabla_estadisticas_formularios;
	protected $tabla_expe_en_participacion;
	protected $tabla_expe_participaciones;
	protected $tabla_inscripciones_defensor_pueblo;
	protected $tabla_observaciones_candidato_dp;
	protected $tabla_descargos_candidato_dp;
	protected $tabla_provincias;
	protected $tabla_solicitudes_banca25;

	/**********************************************************
		Nombre de las tablas del sistema de Personal
	**********************************************************/
	protected $tabla_antecedentes_laborales;
	protected $tabla_areas;
	protected $tabla_auditoria_personal;
	protected $tabla_cargos;
	protected $tabla_codareas;
	protected $tabla_codcargos;
	protected $tabla_estudios;
	protected $tabla_familiares;
	protected $tabla_personal;

	/**********************************************************
		XXXX 18/01/2021
		Nombre de las tablas para Datos Abiertos
	**********************************************************/
	protected $tabla_opendata_publicadores;
	protected $tabla_opendata_catalogos;
	protected $tabla_opendata_datasets;
	protected $tabla_opendata_recursos;

	/************************************************************
		 SGL :: Auditoría Web
	************************************************************/
	// Nombre de la Base de Datos
	//private $base_datos_auditoria_web;
	// Nombre de la tabla
	//protected $tabla_auditoria_web;
	/************************************************************/

	public function __construct() {

		$this->servidor = 'localhost';
		$this->usuario = "hcd_nivel2";
		$this->password = "nivel2_4590";
		$this->base_datos = 'dmz';

		$this->filtro = '';

		$this->tabla_estadisticas_consulta_digesto = $this->base_datos . '.estadisticas_consulta_digesto';
		$this->tabla_estadisticas_formularios = $this->base_datos . '.estadisticas_formularios';
		$this->tabla_expe_en_participacion = $this->base_datos . '.expe_en_participacion';
		$this->tabla_expe_participaciones = $this->base_datos . '.expe_participaciones';
		$this->tabla_inscripciones_defensor_pueblo = $this->base_datos . '.inscripciones_defensor_pueblo';
		$this->tabla_observaciones_candidato_dp = $this->base_datos . '.observaciones_candidato_dp';
		$this->tabla_descargos_candidato_dp = $this->base_datos . '.descargos_candidato_dp';
		$this->tabla_provincias = $this->base_datos . '.provincias';
		$this->tabla_solicitudes_banca25 = $this->base_datos . '.solicitudes_banca25';
	}

	/**
	 * Estable la conexión con la base de datos utilizando un perfil determinado de usuario en la DB
	 * @return resource $this->conexion
	 */
	public function obtenerConexion()
	{
		// Se conecta a la base de datos
		$this->conexion = mysqli_connect($this->servidor, $this->usuario, $this->password, $this->base_datos);

		// Si surgió un error
		if (! $this->conexion)
			throw new RuntimeException("Error de Conexión (".mysqli_connect_errno().") ".mysqli_connect_error());

		// Se establece la codificación utf-8
		if ( ! mysqli_set_charset($this->conexion, "utf8") )
		    throw new RuntimeException("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($this->conexion));

		return $this->conexion;
	}

    /**
     * Ejecuta una query determinada,
     * en caso de surgir un error lo registra en un archivo de log de errores y lanza una excepción
     * @param string $query
     * @return resource $resultado
     */
    public function ejecutarQuery($query)
    {
    	if ($this->conexion === null)
			return null;

		if ($query === null)
			return null;

		// Se ejecuta la query
		$resultado = mysqli_query($this->conexion, $query);

		// Si surgió un error
		if ( !$resultado ) {
			// Se registra el error
			$this->registrarErrorSQL(mysqli_connect_errno(), mysqli_connect_error(), $query);

			// Se lanza la excepción
			throw new RuntimeException("Error al ejecutar la query: " . mysqli_connect_error());
		}

		return $resultado;
	}

    /**
     * Se cierra la conexión
     * @param $conexion
     */
    public function desconectar($conexion)
    {
    	mysqli_close($conexion);
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
     * Devuelve un array asociativo con la información obtenida de una query determinada,
     * en caso que la query no devuelva información retorna null
     * @param resource $resultado
     * @return NULL|array asociativo
     */
    public function crearVector($resultado)
	{
		// Si no se recibió ningún resultado
		if ($resultado == null)
			return null;

		$datos = null;

		while ($row = mysqli_fetch_assoc($resultado)) {
			$datos[] = $row;
		}

		return $datos;
    }

	/**
	 * Se obtiene un array asociativo con el resultado de una query
	 * @param resource $resultado
	 * @return array Registro obtenido
	 */
	public function obtenerFila($resultado)
	{
		//return ($resultado != null) ? $resultado->fetch_assoc() : null;
		return ($resultado != null) ? mysqli_fetch_assoc($resultado) : null;
	}

    /**
     * Se verifica si una fecha determinada es válida
     * @param string $fecha
     * @return boolean
     */
	public function esFechaValida($fecha)
	{
		// Si se recibe un valor
	    if ( $fecha != null && $fecha != '' ) {
	    	// Si NO tiene el formato yyyy-mm-dd
	    	if ( strpos($fecha, '-') === false ) {
	    		// Se separa la fecha dd/mm/yyyy
			    $fec_partes = explode("/",$fecha);

			    $dia  = $fec_partes[0];
			    $mes  = $fec_partes[1];
			    $anio = $fec_partes[2];
			}
			// Si NO tiene el formato dd/mm/yyyy
			elseif ( strpos($fecha, '/') === false ) {
				// Se separa la fecha yyyy-mm-dd
			    $fec_partes = explode("-",$fecha);

			    $dia  = $fec_partes[2];
			    $mes  = $fec_partes[1];
			    $anio = $fec_partes[0];
			}
		    return checkdate($mes, $dia, $anio);
	    } else
		    return false;
	}

    /**
     * Devuelve una fecha en formato año_completo-mes-dia para MySQL
     * @param string $fecha
     * @return null|string $fecha_mysql
     */
    public function formatearFechaMySQL($fecha)
	{
		if ($fecha == null)
			return null;
		else {
			$fec_partes = explode("/", $fecha);
			$fecha_mysql = $fec_partes[2].'-'.$fec_partes[1].'-'.$fec_partes[0];

			return $fecha_mysql;
		}
    }

	/**
	 * Devuelve la fecha anterior a una dada
	 * @param string $fecha, en formato yyyy-mm-dd
	 * @return null|string Fecha de Ayer
	 */
	public function obtenerFechaAyer($fecha)
	{
		if ($fecha == null)
			return null;

		$partes_fecha_alta = explode("-", $fecha);
		$mes = $partes_fecha_alta[1];
		$dia = $partes_fecha_alta[2];
		$anio = $partes_fecha_alta[0];

		$fecha_ayer = date("Y-m-d", mktime(0, 0, 0, $mes, $dia-1, $anio));

		return $fecha_ayer;
	}

	/**
	 * Adapta la sintáxis de una línea en una query según el valor (Texto) de un campo determinado
	 * @param string $campo
	 * @return string Línea adaptada para la query
	 */
    public function adaptarValorStringParaFiltro($campo)
    {
		$filtro = '';
		if ( $_SESSION[$campo.'_original'] != '' )
			$filtro = " AND ".$campo." = '".addslashes($_SESSION[$campo.'_original'])."'";
		elseif ( is_null($_SESSION[$campo.'_original']) )
			$filtro = " AND ".$campo." IS NULL";
		else
			$filtro = " AND ".$campo." = ''";

		return $filtro;
    }

    /**
     * Adapta la sintáxis de una línea en una query según el valor (Numérico) de un campo determinado
     * @param string $campo
     * @return string Línea adaptada para la query
     */
    public function adaptarValorNumericoParaFiltro($campo)
    {
		$filtro = '';
		if ( $_SESSION[''.$campo.'_original'] != '' )
			$filtro = " AND ".$campo." = ".$_SESSION[$campo.'_original'];
		else
			if ( is_null($_SESSION[''.$campo.'_original']) )
				$filtro = " AND ".$campo." IS NULL";

		return $filtro;
    }

	/**
	 * Se libera la memoria utilizada por la query
	 * @param resource $resultado
	 */
	public function liberarMemoria($resultado)
	{
		//$resultado->free();
		 mysqli_free_result($resultado);
	}

	/**
	 * Se obtiene el número de filas del resultado de una query
	 * @param resource $resultado
	 * @return integer Número de filas
	 */
	public function obtenerNumeroFilas($resultado)
	{
		return $resultado->num_rows;
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

		$query = "SELECT COUNT(".$campo.") AS cantidad FROM ".$tabla;

		$resultado = $this->ejecutarQuery($query);

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

		$query = "SELECT MAX(".$campo.") AS ultimo_codigo FROM ".$tabla;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$ultimo_codigo = ($dato['ultimo_codigo'] != null) ? $dato['ultimo_codigo'] : 0;

		$this->desconectar($conexion);

		return $ultimo_codigo;
    }

    /**
     * Devuelve el nombre o descripción de una codificadora determinada
     * @param string $tabla
     * @param string $campo_nombre
     * @param string $campo_codigo
     * @param integer $valor_codigo
     * @return string Nombre de la codificadora
     */
    public function obtenerNombreCodificadora($tabla, $campo_nombre, $campo_codigo, $valor_codigo)
	{
		$conexion = $this->conectar();

		$query = "SELECT ".$campo_nombre." AS nombre_codificadora
				  FROM ".$tabla."
				  WHERE ".$campo_codigo." = ".$valor_codigo;

		$resultado = $this->ejecutarQuery($query);

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
		return ($dato == '' || $dato == '0') ? $valor_predeterminado : "'".addslashes(trim($dato))."'";
	}

	/**
	 * Revisa el valor de la fecha, si es válida se convierte al formato yyyy-mm-dd, sino devuelve null
	 * @param $fecha en formato dd/mm/yyyy
	 * @return fecha en formato yyyy-mm-dd ó la cadena 'null' para utilizarla en una query
	 */
	public function revisarValorFechaAtributo($fecha, $con_hora = false)
	{
		// Si está definida la fecha y es válida
		if ( isset($fecha) && $this->esFechaValida($fecha) ) {
			// Si NO tiene el formato yyyy-mm-dd
	    	if ( strpos($fecha, '-') === false )
	    		// Se convierte a dicho formato
				$valor = ($con_hora) ? "'".$this->formatearFechaMySQL($fecha).date(" H:m:s")."'" : "'".$this->formatearFechaMySQL($fecha)."'";
			else
				// sino, se mantiene en dicho formato
				$valor = ($con_hora) ? "'".$fecha.date(" H:m:s")."'" : "'".$fecha."'";
		}
		else
			$valor = "null";

		return $valor;
	}

	/**
	 * Se revisa un valor numérico determinado para utilizar en una query,
     * si no posee valor, le asigna null por defecto
	 * @param unknown $dato
	 * @param string $valor_predeterminado
	 * @return string
	 */
	public function revisarValorNumericoAtributo($dato, $valor_predeterminado = "null")
	{
		return $valor = ($dato != '') ? strip_tags($dato) : $valor_predeterminado;
	}

	/**
	 * Devuelve el nombre del sistema determinado por su Id
	 * @param integer $id_sistema
	 * @return string Nombre del sistema
	 */
    public function obtenerNombreSistema($id_sistema)
	{
		$conexion = $this->conectar();

		$query = "SELECT nombre_sistema
				  FROM ".$this->tabla_sistemas."
				  WHERE id_sistema = ".$id_sistema;

		$resultado = $this->ejecutarQuery($query);

		$dato = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $dato['nombre_sistema'];
    }

    /**
     * Devuelve el valor de un Id de una tabla, según el valor de otro de sus campos
     * (Ejemplo: SELECT id_codestado FROM ".$this->tabla_codestados." WHERE codigo_estado = 79)
     * @param string $tabla
     * @param string $nombre_campo_id
     * @param string $nombre_campo_codigo
     * @param string|integer $valor_campo_codigo
     * @return integer Id
     */
    public function obtenerIdSegunCodigo($tabla, $nombre_campo_id, $nombre_campo_codigo, $valor_campo_codigo)
	{
		$conexion = $this->conectar();

		$query = "SELECT ".$nombre_campo_id." AS id
				  FROM ".$tabla."
				  WHERE ".$nombre_campo_codigo." = ".$valor_campo_codigo;

		$resultado = $this->ejecutarQuery($query);

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

		$query = "SELECT * FROM ".$tabla." WHERE ".$nombre_campo_id." = '".$valor_id."'";

		$resultado = $this->ejecutarQuery($query);

		$registro = $this->obtenerFila($resultado);

		$this->desconectar($conexion);

		return $registro;
    }

    /**
     * Se verifica si un perfil tiene permiso para realizar una acción determinada,
     * para un controlador y sistema respectivos
     * @param integer $perfil
     * @param integer $id_sistema
     * @param string $nombre_controlador
     * @param string $nombre_accion
     * @return boolean
     */
    public function tienePermisoAcceso($perfil, $id_sistema, $nombre_controlador, $nombre_accion)
    {
    	$conexion = $this->conectar();

    	// Se elimina '_controller', para utilizar sólo el nombre del controlador en la query
    	$nombre_controlador = str_replace('_controller', '', $nombre_controlador);

    	$query = "SELECT id_accion
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
								  )";

    	$resultado = $this->ejecutarQuery($query);

    	$dato = $this->obtenerFila($resultado);

    	$this->desconectar($conexion);

    	// Si tiene permiso o no
    	return ($dato['id_accion'] == '');
    }

    /**
     * Devuelve el Id del cargo de Concejal del HCD
     *
     * @return string
     */
    public function obtenerIdCargoConcejal()
    {
    	return $this->id_cargo_concejal;
    }

    /**
     * 25/0172021 XXXX
     *
     * Devuelve el Id del cargo de Concejal con uso de Licencia
     *
     * @return string
     */
    public function obtenerIdCargoConcejalConLicencia()
    {
    	return $this->id_cargo_concejal_con_licencia;
    }

    /**
     * Devuelve el Id del cargo de Secretario del HCD
     *
     * @return string
     */
    public function obtenerIdCargoSecretarioHCD()
    {
    	return $this->id_cargo_secretario_hcd;
    }

    /**
     * 18/07/2019
     *
     * Devuelve el Id del cargo de Defensor del Pueblo Titular
     *
     * @return string
     */
    public function obtenerIdCargoDefensorPueblo()
    {
    	return $this->id_cargo_defensor_pueblo;
    }

    /**
     * 18/07/2019
     *
     * Devuelve el Id del cargo de Defensor del Pueblo Titular COORDINADOR
     *
     * @return string
     */
    public function obtenerIdCargoDefensorPuebloCoordinador()
    {
    	return $this->id_cargo_defensor_pueblo_coordinador;
    }

    /**
     * Se audita en el sistema de Administración
     * @param  [string] $operacion     Descripción de la operación realizada
     * @param  [string] $tabla         Nombre de la tabla afectada
     * @param  [string] $observaciones
     */
    public function auditarEnAdministracion($operacion, $tabla, $observaciones) {
    	$modelo = new auditoriaAdministracionModel();

		$datos_log = Array();
		$datos_log['operacion']     = $operacion;
		$datos_log['tabla']         = $tabla;
		$datos_log['observaciones'] = $observaciones;

		//SE CARGA EN auditoria EL MOVIMIENTO
		$modelo->registrarMovimiento($datos_log);
    }
}
?>
