<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/personal.php';
require 'modelos/codareas.php';
require 'modelos/codcargos.php';
require '../informes/modelos/informes.php';

//Incluye la vista que corresponde
require 'vistas/personal.php';

class personal_controller extends ControllerBase
{
	private $rango_paginador;
	private $modeloCodAreas;
	private $modeloCodCargos;
	private $modeloInformes;
	private $mensaje;
	private $tipo_mensaje;
	private $directorio_ddjj;
	private $directorio_temporal;
	private $directorio_fotos;
	private $directorio_legajos;
	private $ancho_imagen;
	private $alto_imagen;

	public function __construct()
	{
		parent::__construct();

		$this->rango_paginador = 18;

		// Se crea una instancia del modelo de Personal
		$this->modelo = new personalModel();

		// Se crea una instancia del modelo de Codificadora de Areas
		$this->modeloCodAreas = new codareasModel();

		// Se crea una instancia del modelo de Codificadora de Cargos
		$this->modeloCodCargos = new codcargosModel();

		// Se crea una instancia del modelo de Informes
		$this->modeloInformes = new informesModel();

		// Se crea una instancia de la Vista
		$this->vista = new VistaPersonal();

		// Se inicializa el mensaje de resultados
		$this->mensaje = "";

		// Directorio de imágenes de las DDJJ
		$this->directorio_ddjj = "../ddjj/";

		// Directorio donde se guardó la foto temporalmente para su vista previa
		$this->directorio_temporal = "temporal/";

		// Directorio de las fotos de los legajos del HCD
		$this->directorio_fotos = "../fotos/";

		// Directorio de los Legajos Digitalizados del HCD
		$this->directorio_legajos  = '../legajos/';

		// Ancho y Alto de la foto a redimensionar
		$this->ancho_imagen = 480;
		$this->alto_imagen = 640;
	}

	public function guardarRegistroOriginal($original)
    {
		$_SESSION['p_legajo_original']             = $original['p_legajo'];
		$_SESSION['p_apellido_original']           = $original['p_apellido'];
		$_SESSION['p_nombre_original']             = $original['p_nombre'];
		$_SESSION['p_sexo_original']               = $original['p_sexo'];
		$_SESSION['p_grupo_sanguineo_original']    = $original['p_grupo_sanguineo'];
		$_SESSION['p_factor_sanguineo_original']   = $original['p_factor_sanguineo'];
		$_SESSION['p_tipo_documento_original']     = $original['p_tipo_documento'];
		$_SESSION['p_nro_documento_original']      = $original['p_nro_documento'];
		$_SESSION['p_cuil_original']      		   = $original['p_cuil'];
		$_SESSION['p_foto_original']               = $original['p_foto'];
		$_SESSION['p_fecha_nac_original']          = $original['p_fecha_nac'];
		$_SESSION['p_lugar_nac_original']          = $original['p_lugar_nac'];
		$_SESSION['p_provincia_original']          = $original['p_provincia'];
		$_SESSION['p_pais_original']               = $original['p_pais'];
		$_SESSION['p_nacionalidad_original']       = $original['p_nacionalidad'];
		$_SESSION['p_estado_civil_original']       = $original['p_estado_civil'];
		$_SESSION['p_calle_legal_original']        = $original['p_calle_legal'];
		$_SESSION['p_numero_legal_original']       = $original['p_numero_legal'];
		$_SESSION['p_piso_legal_original']         = $original['p_piso_legal'];
		$_SESSION['p_depto_legal_original']        = $original['p_depto_legal'];
		$_SESSION['p_entre_calles_legal_original'] = $original['p_entre_calles_legal'];
		$_SESSION['p_zona_barrio_legal_original']  = $original['p_zona_barrio_legal'];
		$_SESSION['p_pais_legal_original']         = $original['p_pais_legal'];
		$_SESSION['p_provincia_legal_original']    = $original['p_provincia_legal'];
		$_SESSION['p_localidad_legal_original']    = $original['p_localidad_legal'];
		$_SESSION['p_telefono_legal_original']     = $original['p_telefono_legal'];
		$_SESSION['p_calle_real_original']         = $original['p_calle_real'];
		$_SESSION['p_numero_real_original']        = $original['p_numero_real'];
		$_SESSION['p_piso_real_original']          = $original['p_piso_real'];
		$_SESSION['p_depto_real_original']         = $original['p_depto_real'];
		$_SESSION['p_entre_calles_real_original']  = $original['p_entre_calles_real'];
		$_SESSION['p_zona_barrio_real_original']   = $original['p_zona_barrio_real'];
		$_SESSION['p_pais_real_original']          = $original['p_pais_real'];
		$_SESSION['p_provincia_real_original']     = $original['p_provincia_real'];
		$_SESSION['p_localidad_real_original']     = $original['p_localidad_real'];
		$_SESSION['p_telefono_real_original']      = $original['p_telefono_real'];
		$_SESSION['p_celular_real_original']       = $original['p_celular_real'];
		$_SESSION['p_tel_mensajes_real_original']  = $original['p_tel_mensajes_real'];
		$_SESSION['p_mail_original']  			   = $original['p_mail'];
		$_SESSION['p_fecha_ingreso_planta_politica_original'] = $original['p_fecha_ingreso_planta_politica'];
		$_SESSION['p_fecha_ingreso_planta_permanente_original'] = $original['p_fecha_ingreso_planta_permanente'];
    }

    public function guardarEstudioOriginal($original)
    {
		$_SESSION['e_legajo_original']        = $original['e_legajo'];
		$_SESSION['e_titulo_original']        = $original['e_titulo'];
		$_SESSION['e_fecha_original']         = $original['e_fecha'];
		$_SESSION['e_organismo_original']     = $original['e_organismo'];
		$_SESSION['e_tipo_estudio_original']  = $original['e_tipo_estudio'];
		$_SESSION['e_observaciones_original'] = $original['e_observaciones'];
    }

	public function guardarFamiliarOriginal($original)
	{
		$_SESSION['f_legajo_emp_original']               = $original['f_legajo_emp'];
		$_SESSION['f_id_original']                       = $original['f_id'];
		$_SESSION['f_nro_documento_original']            = $original['f_nro_documento'];
		$_SESSION['f_parentesco_original']               = $original['f_parentesco'];
		$_SESSION['f_apellido_original']                 = $original['f_apellido'];
		$_SESSION['f_nombre_original']                   = $original['f_nombre'];
		$_SESSION['f_vive_original']                     = $original['f_vive'];
		$_SESSION['f_fecha_nac_original']                = $original['f_fecha_nac'];
		$_SESSION['f_nacionalidad_original']             = $original['f_nacionalidad'];
		$_SESSION['f_sexo_original']                     = $original['f_sexo'];
		$_SESSION['f_fecha_inicio_convivencia_original'] = $original['f_fecha_inicio_convivencia'];
		$_SESSION['f_discapacitado_original']            = $original['f_discapacitado'];
		$_SESSION['f_estudios_original']                 = $original['f_estudios'];
		$_SESSION['f_observaciones_original']            = $original['f_observaciones'];
    }

	public function guardarAntecedenteLaboralOriginal($original)
    {
		$_SESSION['al_legajo_original']        = $original['al_legajo'];
		$_SESSION['al_id_original']            = $original['al_id'];
		$_SESSION['al_ambito_original']        = $original['al_ambito'];
		$_SESSION['al_empresa_original']       = $original['al_empresa'];
		$_SESSION['al_cargo_original']         = $original['al_cargo'];
		$_SESSION['al_fecha_desde_original']   = $original['al_fecha_desde'];
		$_SESSION['al_fecha_hasta_original']   = $original['al_fecha_hasta'];
		$_SESSION['al_motivos_cese_original']  = $original['al_motivos_cese'];
		$_SESSION['al_observaciones_original'] = $original['al_observaciones'];
    }

