<?php
// Script de control de variables de sesion
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/librerias/control_sesion.php');

//Incluye el modelo que corresponde
require 'modelos/informes.php';
require '../abms/modelos/personal.php';

//Incluye la vista que corresponde
require 'vistas/credenciales.php';

class credenciales_controller extends ControllerBase
{
	private $modeloPersonal;
	private $duracion_periodo;

	public function __construct()
	{
		parent::__construct();

		// Se crea una instancia del modelo de Informes
		$this->modelo = new informesModel();

		// Se crea una instancia del modelo de Personal
		$this->modeloPersonal = new personalModel();

		// Se crea una instancia de la Vista de Informes
		$this->vista = new VistaCredenciales();

		// Por defecto la duración del período es 4 años
		$this->duracion_periodo = 4;
	}

	/**
	 * Se solicita información para generar la credencial de un legajo determinado
	 */
	public function pedirInfoParaCredencial()
	{
		// Se recibe el legajo desde su ficha
		$legajo = Validador::validarParametro('legajo');

		// Se obtienen los datos de un Legajo determinado (apellido, nombre, dni, etc)
		$datos = $this->modelo->obtenerDatosLegajo($legajo);

		// Se obtiene información del Cargo actual del legajo respectivo
		$info_cargo_actual = $this->modelo->obtenerCargoActual($legajo);

		// Si su cargo es Concejal
		if( $info_cargo_actual[0]['c_nomenclador'] == $this->modelo->obtenerIdCargoConcejal() )
		{
			$datos['nombre_cargo'] = 'Concejal';
		}
		// Si su cargo es Secretario del HCD
		elseif( $info_cargo_actual[0]['c_nomenclador'] == $this->modelo->obtenerIdCargoSecretarioHCD() )
		{
			$datos['nombre_cargo'] = ($datos['p_sexo'] == 'M' ) ? 'Secretario' : 'Secretaria';
		}
		// 18/07/2019
		// Si su cargo es Defensor del Pueblo
		elseif( $info_cargo_actual[0]['c_nomenclador'] == $this->modelo->obtenerIdCargoDefensorPueblo() || $info_cargo_actual[0]['c_nomenclador'] == $this->modelo->obtenerIdCargoDefensorPuebloCoordinador() )
		{
			$datos['nombre_cargo'] = 'Defensor del Pueblo';

			$this->duracion_periodo = 5;// un año más
		}
		// Por defecto se asigna Presidente
		else
		{
			$datos['nombre_cargo'] = 'Presidente';
		}

		// Se separa la fecha de alta
		$fecha_alta_separada = explode('-', $info_cargo_actual[0]['c_fecha_alta']);

		// Se toma el año como inicio de su período
		$datos['periodo_anio_inicio'] = $fecha_alta_separada[0];

		// Para el fin del período se toma el año de inicio más cuatro años, o cinco para Defensoría del Pueblo
		$datos['periodo_anio_fin'] = $fecha_alta_separada[0] + $this->duracion_periodo;

		// Se toma el número del mes, quitando el cero de la izquierda en caso de poseerlo
		$datos['nro_mes_fecha_alta'] = str_replace('0', '', $fecha_alta_separada[1]);

		// Se muestra la ventana modal para solicitar el destinatario del certificado
		$this->vista->pedirInfoParaCredencial($datos);
	}

	/**
	 * Se genera la credencial en formato pdf
	 */
	public function generarCredencial()
	{
		$datos = Array();

		$datos['legajo']                 = Validador::validarParametro('legajo');
		$datos['gc_funcion']             = Validador::validarParametro('gc_funcion');
		$datos['gc_periodo_anio_inicio'] = Validador::validarParametro('gc_periodo_anio_inicio');
		$datos['gc_periodo_anio_fin']    = Validador::validarParametro('gc_periodo_anio_fin');
		$datos['gc_fecha_creacion_mes']  = Validador::validarParametro('gc_fecha_creacion_mes');
		$datos['gc_fecha_creacion_anio'] = Validador::validarParametro('gc_fecha_creacion_anio');

		// Se obtienen los datos de un Legajo determinado (apellido, nombre, dni, etc)
		$info_legajo = $this->modelo->obtenerDatosLegajo($datos['legajo']);

		$this->vista->generarCredencial($datos, $info_legajo);
	}

	/**
	 * Se muestra una ventana modal para elegir los Concejales para generar sus credenciales respectivas
	 */
	public function elegirConcejalesParaCredenciales()
	{
		// Se obtienen los Concejales ACTIVOS
		$listadoConcejales = $this->modelo->obtenerConcejalesActivos();

		// Se muestra la ventana modal
		$this->vista->elegirConcejalesParaCredenciales($listadoConcejales);
	}

