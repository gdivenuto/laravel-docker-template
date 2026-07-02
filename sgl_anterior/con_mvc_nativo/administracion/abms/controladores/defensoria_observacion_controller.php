<?php
if (!isset($_SESSION))
	session_start();

//Incluye el modelo que corresponde
require_once RUTA_MODELOS . "defensoria_observacion_dmz.php";
require_once RUTA_MODELOS . "defensoria_observacion.php";

//Incluye la vista que corresponde
require_once RUTA_VISTAS . "defensoria_observacion/grilla.php";
require_once RUTA_VISTAS . "defensoria_observacion/pdf_observaciones.php";
//require_once RUTA_VISTAS . "defensoria_observacion/pdf_ficha.php";
require_once RUTA_VISTAS . "defensoria_observacion/documentos.php";
require_once RUTA_VISTAS . "defensoria_observacion/habilitacion.php";

class defensoria_observacion_controller extends ControllerBase {

	protected $modelo_defensoria_observacion_dmz;
	protected $modelo_defensoria_observacion;

	protected $vista_listado_pdf;
	protected $vista_ficha_pdf;
	protected $vista_documentos;
	protected $vista_habilitacion;

	protected $url_formulario;
	protected $mail_idp;

	public function __construct() {

		parent::__construct();

		$this->campo_orden_por_defecto = 'fecha';

		$this->modelo_defensoria_observacion_dmz = new DefensoriaObservacionDmzModel();
		$this->modelo_defensoria_observacion = new DefensoriaObservacionModel();

		$this->vista_grilla = new VistaObservacionDpGrilla();
		$this->vista_listado_pdf = new VistaObservacionDpPDF();
		//$this->vista_ficha_pdf = new VistaObservacionDpFichaPDF();
		$this->vista_documentos = new VistaObservacionDpDocumentos();
		$this->vista_habilitacion = new VistaObservacionDpHabilitacion();

		$this->url_formulario = "https://www.concejomdp.gov.ar/participacion/idp/descargo_idp.php";
		$this->mail_idp = "elecciondefensordelpueblo@concejomdp.gov.ar";
	}
	
	/**
	 * Se listan los registros
	 * @param  string $mensaje
	 * @param  string $tipo_mensaje
	 * @return html
	 */
	public function listar($mensaje = '', $tipo_mensaje = '') {

		$filtro = Array();

		// Si se recibe la marca para limpiar, se limpia el filtro en la sesión, sino se mantienen
		$_SESSION['f_defensoria'] = (LibreriaGeneral::recoge('limpiar') == 'si') 
			? '' : $_SESSION['f_defensoria'];

		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		$filtro['f_fecha'] = (isset($f_fecha) && $this->esFechaValida($f_fecha)) 
			? $this->modelo_defensoria_observacion_dmz->formatearFechaMySQL($f_fecha) : '';

		$filtro['f_texto'] = LibreriaGeneral::recoge('f_texto', (isset($_SESSION['f_defensoria']['f_texto']) 
			? $_SESSION['f_defensoria']['f_texto'] : ''));

		$filtro['f_habilitados'] = LibreriaGeneral::recoge('f_habilitados', 0);

		$filtro['f_candidato_id'] = LibreriaGeneral::recoge('candidato_id', 0);

		$_SESSION['f_defensoria'] = $filtro;

		$campo_orden = LibreriaGeneral::recoge('campo_orden');
		if ($campo_orden != '') {
			$filtro['campo_orden'] = $campo_orden;
		} else {
			$filtro['campo_orden'] = $this->campo_orden_por_defecto;
			$_SESSION['ultimo_campo'] = '';
		}

		$filtro['sentido'] = LibreriaGeneral::recoge('sentido');

		if (!isset($_SESSION['ultimo_campo']) || $_SESSION['ultimo_campo'] != $filtro['campo_orden']) {
			// Si es la primera vez que carga la pagina
			// o se esta cambiando el campo por el que se ordena
			$_SESSION['ultimo_campo'] = $filtro['campo_orden'];
			$_SESSION['ultimo_sentido'] = 'asc';
		} else {
			// Si se hizo clic en el mismo que ya estaba ordenado antes, solo hay que cambiar el sentido
			$_SESSION['ultimo_sentido'] = ($_SESSION['ultimo_sentido'] == 'asc' && $filtro['sentido'] == '') 
				? 'desc' : 'asc';
		}

		$filtro['rango'] = $this->rango_paginacion;

		$this->modelo_defensoria_observacion_dmz->setFiltro($filtro);

		$filtro['cantidad'] = $this->modelo_defensoria_observacion_dmz->obtenerCantidad();

		$filtro['nro_paginas'] = ceil($filtro['cantidad'] / $filtro['rango']);

		$filtro['pagina'] = LibreriaGeneral::recoge('pagina');

		if (!$filtro['pagina']) {
			$filtro['pagina'] = ($filtro['nro_paginas'] > 0) ? $filtro['nro_paginas'] : 1;

			if ($filtro['cantidad'] < $filtro['rango'])
				$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];
			else
				$filtro['inicio'] = $filtro['cantidad'] - $filtro['rango'];
		} else
			$filtro['inicio'] = ($filtro['pagina'] * $filtro['rango']) - $filtro['rango'];

