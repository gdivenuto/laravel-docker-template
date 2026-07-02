<?php
/**
 * Capa de negocio base para funcionalidad de PasoActuacionEditorTexto.
 *
 * @author XXXX
 *
 */
class NGPasoActuacionEditorTexto extends NGPasoActuacionBase {

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
		parent::asignarDatosAPasoActuacion($actuacion, $paso, $pUsuario);
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
		$errores = $this->verificarExistenciaParametros(['f_titulo', 'f_texto'], $params);
		if (count($errores) > 0) return $errores;

		// Solo confirmo el titulo si esta permitido y es obligatorio...
	    if ($ops['titulo_permitido'] && $ops['titulo_obligatorio']) {
    	    if (trim($params['f_titulo']) == '') {
        	    $errores[] = 'Debe ingresar un título.';
    	    } else {
	            // Verifico cantidad minima de caracteres (-1 deshabilita el control)
	            if (($ops['titulo_longitud_min'] >= 0) && (strlen($params['f_titulo']) < $ops['titulo_longitud_min']))
	                $errores[] = sprintf('Debe ingresar un título de al menos %d caracteres.', $ops['titulo_longitud_min']);

	            // Verifico cantidad maxima de caracteres (-1 deshabilita el control)
	            if (($ops['titulo_longitud_max'] >= 0) && (strlen($params['f_titulo']) > $ops['titulo_longitud_max']))
	                $errores[] = sprintf('Debe ingresar un título con menos de %d caracteres.', $ops['titulo_longitud_max']);
        	}
	    }

	    // Solo confirmo el texto cuando es obligatorio
	    if ($ops['texto_obligatorio']) {
	        if (trim($params['f_texto']) == '') {
	            $errores[] = 'Debe ingresar un texto.';
	        } else {
	            // Verifico cantidad minima de caracteres (-1 deshabilita el control)
	            if (($ops['texto_longitud_min'] >= 0) && (strlen($params['f_texto']) < $ops['texto_longitud_min']))
	                $errores[] = sprintf('Debe ingresar un texto de al menos %s caracteres.', $ops['texto_longitud_min']);

	            // Verifico cantidad maxima de caracteres (-1 deshabilita el control)
	            if (($ops['texto_longitud_max'] >= 0) && (strlen($params['f_texto']) > $ops['texto_longitud_max']))
	                $errores[] = sprintf('Debe ingresar un texto con menos de %s caracteres.', $ops['texto_longitud_max']);
	        }
	    }

	    // Devolucion parcial de errores
	    if (count($errores) > 0) return $errores;

	    // Verifico si necesito generar el archivo de salida en base al texto ingresado
	    if ($ops['generar_archivo_pdf']['generar_archivo'])
	    {
	    	if (!is_dir($ops['generar_archivo_pdf']['ruta_archivo'])) {
	    		$errores[] = sprintf("La ruta de salida '%s' no existe", $ops['generar_archivo_pdf']['ruta_archivo']);
	    	} else {
	    		$contenido = $params['f_texto'];
				$nombre_archivo_nuevo = sprintf('%s%d_%d_%s_%s.pdf',
					$ops['generar_archivo_pdf']['ruta_archivo'],
					$paso->id_transaccion,
					$paso->id_paso,
					DateTimeHelper::get()->timestampStr('YmdHisu'),
					sha1($contenido)
				);
	    		PDFComposer::get()->setOptions([
	    			'title' => ($ops['titulo_permitido']) ? trim($params['f_titulo']) : ''
	    		]);
				$errores = PDFComposer::get()->generarPDF($contenido, $nombre_archivo_nuevo);

				if (count($errores) == 0) {
					$archivo_salida = PDFComposer::get()->getLastOutput();
					if (!file_exists($archivo_salida))
						$errores[] = 'Error al generar el archivo pdf.';

					// Guardo el archivo generado en la transaccion
					$params['archivo_generado'] = $archivo_salida;
				}
	    	}
	    }

		return $errores;
	}
}
?>
