<?php
/**
 * PDFSigner
 *
 * Clase que permite la firma digital con certificado de un archivo PDF.
 *
 * Requiere:
 *    JSignPdf (http://jsignpdf.sourceforge.net/): firmador de PDFs.
 *    OpenSSL (https://www.openssl.org/): Librería criptográfica y toolkit SSL/TLS.
 *
 * Opcionales:
 *    File (file): Para determinar la versión de PDF de un archivo.
 *    GhostScript (gs): Para convertir un archivo a diferentes versiones de PDF.
 *
 * @author XXXX
 */

// ---- Clase PDFSigner -------------------------------------------------------
class PDFSigner
{
	private static $instance;
    private $defaultOptions;      //!< Default option array values.
    private $ksType;              //!< keystore-type: por defecto 'PKCS12'
    private $ksFile;              //!< keystore-file: archivo .p12 con el keystore usado para firmar, por defecto 'cert.p12'
    private $ksPassword;          //!< keystore-password: contraseña del archivo .p12
    private $keyIndex;            //!< key-index: indice del certificado a utilizar (dentro del archivo .p12); por defecto es '0' (el primero)
    private $outSuffix;           //!< out-suffix: sufijo del archivo firmado, por defecto '_firmado'
    private $outPath;             //!< out-directory: directorio destino del archivo firmado, por defecto './'
    private $signerName;          //!< signer-name: nombre de fantasía del firmante para la marca al agua.
    private $watermarkImage;      //!< img-path: ruta de la imagen que acompañará la marca al agua.
    private $doAppendSignature;   //!< append: agregar una firma a la cadena de firmas, por defecto 'false'
    private $doWatermark;         //!< visible-signature: hacer visible la marca al agua, por defecto 'true'
    private $signatureNumber;     //!< "Número de Firma", utilizado para determinar la posición de la marca al agua.

    private $lastCmd;             //!< Ultima línea de comandos ejecutada para el firmador.
    private $lastInput;           //!< Últimos archivo de entrada procesado.
    private $lastOutput;          //!< Últimos archivo de salida procesado.

	/**
	 * Constructor privado, parte funcional del patron Singleton.
	 */
    private function __construct()
    {
        $this->defaultOptions = [
            'ksType' => 'PKCS12',
            'ksFile' => 'cert.p12',
            'ksPassword' => '',
            'keyIndex' => 0,
            'outSuffix' => '_firmado',
            'outPath' => './',
            'signerName' => '',
            'watermarkImage' => '',
            'doAppendSignature' => false,
            'doWatermark' => true,
            'l2Text' => '',
            'signatureNumber' => 0
        ];

        $this->lastCmd = '';
        $this->lastInput = '';
        $this->lastOutput = '';
    }

	/**
     * Se implementa el patrón Singleton para mantener una única instancia y poder acceder a sus
     * valores desde cualquier script.
     * @return Logger Instancia de la clase.
     */
    public static function GetInstance()
    {
        // Si la instancia no esta definida la creo, sino devuelvo la existente
        if (!isset(self::$instance))
        {
            $claseActual = __CLASS__;			// Obtengo la clase actual
            self::$instance = new $claseActual; // Creo una instancia
        }

		// Devuelvo la instancia existente.
        return self::$instance;
    }

    /**
     * Alias de GetInstance()
     * @return Logger Instancia de la clase.
     */
    public static function get()
    {
        return self::GetInstance();
    }

    /**
     * Es invocado cuando se clona un instancia.
     * Con este método podemos emitir un mensaje de error y proceder a detener la ejecución del
     * script por operación inválida al intentar clonar una instancia de Singleton.
     *
     * E_USER_ERROR: constante que contiene el mensaje de error generado por el usuario
     */
    public function __clone()
    {
		trigger_error("Operación Inválida: No se puede clonar una instancia de ". get_class($this) .".", E_USER_ERROR );
    }

    /**
     * __sleep es invocado cuando un objeto es serializado se evita serializar una instancia de
     * Singleton
     */
    public function __sleep()
    {
        trigger_error("No se puede serializar una instancia de ". get_class($this) .".");
    }

