<?php
/**
 * Capa de negocio base para funcionalidad de PasoActuacionRechazarFirma.
 *
 * @author XXXX
 *
 */
class NGPasoActuacionRechazarFirma extends NGPasoActuacionBase {

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
		// Flag para determinar si hay archivos embebidos
		$paso->datos['es_embebido'] = false;

		switch ($paso->opciones['documento_a_rechazar']['tipo_documento']) {
			case 'directo':
				// Obtengo el url_base del archivo a rechazar la firma y el resto de la ruta (para el URL)
				$paso->datos['archivo_preview'] = sprintf('%s%s',
					$paso->opciones['documento_a_rechazar']['url_base_documento_a_rechazar'],
					$paso->opciones['documento_a_rechazar']['ruta_documento_a_rechazar']
				);

				// Si el paso es 'directo', y a su vez la actuación esta asociada a un
				// expediente, me arriesgo a verificar si el archivo a rechazar se encuentra
				// en la carpeta de documentos del expediente electronico para luego ver
				// si es embebido o no.
				// Otra opción seria disponer de la ruta fisica del documento en el paso,
				// pero es información sensible que no debe ir al cliente (la configuracion
				// del paso es visible en el cliente).
				if (count($actuacion->verificarParametrosExpediente()) == 0)
					try {
						$paso->datos['es_embebido'] = PDFExtractor::get()->hasAttachments(
							sprintf('%s%s',
								PATH_KRAKEN_RESOURCES_PROYECTOS,
								$paso->opciones['documento_a_rechazar']['ruta_documento_a_rechazar']
							)
						);
					} catch (Exception $e) {
						// TODO: hacer algo; se rompe con caratulas porque la ruta base cambia
					}

				break;

			case 'desde_paso':
				// ---- Obtengo el nombre del archivo del preview
				$transac = NG::transacActuaciones()->obtenerTransacActuacion(
					$paso->id_transaccion,
					$paso->opciones['documento_a_rechazar']['id_paso']
				);

				$data = json_decode($transac->data, true); // Datos de la transaccion

				$archivo_a_rechazar = $data[$paso->opciones['documento_a_rechazar']['parametro_paso']];

				// Obtengo el url_base del archivo a rechazar la firma, y el nombre del archivo a
				// rechazar la firma de un paso previo (extrayendolo de la ruta fisica).
				$paso->datos['archivo_preview'] = sprintf('%s%s',
					$paso->opciones['documento_a_rechazar']['url_base_documento_a_rechazar'],
					basename($archivo_a_rechazar)
				);

				// Verifico si el archivo tiene embebidos
				$paso->datos['es_embebido'] = PDFExtractor::get()->hasAttachments($archivo_a_rechazar);

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
		if ($paso->opciones['rechazo_obligatorio']) {
			if ($params['f_op_confirmacion'] != 1)
				$errores[] = 'Debe rechazar la solicitud de firma del documento para poder continuar.';
		} else {
			if (!in_array($params['f_op_confirmacion'], [0, 1]))
				$errores[] = 'Debe definir si desea rechazar o no la solicitud de firma del documento.';
		}

		return $errores;
	}
}
?>
