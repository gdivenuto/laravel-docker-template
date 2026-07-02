<?php
/**
 * Clase de controlador de Gestion de Firmas Pendientes para los Supervisores (Mesa de Entrada)
 *
 * @author XXXX y XXXX
 */
DEFINE('CHECKSUM', 'CHECKSUM_FIRMAS');
DEFINE('SAVE_ACTION', 'SAVE_ACTION_FIRMAS');

class BEFirmaspendientessupervisorController extends BaseController
{
	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct()
	{
		// Llamada al constructor del padre
		parent::__construct();

		// Seteo de ruta base de la interfaz
		$this->baseUrl = URL_KRAKEN_HTML_BACKEND;

		// Nombre del módulo al que corresponde el controlador
		$this->nombreModulo = 'EXPEDIENTES';

		// Se sobreescriben permisos, si aplica:
		$this->accionesPermitidas['view'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['datagrid'] = NIVEL_ACCESO_CONCEJAL;
		$this->accionesPermitidas['enviarrecordatorio'] = NIVEL_ACCESO_CONCEJAL;
	}

	/**
	 * Invoca a la vista 'view' del controlador.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function view($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		// Preparo los parámetros de la vista
		$paramVista = $this->generarParametrosVista();

		// Instancio la vista y la muestro
		$vista = new BEFirmasPendientesParaSupervisorView($paramVista);
		$vista->vistaPendientesFirma();
	}

	/**
	 * Esta acción del controlador permite obtener los datos para completar las grillas con datatables.
	 * Retorna directamente (via 'echo') un JSON con la informacion solicitada.
	 * @param  mixed $requestParams Por claridad del modelo, se pasa por parámetro el conjunto de parametros de la petición (generalmente la union de $_REQUEST y $_FILES).
	 */
	public function datagrid($requestParams)
	{
		// Antes que nada verifico el nivel de acceso, pero sin redireccionar al home en caso de error.
		// Esto lo hago así por ser una respuesta JSON.
		$this->verificarNivelAccesoUsuario(__FUNCTION__, false);

		$resultado = array();

		// En caso de error, lo advierto.
		if (SessionController::get()->existe('MENSAJE_ERROR')) {

			$resultado['recordsTotal'] = 0;
			$resultado['recordsFiltered'] = 0;
			$resultado['data'] = array();
			$resultado['error'] = SessionController::get()->obtener('MENSAJE_ERROR');
			$resultado['numeroError'] = SessionController::get()->obtener('NUMERO_ERROR');

		} else {
			// Seteo el valor de control "draw"
			$p_draw = $requestParams['draw'];

			// Obtengo datos para la paginación
			$p_limitStart = (trim($requestParams['start']) == '') ? null : trim($requestParams['start']);
			$p_limitLength = (trim($requestParams['length']) == '') ? null : trim($requestParams['length']);

			// Saneo todos los parametros
			$p_draw = $this->sanearParametro($p_draw);
			$p_limitStart = $this->sanearParametro($p_limitStart);
			$p_limitLength = $this->sanearParametro($p_limitLength);

			// Realizo la consulta y preparo el resultado
			$resultado['draw'] = $p_draw; // Es un valor entero para control interno del DataTable
			try
			{
				$p_limitStart = Validator::get()->validar($p_limitStart, PATRON_NUMEROS, true, 'L&iacute;mite inicial inv&aacute;lido.');
				$p_limitLength = Validator::get()->validar($p_limitLength, PATRON_NUMEROS, true, 'L&iacute;mite final inv&aacute;lido.');

				$firmas = NG::firmasExpedienteElec()->obtenerFirmasPendientesParaSupervisores(
					$this->obtenerUsuarioActual(),
					// Control de consulta
					['T.`fecha_hora_entrada` ASC'], // La firma pendiente mas vieja primero
					$p_limitLength, // cantidad de registros (paginación)
					$p_limitStart); // corrimiento de registros (paginación)

				// Consulta de cantidad de Firmas pendientes
				$cantidadTotalFirmas = NG::firmasExpedienteElec()->obtenerFirmasPendientesParaSupervisoresCantidad(
					$this->obtenerUsuarioActual()
				);

				// preparo el resultado (como no uso el filtro de datatables, Total y Filtered son iguales)
				$resultado['recordsTotal'] = $cantidadTotalFirmas;
				$resultado['recordsFiltered'] = $cantidadTotalFirmas;
				$resultado['data'] = $firmas;
			}
			catch (Exception $ex)
			{
				$resultado['recordsTotal'] = 0;
				$resultado['recordsFiltered'] = 0;
				$resultado['data'] = array();  // Es un array vacio!!! No NULL.
				$resultado['error'] = $ex->getMessage();
				$resultado['numeroError'] = ERROR_CONTROLLER_GENERICO;
			}
		}
		echo JsonHelper::get()->serializar($resultado);
	}

	public function enviarrecordatorio($requestParams)
	{
		// Antes que nada verifico el nivel de acceso.
		$this->verificarNivelAccesoUsuario(__FUNCTION__);

		try {
			// Preparo los parámetros de la vista
			$paramVista = $this->generarParametrosVista();

			// Saneo parametros
			$requestParams = $this->sanearConjuntoParametros($requestParams);

			// Validación de parámetros de búsqueda
			$f_anio = Validator::get()->validar($requestParams['f_anio'], PATRON_NUMEROS, false, 'A&ntilde;o del expediente');
			$f_tipo = Validator::get()->validar($requestParams['f_tipo'], PATRON_TIPO_EXPEDIENTE, false, 'Tipo de expediente');
			$f_numero = Validator::get()->validar($requestParams['f_numero'], PATRON_NUMEROS, false, 'N&uacute;mero de expediente');
			$f_cuerpo = Validator::get()->validar($requestParams['f_cuerpo'], PATRON_NUMEROS, false, 'Cuerpo de expediente');
			$f_alcance = Validator::get()->validar($requestParams['f_alcance'], PATRON_NUMEROS, false, 'Alcance de expediente');
			$f_orden = Validator::get()->validar($requestParams['f_orden'], PATRON_NUMEROS, false, 'Orden de la entrada del documento electrónico');

			$f_mail_usuario_solicitante = Validator::get()->validar($requestParams['f_mail_usuario_solicitante'], PATRON_USUARIO, false, 'Mail del usuario solicitante de la firma');

			$f_nombre_usuario_solicitante = Validator::get()->validar($requestParams['f_nombre_usuario_solicitante'], PATRON_SEGURO_SIGNOS, false, 'Nombre del usuario solicitante de la firma');

			$f_mail_usuario = Validator::get()->validar($requestParams['f_mail_usuario'], PATRON_USUARIO, false, 'Mail del usuario firmante');

			// Se busca el documento del expediente electrónico
			$expe_elec = NG::expedientesElec()->obtenerExpedienteElec($f_anio, $f_tipo, $f_numero, $f_cuerpo, $f_alcance, $f_orden);

			// Si existe
			if ($expe_elec) {
				// Se prepara el cuerpo del mensaje
				$body = sprintf('<p>Por medio del presente se hace recordar que se encuentra <strong>Pendiente de Firma</strong> el documento <strong>%s</strong>, a través del Sistema de Gestión Legislativa.</p>',
					$expe_elec->obtenerEtiqueta()
				);

				// Envío del correo electrónico
				try {
					$destinatarios = [];
					if (!is_null($f_mail_usuario))
						$destinatarios[] = $f_mail_usuario;

					MailHelper::get()->sendMail([
			           'sender' => [
			                'reply' => ($f_mail_usuario_solicitante) ?? '',
			                'reply_name' => $f_nombre_usuario_solicitante
			            ],
			            'recipients' => ['address' => $destinatarios],
			            'message' => [
			            	'subject' => sprintf('[Recordatorio Firma Pendiente] %s', $expe_elec->obtenerEtiqueta()),
			                'body' => $body,
			                'body_alt' => strip_tags($body),
			            ]
			        ]);

					$paramVista['mensaje_ok'] = sprintf('Recordatorio enviado a <a href="mailto:%1$s">%1$s</a> con &eacute;xito.', $f_mail_usuario);

					// Instancio la vista y la muestro
					$vista = new BEFirmasPendientesParaSupervisorView($paramVista);
					$vista->vistaPendientesFirma();

				} catch (Exception $e) {
					// Si falla, me vuelvo al home
					SessionController::get()->guardarError(
						sprintf('Error al enviar correo electrónico: %s', $e->getMessage()),
						ERROR_CONTROLLER_GENERICO
					);
					$this->redireccionar('firmaspendientessupervisor', 'view');
				}
			} else {
				// Caso Especial: ERROR... los parametros pasados al controlador son de un documento que no existe en la DB
				SessionController::get()->guardarError('El documento no existe! [2]', ERROR_CONTROLLER_GENERICO);
				$this->redireccionar('firmaspendientessupervisor', 'view');
			}
		} catch (Exception $ex) {
			// Si falla, me vuelvo al home
			SessionController::get()->guardarError($ex->getMessage(), ERROR_CONTROLLER_GENERICO);
			$this->redireccionar('firmaspendientessupervisor', 'view');
		}
	}
}
?>