	/**
	 * Se generan las credenciales en lote, para todos los Concejales
	 */
	public function generarCredencialesParaConcejales()
	{
		// Se reciben los Concejales elegidos
		$info['gcc_concejales'] = $_REQUEST["gcc_concejales"];

		// Se reciben los años iniciales de los Concejales elegidos
		$info['gcc_anios_inicio'] = $_REQUEST["gcc_anios_inicio"];

		// Se reciben los años finales de los Concejales elegidos
		$info['gcc_anios_fin'] = $_REQUEST["gcc_anios_fin"];

		$info['gcc_fecha_creacion_mes'] = Validador::validarParametro('gc_fecha_creacion_mes');
		$info['gcc_fecha_creacion_anio'] = Validador::validarParametro('gc_fecha_creacion_anio');

		//Logger::GetInstance()->Log("info_en_generarCredencialesParaConcejales", $info);

		// Se generan las credenciales para cada Concejal
		$this->vista->generarCredenciales($info);
	}

	/**
	 * Se muestra una ventana modal, para solicitar la información necesaria para generar el certificado de trabajo, para un legajo determinado
	 */
	public function pedirInfoParaCertificadoTrabajo()
	{
		// Se recibe el legajo desde su ficha
		$legajo = Validador::validarParametro('legajo');

		$datos = Array();
		// Se obtienen los datos del Legajo respectivo  (apellido, nombre, dni, etc)
		$datos = $this->modelo->obtenerDatosLegajo($legajo);

		// Se obtienen el histórico de cargos del Legajo respectivo
		$cargos_reconocidos = $this->modelo->obtenerHistoricoCargos($legajo);

		// Se muestra la ventana modal para solicitar la información necesaria para generar el certificado de trabajo
		$this->vista->pedirInfoParaCertificadoTrabajo($datos, $cargos_reconocidos);
	}

	/**
	 * Se carga el texto para el certificado correspondiente
	 */
	public function cargarTextoParaCertificadoTrabajo()
	{
		$legajo = Validador::validarParametro('legajo');

		// Se obtienen los datos de un Legajo determinado (apellido, nombre, dni, etc)
		$datos = $this->modelo->obtenerDatosLegajo($legajo);

		$datos['ct_destinatario'] = Validador::validarParametro('ct_destinatario');

		$datos['ct_tipo'] = Validador::validarParametro('ct_tipo');

		$datos['ct_fecha_alta_inicial'] = Validador::validarParametro('ct_fecha_alta_inicial');

		// Se obtiene el Dígito actual del Legajo respectivo
		$datos['digito'] = $this->modeloPersonal->obtenerDigitoActual($legajo);

		// Se obtienen los datos del último o actual Cargo del Legajo respectivo
		$datos['datos_ultimo_cargo'] = $this->modelo->obtenerUltimoCargo($legajo);

		// Se obtienen los datos de la última o actual Área del Legajo respectivo
		$datos['datos_ultima_area'] = $this->modeloPersonal->obtenerNombreUltimaArea($legajo);

		// Se carga el texto según el tipo de certificado elegido
		switch ($datos['ct_tipo'])
		{
			case 1:
				$this->vista->cargarTextoParaCertificadoEmpleadoBloque($datos);
				break;
			case 2:
				$this->vista->cargarTextoParaCertificadoEmpleadoPlanta($datos);
				break;
			case 3:
				$this->vista->cargarTextoParaCertificadoPresidenteHCD($datos);
				break;
			case 4:
				$this->vista->cargarTextoParaCertificadoSecretarioHCD($datos);
				break;
			case 5:
				$this->vista->cargarTextoParaCertificadoConcejal($datos);
				break;
		}
	}

	/**
	 * Se genera el certificado de trabajo en formato PDF, para un legajo determinado
	 */
	public function generarCertificadoTrabajo()
	{
		// Se recibe la información del certificado a generar
		$datos = $_REQUEST;

		// Se genera el certificado de trabajon respectivo, en formato PDF
		$this->vista->generarCertificadoTrabajo($datos);
	}

	/**
	 * 31/07/2019 XXXX
	 * Se muestra una ventana modal para elegir los Concejales para generar sus credenciales respectivas
	 */
	public function elegirDefensoresPuebloParaCredenciales()
	{
		// Se obtienen los Defensores ACTIVOS
		$listadoDefensoresPueblo = $this->modelo->obtenerDefensoresPuebloActivos();

		// Se muestra la ventana modal
		$this->vista->elegirDefensoresPuebloParaCredenciales($listadoDefensoresPueblo);
	}

	/**
	 * 31/07/2019 XXXX
	 * Se generan las credenciales en lote, para todos los Concejales
	 */
	public function generarCredencialesParaDefensoresPueblo()
	{
		// Se reciben los Concejales elegidos
		$info['gcc_concejales'] = $_REQUEST["gcc_concejales"];

		// Se reciben los años iniciales de los Concejales elegidos
		$info['gcc_anios_inicio'] = $_REQUEST["gcc_anios_inicio"];

		// Se reciben los años finales de los Concejales elegidos
		$info['gcc_anios_fin'] = $_REQUEST["gcc_anios_fin"];

		$info['gcc_fecha_creacion_mes'] = Validador::validarParametro('gc_fecha_creacion_mes');
		$info['gcc_fecha_creacion_anio'] = Validador::validarParametro('gc_fecha_creacion_anio');

		// Se generan las credenciales para cada Concejal
		$this->vista->generarCredencialesDefensoresPueblo($info);
	}

}
