<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

// Clase abstracta utilizando la librería mysqli de PHP
abstract class ModeloBaseMySQLi
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

	protected $id_cargo_defensor_pueblo;
	protected $id_cargo_defensor_pueblo_coordinador;

	// 11/03/2021 XXXX
	// Se definió el ID del sistema anterior de Expedientes, en la DB "hcd"
	protected $id_sistema_anterior_expedientes;

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
	protected $tabla_carousel;
	protected $tabla_categorias_sitio;
	protected $tabla_cod_links;
	protected $tabla_comisiones_internas;
	protected $tabla_compras;
	protected $tabla_concejales_historico;
	protected $tabla_contactos; // Contactos del sitio web
	protected $tabla_contenidos_web; // Contenidos del sitio web
	protected $tabla_distinciones;
	protected $tabla_gacetillas;
	protected $tabla_fichas_web;// Para la ficha de los Concejales
	protected $tabla_fotos_secundarias_gacetillas;
	protected $tabla_links;
	protected $tabla_miembros_comision_interna;
	protected $tabla_paginas_sitio;
	protected $tabla_perfiles;
	protected $tabla_pc_en_red;
	protected $tabla_sistemas;
	protected $tabla_usuarios;

	// Para SGLv2
	protected $tabla_usuarios_x_modulo;

	// 21/08/2019 XXXX
	protected $tabla_mails_lista_prensa;

	// PARA LAS ORDENES DEL DIA DE SESION
	protected $tabla_od_sesion;
	protected $tabla_od_sesion_seccion;
	protected $tabla_od_sesion_items;
	protected $tabla_od_sesion_item_despachos;

	// 26/03/2021 XXXX
	// Ordenes del Día de Comisiones
	protected $tabla_od_comision;
	protected $tabla_od_comision_items;

	// 11/05/2020 XXXX
	// Notificaciones Internas
	protected $tabla_notificaciones;
	// 15/05/2020 XXXX y XXXX
	// Grupos de distribución de notificaciones
	protected $tabla_lista_notificaciones_grupos;
	// 02/09/2020 XXXX y XXXX
	// Listas de distribución de notificaciones
	protected $tabla_listas_notificaciones;

	// --- 16/05/2024 XXXX
	protected $tabla_moderados_defensor_pueblo;
	protected $tabla_observaciones_moderadas_dp;

	protected $tabla_banca_25;
	// Utilizada sólo para completar la información histórica
	protected $tabla_solicitudes_banca25_historico;

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
	protected $tabla_expedientes_elec;
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

	// 03/03/2021 XXXX
	// --------------------
	protected $tabla_expe_en_participacion;
	protected $tabla_expe_participaciones;

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

	/**********************************************************
		XXXX 07/02/2025
		Nombre de las tablas para Defensoria
	**********************************************************/
	protected $tabla_auditoria_defensoria;
	protected $tabla_def_expedientes;
	protected $tabla_def_modelos_escrito;
	protected $tabla_def_movimientos;
	protected $tabla_def_notas;
	protected $tabla_def_presentadores;
	protected $tabla_def_provincias;
	protected $tabla_def_tipos_proceso;
	protected $tabla_def_remitentes;
	protected $tabla_def_resoluciones;

	/************************************************************
		 SGL :: Auditoría Web
	************************************************************/
	// Nombre de la Base de Datos
	//private $base_datos_auditoria_web;
	// Nombre de la tabla
	//protected $tabla_auditoria_web;
	/************************************************************/

    public function __construct()
    {
    	$this->base_datos = 'hcd';// Nombre de la base de datos de SGL

		$this->privilegios = Array(
								   "0_user" => "hcd_user", // SE UTILIZA EN EL LOGIN
								   "0_pass" => "user7654",
								   "1_user" => "hcd_nivel1", // PERFIL 1: ADMINISTRADOR
								   "1_pass" => "nivel1_3478",
								   "2_user" => "hcd_nivel2", // PERFIL 2: ALTAS-MODIFICACIONES
								   "2_pass" => "nivel2_4590",
								   "3_user" => "hcd_nivel3", // PERFIL 3: CONSULTAS (CONCEJALES)
								   "3_pass" => "nivel3_2367",
								   "4_user" => "hcd_nivel4", // PERFIL 4: CONSULTAS WEB (PERIODISTAS)
								   "4_pass" => "nivel4_6917",
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
								   "15_pass" => "nivel1_3478",
								   "23_user" => "hcd_nivel1", // PERFIL 23: AREA ACTAS
								   "23_pass" => "nivel1_3478",
								   "24_user" => "hcd_nivel1", // PERFIL 24: AREA MESA DE ENTRADAS
								   "24_pass" => "nivel1_3478",
								   "25_user" => "hcd_nivel1", // PERFIL 25: AREA PRESIDENCIA
								   "25_pass" => "nivel1_3478",
								   "26_user" => "hcd_nivel1", // PERFIL 26: AREA MODERNIZACION
								   "26_pass" => "nivel1_3478"
								  );

		$this->id_cargo_concejal          = '12089901'; // Id del cargo de Concejal del HCD

		// 25/01/2021 XXXX
		$this->id_cargo_concejal_con_licencia = '12990801'; // Id del cargo de Concejal con Uso de Licencia

		$this->id_planta_politica         = '02000000'; // Id de Planta Política

		$this->id_cargo_presidente_hcd    = '00300000'; // Id del cargo de Presidente del HCD

		$this->id_cargo_secretario_hcd    = '00659909'; // Id del cargo de Secretario del HCD

		$this->id_cargo_subsecretario_hcd = '00709901'; // Id del cargo de SubSecretario del HCD

		// 18/07/2019
		$this->id_cargo_defensor_pueblo 		    = '00659908'; // Id del cargo de Defensor del Pueblo Titular
		$this->id_cargo_defensor_pueblo_coordinador = '06659908'; // Id del cargo de Defensor del Pueblo Titular COORDINADOR

		$this->filtro = ''; // para el armado de los filtros de cada query

		// 11/03/2021 XXXX
		// Se definió el ID del sistema anterior de Expedientes, en la DB "hcd"
		$this->id_sistema_anterior_expedientes = 4;

		/*************************************************************************************
			Nombres de las tablas del sistema de Administración
		**************************************************************************************/
		//$this->tabla_accesos  				  = $this->base_datos.'.admin_accesos';
		//$this->tabla_acciones 				  = $this->base_datos.'.admin_acciones';
		//$this->tabla_acciones_por_controlador   = $this->base_datos.'.admin_acciones_por_controlador';
		//$this->tabla_controladores 			  = $this->base_datos.'.admin_controladores';
		//$this->tabla_controladoresxsistema 	  = $this->base_datos.'.admin_controladoresxsistema';
		//$this->tabla_encuestas 				  = $this->base_datos.'.admin_encuestas';
		//$this->tabla_opciones_pregunta_encuesta = $this->base_datos.'.admin_opciones_pregunta_encuesta';
		//$this->tabla_preguntas_encuesta 		  = $this->base_datos.'.admin_preguntas_encuesta';

		$this->tabla_actividades 			  	  = $this->base_datos.'.admin_actividades';
		$this->tabla_auditoria_administracion 	  = $this->base_datos.'.admin_auditoria';
		$this->tabla_autoridades 			  	  = $this->base_datos.'.admin_autoridades';
		$this->tabla_carousel 			  	  	  = $this->base_datos.'.admin_carousel';
		// 14/01/2022 XXXX
		$this->tabla_categorias_sitio 			  = $this->base_datos.'.admin_categorias_sitio';
		$this->tabla_cod_links 				  	  = $this->base_datos.'.admin_cod_links';
		$this->tabla_comisiones_internas      	  = $this->base_datos.'.admin_comisiones_internas';
		$this->tabla_compras 				  	  = $this->base_datos.'.admin_compras';
		$this->tabla_concejales_historico     	  = $this->base_datos.'.admin_concejales_historico';
		$this->tabla_contactos 			    	  = $this->base_datos.'.admin_contactos';
		$this->tabla_distinciones 			  	  = $this->base_datos.'.admin_distinciones';
		$this->tabla_gacetillas 			  	  = $this->base_datos.'.admin_gacetillas_prensa';
		$this->tabla_fichas_web     			  = $this->base_datos.'.admin_fichas_web';
		$this->tabla_fotos_secundarias_gacetillas = $this->base_datos.'.admin_fotos_secundarias_gacetillas';
		$this->tabla_links 					      = $this->base_datos.'.admin_links';
		$this->tabla_miembros_comision_interna    = $this->base_datos.'.admin_miembros_comision_interna';
		// 14/01/2022 XXXX
		$this->tabla_paginas_sitio 				  = $this->base_datos.'.admin_paginas_sitio';
		$this->tabla_pc_en_red 					  = $this->base_datos.'.admin_pc_en_red';
		$this->tabla_perfiles  					  = $this->base_datos.'.admin_perfiles';
		$this->tabla_sistemas  					  = $this->base_datos.'.admin_sistemas';
		$this->tabla_usuarios  					  = $this->base_datos.'.admin_usuarios';
		// 30/11/2018 XXXX
		// Para SGLv2
		$this->tabla_usuarios_x_modulo = $this->base_datos.'.admin_usuarios_x_modulo';

		// 21/08/2019 XXXX
		$this->tabla_mails_lista_prensa = $this->base_datos.'.admin_mails_lista_prensa';

		// 24/05/2022 XXXX
		$this->tabla_contenidos_web	= $this->base_datos.'.admin_contenidos_web';

		// Para las Ordenes del Día de Sesión
		$this->tabla_od_sesion         		  = $this->base_datos.'.od_sesion';
		$this->tabla_od_sesion_seccion 		  = $this->base_datos.'.od_sesion_seccion';
		$this->tabla_od_sesion_items   		  = $this->base_datos.'.od_sesion_items';
		$this->tabla_od_sesion_item_despachos = $this->base_datos.'.od_sesion_item_despachos';

		// 26/03/2021 XXXX
		// Ordenes del Día de Comisiones
		$this->tabla_od_comision = $this->base_datos.'.od_comision';
		$this->tabla_od_comision_items = $this->base_datos.'.od_comision_items';

		// 11/05/2020 XXXX
		// Notificaciones Internas
		$this->tabla_notificaciones = $this->base_datos.'.admin_notificaciones';
		// 15/05/2020 XXXX
		// Grupos de Distribución de notificaciones
		$this->tabla_lista_notificaciones_grupos = $this->base_datos.'.admin_lista_notificaciones_grupos';
		// 02/09/2020 XXXX
		// Listas de Distribución de notificaciones
		$this->tabla_listas_notificaciones = $this->base_datos.'.admin_listas_notificaciones';

		$this->tabla_moderados_defensor_pueblo = $this->base_datos.'.admin_moderados_defensor_pueblo';
		$this->tabla_observaciones_moderadas_dp = $this->base_datos.'.admin_observaciones_moderadas_dp';

		$this->tabla_banca_25 = $this->base_datos.'.admin_banca_25';
		// Utilizada sólo para completar la información histórica
		$this->tabla_solicitudes_banca25_historico = $this->base_datos.'.solicitudes_banca25_historico';

		/*************************************************************************************
			Nombres de las tablas del sistema de Expedientes
		**************************************************************************************/
		$this->tabla_auditoria_expedientes = $this->base_datos.'.expe_auditoria';
		$this->tabla_antecedentes          = $this->base_datos.'.expe_antecedentes';
		$this->tabla_autores               = $this->base_datos.'.expe_autores';
		$this->tabla_codcategoria          = $this->base_datos.'.expe_codcategoria';
		$this->tabla_codestados            = $this->base_datos.'.expe_codestados';
		$this->tabla_codproyectos          = $this->base_datos.'.expe_codproyectos';
		$this->tabla_codtemas              = $this->base_datos.'.expe_codtemas';
		$this->tabla_estados               = $this->base_datos.'.expe_estados';
		$this->tabla_expedientes           = $this->base_datos.'.expe_expedientes';
		$this->tabla_expedientes_elec      = $this->base_datos.'.expe_expedientes_elec';
		$this->tabla_expedientes_externos  = $this->base_datos.'.expe_expedientes_externos';
		$this->tabla_giros                 = $this->base_datos.'.expe_giros';
		$this->tabla_informes              = $this->base_datos.'.expe_informes';
		$this->tabla_lugares               = $this->base_datos.'.expe_lugares';
		$this->tabla_participaciones	   = $this->base_datos.'.expe_participaciones';// 19/02/2021 XXXX
		$this->tabla_prestamos             = $this->base_datos.'.expe_prestamos';
		$this->tabla_proyectos             = $this->base_datos.'.expe_proyectos';
		$this->tabla_ruta                  = $this->base_datos.'.expe_ruta';
		$this->tabla_sanciones             = $this->base_datos.'.expe_sanciones';
		$this->tabla_temas                 = $this->base_datos.'.expe_temas';

		// 03/03/2021 XXXX
		// --------------------
		// Expedientes habilitados para su participación
		$this->tabla_expe_en_participacion = $this->base_datos.'.expe_en_participacion';
		// Participaciones
		$this->tabla_expe_participaciones  = $this->base_datos.'.expe_participaciones';

		/*************************************************************************************
			Nombres de las tablas del sistema de Personal
		**************************************************************************************/
		$this->tabla_antecedentes_laborales = $this->base_datos.'.pers_antecedentes_laborales';
		$this->tabla_areas                  = $this->base_datos.'.pers_areas';
		$this->tabla_auditoria_personal     = $this->base_datos.'.pers_auditoria';
		$this->tabla_cargos                 = $this->base_datos.'.pers_cargos';
		$this->tabla_codareas               = $this->base_datos.'.pers_codareas';
		$this->tabla_codcargos              = $this->base_datos.'.pers_codcargos';
		$this->tabla_estudios               = $this->base_datos.'.pers_estudios';
		$this->tabla_familiares             = $this->base_datos.'.pers_familiares';
		$this->tabla_personal               = $this->base_datos.'.pers_personal';

		/*************************************************************************************
			18/01/2021 XXXX
			Nombres de las tablas para Datos Abiertos
		**************************************************************************************/
		$this->tabla_opendata_publicadores = $this->base_datos.'.opendata_publicadores';
		$this->tabla_opendata_catalogos = $this->base_datos.'.opendata_catalogos';
		$this->tabla_opendata_datasets = $this->base_datos.'.opendata_datasets';
		$this->tabla_opendata_recursos = $this->base_datos.'.opendata_recursos';

		/*************************************************************************************
			07/02/2025 XXXX
			Nombres de las tablas para Defensoria
		**************************************************************************************/
		$this->tabla_auditoria_defensoria = $this->base_datos.'.def_auditoria';
		$this->tabla_def_expedientes = $this->base_datos.'.def_expedientes';
		$this->tabla_def_modelos_escrito = $this->base_datos.'.def_modelos_escrito';
		$this->tabla_def_movimientos = $this->base_datos.'.def_movimientos';
		$this->tabla_def_notas = $this->base_datos.'.def_notas';
		$this->tabla_def_presentadores = $this->base_datos.'.def_presentadores';
		$this->tabla_def_provincias = $this->base_datos.'.def_provincias';
		$this->tabla_def_tipos_proceso = $this->base_datos.'.def_tipos_proceso';
		$this->tabla_def_remitentes = $this->base_datos.'.def_remitentes';
		$this->tabla_def_resoluciones = $this->base_datos.'.def_resoluciones';

		/****************************************************************************************
				PARA LA AUDITORIA WEB
		****************************************************************************************/
		// NOMBRE DE LA BASE DE DATOS DE AUDITORIA WEB
		//$this->base_datos_auditoria_web = 'hcd_auditoria_web';

		//$this->tabla_auditoria_web = $this->base_datos_auditoria_web.'.web_auditoria';
		/**************************************************************************************/
	}

	/**
	 * Estable la conexión con la base de datos utilizando un perfil determinado de usuario en la DB
	 * @param integer $perfil
	 * @return resource $this->conexion
	 */
	public function establecerConexion($perfil)
	{
		// Se define el usuario y su password en base al perfil recibido
		$this->usuario = $this->privilegios[$perfil."_user"];
		$this->password = $this->privilegios[$perfil."_pass"];

		// Se conecta a la base de datos
		$this->conexion = mysqli_connect('localhost', $this->usuario, $this->password, 'hcd');

		// Si surgió un error
		if (! $this->conexion)
			throw new RuntimeException("Error de Conexión (".mysqli_connect_errno().") ".mysqli_connect_error());

		// Se establece la codificación utf-8
		if ( ! mysqli_set_charset($this->conexion, "utf8") )
		    throw new RuntimeException("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($this->conexion));

		// Se guarda en sesión el usuario y password utilizados
		$_SESSION['usuario_actual'] = $this->usuario;
		$_SESSION['password_actual'] = $this->password;

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

		// fputs(fopen("query.txt", 'w'), print_r($query, true));
		// fputs(fopen("conexion.txt", 'w'), print_r($this->conexion, true));

		// Se ejecuta la query
		$resultado = mysqli_query($this->conexion, $query);
		// fputs(fopen("resultado.txt", 'w'), print_r($resultado, true));

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
	 * Inicia una Transacción
	 * @return [boolean]
	 */
	protected function iniciarTransaccion()
	{
		mysqli_begin_transaction($this->conexion);
	}

	/**
	 * Confirma una Transacción
	 * @return [boolean]
	 */
	protected function confirmarTransaccion()
	{
		mysqli_commit($this->conexion);
	}

	/**
	 * Revierte una Transacción
	 * @return [boolean]
	 */
	protected function revertirTransaccion()
	{
		mysqli_rollback($this->conexion);
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

		$this->resultado = $this->ejecutarQuery($this->sql);

		$dato = $this->obtenerFila($this->resultado);

		$this->desconectar($this->conexion);

		return $dato['perfil'];
	}

	/**
	 * Se obtiene la conexión según el Id de un sistema determinado, por defecto Administración
	 * @param integer $id_sistema
	 * @return resource $conexion
	 */
    public function conectarDB($id_sistema = 1)
	{
		// Se obtiene el perfil según el sistema a acceder
		$perfil = $this->obtenerPerfilSegunSistema($id_sistema, $_SESSION['id_usuario']);

		// Se guarda el perfil en sesión para habilitar o no las acciones para el usuario en dicho sistema
		$_SESSION['perfil'.$id_sistema] = $perfil;

		// Se abre la conexión con el perfil determinado
		$this->conexion = $this->establecerConexion($perfil);

		return $this->conexion;
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
		$i = 0;
		while ($row = mysqli_fetch_assoc($resultado)) {
			$datos[$i] = $row;
			$i++;
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
    public function obtenerCantidadDB($tabla, $campo)
	{
		$conexion = $this->conectarDB();

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
    public function obtenerUltimoCodigo($tabla, $campo = 'id')
	{
		$conexion = $this->conectarDB();

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
		$conexion = $this->conectarDB();

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
	 * Se revisa un valor determinado para utilizar en una query,
	 * si no posee valor o es cero, le asigna null
	 * @param  string $dato                 	Cadena a evaluar
	 * @param  string $valor_predeterminado 	Valor predeterminado
	 * @return string                       	Cadena evaluada | null
	 */
	public function revisarValorContenidoTextArea($dato, $valor_predeterminado = "null")
	{
		return ($dato != '') ? "'" . htmlentities(addslashes($dato)) . "'" : $valor_predeterminado;
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
		$conexion = $this->conectarDB();

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
		$conexion = $this->conectarDB();

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
    public function obtenerRegistroDB($tabla, $nombre_campo_id, $valor_id)
	{
		$conexion = $this->conectarDB();

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
    	$conexion = $this->conectarDB();

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

    /**
     * Se audita en el sistema de Personal
     * @param  string 	$operacion     Descripción de la operación realizada
     * @param  string 	$tabla         Nombre de la tabla afectada
     * @param  string 	$observaciones
     * @param  integer  $legajo
     */
    public function auditarEnPersonal($operacion, $tabla, $observaciones, $legajo = 0) {

    	$modelo = new auditoriaPersonalModel();
		$modelo->registrarMovimiento($operacion, $tabla, $observaciones, $legajo);
    }

    /**
     * Se audita en el sistema de Defensoria
     * @param  string 	$operacion     Descripción de la operación realizada
     * @param  string 	$tabla         Nombre de la tabla afectada
     * @param  integer  $id_registro   Identificador del registro auditado
     * @param  string 	$observaciones
     */
    public function auditarEnDefensoria($operacion, $tabla, $id_registro, $observaciones) {

    	$modelo = new auditoriaDefensoriaModel();
		$modelo->registrarMovimiento($operacion, $tabla, $id_registro, $observaciones);
    }
}
?>
