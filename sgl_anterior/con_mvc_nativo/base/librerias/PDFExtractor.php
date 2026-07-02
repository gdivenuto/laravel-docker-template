<?php
/**
 * PDFExtractor
 *
 * Clase que permite la manipulación de archivos embebidos (attachements)
 * de un archivo PDF.
 *
 * Requiere:
 *    pdfdetach (poppler-utils): Herramienta incluída en la librería de renderizado
 *                               de PDFs basada en el visor PDF Xpdf.
 *
 * https://www.xpdfreader.com/pdfdetach-man.html
 *
 * @author XXXX, XXXX
 */

// ---- Clase PDFExtractor ----------------------------------------------------
class PDFExtractor
{
	private static $instance;
    private $defaultOptions;      //!< Default option array values.

    private $lastCmd;             //!< Ultima línea de comandos ejecutada para el firmador.
    private $lastInput;           //!< Último archivo de entrada procesado.
    private $lastOutput;          //!< Último archivo de salida procesado.

	/**
	 * Constructor privado, parte funcional del patron Singleton.
	 */
    private function __construct()
    {
        $this->defaultOptions = [];

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
     * Determina cual sería el nombre de archivo de salida para un archivo a procesar.
     *
     * @param  string $inputFilename   Nombre del archivo a sanear
     * @param  string $outputDirectory Directorio del archivo de salida
     * @param  string $fileSuffix      Sufijo para el nombre de archivo
     * @return string                  Nombre del archivo saneado
     */
    private function getOutputFilename($inputFilename, $outputDirectory, $fileSuffix)
    {
        $input_parts = pathinfo(
            str_replace('%', '_',
                str_replace(' ', '_',
                    str_replace('(', '_',
                        str_replace(')', '_', $inputFilename)
                    )
                )
            )
        );

        $outputDirectory = rtrim($outputDirectory, '/') . '/';

        return sprintf('%s%s%s.%s',
            $outputDirectory,
            $input_parts['filename'],
            $fileSuffix,
            $input_parts['extension']
        );
    }

    /**
     * Firma un documento PDF.
     * @param  [type] $filename [description]
     * @param  array  $options  [description]
     * @return [type]           [description]
     */
    public function getAttachments($filename)
    {
        if (! file_exists($filename))
            throw new Exception(sprintf('%s: El archivo %s no existe.', get_class($this), $filename));

        $cmd = sprintf('pdfdetach -list %s', $filename);

        // ---- Fix para evitar que los acentos salgan rotos ------------------
        //$locale = 'en_US.utf-8';
        $locale = 'es_AR.UTF-8';
        setlocale(LC_ALL, $locale);
        putenv('LC_ALL='.$locale);
        // --------------------------------------------------------------------

        $output = shell_exec("( $cmd ) 2>&1");

        // $output = "1 embedded files\n";
        // $output .= "1: 11752-5-2010c1al1c1.pdf\n";
        // $output .= "2: image-5-xxxxxxxxx.gif\n";
        // $output .= "3: 11752-5-yyyyyyyyy.pdf\n";
        // $output .= "4: excel-5-zzzzzzzzz.xls\n";

        // ---- Capturo resultados --------------------------------------------
        $capturas = [];
        preg_match_all('/^([0-9]+):\s+(.*)$/mi', $output, $capturas, PREG_SET_ORDER);

        $attachments = [
            'pdf' => [],
            'other' => []
        ];

        foreach ($capturas as $c) {
            if (preg_match('/\.pdf$/i', $c[2]))
                $attachments['pdf'][] = [
                    'id' => $c[1],
                    'file' => $c[2]
                ];
            else
                $attachments['other'][] = [
                    'id' => $c[1],
                    'file' => $c[2]
                ];
        }

        // ---- Guardo datos de ultima ejecucion ------------------------------
        $this->lastCmd = $cmd;
        $this->lastInput = $filename;
        $this->lastOutput = $output;

        // ---- Retorna  la información de los archivos embebidos
        return $attachments;
    }

    /**
     * Determina si un archivo .PDF tiene attachments.
     * @param  [type]  $filename [description]
     * @return boolean           [description]
     */
    public function hasAttachments($filename)
    {
        $attachments = $this->getAttachments($filename);

        return (count($attachments['pdf']) > 0) || (count($attachments['other']) > 0);
    }

    /**
     * Determina si un archivo .PDF tiene attachments de tipo PDF.
     * @param  [type]  $filename [description]
     * @return boolean           [description]
     */
    public function hasPDFAttachments($filename)
    {
        $attachments = $this->getAttachments($filename);

        return (count($attachments['pdf']) > 0);
    }

    /**
     * Determina si un archivo .PDF tiene attachments de Otro tipo (No PDF).
     * @param  [type]  $filename [description]
     * @return boolean           [description]
     */
    public function hasOtherAttachments($filename)
    {
        $attachments = $this->getAttachments($filename);

        return (count($attachments['other']) > 0);
    }

    /**
     * Extrae un documento (PDF o de Otro tipo) embebido en un PDF, a un directorio temporal.
     * Se utiliza el comando 'pdfdetach'
     *
     * @param  string  $filename         Archivo contenedor de los embebidos
     * @param  integer $id_attachment    ID del attachment
     * @param  string  $output_directory Directorio de salida
     *
     * @return string  Devuelve la ruta completa del archivo extraido.
     */
    public function extractFile($filename, $id_attachment, $output_directory = PATH_SGL_DOC_TEMPORALES)
    {
        // ---- Validaciones --------------------------------------------------
        if (! file_exists($filename))
            throw new Exception(sprintf('%s: El archivo %s no existe.', get_class($this), $filename));

        if (!is_dir($output_directory))
            throw new Exception(sprintf('%s: El directorio de salida no existe.', get_class($this)));

        $attachments = $this->getAttachments($filename);

        // Si hay embebidos (PDF o de Otro tipo)
        if ( $this->hasAttachments($filename) ) {
            $attach_filename = '';
            foreach ($attachments['pdf'] as $a) {
                if ($a['id'] == $id_attachment)
                    $attach_filename = $a['file'];
            }
            foreach ($attachments['other'] as $a) {
                if ($a['id'] == $id_attachment)
                    $attach_filename = $a['file'];
            }
            if ($attach_filename == '') {
                throw new Exception(sprintf('%s: El id de embebido %s no existe.', get_class($this), $id_attachment));
            } else {
                // ---- Extraigo el attachment
                // Se genera el nombre con el que se va a guardar dicho temporal:
                $attach_output = $this->getOutputFilename(
                    $attach_filename,
                    $output_directory,
                    DateTimeHelper::get()->timestampStr('YmdHisu')
                );

                $cmd = sprintf('pdfdetach -save %s -o %s %s', $id_attachment, $attach_output, $filename);

                // ---- Fix para evitar que los acentos salgan rotos ------------------
                $locale = 'es_AR.UTF-8';
                setlocale(LC_ALL, $locale);
                putenv('LC_ALL='.$locale);
                // --------------------------------------------------------------------

                $output = shell_exec("( $cmd ) 2>&1");

                // ---- Verifico Salida -----------------------------------------------
                if (! file_exists($attach_output))
                    throw new Exception(sprintf('%s: El documento de salida no se ha podido extraer.', get_class($this)));
            }
        }

        // ---- Guardo datos de ultima ejecucion ------------------------------
        $this->lastCmd = $cmd;
        $this->lastInput = $filename;
        $this->lastOutput = $output;

        // ---- Retorna el documento extraído
        return $attach_output;
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