    /**
     * __wakeup es invocado cuando un objeto es deserializado se evita deserializar una instancia
     * de Singleton
     */
    public function __wakeup()
    {
		trigger_error("No se puede deserializar una instancia de ". get_class($this) .".");
    }

    /**
     * Configura el set de opciones del firmador.
     * @param array $options [description]
     */
    public function setOptions($options = [])
    {
        $mop = array_merge($this->defaultOptions, $options);

        foreach ($mop as $k => $v)
            if (array_key_exists($k, $this->defaultOptions))
                $this->{$k} = $v;
    }

    /**
     * Devuelve la versión de un archivo PDF.
     * @param  [type] $filename [description]
     * @return [string]         [description]
     */
    public function getPdfVersion($filename)
    {
    	$version = '';

    	if (file_exists($filename)) {
    		$cmd_output = shell_exec("file -b $filename");
    		$matches = [];
    		preg_match('/[0-9]+\.[0-9]+$/i', $cmd_output, $matches);
    		if (count($matches) > 0)
    			$version = $matches[0];
    	}

    	return $version;
    }

    /**
     * Convierte un archivo PDF una determinada version.
     * @param  [type] $filename [description]
     * @return [type]           [description]
     */
    public function convertToPdf14($filename, $dst_version = '1.4')
    {
    	if ($this->getPdfVersion($filename) != $dst_version) {
    		shell_exec("gs -sDEVICE=pdfwrite -dCompatibilityLevel=$dst_version -dNOPAUSE -dQUIET -dBATCH -sOutputFile=v14-$filename $filename");
    		return sprintf("v%s-%s", str_replace('.', '', $dst_version), $filename);
    	} else
    		return $filename;
    }

    /**
     * Determina cual sería el nombre de archivo de salida para un archivo a procesar.
     * @param  [type] $inputFilename   [description]
     * @param  [type] $outputDirectory [description]
     * @param  [type] $fileSuffix      [description]
     * @return [type]                  [description]
     */
    private function getOutputFilename($inputFilename, $outputDirectory, $fileSuffix)
    {
        $input_parts = pathinfo($inputFilename);
        $outputDirectory = rtrim($outputDirectory, '/') . '/';

        return sprintf('%s%s%s.%s',
            $outputDirectory,
            $input_parts['filename'],
            $fileSuffix,
            $input_parts['extension']
        );
    }

    /**
     * Genera un array con todos los parametros de posicionamiento de la marca al agua
     * de la firma en base a las constantes de configuracion y el número de firma.
     * @param  [type] $signature_number [description]
     * @return [type]                   [description]
     */
    private function getWatermarkPosition($signature_number) {
        $fila = intdiv($signature_number, SGL_WM_CANT_POR_FILA);
        $columna = $signature_number % SGL_WM_CANT_POR_FILA;
        return [
            'x'   => $columna,
            'y'   => $fila,
            'llx' => ($columna * SGL_WM_ANCHO) + SGL_WM_OFFSET_Y,
            'lly' => ($fila * SGL_WM_ALTO) + SGL_WM_OFFSET_X,
            'urx' => ($columna * SGL_WM_ANCHO) + SGL_WM_ANCHO + SGL_WM_OFFSET_Y,
            'ury' => ($fila * SGL_WM_ALTO) + SGL_WM_ALTO + SGL_WM_OFFSET_X
        ];
    }

