<?php
if (!isset($_SESSION)) {
	session_start();
}

//Incluye el modelo que corresponde
require_once RUTA_MODELOS."comisiones_internas.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "comisiones_internas/grilla.php";
require_once RUTA_VISTAS . "comisiones_internas/edicion.php";

class comisiones_internas_controller extends ControllerBase
{
    /* PRODUCCION */
	const RUTA_DIRECTORIO_DESTINO = "/web/institucional/";
	const FTP_SERVER              = 'lobo1.concejomdp.gov.ar';
	const USUARIO                 = 'informaticasgl';
	const PASSWORD                = '12sgl34hcd';
    /* DESARROLLO *
	const RUTA_DIRECTORIO_DESTINO = "/var/www/concejomdp/secciones/institucional/";
	const FTP_SERVER              = 'localhost';
	const USUARIO                 = 'expe';
	const PASSWORD                = '123456';
	/**/
	
	private $id_conexion;
	private $resultado_login;

	public function __construct()
	{
		parent::__construct();

		$this->campo_orden_por_defecto = 'ci_nombre';

		// Se crea una instancia del modelo
		$this->modelo = new comisionesInternasModel();
	
		// Se crea una instancia de la Vista
		$this->vista_grilla = new VistaComisionesInternasGrilla();
		$this->vista_edicion = new VistaComisionesInternasEdicion();
	
		// Se inicializa el mensaje de resultados
		$this->mensaje = "";
	}
	
	public function guardarRegistroOriginal($original)
    {
		$_SESSION['ci_codigo_original'] = $original['ci_codigo'];
		$_SESSION['ci_dia_original'] = $original['ci_dia'];
		$_SESSION['ci_horario_original'] = $original['ci_horario'];
		$_SESSION['ci_relator_original'] = $original['ci_relator'];
		$_SESSION['ci_incumbencias_original'] = $original['ci_incumbencias'];
		$_SESSION['ci_habilitado_original'] = $original['ci_habilitado'];
    }
	
    public function listar($mensaje = '', $tipo_mensaje = '')
    {
		$filtro = Array();
				
		// FILTRO POR NOMENCLADOR
		$filtro['f_codigo'] = LibreriaGeneral::recoge('f_codigo');
		
		// FILTRO POR NOMBRE
		$filtro['f_nombre'] = LibreriaGeneral::recoge('f_nombre');
				
		// se establece el campo por el cual ordenar
		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ($campo_orden != '') {
			$filtro['campo_orden'] = $campo_orden;
		} else {
			//por defecto
			$filtro['campo_orden'] = $this->campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}
		
		// DIRECCION PARA LA PAGINACION (PRIMERO, ANTERIOR, SGTE., ULTIMO)
		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');

		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			// Si es la primera vez que carga la pagina o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') ? 'desc' : 'asc';
		}

		// Cantidad de registros a mostrar
		$filtro['rango'] = $this->rango_paginacion;
		
		// SE ESTABLECE EL FILTRO EN EL MODELO
		$this->modelo->setFiltro($filtro);
		
		// Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();
		
		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		$filtro['pagina'] = LibreriaGeneral::recoge('pagina');