	public function listar($mensaje = '', $tipo_mensaje = '')
	{
		$filtro = Array();

		// SI SE RECIBE UN MENSAJE DEL RESULTADO DE UNA OPERACION REALIZADA
		if ( Validador::validarParametro('mensaje') )
		{
			$mensaje = Validador::validarParametro('mensaje');
			$tipo_mensaje = Validador::validarParametro('tipo_mensaje');
		}

		// FILTRO POR AREA
		$filtro['id_area'] = Validador::validarParametro('cmb_area');
		if ( !$filtro['id_area'] )
		{
			$filtro['id_area'] = 0;
		}

		// FILTRO POR CARGO
		$filtro['nomenclador'] = Validador::validarParametro('cmb_cargo');
		if ( !$filtro['nomenclador'] )
		{
			$filtro['nomenclador'] = 0;
		}

		// FILTRO POR CONCEJAL
		$filtro['concejal'] = Validador::validarParametro('cmb_concejal');
		if ( !$filtro['concejal'] )
		{
			$filtro['concejal'] = 0;
		}

		// FILTRO POR LEGAJO
		$f_legajo = Validador::validarParametro('f_legajo');
		$filtro['legajo'] = ( $f_legajo != '' ) ? $f_legajo : '';

		// FILTRO POR APELLIDO Ó NOMBRE
		$f_apellido_y_nombre = Validador::validarParametro('f_apellido_y_nombre');
		$filtro['apellido_y_nombre'] = ( $f_apellido_y_nombre != '' ) ? $f_apellido_y_nombre : '';

		// PARA LISTAR SOLO LOS ACTIVOS O TODOS
		$f_activos = Validador::validarParametro('f_activos');
		$filtro['f_activos'] = ( $f_activos != '' ) ? $f_activos : '0';

		// SE SETEA EL CAMPO POR EL CUAL SE ORDENA
		$campo_orden = Validador::validarParametro('campo_orden');

		if ( !empty($campo_orden) )
		{
			$filtro['campo_orden'] = $campo_orden;
		}
		else
		{
			//por defecto
			$filtro['campo_orden'] = 'p_apellido';
			$_SESSION['ultimo_campo'] = '';
		}

		//DIRECCION PARA LA PAGINACION (PRIMERO, ANTERIOR, SGTE., ULTIMO)
		$filtro['sentido'] = Validador::validarParametro('sentido');

		if ( !isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden'] )
		{
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		}
		else
		{
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido:
			$_SESSION['ultimo_sentido'] = ( $_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '' ) ? 'desc' : 'asc';
		}

		// Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		//Se obtiene la cantidad total para calcular el nro. de paginas en la Vista
		$filtro['cantidad'] = $this->modelo->obtenerCantidad();

		$filtro['rango'] = $this->rango_paginador;

		//NUMERO TOTAL DE PAGINAS
		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		$filtro['pagina'] = Validador::validarParametro('pagina');	//se obtiene el valor de la pagina

		if ( !$filtro['pagina'] )
		{
			//al comienzo no se sabe el valor de la pagina
			$filtro['inicio'] = 0;	//por lo tanto se inicia en el primer registro
			$filtro['pagina'] = 1;	//con la primer pagina
		} else {
			//sino se calcula el valor del registro inicial de la pagina deseada
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
		}
		$filtro['pagina_ant'] = $filtro['pagina'] - 1;		//para la pagina anterior
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;		//para la pagina posterior

		// SI SE LLEGÓ MEDIANTE EL TECLADO AL RECORRER EL LISTADO
		$filtro['por_teclado'] = Validador::validarParametro('por_teclado');

		// SE GUARDAN EN SESION LOS PARAMETROS DE BUSQUEDA PARA NO PERDER UNA REFERENCIA ANTERIOR
		$_SESSION['filtro_personal'] = $filtro;

		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		// SE OBTIENE EL LISTADO
		$listado = $this->modelo->listar();

		$listadoAreas = $this->modelo->obtenerAreasCombo();

		$listadoCargos = $this->modelo->obtenerCargosCombo();

		$listadoConcejales = $this->modelo->obtenerConcejales($filtro['id_area'], $filtro['f_activos']);

		if ( empty($mensaje) && empty($tipo_mensaje) )
		{
			$mensaje = (isset($_SESSION['mensaje'])) ? $_SESSION['mensaje'] : '';
			$tipo_mensaje = (isset($_SESSION['tipo_mensaje'])) ? $_SESSION['tipo_mensaje'] : '';
		}

		// Se muestra el listado
		$this->vista->listar($listado, $mensaje, $tipo_mensaje, $filtro, $listadoAreas, $listadoCargos, $listadoConcejales);
    }

    /**
     * Se sigue editando un legajo determinado luego de haber cargado su foto
     */
    public function seguirEditando()
    {
    	$datos = Array();

    	// Se toman los datos para seguir editando el legajo
    	$datos = $_SESSION['personal'];

    	// Se elimina la información guardada en sesión
    	unset($_SESSION['personal']);

    	// Se formatean las fechas recibidas como yyyy-mm-dd para seguir editando en la Vista
    	$datos['p_fecha_ingreso_planta_politica']   = ( $datos['p_fecha_ingreso_planta_politica'] != '' ) ? $this->convertirFechaToMySQL($datos['p_fecha_ingreso_planta_politica']) : '';
    	$datos['p_fecha_ingreso_planta_permanente'] = ( $datos['p_fecha_ingreso_planta_permanente'] != '' ) ? $this->convertirFechaToMySQL($datos['p_fecha_ingreso_planta_permanente']) : '';
    	$datos['p_fecha_nac'] 						= ( $datos['p_fecha_nac'] != '' ) ? $this->convertirFechaToMySQL($datos['p_fecha_nac']) : '';

    	// SI SE RECIBE UN MENSAJE DEL RESULTADO DE UNA OPERACION REALIZADA
    	if ( Validador::validarParametro('mensaje') ) {
    		$mensaje = Validador::validarParametro('mensaje');
    		$tipo_mensaje = Validador::validarParametro('tipo_mensaje');
    	}

    	// Se siguen editando los datos personales
    	$this->vista->editar($datos['p_legajo'], $datos['pagina'], $mensaje, $tipo_mensaje, 'editarFicha');
    }

    /**
     * Se edita un legajo determinado
     */
    public function editar()
    {
		$legajo = Validador::validarParametro('legajo', 0);
		$pagina = Validador::validarParametro('pagina');

		$this->vista->editar($legajo, $pagina);
    }

    public function editarFicha($plegajo = '')
	{
		// Se recibe el legajo para su edición
		$legajo = ($plegajo != '') ? $plegajo : Validador::validarParametro('legajo', 0);

		//Se le pide al modelo todos los datos del Empleado
		$datos = $this->modelo->obtenerRegistro($legajo);

		// Si existe en la base de datos
		if ( $datos['p_legajo'] ) {
			// Se guarda el registro en sesión para verificar luego si no ha modificado otro usuario
			$this->guardarRegistroOriginal($datos);

			$datos['pagina'] = Validador::validarParametro('pagina');
		} else
			// En caso de editarse un NUEVO legajo
			$datos = null;

		$this->vista->editarFicha($datos);
    }

    /**
     * Se ingresan los Datos Personales de un NUEVO Legajo
     */
    public function insertar()
	{
		$_SESSION['p_operacion_confirmada'] = "si";

		$datos_recibidos = $_REQUEST;

		//Para que dos usuarios NO ingresen el mismo Legajo
		if ( $this->modelo->existe($datos_recibidos['p_legajo']) )
		{
			$this->mensaje = "Ya se ha ingresado a ".$datos_recibidos['p_nombre']." ".$datos_recibidos['p_apellido']." cuyo Legajo es ".$datos_recibidos['p_legajo'].".";
			$this->tipo_mensaje = 2;

			$this->listar($this->mensaje, $this->tipo_mensaje);
		}
		else
		{
			if ( $this->modelo->insertar($datos_recibidos) )
			{
				$this->mensaje = "Se ha ingresado a ".$datos_recibidos['p_nombre']." ".$datos_recibidos['p_apellido'].".";
				$this->mensaje .= "\n\n Puede seguir registrando informaci&oacute;n mediante las solapas.";
				$this->tipo_mensaje = 1;

				$this->vista->editar($datos_recibidos['p_legajo'], 0, $this->mensaje, $this->tipo_mensaje);
			}
			else
			{
				$this->mensaje = "Error al ingresar a ".$datos_recibidos['p_nombre']." ".$datos_recibidos['p_apellido'].".";
				$this->tipo_mensaje = 2;

				$this->listar($this->mensaje, $this->tipo_mensaje);
			}
		}
    }

    /**
     * Se modifican los Datos Personales de un Legajo existente
     */
    public function modificar()
	{
		$datos_recibidos = $_REQUEST;

		if ( $this->modelo->modificar($datos_recibidos) )
			$this->vista->editar($datos_recibidos['p_legajo'], 0, "Se ha modificado a ".$datos_recibidos['p_nombre']." ".$datos_recibidos['p_apellido']." satisfactoriamente, puede continuar con la edici&oacute;n o volver al listado principal.", 1);
		else
			$this->vista->editar($datos_recibidos['p_legajo'], 0, "Error al modificar a ".$datos_recibidos['p_nombre']." ".$datos_recibidos['p_apellido'].".", 2);
    }

    public function eliminar()
	{
		$legajo = Validador::validarParametro('legajo', 0);
		$cancela = Validador::validarParametro('cancela');

		if ( $this->modelo->eliminar($legajo) ) {
			if ( $cancela == "si" )
				$mensaje = "Se ha cancelado el registro.";
			else
				$mensaje = "El empleado con legajo ".$legajo." se ha dado de baja con &eacute;xito.";

			$_SESSION['p_operacion_confirmada'] = "si";

			$this->listar($mensaje, 1);
		} else
			$this->listar("Error al eliminar el Legajo ".$legajo.", puede poseer personal a su cargo.", 2);
	}

    public function listarEstudios($mensaje = '', $tipo_mensaje = '', $plegajo = 0)
	{
		$legajo = ( $plegajo != 0 ) ? $plegajo : Validador::validarParametro('legajo', 0);

		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		// SE OBTIENE EL Historico
		$estudios = $this->modelo->obtenerEstudios($legajo);

		$this->vista->listarEstudios($estudios, $info_legajo, $mensaje, $tipo_mensaje);
    }

    public function agregarEstudio()
	{
		$legajo = Validador::validarParametro('legajo', 0);

		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		$this->vista->editarEstudio(null, $info_legajo);
    }

    public function editarEstudio()
	{
		$legajo = Validador::validarParametro('legajo', 0);
		$fecha = Validador::validarParametro('fecha');

		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		// SE OBTIENEN LOS ESTUDIOS CURSADOS
		$estudio = $this->modelo->obtenerEstudios($legajo, $fecha);

		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarEstudioOriginal($estudio);

		$this->vista->editarEstudio($estudio, $info_legajo);
    }

    public function insertarEstudio()
	{
		$datos = $_REQUEST;

		if ( $this->modelo->insertarEstudio($datos) )
			$this->listarEstudios("El Estudio se ingres&oacute; con &eacute;xito.", 1, $datos['e_legajo']);
		else
			$this->listarEstudios("Error al ingresar el Estudio.", 2, $datos['e_legajo']);
    }

    public function modificarEstudio()
	{
		$datos = $_REQUEST;

		// SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ( $this->modelo->verificarEstudioEntero() ) {
			if ( $this->modelo->modificarEstudio($datos) )
				$this->listarEstudios("El Estudio se modific&oacute; con &eacute;xito.", 1, $datos['e_legajo']);
			else
				$this->listarEstudios("Error al modificar el Estudio.", 2, $datos['e_legajo']);
		} else
			$this->listarEstudios("El registro se ha modificado previamente.", 2, $datos['e_legajo']);
    }