    /**
     * Firma un documento PDF.
     * @param  [type] $filename [description]
     * @param  array  $options  [description]
     * @return [type]           [description]
     */
    public function sign($filename)
    {
        $render_mode = ($this->watermarkImage == '')
            ? '--render-mode DESCRIPTION_ONLY '
            : '--render-mode GRAPHIC_AND_DESCRIPTION ';

        $wp = $this->getWatermarkPosition($this->signatureNumber);
        $watermark_position = sprintf('--font-size 6.0 -urx %s -ury %s -llx %s -lly %s ',
            $wp['urx'], $wp['ury'], $wp['llx'], $wp['lly']
        );

        $filtered_signerName = str_replace("'", ' ', $this->signerName);

        $cmd = PATH_SGL_FIRMADOR_PDF . ' ';
        $cmd .= "--keystore-type {$this->ksType} ";
        $cmd .= "--keystore-file {$this->ksFile} ";
        $cmd .= (trim($this->ksPassword) != '') ? "--keystore-password {$this->ksPassword} " : '';
        $cmd .= "--key-index {$this->keyIndex} ";
        $cmd .= "--out-suffix {$this->outSuffix} ";
        $cmd .= "--out-directory {$this->outPath} ";
        $cmd .= (trim($filtered_signerName) != '') ? "--signer-name '{$filtered_signerName}' " : '';
        $cmd .= (trim($this->l2Text) != '') ? "--l2-text '{$this->l2Text}' " : '';
        $cmd .= ($this->doAppendSignature) ? '--append ' : '';
        $cmd .= ($this->doWatermark) ? '--visible-signature ' : '';
        $cmd .= ($this->doWatermark) ? $render_mode : '';
        $cmd .= ($this->doWatermark) ? '--page -1 ' : '';
        $cmd .= ($this->doWatermark && trim($this->watermarkImage) != '') ? "--img-path {$this->watermarkImage} " : '';
        $cmd .= ($this->doWatermark) ? $watermark_position : '';
        $cmd .= $filename;

        $this->lastCmd = $cmd;
        $this->lastInput = $filename;
        $this->lastOutput = $this->getOutputFilename($filename, $this->outPath, $this->outSuffix);

        // ---- Fix para evitar que los acentos salgan rotos ------------------
        //$locale = 'en_US.utf-8';
        $locale = 'es_AR.UTF-8';
        setlocale(LC_ALL, $locale);
        putenv('LC_ALL='.$locale);
        // --------------------------------------------------------------------

        $output = shell_exec("( $cmd ) 2>&1");

        return $output;
    }

    /**
     * Obtiene la información en crudo de las firmas digitales aplicadas a un PDF.
     * @param  [string] $filename Nombre del archivo.
     * @return [array]            Información de las firmas.
     */
    public function getPdfSignatureRawData($filename)
    {
        // Las 'subexpresiones' (\d+) se utilizan en la expresión regular para capturar
        // los valores del ByteRange[n1 n2 n3 n4]. Estos valores son de a pares, es
        // decir:
        //   n1 n2: offset y longitud del prefijo del hash de la firma digital.
        //   n3 n4: offset y longitud del sufijo del hash de la firma digital.
        //
        // Sabemos que entre el prefijo y el sufijo del hash de la firma se encuentra
        // el certificado (firma) utilizado. Por ende, si tomamos el largo del prefijo
        // y el offset del sufijo, tenemos el rango donde se encuentra el certificado
        // digital. Cabe destacar que el certificado se entuentra como info binaria,
        // encerrado entre los caracteres '<' y '>' (los cuales serán excluidos del
        // certificado extraído).
        //
        // Referencia: https://www.adobe.com/devnet-docs/acrobatetk/tools/DigSig/Acrobat_DigitalSignatures_in_PDF.pdf

        if (!file_exists($filename))
            throw new Exception(sprintf("getSignatureData: el archivo '%s' no existe.", $filename));

        $sig_data = [];
        preg_match_all(
            '/ByteRange\s*\[\s*(\d+)\s*(\d+)\s*(\d+)\s*(\d+)\s*\]/',
            file_get_contents($filename),
            $sig_data
        );

        // $sig_data[0] -> Contiene los resultados de todas las firmas encontradas.
        // $sig_data[2] -> Contiene los valores 'n2' (longitud del prefijo) de todas las firmas encontradas.
        // $sig_data[3] -> Contiene los valores 'n3' (offset del sufijo) de todas las firmas encontradas.

        if (! (isset($sig_data) && count($sig_data) > 0)) return [];
        if (! (isset($sig_data[0]) && count($sig_data[0]) > 0)) return [];
        if (! (isset($sig_data[2]) && count($sig_data[2]) > 0)) return [];
        if (! (isset($sig_data[3]) && count($sig_data[3]) > 0)) return [];

        $ret = [];

        // Recorro todas las firmas
        for ($idx = 0; $idx < count($sig_data[0]); $idx++) {
            $start = $sig_data[2][$idx]; // longitud del prefijo (n2)
            $end = $sig_data[3][$idx];   // offset del sufijo (n3)

            if ($stream = fopen($filename, 'rb')) {
                // Se hacen ajustes para excluír los caracteres '<' y '>'
                $signature = stream_get_contents($stream, $end - $start - 2, $start + 1);
                fclose($stream);
            }

            // Guardo temporalmente la firma para decodificarla con openssl.
            try {
                $tmpfile = tempnam('/tmp', 'signature_');
                file_put_contents($tmpfile, hex2bin($signature));
                $cmd = "openssl pkcs7 -in {$tmpfile} -inform DER -print_certs";
                $this->lastCmd = $cmd;
                $output = shell_exec("( $cmd ) 2>&1");

                // TODO: validar $output...
                $ret[] = $output;
            } catch (Exception $e) {

            } finally {
                if (file_exists($tmpfile)) unlink($tmpfile);
            }
        }

        return $ret;
    }

