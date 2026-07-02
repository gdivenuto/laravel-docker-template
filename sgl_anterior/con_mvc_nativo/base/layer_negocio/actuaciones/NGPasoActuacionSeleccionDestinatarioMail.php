<?php
/**
 * Capa de negocio base para funcionalidad de PasoActuacionSeleccionDestinatarioMail.
 *
 * @author XXXX
 *
 */
class NGPasoActuacionSeleccionDestinatarioMail extends NGPasoActuacionBase {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct() {
		parent::__construct();
	}

	// ------------------------------------------------------------------------
	// ---- Asignacion de datos de Pasos --------------------------------------
	// ------------------------------------------------------------------------
	/**
	 * Delega la lógica de obtencion de datos para que un paso determinado
	 * disponga de todo lo necesario para generar su vista, por ejemplo, una consulta
	 * a la BD con los posibles firmantes.
	 * @param  Actuacion     $actuacion [description]
	 * @param  PasoActuacion $paso   [description]
	 * @param  Usuario       $pUsuario  [description]
	 */
	public function asignarDatosAPasoActuacion(Actuacion $actuacion, PasoActuacion $paso, Usuario $pUsuario)
	{
		// Obtengo los usuarios/personal notificables: se seleccionan aquellos usuarios
		// activos que posean una direccion de mail o bien agentes de planta que posean
		// una direccion de mail y esten 'disponibles' (no estan de baja).
		$paso->datos['destinatarios'] = array_map(function ($d) {
				return ['mail' => $d->mail, 'nombre_completo' => $d->nombre_completo];
			},
			NG::seguridad()->obtenerEMailsNotificables()
		);
	}

	// ------------------------------------------------------------------------
	// ---- Procesamiento de Pasos --------------------------------------------
	// ------------------------------------------------------------------------

	/**
	 * Toma un paso en particular y ejecutar su funcion de validacion y
	 * procesamiento (guardar transaccion) en base a los parametros recopilados.
	 * @param  Actuacion     $actuacion [description]
	 * @param  PasoActuacion $paso   [description]
	 * @param  Usuario       $pUsuario  [description]
	 * @param  Array         $params Parámetros del paso, por referencia (esto permite modificar los parametros desde el procesamiento del paso).
	 * @return Array                 Array de errores detectados; si es '[]', no hay errores.
	 */
	public function procesarPasoActuacion(Actuacion $actuacion, PasoActuacion $paso, Usuario $pUsuario, &$params)
	{
		$ops = $paso->opciones;

		// Verifico la existencia obligatoria de los parametros
		$errores = $this->verificarExistenciaParametros(['f_destinatarios'], $params);
		if (count($errores) > 0) return $errores;

		// Obtengo los destinatarios (con un fix para destinatarios vacios)
		if (!is_array($params['f_destinatarios']))
			$params['f_destinatarios'] = []; // actualizo los parámetros del paso (que llevará a actualizar la transacción)

		$destinatarios = $params['f_destinatarios'];

	    // Verifico cantidad mínima/máxima de destinatarios
	    if (count($destinatarios) < $ops['cantidad_minima'])
	        $errores[] = sprintf('Debe elegir al menos %s destinatario(s).', $ops['cantidad_minima']);

	    if (($ops['cantidad_maxima'] >= 0) && (count($destinatarios) > $ops['cantidad_maxima']))
	        $errores[] = sprintf('Debe elegir no más de %s destinatario(s).', $ops['cantidad_maxima']);

	    // Verifico que todos los destinatarios son direcciones de correo validas
	    foreach ($destinatarios as $d) {
	    	if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i', $d))
	    		$errores[] = sprintf('%s no es una dirección de correo electrónico válida.', $d);
	    }

		return $errores;
	}
}
?>