		$filtro['pagina_ant'] = $filtro['pagina'] - 1;
		$filtro['pagina_sgte'] = $filtro['pagina'] + 1;

		$this->modelo_defensoria_observacion_dmz->setFiltro($filtro);

		$datos = $this->modelo_defensoria_observacion_dmz->listar();

		$this->vista_grilla->mostrar($datos, $mensaje, $tipo_mensaje, $filtro);
	}

	public function generarInforme() {

		$f_fecha = LibreriaGeneral::recoge('f_fecha');
		$filtro['f_fecha'] = (isset($f_fecha) && $this->esFechaValida($f_fecha)) 
			? $this->modelo_defensoria_observacion_dmz->formatearFechaMySQL($f_fecha) : '';

		$filtro['f_texto'] = LibreriaGeneral::recoge('f_texto', (isset($_SESSION['f_defensoria']['f_texto']) 
			? $_SESSION['f_defensoria']['f_texto'] : ''));

		$filtro['f_habilitados'] = LibreriaGeneral::recoge('f_habilitados', 0);

		$filtro['rango'] = 0;// a propósito para que retorne todos los registros
		
		$this->modelo_defensoria_observacion_dmz->setFiltro($filtro);
		
		$listado = $this->modelo_defensoria_observacion_dmz->listar();
		
		$this->vista_listado_pdf->mostrar($listado);
	}

	public function generarFicha() {

		$id = LibreriaGeneral::recoge('id');

		$registro = $this->modelo_defensoria_observacion_dmz->obtenerRegistro($id);

		$this->vista_ficha_pdf->mostrar($registro);
	}

	public function mostrarDocumentos() {

		$id = LibreriaGeneral::recoge('id', 0);
		$registro = $this->modelo_defensoria_observacion_dmz->obtenerRegistro($id);
		
		$this->vista_documentos->mostrar($registro);
	}

	public function editarHabilitacion() {

		$observacion_id = LibreriaGeneral::recoge('id', 0);
		$pagina = LibreriaGeneral::recoge('pagina');
		$datos = $this->modelo_defensoria_observacion->obtenerRegistro($observacion_id);

		if (! isset($datos))
			$datos['observacion_id'] = $observacion_id;
		
		$this->vista_habilitacion->mostrar($datos, $pagina);
	}

	/**
	 * Se modifica el estado Habilitado|Deshabilitado
	 * del Candidato en la base 'hcd', para evitar la sobreescritura en la sincronización con la DMZ
	 */
	public function modificarEstado() {

		$observacion_id = LibreriaGeneral::recoge('observacion_id', 0);
		$habilitado = LibreriaGeneral::recoge('habilitado');
		$motivo = LibreriaGeneral::recoge('motivo');
		$pagina = LibreriaGeneral::recoge('pagina');

		if ($this->modelo_defensoria_observacion->modificarEstado($observacion_id, $habilitado, $motivo))
			$this->listar($this->mensaje_modificacion_estado_ok, 1, $pagina);
		else
			$this->listar($this->mensaje_modificacion_estado_error, 2, $pagina);
	}

	/**
	 * Versión con HTML
	 */
	public function notificarCandidato() {

		$observacion_id = LibreriaGeneral::recoge('id', 0);
		$pagina = LibreriaGeneral::recoge('pagina');

		$datos = $this->modelo_defensoria_observacion_dmz->obtenerObservacion($observacion_id);

		$asunto = "Observaci&oacute;n de su candidatura a Defensor del Pueblo.";

		$cuerpo  = "<html>\n";
		$cuerpo .= "<body>\n";
		$cuerpo .= "Hola " . $datos['candidato_nombre'] . ".<br>\n";
		$cuerpo .= "Ha recibido una observaci&oacute;n a su candidatura.<br>\n<br>\n";
		$cuerpo .= "<b>Mensaje:</b><br>\n";
		$cuerpo .= "<p>\n" . nl2br($datos['mensaje']) . "\n</p>\n";
		$cuerpo .= "--------------------------------------------------------------\n";
		$cuerpo .= "A continuaci&oacute;n podr&aacute; realizar su descargo, utilizando el siguiente bot&oacute;n:<br>";

		$cuerpo .= "<p style='text-center'>\n";
		$cuerpo .= "<a href='".$this->url_formulario."?id=".$datos['id']."&v=".date("Ymd_His")."'";
		$cuerpo .= " style='color: #fff;text-decoration: none;font-family: Arial,sans-serif;";
		$cuerpo .= "background-color: #339900;font-size: 14px;display: inline-block;padding:5px;'";
		$cuerpo .= " target='_blank'>REALICE SU DESCARGO</a>\n";
		$cuerpo .= "</p>\n";

		$cuerpo .= "<p>Desde ya muchas gracias.</p>\n";
		$cuerpo .= "</html>\n";
		$cuerpo .= "</body>\n";

		$encabezado  = "MIME-Version: 1.0\n";
		$encabezado .= "From: ".$this->mail_idp."\n";
		$encabezado .= "Reply-To: ".$this->mail_idp."\n";
		$encabezado .= "Content-type: text/html\n";

		if (mail($datos['candidato_email'], $asunto, $cuerpo, $encabezado))
			$this->listar("Se ha notificado al candidato para su descargo.", 1, $pagina);
		else
			$this->listar("No se ha podido notificar por mail al candidato.", 2, $pagina);
	}

	/**
	 * Versión sin html
	 */
	public function notificarCandidatoV2() {

		$observacion_id = LibreriaGeneral::recoge('id', 0);
		$pagina = LibreriaGeneral::recoge('pagina', 1);

		$datos = $this->modelo_defensoria_observacion_dmz->obtenerObservacion($observacion_id);
		
		$asunto = "Observación de su candidatura a Defensor del Pueblo.";

		$cuerpo  = "Hola " . $datos['candidato_nombre'].".\n\n";
		$cuerpo .= "Ha recibido una observación a su candidatura a Defensor del Pueblo.\n\n";
		$cuerpo .= "Mensaje:\n";
		$cuerpo .= nl2br($datos['mensaje'])."\n";
		$cuerpo .= "--------------------------------------------------------------------------------------\n\n";
		$cuerpo .= "A continuación podrá realizar su descargo, utilizando el siguiente enlace:\n\n";

		$cuerpo .= $this->url_formulario."?id=".$datos['id']."&v=".date("Ymd_His")."\n\n";
		
		$cuerpo .= "Desde ya muchas gracias.\n";

		LibreriaGeneral::registrarLog('cuerpo', $cuerpo);

		$encabezado  = "MIME-Version: 1.0\n";
		$encabezado .= "From: ".$this->mail_idp."\n";
		$encabezado .= "Reply-To: ".$this->mail_idp."\n";
		$encabezado .= "Content-type: text/html\n";

		if (mail($datos['candidato_email'], $asunto, $cuerpo, $encabezado))
			$this->listar("Se ha notificado al candidato para su descargo.", 1, $pagina);
		else
			$this->listar("No se ha podido notificar por mail al candidato.", 2, $pagina);
	}
}
?>