    /**
     * [getPDFSignatureData description]
     * @param  [type] $filename [description]
     * @return [type]           [description]
     */
    public function getPdfSignatureData($filename)
    {
        $signature_raw_data = $this->getPdfSignatureRawData($filename);
        $ret = [];

        foreach ($signature_raw_data as $raw_data) {
            $certdata = [];

            $filtered = preg_replace('/-----BEGIN CERTIFICATE-----(.*?)-----END CERTIFICATE-----/su', '###ENDSIGNATURE###', $raw_data);
            $filtered = preg_replace('/^[ \t]*[\r\n]+/m', '', $filtered);
            $filtered = preg_replace('/###ENDSIGNATURE###[\r\n]+$/', '', $filtered);

            $signatures = preg_split('/^###ENDSIGNATURE###$/m', $filtered);


            foreach ($signatures as $raw_str) {
                $s_data = [];

                // Obtengo los datos del certificante
                $cert_raw_info = [];
                preg_match('/^issuer\s*=(.*)$/m', $raw_str, $cert_raw_info);
                if (isset($cert_raw_info) && isset($cert_raw_info[1])) {
                    $data = preg_split('/(,)(?=(?:[^"]|"[^"]*")*$)/', $cert_raw_info[1]);
                    foreach ($data as $keyval) {
                        $d = preg_split('/\s*=\s*/', $keyval);
                        if (isset($d[0]) && isset($d[1]))
                            $s_data['issuer'][trim($d[0])] = trim($d[1]);
                    }
                }

                // Obtengo los datos del firmante
                $subject_raw_info = [];
                preg_match('/^subject\s*=(.*)$/m', $raw_str, $subject_raw_info);
                if (isset($subject_raw_info) && isset($subject_raw_info[1])) {
                    $data = preg_split('/(,)(?=(?:[^"]|"[^"]*")*$)/', $subject_raw_info[1]);
                    foreach ($data as $keyval) {
                        $d = preg_split('/\s*=\s*/', $keyval);
                        if (isset($d[0]) && isset($d[1]))
                            $s_data['subject'][trim($d[0])] = trim($d[1]);
                    }
                }

                // Anexo una firma de la cadena de certificados
                $certdata[] = $s_data;
            }

            // Anexo a los resultados totales
            $ret[] = $certdata;
        }

        return $ret;
    }

    /**
     * Obtiene la ultima instrucción de línea de comandos ejecutada para firma.
     * @return [type] [description]
     */
    public function getLastCmd()
    {
        return $this->lastCmd;
    }

    /**
     * Obtiene el último nombre de archivo procesado como entrada.
     * @return [type] [description]
     */
    public function getLastInput()
    {
        return $this->lastInput;
    }

    /**
     * Obtiene el último nombre de archivo procesado como salida.
     * @return [type] [description]
     */
    public function getLastOutput()
    {
        return $this->lastOutput;
    }

}
?>
