<?php
/**
 * Capa de negocio base para funcionalidad de PasoActuacionSeleccionRevisor.
 *
 * @author XXXX
 *
 */
class NGPasoActuacionSeleccionRevisor extends NGPasoActuacionBase {

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
	 * a la BD con los posibles revisores.
	 * @param  Actuacion     $actuacion [description]
	 * @param  PasoActuacion $paso   [description]
	 * @param  Usuario       $pUsuario  [description]
	 */
	public function asignarDatosAPasoActuacion(Actuacion $actuacion, PasoActuacion $paso, Usuario $pUsuario)
	{
		// Obtengo los usuarios revisores: Tentativamente, solamente pueden
		// revisar los usuarios que son parte de la planta de personal, y
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

		$revisores = [];

		// TODO: agregar datos de la dependencia

		// Aplico lógica de exclusión al usuario actual
		if ($paso->opciones['excluir_usuario_actual']) {
			foreach ($data as $d)
				if ($d->id_usuario != $pUsuario->id_usuario)
					$revisores[] = [
						'id_usuario' => $d->id_usuario,
						'nombre' => trim($d->nombre_usuario)
					];
		} else {
			foreach ($data as $d)
				$revisores[] = [
					'id_usuario' => $d->id_usuario,
					'nombre' => trim($d->nombre_usuario)
				];
		}

		$paso->datos['revisores'] = $revisores;
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
		$errores = $this->verificarExistenciaParametros(['f_revisores'], $params);
		if (count($errores) > 0) return $errores;

		// Fix para el campo requiere_revision: como es un checkbox, si no está tildado no llega
		if (array_key_exists('f_requiere_revision', $params)) {
			if (! in_array($params['f_requiere_revision'], [0, 1]) )
				return ["Parámetro f_requiere_revision inválido"];
		} else {
			$params['f_requiere_revision'] = 0;
		}

		// Obtengo los revisores (con un fix para revisores vacios)
		if (!is_array($params['f_revisores']))
			$params['f_revisores'] = []; // actualizo los parámetros del paso (que llevará a actualizar la transacción)

		$revisores = $params['f_revisores'];

	    // Verifico cantidad mínima/máxima de revisores solamente cuando
	    // se requiere revision.
	    if ($params['f_requiere_revision'] == 1) {
		    if (count($revisores) < $ops['cantidad_minima'])
		        $errores[] = sprintf('Debe elegir al menos %s revisor(es).', $ops['cantidad_minima']);

		    if (($ops['cantidad_maxima'] >= 0) && (count($revisores) > $ops['cantidad_maxima']))
		        $errores[] = sprintf('Debe elegir no más de %s revisor(es).', $ops['cantidad_maxima']);
	    }

	    // Si tengo revisores, realizo mas verificaciones
	    if (count($revisores) > 0) {
			foreach ($revisores as $k => $f) {
				if (!preg_match('/^[0-9]{1,6}$/i', $f))
		    		return ['Inconsistencia de datos: formato inválido en uno de los identificadores de revisor.'];
			}

			// Obtengo todos los usuarios de la lista de revisores
			if (count($revisores) != NG::seguridad()->obtenerUsuariosCantidad($revisores))
				return ['La cantidad de revisores requeridos difiere de los revisores disponibles'];
	    }

		return $errores;
	}
}
?>
