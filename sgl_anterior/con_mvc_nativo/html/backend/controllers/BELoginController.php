<?php
/**
 * Clase de controlador del proceso de login.
 *
 * @author Kaleb
 */
class BELoginController extends BaseController {
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

		// Determino las acciones válidas y su nivel de acceso MÍNIMO requerido
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['login'] = NIVEL_ACCESO_PERIODISTA;
		$this->accionesPermitidas['logout'] = NIVEL_ACCESO_PERIODISTA;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		// En el caso del Login, no verifico nivel de usuario porque es posible que
		// no se haya iniciado sesion, pero debe ser posible accederlo.
		//$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Si el usuario no esta logueado, lo llevo al formulario de login, sino lo llevo al home.
		if (!$this->usuarioValido()) {
			$vista = new BELoginView($this->generarParametrosVista()); // parámetros básicos
			$vista->vistaFormularioLogin();
		} else {
			$this->redireccionar('home', 'view');
		}
	}

	/**
	 * Invoca a la vista 'login' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function login($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		// En el caso del Login, no verifico nivel de usuario porque es posible que
		// no se haya iniciado sesion, pero debe ser posible accederlo.
		//$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		// Validación de parámetros
		$errorValidacion = "";
		try {
			$idUsuario = Validator::get()->validar($requestParams['f_usuario'], PATRON_USUARIO, false, 'Usuario');
			$password = Validator::get()->validar($requestParams['f_password'], PATRON_PASSWORD, false, 'Contrase&ntilde;a');
		} catch (Exception $ve) {
			// $errorValidacion = $ve->getMessage();
			$errorValidacion = "Usuario o contrase&ntilde;a incorrectos.";
			SessionController::get()->eliminar("USUARIO");
		}

		// Si la validacion de parametros es correcta, procedo con la validacion de credenciales.
		if ($errorValidacion == '') {
			try {
				// Busco el usuario
				$usuario = NG::seguridad()->validarCredencialesUsuario($idUsuario, $password);
				// Si el usuario existe
				if ($usuario !== null) {
					// Se guarda la info del usuario (serializada)
					SessionController::get()->guardarSerializado('USUARIO', $usuario);

					// Se adapta la versión 1 con la versión 2
					// -------------------------------------------------------------------------------------------
					// Se guardan ciertos datos en sesión para hacerlos compatibles con SGLv1
					// Aquí el perfil no se guarda en la sesión (se realiza debajo)
					NG::adaptadorSGLv1()->guardarEnSesionInfoAlAcceder($idUsuario, $password, $usuario->id_usuario, $usuario->nombre_usuario);

					// Se consulta a qué sistemas tiene acceso y el perfil para cada uno
					$accesos = NG::seguridad()->obtenerAccesosUsuario($usuario->id_usuario);

					// Si el usuario no posee ningún acceso
					if (empty($accesos)) {
						SessionController::get()->eliminar("USUARIO", $usuario);
						SessionController::get()->guardarError('Usuario sin acceso.', ERROR_CONTROLLER_GENERICO);
						// De vuelta al login
						$vista = new BELoginView($this->generarParametrosVista()); // parámetros básicos
						$vista->vistaFormularioLogin();
					} else {
						// Se guardan en sesión los accesos del usuario autenticado
						$_SESSION['accesos'] = $accesos;

						$nro_accesos = count($accesos);
						for ($a = 0; $a < $nro_accesos; $a++) {

							// 2023-06-09:
							// En Expedientes, se fuerza el perfil si puede confirmar giros
							// ------------------------------------------------------------
							// Si posee acceso a Expedientes (id_sistema=2)
							if ($accesos[$a]['id_sistema'] == 2) {

								// Si el usuario puede Confirmar Giros y tiene perfil 3 o 4 (porque con éstos no tiene permiso)
								if ( $usuario->confirma_giros == 1 && ($accesos[$a]['perfil'] == 3 || $accesos[$a]['perfil'] == 4)) {

									// Se le asigna el perfil 5 en Expedientes, para que Confirme Giros
									$_SESSION['perfil' . $accesos[$a]['id_sistema']] = 5;
								} else {
									// Se guarda en sesión el perfil que posea para expedientes
									$_SESSION['perfil' . $accesos[$a]['id_sistema']] = $accesos[$a]['perfil'];
								}
							}
							else // sino, se sigue como siempre
							{
								// Se guardan en sesión los perfiles para cada sistema al que posee acceso
								$_SESSION['perfil' . $accesos[$a]['id_sistema']] = $accesos[$a]['perfil'];
							}
						}

						// Si tiene acceso a más de un sistema
						if ($nro_accesos > 1) {
							// Se lo direcciona al menú para que seleccione los sistemas que le corresponden
							$this->redireccionar('home', 'menusistemas');
						}
						else // Si tiene acceso a UN sólo sistema
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
							// Si es el sistema de Administración
							elseif ($accesos[0]['id_sistema'] == 1)
							{
								// 05/03/2021 XXXX
								// Para implementar el nuevo sistema de Administración
								// y dejar el actual temporalmente hasta migrarlo entero
								// -----------------------------------------------------

								// Sólo si el perfil es 1
								if ($accesos[0]['perfil'] == 1) {
									// Se lo direcciona al menú para que seleccione los sistemas que le corresponden
									$this->redireccionar('home', 'menusistemas');
								} else {
									// Se obtiene su nombre (es el nombre del directorio respectivo en la versión 1 del SGL)
									$nombre_sistema = NG::seguridad()->obtenerNombreSistema($accesos[0]['id_sistema']);

									// Se lo dirige a dicho sistema
									header("Location: " . URL_KRAKEN_BASE . $nombre_sistema[0]['nombre_sistema'] . "/abms/");
									exit();
								}
							}
							// Si es el sistema de Personal
							elseif ($accesos[0]['id_sistema'] == 3) {
								// Se obtiene su nombre (es el nombre del directorio respectivo en la versión 1 del SGL)
								$nombre_sistema = NG::seguridad()->obtenerNombreSistema($accesos[0]['id_sistema']);

								// Se lo dirige a dicho sistema
								header("Location: " . URL_KRAKEN_BASE . $nombre_sistema[0]['nombre_sistema'] . "/index.php");
								exit();
							}
							// Si es el sistema de Biblioteca
							elseif ($accesos[0]['id_sistema'] == 4) {
								// Se lo dirige a dicho sistema
								header("Location: " . URL_KRAKEN_BASE . "administracion/abms/index.php?controlador=usuarios&accion=ingresarAlSistemaDeBiblioteca");
								exit();
							}
							// Si es el sistema de Inventario
							elseif ($accesos[0]['id_sistema'] == 5) {
								// Se lo dirige a dicho sistema
								header("Location: " . URL_KRAKEN_BASE . "administracion/abms/index.php?controlador=usuarios&accion=ingresarAlSistemaDeInventario");
								exit();
							}
							// Si es el sistema de Defensoria
							elseif ($accesos[0]['id_sistema'] == 6) {
								// Se obtiene su nombre (es el nombre del directorio respectivo en la versión 1 del SGL)
								$nombre_sistema = NG::seguridad()->obtenerNombreSistema($accesos[0]['id_sistema']);

								// Se lo dirige a dicho sistema
								header("Location: " . URL_KRAKEN_BASE . $nombre_sistema[0]['nombre_sistema'] . "/abms/");
								exit();
							}
						}
					}
					// -------------------------------------------------------------------------------------------
				} else {
					SessionController::get()->eliminar("USUARIO", $usuario);
					SessionController::get()->guardarError('Usuario o contrase&ntilde;a incorrectos.', ERROR_CONTROLLER_GENERICO);
					// De vuelta al login
					$vista = new BELoginView($this->generarParametrosVista()); // parámetros básicos
					$vista->vistaFormularioLogin();
				}
			} catch (Exception $e) {
				SessionController::get()->eliminar("USUARIO", $usuario); // Elimino el usuario, por las dudas
				SessionController::get()->guardarError('Error: ' . $e->getMessage(), ERROR_CONTROLLER_GENERICO);
				// De vuelta al login
				$vista = new BELoginView($this->generarParametrosVista()); // parámetros básicos
				$vista->vistaFormularioLogin();
			}
		} else {
			SessionController::get()->guardarError($errorValidacion, ERROR_CONTROLLER_GENERICO);
			// De vuelta al login
			$vista = new BELoginView($this->generarParametrosVista()); // parámetros básicos
			$vista->vistaFormularioLogin();
		}
	}

	/**
	 * Invoca a la vista 'logout' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function logout($requestParams) {
		// Antes que nada verifico el nivel de acceso.
		// En el caso del Login, no verifico nivel de usuario porque es posible que
		// no se haya iniciado sesion, pero debe ser posible accederlo.
		//$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Saneo parametros
		$requestParams = $this->sanearConjuntoParametros($requestParams);

		SessionController::get()->eliminar("USUARIO");
		SessionController::get()->guardar('MENSAJE_OK', 'Se ha cerrado la sesión.');

		// 2018-11-27 XXXX
		// Se eliminan las variables de sesion utilizadas durante el ingreso del usuario respectivo
		NG::adaptadorSGLv1()->eliminarEnSesionInfoAlAcceder();

		// Si existe en sesión alguna Actuación
  		if (SessionController::get()->existe('actuacion'))
  			// Se elimina
  			SessionController::get()->eliminar('actuacion');

		// De vuelta al login
		$vista = new BELoginView($this->generarParametrosVista()); // parámetros básicos
		$vista->vistaFormularioLogin();
	}

}
?>
