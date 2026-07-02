<?php
/**
 * Clase de controlador del Home del subsitio.
 *
 * @author Kaleb
 */
class BEHomeController extends BaseController {
	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct() {
		// Llamada al constructor del padre
		parent::__construct();

		// Seteo de ruta base de la interfaz
		$this->baseUrl = URL_KRAKEN_HTML_BACKEND;

		// Nombre del módulo al que corresponde el controlador
		$this->nombreModulo = 'EXPEDIENTES';

		// Determino las acciones válidas y su nivel de acceso mínimo requerido
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['menusistemas'] = NIVEL_ACCESO_CONCEJAL;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		// En el caso del Home, no verifico nivel de usuario porque es posible que
		// no se haya iniciado sesion, pero debe ser posible accederlo.
		//$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Si el usuario no esta logueado, lo llevo al formulario de login
		if (!$this->usuarioValido()) {
			$vista = new BELoginView($this->generarParametrosVista()); // parámetros básicos
			$vista->vistaFormularioLogin();
		} else {
			// 04/09/2020 XXXX y XXXX
			// Se lo redirige al usuario, ya autenticado, al menú de sistemas o al único sistema, según los Accesos que posea.
			$this->redirigirSegunAccesos($_SESSION['accesos']);
			//$this->redireccionar('home', 'view'); se cambia la redireccion a otra cosa que NO sea el home/view para que no quede en un loop de redireccion
		}
	}

	/**
	 * Se redirige al usuario, ya autenticado, al menú de sistemas o al único sistema, según los Accesos que posea.
	 * @param  array $accesos Los accesos a los diferentes sistemas que posee el usuario ya autenticado
	 */
	private function redirigirSegunAccesos($accesos) {

		$nro_accesos = count($accesos);
		// Si tiene acceso a más de un sistema
		if ($nro_accesos > 1) {
			// Se lo direcciona al menú para que seleccione los sistemas que le corresponden
			$this->redireccionar('home', 'menusistemas');
		}
		else // Si tiene acceso a un sólo sistema
		{
			// Si es el sistema de Expedientes
			if ($accesos[0]['id_sistema'] == 2) {

				// Sólo si el perfil es 1 ó 2
				if ($accesos[0]['perfil'] == 1 || $accesos[0]['perfil'] == 2) {
					// Se lo direcciona al menú para que seleccione los sistemas que le corresponden
					$this->redireccionar('home', 'menusistemas');
				} else {
					// Versión 2.0 de Expedientes
					$this->redireccionar('expedientes', 'view');
				}
			}
			else // Si es Personal o Administración
			{
				// Se obtiene su nombre (es el nombre del directorio respectivo en la versión 1 del SGL)
				$nombre_sistema = NG::seguridad()->obtenerNombreSistema($accesos[0]['id_sistema']);

				// Se lo dirige a dicho sistema
				header("Location: " . URL_KRAKEN_BASE . $nombre_sistema[0]['nombre_sistema'] . "/index.php");
				exit();
			}
		}
	}

	/**
	 * Invoca a la vista 'menusistemas' del controlador.
	 */
	public function menusistemas() {
		// Instancio la vista del Menú de Sistemas
		$vista = new BEMenuSistemasView();
		// Se muestra
		$vista->vistaMenuSistemas();
	}
}
?>