    public function eliminarEstudio()
	{
		$legajo = Validador::validarParametro('legajo');
		$fecha = Validador::validarParametro('fecha');

		if ( $this->modelo->eliminarEstudio($legajo, $fecha) )
			$this->listarEstudios("El Estudio se elimin&oacute; con &eacute;xito.", 1, $legajo);
		else
			$this->listarEstudios("Error al eliminar el Estudio.", 2, $legajo);
    }

    public function listarAreas($mensaje = '', $tipo_mensaje = '', $plegajo = 0)
	{
		$legajo = ( $plegajo != 0 ) ? $plegajo : Validador::validarParametro('legajo', 0);

		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		// SE OBTIENEN LOS LISTADOS DE Areas
		$areasReconocidas = $this->modelo->obtenerAreas($legajo);

		$this->vista->listarAreas($areasReconocidas, $info_legajo, $mensaje, $tipo_mensaje);
    }

    public function agregarArea()
	{
		$legajo = Validador::validarParametro('legajo');

		// Se obtiene la informacion del legajo respectivo
		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		// Se verifica si es Concejal o no, se agrega el resultado
		$info_legajo['es_concejal'] = $this->modelo->esConcejal($legajo);

		// Para cargar el combo de Areas
		$areas_para_combo = $this->modeloCodAreas->listadoCombo();

		$this->vista->editarArea(null, $info_legajo, $areas_para_combo);
    }

    public function editarArea()
 	{
		$legajo = Validador::validarParametro('legajo', 0);
		$fecha_alta = Validador::validarParametro('fecha_alta');
		$id_area = Validador::validarParametro('id_area');

		// Se obtiene la informacion del legajo respectivo
		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		// Se verifica si es Concejal o no, se agrega el resultado
		$info_legajo['es_concejal'] = $this->modelo->esConcejal($legajo);

		// SE OBTIENE EL REGISTRO DEL AREA
		$info_area = $this->modelo->obtenerRegistroArea($legajo, $fecha_alta, $id_area);

		// Para cargar el combo de Areas
		$areas_para_combo = $this->modeloCodAreas->listadoCombo();

		$this->vista->editarArea($info_area, $info_legajo, $areas_para_combo);
    }

    /**
     * Se ingresa un legajo determinado a un Area respectiva
     */
    public function insertarArea()
	{
		$datos = $_REQUEST;

		// Si se ingresó el legajo al área
		if ( $this->modelo->insertarArea($datos) ) {
			// Si el legajo es de un Concejal
			if ( $datos['es_concejal'] == 1 && $datos['desea_modificar_para_asesores'] == 1 ) {
				// Si se ingresó el área también a sus asesores
				if ( $this->insertarAreasParaAsesores($datos) )
					$this->listarAreas("Se ingres&oacute; al &Aacute;rea con &eacute;xito tanto el Concejal como sus asesores que dependen de él.", 1, $datos['a_legajo']);
				else
					$this->listarAreas("Se ingresó&oacute; el Concejal al &Aacute;rea con &eacute;xito.\n\n Sin embargo por lo menos un asesor del Concejal no ha sido ingresado a dicha Área, porque ha sido ingresado en dicha fecha anteriormente.", 1, $datos['a_legajo']);
			} else
				$this->listarAreas("Se ingres&oacute; al &Aacute;rea con &eacute;xito.", 1, $datos['a_legajo']);
		} else
			$this->listarAreas("Error al intentar ingresar al &Aacute;Área determinada.", 2, $datos['a_legajo']);
    }

    /**
     * Se ingresan los asesores en un área determinada
     *
     * @param array $datos_area_del_concejal
     * @return boolean
     */
    public function insertarAreasParaAsesores($datos_area_del_concejal)
    {
    	// Se obtiene el listado de los asesores del Concejal
    	$listado_asesores = $this->modelo->obtenerDependientesPorConcejal($datos_area_del_concejal['a_legajo']);

    	$cantidad_asesores = count($listado_asesores);
    	for ($i=0; $i < $cantidad_asesores; $i++) {
    		// Se toma el legajo del asesor correspondiente para agregarlo al conjunto de datos del área
    		$datos_area_del_concejal['a_legajo'] = $listado_asesores[$i]['c_legajo'];

    		// Si NO se ingresó el legajo respectivo al área determinada
    		if ( !$this->modelo->insertarArea($datos_area_del_concejal) )
    			return false;
    	}

    	return true;
    }

	public function esElUltimoCargo($datos)
	{
		// SE VERIFICA SI ES EL ULTIMO CARGO DE UN LEGAJO DETERMINADO
		return $this->modelo->esElUltimoCargo($datos);
	}

	/**
	 * Se verifica si el Concejal posee dependientes en una fecha de alta determinada
	 * @param  integer $legajo     	Legajo del Concejal
	 * @param  string  $fecha_alta 	Fecha de alta a verificar
	 */
	public function verificarSiPoseeDependientes($legajo)
	{
		// Se obtienen los dependientes de dicho Concejal
		$dependientes = $this->modelo->obtenerDependientesPorConcejal($legajo);

		// Si posee dependientes o no
		return ( $dependientes[0]['c_legajo'] != '' );
	}

	/**
	 *  NO UTILIZADO Se modifica el área de un legajo determinado
	 */
    public function modificarArea()
	{
		$datos = $_REQUEST;

		// Si se recibe una Fecha de Baja, y el legajo posee dependientes (es un Cjal.)
		if ( $datos['a_fecha_baja'] != '' && $this->verificarSiPoseeDependientes($datos['a_legajo']) ) {
			$this->mensaje = "Antes de establecer la fecha de baja debe reasignar el personal dependiente a otro Concejal.";
			$this->tipo_mensaje = 2;
		} else {
			// Si se modificó el área
			if ( $this->modelo->modificarArea($datos) ) {
				// Si el legajo es de un Concejal
				if ( $datos['es_concejal'] == 1 )
					// Si se modificó el Bloque (área) también a sus asesores
					$error = ( $this->modificarAreasParaAsesores($datos) ) ? false : true;
				else
					$error = false;// Se modificó el área
			} else
				$error = true;// No se modificó el área

			$this->mensaje = ($error) ? "Error al modificar el Área." : "Se modificó el Área con éxito.";
			$this->tipo_mensaje = ($error) ? 2 : 1;
		}

		$this->listarAreas($this->mensaje, $this->tipo_mensaje, $datos['a_legajo']);
    }

    /**
     * NO UTILIZADO Se modifica un área determinada para los asesores de un concejal determinado
     *
     * @param array $datos_area_del_concejal
     * @return boolean
     */
    public function modificarAreasParaAsesores($datos_area_del_concejal)
    {
    	// Se obtiene el listado de los asesores del Concejal
    	$listado_asesores = $this->modelo->obtenerDependientesPorConcejal($datos_area_del_concejal['a_legajo']);

    	$cantidad_asesores = count($listado_asesores);
    	for ($i=0; $i < $cantidad_asesores; $i++) {
    		// Se toma el legajo del asesor correspondiente, para agregarlo al conjunto de datos del área
    		$datos_area_del_concejal['a_legajo'] = $listado_asesores[$i]['c_legajo'];

    		// Si NO se modificó el área para el legajo respectivo
    		if ( !$this->modelo->modificarArea($datos_area_del_concejal) )
	    		return false;
    	}

    	return true;
    }

    /**
     * Se elimina la asignación de un legajo a un área y fecha determinadas
     */
    public function eliminarArea()
	{
		$filtro = Array();
		$filtro['legajo'] 	  = Validador::validarParametro('legajo', 0);
		$filtro['fecha_alta'] = Validador::validarParametro('fecha_alta');
		$filtro['id_area'] 	  = Validador::validarParametro('id_area');

		//Se establece el filtro en el modelo
		$this->modelo->setFiltro($filtro);

		if ( $this->modelo->eliminarArea() )
			$this->listarAreas("El &Aacute;rea se elimin&oacute; con &eacute;xito.", 1);
		else
			$this->listarAreas("Error al eliminar el &Aacute;rea.", 2);
    }