		// SI NO SE RECIBIÓ LA PÁGINA
		if (!$filtro['pagina']) {
			// SE ESTABLECE LA ÚLTIMA
			$filtro['pagina'] = ($filtro['nro_paginas'] > 0) ? $filtro['nro_paginas'] : 1;

			// SI LA CANTIDAD ES MENOR AL RANGO DE PAGINA
			if ($filtro['cantidad'] < $filtro['rango'])
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			else
				// SE MUESTRAN LOS ÚLTIMOS (VALOR DEL RANGO) REGISTROS
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
		} else
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];

		$filtro['pagina_ant'] = $filtro['pagina'] - 1;	// para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;	// para la pagina posterior

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_comisiones_internas'] = $filtro;
		
		// SE ESTABLECE EL FILTRO EN EL MODELO
		$this->modelo->setFiltro($filtro);
		
		// SE OBTIENE EL LISTADO
		$listado = $this->modelo->listar();
		
		// se muestra el listado
		$this->vista_grilla->mostrar($listado, $mensaje, $tipo_mensaje, $filtro);
    }
	
    public function editar($datos_formulario = null, $mensaje = '', $tipo_mensaje = '') {

		// Si NO se viene del formulario de edición por un error
		if ($datos_formulario === null) {
			// Se recibe el Id para su edición
			$id = LibreriaGeneral::recoge('id', 0);

			// Se busca el registro en la base de datos
			$datos = $this->modelo->obtenerRegistro($id);
			
			// Si existe
			if (isset($datos['ci_codigo']) && $datos['ci_codigo']) {
				// Se audita la consulta del registro
				//$this->modelo->auditarConsultaRegistro($datos);

				// Se guarda el registro en sesion para verificar luego si no ha modificado otro usuario
				$this->guardarRegistroOriginal($datos);

				// Se marca para saber que se encuentra en edición
				//$this->modelo->marcarEnEdicion($datos['ci_codigo']);

				$datos['pagina'] = LibreriaGeneral::recoge('pagina', 1);

				$datos = $this->retirarBarraInvertida($datos);
			} else {
				// En caso de editarse un NUEVO registro
				$datos = null;
			}

		} else {
			// SI SE VIENE DEL FORMULARIO DEBIDO A UN ERROR
			$datos = $datos_formulario;
		}

		$datos['concejales'] = $this->modelo->obtenerConcejalesActivos();

		$datos['relatores'] = $this->modelo->obtenerRelatores();
		
		$datos['comisiones_internas'] = $this->modelo->obtenerComisionesInternas();

		$this->vista_edicion->mostrar($datos, $mensaje, $tipo_mensaje);
	}
	
    public function insertar() {

		$datos = $_REQUEST;
		
		if ( $this->modelo->existe($datos['ci_codigo']) ) { 	
			$this->listar("La Comisi&oacute;n Interna ".$this->modelo->obtenerNombreComision($datos['ci_codigo'])." se ha ingresado previamente.", 2);
		} else {
			if ($this->modelo->insertar($datos)) {
				$this->listar("La Comisi&oacute;n Interna ".$this->modelo->obtenerNombreComision($datos['ci_codigo'])." se ingres&oacute; con &eacute;xito.", 1);
			} else {
				$this->listar("Error al ingresar la Comisi&oacute;n Interna ".$this->modelo->obtenerNombreComision($datos['ci_codigo']), 2);
			}
		}
    }

    public function modificar() {

		$datos = $_REQUEST;
		
		if ( $this->modelo->noLoModificoOtroUsuario() ) {
			if ( $this->modelo->modificar($datos) ) {
				$this->listar("La Comisi&oacute;n Interna ".$this->modelo->obtenerNombreComision($datos['ci_codigo'])." se modific&oacute; con &eacute;xito.", 1);
			} else {
				$this->listar("Error al modificar la Comisi&oacute;n Interna ".$this->modelo->obtenerNombreComision($datos['ci_codigo']), 2);
			}
		} else {
			$this->listar("La Comisi&oacute;n Interna ".$this->modelo->obtenerNombreComision($datos['ci_codigo'])." se ha modificado previamente.", 2);
		}
    }
    
    public function eliminar() {

		$id = LibreriaGeneral::recoge('id');
		
		if ( $this->modelo->eliminar($id) ) {
			$this->listar("La Comisi&oacute;n Interna ".$this->modelo->obtenerNombreComision($id)." se elimin&oacute; con &eacute;xito.", 1);
		} else {
			$this->listar("La Comisi&oacute;n Interna ".$this->modelo->obtenerNombreComision($id)." no se ha eliminado, no debe poseer integrantes para su eliminaci&oacute;n.", 2);
		}

    }
    
    public function listarMiembrosComisionInterna()
    {
		$cod_comision_interna = LibreriaGeneral::recoge('cod_comision_interna');
		
		$miembros_comision_interna = $this->modelo->listarMiembrosComisionInterna($cod_comision_interna);
		
		$this->vista->listarMiembrosComisionInterna($miembros_comision_interna);
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 */
	public function modificarEstado() {
		parent::modificarEstadoBase();
	}

	/**
	 * Se setean en mantenimiento o no, las comisiones habilitadas
	 */
	public function setearMantenimiento() {

		$mantenimiento = LibreriaGeneral::recoge('m');

		if ($this->modelo->setearMantenimiento($mantenimiento))
			$this->listar("Se ha modificado el estado en Mantenimiento de las Comisiones Internas.", 1);
		else
			$this->listar("No se podido modificar el estado en Mantenimiento de las Comisiones Internas.", 2);
	}

	/**
	 * Se genera el HTML de las Comisiones Internas
	 */
    public function crearFormatoHTMLComisionesInternas() {

		// Se obtienen las Comisiones Internas
		$datos = $this->modelo->listar();
		
		// Se genera el HTML de las Comisiones Internas
		$this->vista->crearFormatoHTMLComisionesInternas($datos);
    }
    
    /**
     * Se confirma la publicación del HTML de Comisiones Internas en el directorio destino respectivo
     */
	public function confirmarPublicacion()
	{
		$nombre_archivo = LibreriaGeneral::recoge('nombre_archivo');
		
		// ARCHIVO EN /administracion/abms/temporal/
		$archivo_en_temporal = RUTA_DIRECTORIO_TEMPORAL.$nombre_archivo.".html";
		
		// DIRECTORIO DESTINO
		$directorio_destino = self::RUTA_DIRECTORIO_DESTINO;

		// SE ESTABLECE UNA CONEXION FTP
		$this->id_conexion = ftp_connect(self::FTP_SERVER);
		
		// SE ESTABLECE EL INICIO DE SESION FTP CON USUARIO Y PASSWORD
		$this->resultado_login = ftp_login($this->id_conexion, self::USUARIO, self::PASSWORD);
		
		// SE CHEQUEA LA CONEXION FTP
		if ( ( !$this->id_conexion ) || ( !$this->resultado_login ) ) {
			$mensaje = "Error al intentar conectarse o autentificarse en el Servidor FTP.";
			$tipo_mensaje = 2;
		} else {
			// SE CAMBIA AL DIRECTORIO DONDE SE QUIERE TRANSFERIR EL ARCHIVO
			ftp_chdir($this->id_conexion, $directorio_destino);
		
			$dir_actual = ftp_pwd($this->id_conexion);
		
			$ruta_archivo_remoto = $dir_actual."/".$nombre_archivo.".html";
			
			// SE CARGA EL ARCHIVO
			if ( ftp_put($this->id_conexion, $ruta_archivo_remoto, $archivo_en_temporal, FTP_BINARY) ) {
				$mensaje = "Se ha confirmado la publicaci&oacute;n del listado de ".$nombre_archivo.".";
				$tipo_mensaje = 1;
			} else {
				$mensaje = "&iexcl;La publicaci&oacute;n del listado de ".$nombre_archivo." ha fallado!";
				$tipo_mensaje = 2;
			}
		
			// SE CIERRA LA CONEXIÓN FTP
			ftp_close($this->id_conexion);
		}
		
		$_SESSION['administracion']['mensaje']        = $mensaje;
		$_SESSION['administracion']['tipo_mensaje']   = $tipo_mensaje;
		$_SESSION['administracion']['controlador']    = 'comisiones_internas';
		$_SESSION['administracion']['nombre_archivo'] = $nombre_archivo;
		
		// SE VUELVE AL LISTADO
		header ("Location: ../index.php");
	}
}
?>
