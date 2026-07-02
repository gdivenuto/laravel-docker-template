<?php
/**
 * Capa de negocio base para funcionalidad de PasoActuacionSeleccionFirmante.
 *
 * @author XXXX
 *
 */
class NGPasoActuacionSeleccionFirmante extends NGPasoActuacionBase {

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
		switch ($paso->opciones['tipo_firmantes']) {
			case 'todos':
				// Obtengo los usuarios firmantes: Tentativamente, solamente pueden
				// firmar los usuarios que son parte de la planta de personal, y
				// que a su vez estan 'disponibles' (no estan de baja).
				$data = NG::seguridad()->obtenerUsuariosFirmantes(
					null, // $pid_usuario
					null, // $pcodigo_usuario
					null, // $pnombre_usuario
					null, // $piniciales_usuario
					null, // $ppassword_usuario
					true, // $phabilitado_usuario
					null, // $pconfirma_giros
					null, // $pobservaciones_usuario
					null, // $pu_legajo
					// Control de consulta
					['nombre_usuario asc'], // array $pOrdenColumnas
					null, // $pLimiteCantidad
					null // $pLimiteOffset
				);
				break;

			case 'giros':
				// Obtengo los usuarios firmantes: Tentativamente, solamente pueden
				// firmar los usuarios que tienen un cargo de tipo Secretario HCD o tienen el campo confirma_giros activo
				$data = NG::seguridad()->obtenerUsuariosHabilitadosParaGiros(
					null, // $pid_usuario
					null, // $pcodigo_usuario
					null, // $pnombre_usuario
					null, // $piniciales_usuario
					null, // $ppassword_usuario
					true, // $phabilitado_usuario
					null, // $pconfirma_giros
					null, // $pobservaciones_usuario
					null, // $pu_legajo
					// Control de consulta
					['nombre_usuario asc'], // array $pOrdenColumnas
					null, // $pLimiteCantidad
					null // $pLimiteOffset
				);
				break;
		}

		$firmantes = [];

		// TODO: agregar datos de la dependencia

		// Aplico lógica de exclusión al usuario actual
		if ($paso->opciones['excluir_usuario_actual']) {
			foreach ($data as $d)
				if ($d->id_usuario != $pUsuario->id_usuario)
					$firmantes[] = [
						'id_usuario' => $d->id_usuario,
						'nombre' => trim($d->nombre_usuario)
					];
		} else {
			foreach ($data as $d)
				$firmantes[] = [
					'id_usuario' => $d->id_usuario,
					'nombre' => trim($d->nombre_usuario)
				];
		}

		$paso->datos['firmantes'] = $firmantes;
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
		$errores = $this->verificarExistenciaParametros(['f_firmantes'], $params);
		if (count($errores) > 0) return $errores;

		// Obtengo los firmantes (con un fix para firmantes vacios)
		if (!is_array($params['f_firmantes']))
			$params['f_firmantes'] = []; // actualizo los parámetros del paso (que llevará a actualizar la transacción)

		$firmantes = $params['f_firmantes'];

	    // Verifico cantidad mínima/máxima de firmantes
	    if (count($firmantes) < $ops['cantidad_minima'])
	        $errores[] = sprintf('Debe elegir al menos %s firmante(s).', $ops['cantidad_minima']);

	    if (($ops['cantidad_maxima'] >= 0) && (count($firmantes) > $ops['cantidad_maxima']))
	        $errores[] = sprintf('Debe elegir no más de %s firmante(s).', $ops['cantidad_maxima']);

	    // Si tengo firmantes, realizo mas verificaciones
	    if (count($firmantes) > 0) {
			foreach ($firmantes as $k => $f) {
				if (!preg_match('/^[0-9]{1,6}$/i', $f))
		    		return ['Inconsistencia de datos: formato inválido en uno de los identificadores de firmante.'];
			}

			// Obtengo todos los usuarios de la lista de firmantes
			if (count($firmantes) != NG::seguridad()->obtenerUsuariosCantidad($firmantes))
				return ['La cantidad de firmantes requeridos difiere de los firmantes disponibles'];
	    }

		return $errores;
	}
}
?>