    public function listarCargos($mensaje = '', $tipo_mensaje = '', $plegajo = 0)
	{
		$legajo = ( $plegajo != 0 ) ? $plegajo : Validador::validarParametro('legajo');

		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		// Se obtienen los cargos del legajo respectivo
		$cargosReconocidos = $this->modelo->obtenerCargos($legajo);

		$this->vista->listarCargos($cargosReconocidos, $info_legajo, $mensaje, $tipo_mensaje);
    }

    public function agregarCargo()
	{
		$legajo = Validador::validarParametro('legajo', 0);

		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		$info_legajo['digito'] = $this->modelo->obtenerDigitoActual($legajo);

		// OBTENER EL CONCEJAL DEL CUAL DEPENDE, SI YA ESTUVIESE REGISTRADO POR UN CARGO ANTERIOR
		$info_legajo['depende_de'] = $this->modelo->obtenerConcejalQueDepende($legajo);

		// PARA CARGAR EL COMBO DE Cargos
		$cargos_para_combo = $this->modeloCodCargos->listadoCombo($legajo);

		$this->vista->editarCargo(null, $info_legajo, $cargos_para_combo);
    }

    public function editarCargo()
 	{
		$legajo = Validador::validarParametro('legajo', 0);
		$fecha_alta = Validador::validarParametro('fecha_alta');
		$id_cargo = Validador::validarParametro('nomenclador');

		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		// Se obtiene el registro del Cargo
		$info_cargo = $this->modelo->obtenerRegistroCargo($legajo, $fecha_alta, $id_cargo);

		// Para cargar el combo de Cargos
		// 20/01/2020 XXXX
		// Se consideran los Cargos deshabilitados también
		$cargos_para_combo = $this->modeloCodCargos->listadoComboAlEditar($legajo);

		$this->vista->editarCargo($info_cargo, $info_legajo, $cargos_para_combo);
    }

    public function insertarCargo()
	{
		$datos = $_REQUEST;

	// 06/04/2015 SE COMENTO PARA LA CARGA DE HISTORICOS
		// Se verifica si la fecha de alta es mayor o igual a la fecha de ingreso al Municipio
		//if ( $this->modelo->esValidaFechaAlta($datos) ) {
			if ( $this->modelo->insertarCargo($datos) )
				$this->listarCargos("El cargo se agreg&oacute; con &eacute;xito.", 1, $datos['c_legajo']);
			else
				$this->listarCargos("Error al ingresar el cargo, ya se encuentra registrado un cargo para dicha fecha.", 2, $datos['c_legajo']);
		//}
		//else
		//	$this->listarCargos("La fecha de alta debe ser mayor o igual a la fecha de ingreso al Municipio, y en caso de existir, mayor que la fecha de baja del cargo anterior.", 2, $datos['c_legajo']);
    }

    public function modificarCargo()
	{
		$datos = $_REQUEST;

		// && $this->esElUltimoCargo($post)
		// SI SE RECIBE UNA FECHA DE BAJA Y EL LEGAJO POSEE DEPENDIENTES
		if ( $datos['c_fecha_baja'] != '' &&  $this->verificarSiPoseeDependientes($datos['c_legajo']) )
			$this->listarCargos("Antes de establecer la fecha de baja debe reasignar el personal dependiente a otro Concejal.", 2, $datos['c_legajo']);
		else {
			if ( $this->modelo->modificarCargo($datos) )
				$this->listarCargos("El Cargo se modific&oacute; con &eacute;xito.", 1, $datos['c_legajo']);
			else
				$this->listarCargos("Error al modificar el Cargo.", 2, $datos['c_legajo']);
		}
	}

    public function eliminarCargo()
	{
		$filtro = Array();
		$filtro['legajo'] = Validador::validarParametro('legajo', 0);
		$filtro['fecha_alta'] = Validador::validarParametro('fecha_alta');
		$filtro['nomenclador'] = Validador::validarParametro('nomenclador');

		$this->modelo->setFiltro($filtro);

		if ( $this->modelo->eliminarCargo() )
			$this->listarCargos("El Cargo se elimin&oacute; con &eacute;xito.", 1);
		else
			$this->listarCargos("Error al eliminar el Cargo.", 2);
    }

    public function buscarApellidoNombre()
	{
		$c_depende_de = Validador::validarParametro('c_depende_de');

		$apellido_y_nombre = $this->modelo->buscarApellidoNombre($c_depende_de);

        echo "{'apellido_y_nombre':'".$apellido_y_nombre[0]['p_apellido'].", ".$apellido_y_nombre[0]['p_nombre']."'}";
    }

    public function listarFamilia($mensaje = '', $tipo_mensaje = '', $plegajo = 0)
	{
		$legajo = ( $plegajo != 0 ) ? $plegajo : Validador::validarParametro('legajo', 0);

		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		// SE OBTIENE LA Familia comnpleta
		$familia = $this->modelo->obtenerFamilia($legajo);

		$this->vista->listarFamilia($familia, $info_legajo, $mensaje, $tipo_mensaje);
    }

    public function agregarFamiliar()
	{
		$legajo = Validador::validarParametro('legajo', 0);

		// SE AGREGA AL FILTRO EL Apellido Y Nombre DEL EMPLEADO
		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		$this->vista->editarFamiliar(null, $info_legajo);
    }

	public function editarFamiliar()
	{
		$legajo = Validador::validarParametro('legajo', 0);
		$id = Validador::validarParametro('id');

		// SE AGREGA AL FILTRO EL Apellido Y Nombre DEL EMPLEADO
		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		// SE OBTIENE EL Familiar
		$familiar = $this->modelo->obtenerFamilia($legajo, $id);
		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarFamiliarOriginal($familiar);

		$this->vista->editarFamiliar($familiar, $info_legajo);
    }

	public function insertarFamiliar()
	{
		$datos = $_REQUEST;

		if ( $this->modelo->yaPoseeParentesco($datos['f_legajo_emp'], $datos['f_parentesco']) )
			$this->listarFamilia("Ya se ha registrado a su ".$datos['f_parentesco'].".", 2, $datos['f_legajo_emp']);
		else {
			if ( $this->modelo->insertarFamiliar($datos) )
				$this->listarFamilia("El Familiar se ingres&oacute; con &eacute;xito.", 1, $datos['f_legajo_emp']);
			else
				$this->listarFamilia("Error al ingresar el Familiar.", 2, $datos['f_legajo_emp']);
		}
    }

