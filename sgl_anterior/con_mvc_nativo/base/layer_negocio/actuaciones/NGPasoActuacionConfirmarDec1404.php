<?php
/**
 * Capa de negocio base para funcionalidad de PasoActuacionConfirmarDec1404.
 *
 * @author XXXX
 *
 */
class NGPasoActuacionConfirmarDec1404 extends NGPasoActuacionBase {

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
		switch ($paso->opciones['documento_a_confirmar']['tipo_documento']) {
			case 'directo':
				// Obtengo el url_base del archivo y el resto de la ruta (para el URL)
				$paso->datos['archivo_preview'] = sprintf('%s%s',
					$paso->opciones['documento_a_confirmar']['url_base_documento_a_confirmar'],
					$paso->opciones['documento_a_confirmar']['ruta_documento_a_confirmar']
				);
				break;

			case 'desde_paso':
				// ---- Obtengo el nombre del archivo del preview
				$transac = NG::transacActuaciones()->obtenerTransacActuacion(
					$paso->id_transaccion,
					$paso->opciones['documento_a_confirmar']['id_paso']
				);

				$data = json_decode($transac->data, true); // Datos de la transaccion

				// Obtengo el url_base del archivo, y el nombre del archivo a
				// firmar de un paso previo (extrayendolo de la ruta fisica).
				$paso->datos['archivo_preview'] = sprintf('%s%s',
					$paso->opciones['documento_a_confirmar']['url_base_documento_a_confirmar'],
					basename($data[$paso->opciones['documento_a_confirmar']['parametro_paso']])
				);
				break;
		}
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

		$errores = [];
		// Verifico la existencia obligatoria de los parametros
		$errores = $this->verificarExistenciaParametros(['f_op_decreto'], $params);
		if (count($errores) > 0) return $errores;

		if (!in_array($params['f_op_decreto'], [0, 1]))
			$errores[] = 'Debe definir si el documento es alcanzado por el Art. 11 Decreto 1404.';

		return $errores;
	}
}
?>
