<?php
/**
 * Capa de negocio de Firmas Digitales para SGLv2.
 *
 * @author XXXX
 *
 */
class NGFirmas extends NGBaseClass {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	public $debug_mode = false;

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct() {
		parent::__construct();
	}

	// ************************************************************************
	// Firma Digital
	// ************************************************************************

	/**
	 * Genera un nombre de archivo PDF 'seguro', sin caracteres raros ni espacios.
	 * @param  [string] $nombreArchivo Nombre del archivo.
	 * @return [type]          [description]
	 */
	public function generarNombreArchivoPDF($nombreArchivo)
	{
		$aux = strtolower($nombreArchivo);
		$aux = preg_replace('/\.pdf$/i', '', $aux);
		$aux = preg_replace('/\s+/', '_', $aux);
		$aux = preg_replace('/[^a-z0-9_]/i', '', $aux);
		$aux = preg_replace('/__+/', '_', $aux);

		return $aux . '.pdf';
	}

	/**
	 * Obtiene la información correspondiente a la firma digital de un documento PDF.
	 * @param  [string] $nombreArchivo Nombre del archivo.
	 * @return [type]                [description]
	 */
	public function obtenerFirmasPDF($nombreArchivo)
	{
		return PDFSigner::get()->getPdfSignatureData($nombreArchivo);
	}

	/**
	 * Obtiene la cantidad de firmas en el documento realizadas con el certificado
	 * de aplicación del SGL.
	 * @param  [type] $documento [description]
	 * @return [type]            [description]
	 */
	public function obtenerCantidadFirmasSGL($documento) {
		// Obtengo las firmas y cuento las pertenecientes al SGL
		$signature_data = $this->obtenerFirmasPDF($documento);

		$cant = 0;

		foreach ($signature_data as $cadena) {
			foreach ($cadena as $c) {
				if ($c['subject']['CN'] == SGL_APPLICATION_CERT_CN) {
					$cant++;
					// Para evitar seguir verificando elementos de la misma cadena
					// de certificados, si encuentro un match, salgo de esta
					// iteración (de la cadena).
					break;
				}
			}
		}

		return $cant;
	}

	/**
	 * NGFirmas: firma un documento con credenciales de aplicacion, agregando la marca al agua
	 * de la misma o del usuario especificado, y guarda la salida el destino.
	 * @param  String  $doc_origen  Ruta completa del archivo origen (a firmar).
	 * @param  String  $doc_destino Ruta completa del archivo destino (a firmar).
	 * @param  Usuario $pUsuario    Instancia de usuario que firma.
	 * @param  integer $permisos    Permisos del archivo de salida.
	 * @return Array                Lista de errores
	 */
	public function firmarPDF($doc_origen, $doc_destino, Usuario $pUsuario = null, $permisos = 0664)
	{
		$path_destino = dirname($doc_destino);
		$file_destino = basename($doc_destino);

		// Obtengo el certificado de aplicacion y su contraseña
		$cert_app = SGL_APPLICATION_CERT_FILE;
		$cert_app_passw = SGL_APPLICATION_CERT_PASSWORD;

		// --------- Verifico origen y destino
		if (!file_exists($doc_origen))
			return [sprintf("El archivo origen '%s' no existe.", $doc_origen)];

		// Los pdf versión 1.2 y menores no soportan firma multiple.
		if (PDFSigner::get()->getPdfVersion($doc_origen) < '1.3')
			return ["El archivo PDF seleccionado es obsoleto: debe ser de versión igual o mayor a v1.3"];

		if (!is_dir($path_destino))
			return [sprintf("El directorio destino '%s' no existe.", $path_destino)];

		// Verifico certificado de aplicacion
		if (!file_exists($cert_app))
			return ["El certificado de aplicacion no existe."];

		// 2023-02-28
		// Si el usuario es nulo
		if (is_null($pUsuario)) {
			// El que firma es el Sistema
			$firmante_nombre = 'Sistema de Gestión Legislativa';
		}
		else // Si el usuario no es nulo, lo valida
		{
			// Si el usuario no tiene asignado su legajo, no es un funcionario con posibilidad de firma
			if ( is_null($pUsuario->u_legajo) )
				return [sprintf("El usuario '%s' (%s) no puede firmar documentos digitalmente.", $pUsuario->nombre_usuario, $pUsuario->codigo_usuario)];

			$firmante_nombre = $pUsuario->nombre_usuario;
		}

		// Obtengo la cantidad de firmas de SGL del documento origen
		// No hace falta sumar 1 porque el indice arranca en 0.
		$nro_firma = $this->obtenerCantidadFirmasSGL($doc_origen);

		// Configuro el firmador
		PDFSigner::get()->setOptions([
		    'ksType' => 'PKCS12',
		    'ksFile' => $cert_app,
		    'ksPassword' => $cert_app_passw,
		    'keyIndex' => 0,
		    'outSuffix' => '_firmado',
		    'outPath' => $path_destino,
		    'signerName' => $firmante_nombre,
		    'doAppendSignature' => true,
		    'doWatermark' => true,
		    'l2Text' => sprintf('DOCUMENTO FIRMADO DIGITALMENTE%1$sSignatario: ${signer}%1$sDesde: %2$s%1$sFecha: ${timestamp}', "\n", KRAKEN_VERSION_TAG),
		    'signatureNumber' => $nro_firma
		]);

		//Logger::getinstance()->Log("doc_origen", $doc_origen, true);

		// Firmo los archivos seleccionados
		$output = PDFSigner::get()->sign($doc_origen);
		//Logger::getinstance()->Log("output", $output, true);

		$tmp_salida = PDFSigner::get()->getLastOutput();
		//Logger::getinstance()->Log("tmp_salida", $tmp_salida, true);

		if (!file_exists($tmp_salida))
			return [sprintf("Error al obtener el documento a firmar '%s'.", $doc_origen)];

		// Muevo la salida al destino
		FTPHelper::get()->connect('localhost', FTP_LOCAL_USER, FTP_LOCAL_PASSWORD);
		FTPHelper::get()->moveFile($tmp_salida, $doc_destino, $permisos);
		FTPHelper::get()->disconnect();

		// Verifico que exista la salida
		if (!file_exists($doc_destino))
			return [sprintf("Error al firmar el documento '%s'.", $doc_destino)];

		// Debug data
		if ($this->debug_mode) {
			Logger::getinstance()->Log("firmador_output", join($output, '\n----\n'), true);
			Logger::getinstance()->Log("firmador_cmd", PDFSigner::get()->getLastCmd(), true);
		}

		return [];
	}

}
