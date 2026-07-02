<?php
/**
 * Capa de negocio base para funcionalidad de PasoActuacionConfirmarFirmaLote.
 *
 * @author XXXX
 *
 */
class NGPasoActuacionConfirmarFirmaLote extends NGPasoActuacionBase {

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
		switch ($paso->opciones['lote_a_firmar']['tipo_lote']) {
			case 'directo':
				// Obtengo el url_base del archivo a firmar y el resto de la ruta (para el URL)
				// ------ AUN NO IMPLEMENTADO
				$paso->datos['lote_documentos'] = [];
				break;

			case 'desde_paso':
				// ---- Obtengo el nombre del archivo del preview
				$transac = NG::transacActuaciones()->obtenerTransacActuacion(
					$paso->id_transaccion,
					$paso->opciones['lote_a_firmar']['id_paso']
				);

				$data = json_decode($transac->data, true); // Datos de la transaccion

				// Obtengo lote de archivos subidos para firmar, extrayendo los nombres
				// de los archivos y guardandolos en una lista que se mostrará
				// en la interfase (lista de links).
				$archivos_a_firmar = $data[$paso->opciones['lote_a_firmar']['parametro_paso']];
				$links = [];
				foreach ($archivos_a_firmar as $a) {
					// Solo tengo el url base de los temporales; ademas tengo que extraer
					// el subdirectorio de cada archivo
					$subdir = basename(pathinfo($a, PATHINFO_DIRNAME));
					$links[] = [
						'es_embebido' => PDFExtractor::get()->hasAttachments($a),
						'link' => sprintf('%s%s/%s',
							$paso->opciones['lote_a_firmar']['url_base_directorio_a_firmar'],
							$subdir,
							basename($a)
						)
					];
				}

				$paso->datos['lote_documentos'] = $links;
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
		$errores = $this->verificarExistenciaParametros(['f_op_confirmacion'], $params);
		if (count($errores) > 0) return $errores;

		// Si la firma es obligatoria, f_op_confirmacion debe ser 1
		if ($paso->opciones['firma_obligatoria']) {
			if ($params['f_op_confirmacion'] != 1)
				$errores[] = 'Debe firmar el lote de documentos para poder continuar.';
		} else {
			if (!in_array($params['f_op_confirmacion'], [0, 1]))
				$errores[] = 'Debe definir si desea firmar o no el lote de documentos.';
		}

		return $errores;
	}
}
?>