    public function modificarFamiliar()
	{
		$datos = $_REQUEST;

		// SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ( $this->modelo->verificarFamiliarEntero() ) {
			// SI SE HA CAMBIADO EL PARENTESCO
			if ( $datos['f_parentesco'] != $_SESSION['f_parentesco_original'] ) {
				// SI ES UN HIJO SE MODIFICA DIRECTAMENTE
				if ( $datos['f_parentesco'] == "Hijo" )
					$esModificable = true;
				else {
					// SE VERIFICA SI YA POSEE EL PARENTESCO
					if ( $this->modelo->yaPoseeParentesco($datos['f_legajo_emp'], $datos['f_parentesco']) ) {
						$this->mensaje = "Ya se ha registrado a su ".$datos['f_parentesco'].".";
						$this->tipo_mensaje = 2;
						$esModificable = false;
					}
					else
						$esModificable = true;
				}
			}
			else
				$esModificable = true;

			if ( $esModificable ) {
				if ( $this->modelo->modificarFamiliar($datos) ) {
					$this->mensaje = 'El Familiar se modific&oacute; con &eacute;xito.';
					$this->tipo_mensaje = 1;
				} else {
					$this->mensaje = 'Error al modificar el Familiar.';
					$this->tipo_mensaje = 2;
				}
		    }

		    $this->listarFamilia($this->mensaje, $this->tipo_mensaje, $datos['f_legajo_emp']);
		}
		else
		{
			$this->mensaje = 'El registro se ha modificado previamente.';
			$this->tipo_mensaje = 2;

			$this->listarFamilia($this->mensaje, $this->tipo_mensaje, $datos['f_legajo_emp']);
		}

    }

	public function eliminarFamiliar()
	{
		$legajo = Validador::validarParametro('legajo', 0);
		$id = Validador::validarParametro('id');

		if ( $this->modelo->eliminarFamiliar($legajo, $id) )
			$this->listarFamilia("El integrante del grupo familiar se elimin&oacute; con &eacute;xito.", 1, $legajo);
		else
			$this->listarFamilia("Error al eliminar el integrante del grupo familiar.", 2, $legajo);
    }

	public function listarAntecedentesLaborales($mensaje = '', $tipo_mensaje = '', $legajo = 0)
	{
		$legajo = ( $legajo != 0 ) ? $legajo : Validador::validarParametro('legajo');

		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		// SE OBTIENEN LOS Antecedentes Laborales completos
		$antecedentes_laborales = $this->modelo->listarAntecedentesLaborales($legajo);

		$this->vista->listarAntecedentesLaborales($antecedentes_laborales, $info_legajo, $mensaje, $tipo_mensaje);
    }

    public function agregarAntecedenteLaboral()
	{
		$legajo = Validador::validarParametro('legajo', 0);

		// Se obtiene la informacion del legajo respectivo
		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		$this->vista->editarAntecedenteLaboral(null, $info_legajo);
    }

	public function editarAntecedenteLaboral()
	{
		$legajo = Validador::validarParametro('legajo', 0);
		$id = Validador::validarParametro('id');

		// SE AGREGA AL FILTRO EL Apellido Y Nombre DEL EMPLEADO
		$info_legajo = $this->modelo->obtenerRegistro($legajo);

		// SE OBTIENE EL Familiar
		$antecedente_laboral = $this->modelo->listarAntecedentesLaborales($legajo, $id);
		// SE GUARDA EL REGISTRO EN SESION PARA VERIFICAR LUEGO SI LO HA MODIFICADO OTRO USUARIO
		$this->guardarAntecedenteLaboralOriginal($antecedente_laboral);

		$this->vista->editarAntecedenteLaboral($antecedente_laboral, $info_legajo);
    }

	public function insertarAntecedenteLaboral()
	{
		$datos = $_REQUEST;

		if ( $this->modelo->insertarAntecedenteLaboral($datos) )
			$this->listarAntecedentesLaborales("El Trabajo se ingres&oacute; con &eacute;xito.", 1, $datos['al_legajo']);
		else
			$this->listarAntecedentesLaborales("Error al ingresar el Trabajo.", 2, $datos['al_legajo']);
    }

    public function modificarAntecedenteLaboral()
	{
		$datos = $_REQUEST;

		// SE VERIFICA SI EL REGISTRO NO HA SIDO MODIFICADO PREVIAMENTE
		if ( $this->modelo->verificarAntecedenteLaboralEntero() ) {
			// Se modifica el Antecedente
			if ( $this->modelo->modificarAntecedenteLaboral($datos) )
				$this->listarAntecedentesLaborales("El Trabajo se modific&oacute; con &eacute;xito.", 1, $datos['al_legajo']);
			else
				$this->listarAntecedentesLaborales("Error al modificar el Trabajo.", 2, $datos['al_legajo']);
		} else
			$this->listarAntecedentesLaborales("El registro se ha modificado previamente.", 2, $datos['al_legajo']);
    }

	public function eliminarAntecedenteLaboral()
	{
		$legajo = Validador::validarParametro('legajo', 0);
		$id = Validador::validarParametro('id');

		if ( $this->modelo->eliminarAntecedenteLaboral($legajo, $id) )
			$this->listarAntecedentesLaborales("El Trabajo se elimin&oacute; con &eacute;xito.", 1, $legajo);
		else
			$this->listarAntecedentesLaborales("Error al eliminar el Trabajo.", 2, $legajo);
    }

    /**
     * Se muestra una ventana modal para la carga de la foto, se embebe el html respectivo en un iframe para utilizar jQuery
     */
    public function mostrarIframe()
    {
    	$p_legajo = Validador::validarParametro('legajo', 0);
    	$p_foto = Validador::validarParametro('foto');

    	$this->vista->mostrarIframe($p_legajo, $p_foto);
    }

    /**
     * Se muestra una modal para visualizar la foto carnet actual y los botones "Borrar", "Subir otra" y "Cerrar"
    */
	public function editarFotoActual()
	{
		$p_legajo = Validador::validarParametro('legajo', 0);
		$p_foto = Validador::validarParametro('foto');

		$this->vista->editarFotoActual($p_legajo, $p_foto);
	}

    /**
     * Se borra la foto actual de un legajo respectivo en el directorio fotos/ y en la DB mediante su id
     */
    public function borrarFotoActual()
    {
    	// Se recibe el legajo
    	$legajo = Validador::validarParametro('legajo');

    	// Se recibe el nombre de la foto
    	$nombre_foto = Validador::validarParametro('nombre_foto');

    	// Si existe la foto en el directorio de fotos de los legajos
    	if ( is_file("../fotos/".$nombre_foto) )
		{
			// Si se borra la foto del directorio "fotos/"
			if ( unlink("../fotos/".$nombre_foto) )
			{
				// Si se borra el nombre de la foto en la DB
				if ( $this->modelo->eliminar_foto($legajo) )
				{
					$this->mensaje = "Se elimin&oacute; la foto ".$nombre_foto." con &eacute;xito.";
					$this->tipo_mensaje = 1;
				}
				else
				{
					$this->mensaje = "No se ha eliminado la foto ".$nombre_foto.".";
					$this->tipo_mensaje = 2;
				}
			}
		}

    	// Se vuelve a la ficha de datos personales para seguir editando el legajo
    	$this->editarFicha($legajo);
    }

	/**
	 * Se verifica la existencia del legajo, si no existe se elimina la foto en caso de poseer
	 */
	public function verificarExistenciaLegajo()
	{
		$legajo = Validador::validarParametro('legajo', 0);
		$foto = Validador::validarParametro('foto');

		// SE REVISA LA EXISTENCIA DEL LEGAJO
		$datos = $this->modelo->obtenerRegistro($legajo);

		// SI NO EXISTE EL LEGAJO
		if ( $datos['p_legajo'] == '' )
			// SI EXISTE LA FOTO EN EL DIRECTORIO DE IMAGENES
			if ( is_file("../fotos/".$foto) )
				// SE ELIMINA DICHA FOTO
				unlink("../fotos/".$foto);

		// SE MUESTRA EL LISTADO
		$this->listar();
	}

    public function verificarAreaAsignada()
    {
		$legajo = Validador::validarParametro('legajo');

		$tiene = $this->modelo->verificarAreaAsignada($legajo);

		echo "{'tiene':'".$tiene['a_legajo']."'}";
	}

	public function refrescarComboCargos()
	{
		$cmb_area = Validador::validarParametro('cmb_area');

		$tipo_area = $this->modeloInformes->obtenerTipoArea($cmb_area);

		$listadoCargos = $this->modelo->obtenerCargosPorTipo($tipo_area);

	    $this->vista->comboCargos($listadoCargos);
	}

	public function refrescarComboConcejales()
	{
		$cmb_area = Validador::validarParametro('cmb_area');
		$f_activos = Validador::validarParametro('f_activos');

		$listadoConcejales = $this->modelo->obtenerConcejales($cmb_area, $f_activos);

	    $this->vista->comboConcejales($listadoConcejales);
	}

    public function buscarTipoCargo()
	{
		$c_nomenclador = Validador::validarParametro('c_nomenclador');

		$tipo_cargo = $this->modelo->buscarTipoArea($c_nomenclador);

        echo "{'tipo_cargo':'".$tipo_cargo['cc_tipo']."'}";
    }

    public function listarModalDependientes()
	{
		$c_legajo = Validador::validarParametro('c_legajo');

		$listado = $this->modelo->listarModalDependientes($c_legajo);

		//se muestra la Ventana Modal
		$this->vista->listarModalDependientes($listado);
    }

    /**
     * Se sigue editando las DDJJ de un legajo determinado
     */
    public function seguirEditandoDDJJ()
    {
    	// Se asigna el legajo respectivo para seguir en la solapa de DDJJ
    	$legajo = $_SESSION['personal']['p_legajo'];

    	// Se elimina la información del legajo guardada en sesión
    	unset($_SESSION['personal']);

    	// SI SE RECIBE UN MENSAJE DEL RESULTADO DE UNA OPERACION REALIZADA
    	if ( Validador::validarParametro('mensaje') )
    	{
    		$mensaje = Validador::validarParametro('mensaje');
    		$tipo_mensaje = Validador::validarParametro('tipo_mensaje');
    	}

    	// Se ejecuta el mismo método del controlador para editar la DDJJ
    	$this->vista->editar($legajo, '', $mensaje, $tipo_mensaje, 'editarDDJJ');
    }

    /**
     * Se obtiene el legajo del nombre del archivo de DDJJ/Legajos
     * @param  [string]  $archivo_digitalizado 	Nombre del archivo de DDJJ/Legajos
     * @return [integer] 						Legajo
     */
    public function obtenerLegajoDelNombreArchivoDDJJ($archivo_digitalizado) {

		$partes = explode("_", $archivo_digitalizado);

		return $partes[1];
    }

    public function obtenerDDJJPorLegajo($plegajo) {

    	$archivos_ddjj = Array();

    	// Si pudo abrirse el directorio de imágenes de DDJJ respectivo
       	if ( $dir_abierto = opendir($this->directorio_ddjj) ) {
       		// Mientras se encuentre un archivo en el directorio respectivo
			while ( false !== ( $imagen_ddjj = readdir($dir_abierto) ) ) {
				// Si se trata de un archivo de DDJJ
				if ( $imagen_ddjj != '..' && $imagen_ddjj != '.' && $imagen_ddjj != 'index.html' && $imagen_ddjj != 'resize.php'  ) {
					// Tomamos sólo las del legajo respectivo
					// usamos los guiones bajos porque un legajo puede ser igual a la parte de otro
					// Ejemplo: 27446 y 127446
					if (strpos($imagen_ddjj, '_'.$plegajo.'_') !== false)
					    $archivos_ddjj[] = $imagen_ddjj;
				}
			}
		}

       	return $archivos_ddjj;
    }

    /**
     * Se muestra un formulario para cargar las DDJJ de un legajo determinado
     */
    public function editarDDJJ($plegajo = 0, $mensaje = '', $tipo_mensaje = '')
    {
    	$legajo = ( $plegajo != 0 ) ? $plegajo : Validador::validarParametro('legajo');

    	// Se obtienen los datos del legajo respectivo
    	$datos = $this->modelo->obtenerRegistro($legajo);

    	// Se obtienen las DDJJ del legajo respectivo
    	$datos['ddjj'] = $this->obtenerDDJJPorLegajo($legajo);

    	// Se ordenan de mayor a menor
    	rsort($datos['ddjj']);

    	$this->vista->editarDDJJ($datos, $mensaje, $tipo_mensaje);
    }

    /**
     * Devuelve la fecha en el formato Y_m_d, para ser utilizada en el nombre de la DDJJ a cargar
     *
     * @param string $fecha
     *
     * @return string $fecha_ddjj
     */
    public function formatearFechaDDJJ($fecha)
    {
    	// Se separa la fecha por la barra invertida
    	$partes_fecha = explode("/", $fecha);

    	// Devuelve la fecha en el formato Y_m_d
    	return $fecha_ddjj = $partes_fecha[2].'_'.$partes_fecha[1].'_'.$partes_fecha[0];
    }

    /**
     * Se cargan las DDJJ del legajo respectivo
     */
    public function guardarDDJJ()
    {
    	// Se reciben los datos del legajo utilizados para cargar sus DDJJ
    	$datos_recibidos = $_POST;

    	// Se recibe la info de los archivos a subir
    	$info_de_archivos = $_FILES['imagen_ddjj'];

    	// Si se recibió una imagen de DDJJ para cargar
    	if ( isset($info_de_archivos['name']) && $info_de_archivos['name'] != '' )
    		// Se carga la imagen en el directorio de DDJJ
    		$this->cargarImagenDDJJ($datos_recibidos, $info_de_archivos);
    	else
    		// Se setea la variable para NO volver a la solapa de DDJJ
    		$datos_recibidos['seguir_en_solapa_ddjj'] = 'no';

    	// Se vuelve a la solapa de DDJJ del legajo respectivo
    	$this->volverSolapaDDJJ($datos_recibidos);
    }

    /**
     * Se carga la imagen en el directorio de DDJJ
     *
     * @param array $datos_recibidos
     * @param array $info_de_archivos
     */
    public function cargarImagenDDJJ($datos_recibidos, $info_de_archivos)
    {
    	// Se formatea la fecha para utlizarla en el nombre de la imagen de DDJJ a cargar
    	$fecha_ddjj = $this->formatearFechaDDJJ($datos_recibidos['fecha_ddjj']);

    	// Extensiones válidas de archivo a subir
    	$extensiones_validas = array("jpg", "jpeg", "png", "gif", "bmp", "pdf");

    	// Tamaño máximo permitido de 40MB = 1024*1024*40
    	$tamanio_maximo_archivo = 41943040;

    	// Directorio donde se almacenan las DDJJ
    	$directorio_destino = $this->directorio_ddjj;

        // Si se reciben los datos
    	if ( isset($datos_recibidos) )
    	{
    		// Archivo de la imagen
    		$archivo_a_guardar = $info_de_archivos['tmp_name'];

    		// Se eliminan los espacios vacíos que contenga el nombre del archivo
    		$nombre_archivo = LibreriaGeneral::eliminarEspacios($info_de_archivos['name']);

    		// Se toma la extensión del archivo y se convierte a minúscula
    		$extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

    		// Se conforma el nombre de la imagen a guardar con: ddjj_ + legajo_ + fecha en formato Y_m_d + "." + extensión
    		$nombre_archivo_a_guardar = "ddjj_".$datos_recibidos['p_legajo'].'_'.$fecha_ddjj.".".$extension;

    		// Si no se recibió el archivo
    		if ($info_de_archivos['error'] == 4)
    		{
    			$this->mensaje = "No se ha subido el archivo ".$nombre_archivo;
    			$this->tipo_mensaje = 2;
    		}

    		// Si el archivo fue recibido sin errores
    		if ( $info_de_archivos['error'] == 0 )
    		{
    			// Si el tamaño del archivo supera el límite determinado
    			if ($info_de_archivos['size'] > $tamanio_maximo_archivo)
    			{
    				$this->mensaje = $nombre_archivo." supera el tama&ntilde;o m&aacute;ximo permitido.";
    				$this->tipo_mensaje = 2;
    			}
    			// Si su extensión no es válida
    			elseif( !in_array($extension, $extensiones_validas) )
    			{
    				$this->mensaje = "La extensi&oacute;n de ".$nombre_archivo." no es v&aacute;lida.";
    				$this->tipo_mensaje = 2;
    			}
    			else
    			{
    				// Se obtienen datos específicos de la imagen
    				$datos_archivo_a_guardar = getimagesize($archivo_a_guardar);

        			// Si el archivo realmente es una imagen
    				if ( $datos_archivo_a_guardar )
    				{
    					// Si no existe el directorio
    					if ( !is_dir($directorio_destino) )
    					{
    						$permisos = '777';
    						$permisos = octdec(str_pad($permisos, 4, '0', STR_PAD_LEFT));

    						@mkdir($directorio_destino, $permisos); // Se crea
    						@chmod($directorio_destino, $permisos); // Se le da permisos
    					}
    				}

    				// Se arma la ruta destino: directorio + nombre de archivo
    				$ruta_destino_completa = $directorio_destino.$nombre_archivo_a_guardar;

    				// Si NO se mueve el archivo al directorio destino
    				if( !move_uploaded_file($archivo_a_guardar, $ruta_destino_completa) )
    				{
    					$this->mensaje = "Error al ingresar la imagen ".$nombre_archivo." de la DDJJ.";
    					$this->tipo_mensaje = 2;
    				}
    			}
    		}

    		// Si no surgió un error
    		if ( $this->tipo_mensaje != 2 )
    		{
    			$this->mensaje = "Se ha cargado la imagen de la Declaraci&oacute;n Jurada satisfactoriamente.";
    			$this->tipo_mensaje = 1;
    		}
    	}
    	else
    	{
    		$this->mensaje = "No se ha recibido la imagen de la Declaraci&oacute;n Jurada.";
    		$this->tipo_mensaje = 2;
    	}
    }

    /**
     * Se vuelve a la solapa de DDJJ del legajo respectivo
     * @param array $datos_recibidos
     */
    public function volverSolapaDDJJ($datos_recibidos)
    {
    	// Se guardan en sesión los datos para volver a la solapa de DDJJ
    	$_SESSION['personal'] = $datos_recibidos;
    	$_SESSION['personal']['mensaje'] = $this->mensaje;
    	$_SESSION['personal']['tipo_mensaje'] = $this->tipo_mensaje;

    	header ("Location: ../index.php");
    }

    /**
     * Se elimina una DDJJ determinada
     */
    public function eliminarDDJJ()
    {
    	// Se recibe el legajo respectivo
    	$legajo = Validador::validarParametro('legajo', 0);

    	// Se recibe el nombre de la imagen de DDJJ
    	$nombre_imagen_ddjj = Validador::validarParametro('nombre_imagen_ddjj');

    	// Si existe la DDJJ respectiva
    	if ( is_file($this->directorio_ddjj.$nombre_imagen_ddjj) ) {
    		// Si se elimina la DDJJ del directorio determinado
    		if ( unlink($this->directorio_ddjj.$nombre_imagen_ddjj) ) {
    			$this->mensaje = "Se elimin&oacute; la DDJJ ".$nombre_imagen_ddjj." con &eacute;xito.";
    			$this->tipo_mensaje = 1;
    		} else {
    			$this->mensaje = "No se ha eliminado la DDJJ ".$nombre_imagen_ddjj;
    			$this->tipo_mensaje = 2;
    		}
    	}

    	$this->editarDDJJ($legajo, $this->mensaje, $this->tipo_mensaje);
    }

    /**
     * Se carga la foto y se registra la información del legajo respectivo
     */
    public function guardarFotoLegajo()
    {
    	// Se reciben los datos del legajo
    	$datos_recibidos = $_POST;

    	// Se recibe la info de la foto a subir
    	$info_archivo = $_FILES['imagen'];

    	// Si se recibió una imagen para cargar
    	if ( isset($info_archivo['name']) && $info_archivo['name'] != '' )
    		// Se carga la imagen en el directorio de fotos
    		$this->cargarFotoLegajo($datos_recibidos, $info_archivo);
    	else
    		// Se setea la variable para NO volver a la solapa de Datos Personales
    		$datos_recibidos['se_sigue_editando'] = 'no';

    	// Se vuelve a la solapa de Datos Personales del legajo respectivo
    	$this->volverSolapaDatosPersonales($datos_recibidos);
    }

    /**
     * Se carga la imagen en el directorio de fotos
     *
     * @param array $datos_recibidos
     * @param array $info_de_archivos
     */
    public function cargarFotoLegajo($datos_recibidos, $info_archivo)
    {
    	// Si se desea borrar la foto en el directorio y en la base de datos
    	if ( $datos_recibidos['borrar_imagen'] == 'si' )
    	{
    		// Se obtiene el nombre de la foto a eliminar
    		$nombre_foto = $this->modelo->obtenerNombreFoto($datos_recibidos['p_legajo']);

    		// Si existe la foto en el directorio de imagenes
    		if ( is_file($this->directorio_fotos.$nombre_foto) )
    			// Se elimina la foto en el directorio
    			unlink($this->directorio_fotos.$nombre_foto);

    		// Se elimina el nombre de la foto del Legajo en la base de datos
    		if ( $this->modelo->eliminar_foto($datos_recibidos['p_legajo']) )
    		{
    			// Si existe el legajo
    			if ( $this->modelo->existe($datos_recibidos['p_legajo']) )
    			{
    				// Si se modifica la información recibida del legajo respectivo
    				if ( $this->modelo->modificar($datos_recibidos ) )
    				{
    					$this->mensaje = "Se ha eliminado la foto y modificado la informaci&oacute;n satisfactoriamente, puede continuar con la edici&oacute;n o volver al listado principal.";
    					$this->tipo_mensaje = 1;
    				}
    				else
    				{
    					$this->mensaje = "Error al modificar la informaci&oacute;n del legajo.";
    					$this->tipo_mensaje = 2;
    				}
    			}
    			else
    			{
    				// Si se ingresa la información recibida del legajo respectivo
    				if ( $this->modelo->insertar($datos_recibidos ) )
    				{
    					$this->mensaje = "Se ha eliminado la foto e ingresado la informaci&oacute;n satisfactoriamente, puede continuar con la edici&oacute;n o volver al listado principal.";
    					$this->tipo_mensaje = 1;
    				}
    				else
    				{
    					$this->mensaje = "Error al ingresar la informaci&oacute;n del legajo.";
    					$this->tipo_mensaje = 2;
    				}
    			}
    		}
    		else
    		{
    			$this->mensaje = "Error al eliminar la foto.";
    			$this->tipo_mensaje = 2;
    		}
    	}
    	else
    	{
    		// SI SE RECIBE UNA FOTO PARA SUBIR
    		if ( $info_archivo['name'] != '' )
    		{
    			// SE VERIFICA SI REALMENTE SE SUBIO EL ARCHIVO DE LA FOTO MEDIANTE HTTP POST
    			if (is_uploaded_file($info_archivo['tmp_name']))
    			{
    				// ARCHIVO DE LA IMAGEN
    				$archivo = $info_archivo['tmp_name'];

    				// NOMBRE DEL ARCHIVO
    				$nombre_archivo = $info_archivo['name'];

    				// SE EXTRAE SU EXTENSION
    				$extension = strtolower(end(explode('.', $nombre_archivo)));

    				// SE ARMA EL NOMBRE DE LA FOTO: LEGAJO + _ + FECHA + . + EXTENSION
    				$nombre_foto = $datos_recibidos['p_legajo'].'_'.date("Y-m-d").'.'.$extension;

    				// SE OBTIENE INFORMACION DEL ARCHIVO SUBIDO
    				$datos = @getimagesize($archivo);

    				// SI EL ARCHIVO ES UNA IMAGEN
    				if ( $datos )
    				{
    					// SE GUARDA EL NUEVO NOMBRE
    					if ( $nombre_foto != '' )
    						$datos_recibidos['p_foto'] = $nombre_foto;

    					// DIRECTORIO DE LAS FOTOS DE LOS EMPLEADOS
    					$directorio = $this->directorio_fotos;

    					// SI NO EXISTE
    					if ( !is_dir($directorio) )
    					{
    						$permisos = '777';
    						$permisos = octdec(str_pad($permisos, 4, '0', STR_PAD_LEFT));

    						@mkdir($directorio, $permisos); // SE CREA
    						@chmod($directorio, $permisos); // SE LE DA PERMISOS
    					}

    					// SE CREA UNA NUEVA
    					if ( $datos[2]==1 )
    						$img = @imagecreatefromgif($archivo);

    					if ( $datos[2]==2 )
    						$img = @imagecreatefromjpeg($archivo);

    					if ( $datos[2]==3 )
    						$img = @imagecreatefrompng($archivo);

    					$ancho_original = $datos[0];// ANCHO DE LA IMAGEN RECIBIDA
    					$alto_original = $datos[1];// ALTO DE LA IMAGEN RECIBIDA

    					// SI EL ANCHO ES MENOR AL ALTO
    					if ( $ancho_original < $alto_original )
    					{
    						// SE FIJA EL ALTO DE LA IMAGEN
    						$alto_final = $this->alto_imagen;// ALTO_IMAGEN

    						// SE CALCULA LA PROPORCION DEL ANCHO DE LA IMAGEN
    						$ancho_final = ($alto_final / $alto_original) * $ancho_original;
    					}
    					else
    					{
    						// SE FIJA EL ANCHO DE LA IMAGEN
    						$ancho_final = $this->ancho_imagen;// ANCHO_IMAGEN
    						// SE CALCULA LA PROPORCION DEL ALTO DE LA IMAGEN
    						$alto_final = ($ancho_final / $ancho_original) * $alto_original;
    					}

    					// Crea una imagen que representa una imagen en negro del tamaño especificado
    					$imagen_redimensionada = imagecreatetruecolor($ancho_final, $alto_final);

    					// Copia y cambia el tamaño de parte de la imagen redimensionándola
    					imagecopyresampled($imagen_redimensionada, $img, 0, 0, 0, 0, $ancho_final, $alto_final, $datos[0], $datos[1]);

    					if ( $datos[2] == 1 )
    						@imagegif($imagen_redimensionada, $directorio.$nombre_foto);

    					if ( $datos[2] == 2 )
    						@imagejpeg($imagen_redimensionada, $directorio.$nombre_foto);

    					if ( $datos[2] == 3 )
    						@imagepng($imagen_redimensionada, $directorio.$nombre_foto);

    					imagedestroy($imagen_redimensionada);

    					// Se elimina la foto en el directorio temporal, usado para la vista previa
    					if ( unlink($this->directorio_temporal.$nombre_archivo) )
    					{
    						// Si existe el legajo
    						if ( $this->modelo->existe($datos_recibidos['p_legajo']) )
    						{
	    						// Si se modifica la información recibida del legajo respectivo
	    						if ( $this->modelo->modificar($datos_recibidos ) )
	    						{
	    							$this->mensaje = "Se ha subido la foto y modificado la informaci&oacute;n satisfactoriamente, puede continuar con la edici&oacute;n o volver al listado principal.";
	    							$this->tipo_mensaje = 1;
	    						}
	    						else
	    						{
	    							$this->mensaje = "Error al modificar la informaci&oacute;n del legajo.";
	    							$this->tipo_mensaje = 2;
	    						}
    						}
    						else
    						{
    							// Si se ingresa la información recibida del legajo respectivo
    							if ( $this->modelo->insertar($datos_recibidos ) )
    							{
    								$this->mensaje = "Se ha subido la foto e ingresado la informaci&oacute;n satisfactoriamente, puede continuar con la edici&oacute;n o volver al listado principal.";
    								$this->tipo_mensaje = 1;
    							}
    							else
    							{
    								$this->mensaje = "Error al ingresar la informaci&oacute;n del legajo.";
    								$this->tipo_mensaje = 2;
    							}
    						}
	    				}
    					else
    					{
    						//$this->mensaje = "No se ha podido eliminar la imagen temporal para vista previa.";
    						$this->tipo_mensaje = 2;
    					}
    				}
    				else
    				{
    					$this->mensaje = "El archivo no es una imagen, extensi&oacute;n inv&aacute;lida.";
    					$this->tipo_mensaje = 2;
    				}
    			}
    			else
    			{
    				$this->mensaje = "No se ha subido la foto.";
    				$this->tipo_mensaje = 2;
    			}
    		}
    	}
    }

    /**
     * Se vuelve a la solapa de Datos Personales del legajo respectivo
     * @param array $datos_recibidos
     */
    public function volverSolapaDatosPersonales($datos_recibidos)
    {
    	// Se guardan en sesión los datos para volver a la solapa de Datos Personales
    	$_SESSION['personal'] = $datos_recibidos;
    	$_SESSION['personal']['mensaje'] = $this->mensaje;
    	$_SESSION['personal']['tipo_mensaje'] = $this->tipo_mensaje;

    	header ("Location: ../index.php");
    }

    // 10/05/2022 XXXX ---------------------------------------------------

    /**
     * Se muestra un formulario para cargar los Legajos digitalizados de un legajo determinado
     */
    public function editarLegajos($plegajo = 0, $mensaje = '', $tipo_mensaje = '')
    {
    	$legajo = ( $plegajo != 0 ) ? $plegajo : Validador::validarParametro('legajo');

    	// Se obtienen los datos del legajo respectivo
    	$datos = $this->modelo->obtenerRegistro($legajo);

    	// Se obtienen los Legajos digitalizados del legajo respectivo
    	$datos['legajos'] = $this->obtenerLegDigitalizadosPorLegajo($legajo);

    	// Se ordenan de mayor a menor
    	rsort($datos['legajos']);

    	$this->vista->editarLegajos($datos, $mensaje, $tipo_mensaje);
    }

    public function unificarLegajoDigitalizado()
    {
    	$legajo = Validador::validarParametro('legajo');

    	$legajos_digitalizados = $this->obtenerLegDigitalizadosPorLegajo($legajo);

    	sort($legajos_digitalizados);

		for ($i=0; $i < count($legajos_digitalizados); $i++)
			$legajos_digitalizados_con_path[$i] = $this->directorio_legajos . $legajos_digitalizados[$i];

    	$pdf_unificado = sprintf('%s%d_completo.pdf', $this->directorio_legajos, $legajo);

    	// Se define el comando para unir los legajos digitalizados, utilizando el comando gs (ghostscript)
		$cmd = sprintf("gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile='%s' %s",
			$pdf_unificado,
			join(' ', $legajos_digitalizados_con_path)
		);

		$cmd_result = shell_exec($cmd);

		if (! file_exists($pdf_unificado))
			throw new Exception("Error en unificarLegajoDigitalizado, no se encuentra el pdf: ".$pdf_unificado);

    	header("location:".$pdf_unificado.'?v='.date('YmdHis'));
    	exit();
    }

    /**
     * Se sigue editando los Legajos digitalizados de un legajo determinado
     */
    public function seguirEditandoLegajosDigitalizados()
    {
    	// Se asigna el legajo respectivo para seguir en la solapa de DDJJ
    	$legajo = $_SESSION['personal']['p_legajo'];

    	// Se elimina la información del legajo guardada en sesión
    	unset($_SESSION['personal']);

    	if ( Validador::validarParametro('mensaje') ) {
    		$mensaje = Validador::validarParametro('mensaje');
    		$tipo_mensaje = Validador::validarParametro('tipo_mensaje');
    	}

    	// Se ejecuta el mismo método del controlador para editar los legajos digitalizados
    	$this->vista->editar($legajo, '', $mensaje, $tipo_mensaje, 'editarLegajos');
    }

    public function obtenerLegDigitalizadosPorLegajo($plegajo) {

    	$archivos_legajos_digitalizados = Array();

    	// Si pudo abrirse el directorio respectivo
       	if ( $dir_abierto = opendir($this->directorio_legajos) ) {

       		// Mientras se encuentre un archivo en el directorio
			while ( false !== ( $pdf_legajo = readdir($dir_abierto) ) ) {

				// Si se trata de un archivo
				if ( $pdf_legajo != '..' && $pdf_legajo != '.' && $pdf_legajo != $plegajo.'_completo.pdf' ) {

					// Tomamos sólo las del legajo respectivo
					// usamos los guiones bajos porque un legajo puede ser igual a la parte de otro
					// Ejemplo: 27446 y 127446
					if (strpos($pdf_legajo, $plegajo.'_') !== false)
					    $archivos_legajos_digitalizados[] = $pdf_legajo;
				}
			}
		}

       	return $archivos_legajos_digitalizados;
    }

    /**
     * Se cargan los Legajos digitalizados del legajo respectivo
     */
    public function guardarLegajosDigitalizados()
    {
    	// Se reciben los datos del legajo
    	$datos_recibidos = $_POST;

    	// Se recibe la info de los archivos a subir
    	$info_de_archivos = $_FILES['legajo_digitalizado'];

    	// Si se recibió un archivo para cargar
    	if ( isset($info_de_archivos['name']) && $info_de_archivos['name'] != '' )
    		// Se carga
    		$this->cargarLegajosDigitalizados($datos_recibidos, $info_de_archivos);
    	else
    		// Se setea la variable para NO volver a la solapa de DDJJ
    		$datos_recibidos['seguir_en_solapa_legajos_digitalizados'] = 'no';

    	// Se vuelve a la solapa de DDJJ del legajo respectivo
    	$this->volverSolapaLegajoDigitalizado($datos_recibidos);
    }

    /**
     * Se carga el legajo digitalizado
     *
     * @param array $datos_recibidos
     * @param array $info_de_archivos
     */
    public function cargarLegajosDigitalizados($datos_recibidos, $info_de_archivos)
    {
    	// Se formatea la fecha para utlizarla en el nombre de la imagen de DDJJ a cargar
    	$fecha_legajo_digitalizado = $this->formatearFechaDDJJ($datos_recibidos['fecha_legajo_digitalizado']);

    	// Tamaño máximo permitido de 40MB = 1024*1024*40
    	$tamanio_maximo_archivo = 41943040;

    	// Directorio donde se almacenan los Legajos digitalizados
    	$directorio_destino = $this->directorio_legajos;

        // Si se reciben los datos
    	if ( isset($datos_recibidos) )
    	{
    		// Archivo de la imagen
    		$archivo_a_guardar = $info_de_archivos['tmp_name'];

    		// Se eliminan los espacios vacíos que contenga el nombre del archivo
    		$nombre_archivo = LibreriaGeneral::eliminarEspacios($info_de_archivos['name']);

    		// Se toma la extensión del archivo y se convierte a minúscula
    		$extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));

    		// Se conforma el nombre de la digitalización a guardar con:
    		// legajo_ + fecha en formato Y_m_d + "." + extensión
    		$nombre_archivo_a_guardar = $datos_recibidos['p_legajo'].'_'.$fecha_legajo_digitalizado.'_'.date("H_i_s").".".$extension;

    		// Si no se recibió el archivo
    		if ($info_de_archivos['error'] == 4)
    		{
    			$this->mensaje = "No se ha subido el archivo ".$nombre_archivo;
    			$this->tipo_mensaje = 2;
    		}

    		// Si el archivo fue recibido sin errores
    		if ( $info_de_archivos['error'] == 0 )
    		{
    			// Si el tamaño del archivo supera el límite determinado
    			if ($info_de_archivos['size'] > $tamanio_maximo_archivo)
    			{
    				$this->mensaje = $nombre_archivo." supera el tama&ntilde;o m&aacute;ximo permitido.";
    				$this->tipo_mensaje = 2;
    			}
    			// Si su extensión no es válida
    			elseif( $extension != 'pdf')
    			{
    				$this->mensaje = "La extensi&oacute;n de ".$nombre_archivo." no es v&aacute;lida.";
    				$this->tipo_mensaje = 2;
    			}
    			else
    			{
					// Si no existe el directorio
					if ( !is_dir($directorio_destino) )
					{
						$permisos = '777';
						$permisos = octdec(str_pad($permisos, 4, '0', STR_PAD_LEFT));

						@mkdir($directorio_destino, $permisos); // Se crea
						@chmod($directorio_destino, $permisos); // Se le da permisos
					}

    				// Se arma la ruta destino: directorio + nombre de archivo
    				$ruta_destino_completa = $directorio_destino.$nombre_archivo_a_guardar;

    				// Si NO se mueve el archivo al directorio destino
    				if( !move_uploaded_file($archivo_a_guardar, $ruta_destino_completa) )
    				{
    					$this->mensaje = "Error al ingresar la digitalizacion ".$nombre_archivo." del Legajo.";
    					$this->tipo_mensaje = 2;
    				}
    			}
    		}

    		// Si no surgió un error
    		if ( $this->tipo_mensaje != 2 )
    		{
    			$this->mensaje = "Se ha cargado la digitalizacion del Legajo satisfactoriamente.";
    			$this->tipo_mensaje = 1;
    		}
    	}
    	else
    	{
    		$this->mensaje = "No se ha recibido la digitalizacion del Legajo.";
    		$this->tipo_mensaje = 2;
    	}
    }

    /**
     * Se vuelve a la solapa de DDJJ del legajo respectivo
     * @param array $datos_recibidos
     */
    public function volverSolapaLegajoDigitalizado($datos_recibidos)
    {
    	// Se guardan en sesión los datos para volver a la solapa de DDJJ
    	$_SESSION['personal'] = $datos_recibidos;
    	$_SESSION['personal']['mensaje'] = $this->mensaje;
    	$_SESSION['personal']['tipo_mensaje'] = $this->tipo_mensaje;

    	header ("Location: ../index.php");
    }

    /**
     * Se elimina un Legajo Digitalizado determinado
     */
    public function eliminarLegajoDigitalizado()
    {
    	// Se recibe el legajo respectivo
    	$legajo = Validador::validarParametro('legajo', 0);

    	// Se recibe el nombre de la imagen de DDJJ
    	$nombre_legajo_digitalizado = Validador::validarParametro('nombre_legajo_digitalizado');

    	if ( is_file($this->directorio_legajos.$nombre_legajo_digitalizado) ) {

    		if ( unlink($this->directorio_legajos.$nombre_legajo_digitalizado) ) {
    			$this->mensaje = "Se elimin&oacute; el legajo digitalizado ".$nombre_legajo_digitalizado." con &eacute;xito.";
    			$this->tipo_mensaje = 1;
    		} else {
    			$this->mensaje = "No se ha eliminado el legajo digitalizado ".$nombre_legajo_digitalizado;
    			$this->tipo_mensaje = 2;
    		}
    	}

    	$this->editarLegajos($legajo, $this->mensaje, $this->tipo_mensaje);
    }

}
?>
