<?php
// Se incluye la clase para convertir el HTML en PDF
require_once(PATH_KRAKEN_LIBRERIAS_HTML2PDF.'html2pdf.class.php');
/**
 * Clase que implementa el patrón wrapper para la generación de PDFs para los reportes
 *
 * @author XXXX y XXXX
 */
class PDFComposer
{
    private static $instance;

    private $defaultOptions;   //!< Default option array values.

    private $marginTop;
    private $marginRight;
    private $marginBottom;
    private $marginLeft;
    private $marginBodyTop;
    private $marginBodyRight;
    private $marginBodyBottom;
    private $marginBodyLeft;
    private $orientation;      //!< Orientación horizontal (L = landscape)
    private $font;             //!< Tamaño hoja A4
    private $displayMode;      //!< Modo de salida
    private $defaultFont;      //!< Fuente del documento
    private $name;             //!< Nombre del documento por defecto
    private $outputType;       //!< Tipo de salida para el PDF generado. I: browser, F: archivo en disco del server
    private $title;            //!< Título del documento
    private $cssFile;

    private $lastOutput;       //!< Último archivo de salida procesado.

    // ************************************************************************
    // Definición de Métodos que requieren implementación *********************
    // ************************************************************************

    // ************************************************************************
    // Definición de Métodos **************************************************
    // ************************************************************************
    /**
     * Constructor de clase
     */
    private function __construct()
    {
        $this->defaultOptions = [
            'marginTop'    => KRAKEN_REPORT_MARGIN_TOP,
            'marginRight'  => KRAKEN_REPORT_MARGIN_RIGHT,
            'marginBottom' => KRAKEN_REPORT_MARGIN_BOTTOM,
            'marginLeft'   => KRAKEN_REPORT_MARGIN_LEFT,
            'marginBodyTop'    => KRAKEN_REPORT_MARGIN_BODY_TOP,
            'marginBodyRight'  => KRAKEN_REPORT_MARGIN_BODY_RIGHT,
            'marginBodyBottom' => KRAKEN_REPORT_MARGIN_BODY_BOTTOM,
            'marginBodyLeft'   => KRAKEN_REPORT_MARGIN_BODY_LEFT,
            'orientation'  => KRAKEN_REPORT_ORIENTATION_VERTICAL,
            'font'         => KRAKEN_REPORT_HOJA_A4,
            'displayMode'  => KRAKEN_REPORT_DISPLAY_MODE_FULL_PAGE,
            'defaultFont'  => KRAKEN_REPORT_FONT_ARIAL,
            'name'         => 'reporte_'.date("Ymd_hi").'.pdf',
            'outputType'   => KRAKEN_REPORT_OUTPUT_FILE,
            'title'        => 'Documento',
            'cssFile'      => PATH_KRAKEN_HTML_BACKEND.'css/documento_generado.css',
        ];

        $this->lastOutput = '';
	}

    /**
     * Configura el set de opciones del Generador de PDFs.
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
     * Se genera el documento PDF y se guarda en un directorio destino
     * @param  [type] $contenidoHTML [description]
     * @param  string $doc_destino   [description]
     * @return [type]                [description]
     */
    public function generarPDF($contenidoHTML, $doc_destino = '')
    {
        $errores = [];
        $doc_destino = ($doc_destino == '') ? $this->name : $doc_destino;
        $this->lastOutput = '';

        // Se prepara el formato del documento completo
        // -----------------------------------------
        $css_data = file_get_contents($this->cssFile); // Se obtiene el contenido del archivo .css
        $fecha_hora = date("d/m/Y");
        $logo_img_encabezado = URL_KRAKEN_RESOURCES_ASSET_IMAGES.'logo_118x55.png';
        $logo_img_pie_izquierdo = URL_KRAKEN_RESOURCES_ASSET_IMAGES.'logo_pie_izquierdo.png';
        $logo_img_pie_derecho = URL_KRAKEN_RESOURCES_ASSET_IMAGES.'logo_pie_derecho.png';

        // ---- HTML DATA -----------------------------------------------------
        $html = <<<HTML
<style type="text/css">
<!--
{$css_data}
-->
</style>
<page   backtop="{$this->marginBodyTop}" backbottom="{$this->marginBodyBottom}"
        backleft="{$this->marginBodyLeft}" backright="{$this->marginBodyRight}">
    <page_header>
        <table id="pdf_encabezado" class="pdf_texto_gris">
            <tr>
                <td id="pdf_encabezado_logo" rowspan="3">
                    <img src="{$logo_img_encabezado}">
                </td>
                <td id="pdf_encabezado_nombre_sistema">
                    <table align="right">
                        <tr>
                            <td>
                                <!-- 1983 - 40&deg; Aniversario de la Recuperaci&oacute;n Democr&aacute;tica - 2023
                                <br>
                                A&ntilde;o Homenaje al cuadrag&eacute;simo aniversario de vigencia ininterrumpida de la democracia -->
                                &nbsp;
                            </td>
                        </tr>
                        <tr><td>Detalle: {$this->title}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
        <div class="pdf_separador"></div>
    </page_header>
    <page_footer>
        <table id="pdf_pie" class="pdf_texto_gris">
            <tr>
                <td class="pdf_pie_logo" rowspan="3">
                    <img src="{$logo_img_pie_izquierdo}" width="80" height="80" />
                </td>
                <td id="pdf_pie_info">
                    Hip. Yrigoyen 1627 | 2&deg; Piso Palacio Municipal | Ala izquierda
                    <br>
                    Tel. 223 499 6510 – B7600DOM | Mar del Plata | Prov. de Buenos Aires | Rep. Argentina
                    <br>
                    info@concejomdp.gob.ar | www.concejomdp.gob.ar
                </td>
                <td class="pdf_pie_logo" rowspan="3">
                    <img src="{$logo_img_pie_derecho}" width="80" height="80" />
                </td>
            </tr>
        </table>
    </page_footer>
    {$contenidoHTML}

</page>

<!-- Agrego una página forzada, con la misma cabecera, para que me agregue una hoja mas -->
<page   backtop="{$this->marginBodyTop}" backbottom="{$this->marginBodyBottom}"
        backleft="{$this->marginBodyLeft}" backright="{$this->marginBodyRight}">
    <page_header>
        <table id="pdf_encabezado" class="pdf_texto_gris">
            <tr>
                <td id="pdf_encabezado_logo" rowspan="3">
                    <img src="{$logo_img_encabezado}">
                </td>
                <td id="pdf_encabezado_nombre_sistema">
                    <table align="right">
                        <tr>
                            <td>
                                <!-- 1983 - 40&deg; Aniversario de la Recuperaci&oacute;n Democr&aacute;tica - 2023
                                <br>
                                A&ntilde;o Homenaje al cuadrag&eacute;simo aniversario de vigencia ininterrumpida de la democracia -->
                                &nbsp;
                            </td>
                        </tr>
                        <tr><td>Detalle: {$this->title}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
        <div class="pdf_separador"></div>
    </page_header>
    <page_footer>
        <table id="pdf_pie" class="pdf_texto_gris">
            <tr>
                <td class="pdf_pie_logo" rowspan="3">
                    <img src="{$logo_img_pie_izquierdo}" width="80" height="80" />
                </td>
                <td id="pdf_pie_info">
                    Hip. Yrigoyen 1627 | 2&deg; Piso Palacio Municipal | Ala izquierda
                    <br>
                    Tel. 223 499 6510 – B7600DOM | Mar del Plata | Prov. de Buenos Aires | Rep. Argentina
                    <br>
                    info@concejomdp.gob.ar | www.concejomdp.gob.ar
                </td>
                <td class="pdf_pie_logo" rowspan="3">
                    <img src="{$logo_img_pie_derecho}" width="80" height="80" />
                </td>
            </tr>
        </table>
    </page_footer>
</page>
HTML;
        // ---- EOF HTML DATA -------------------------------------------------

        try
        {
            // Conversión HTML => PDF (los métodos utilizados aquí están definidos en el archivo html2pdf.class.php)
            // Se instancia un 'html2pdf', con:
            // orientación horizontal (L = landscape),
            // tamaño hoja A4,
            // idioma español,
            // márgenes Left, Top, Right y Bottom
            $html2pdf = new HTML2PDF(
                $this->orientation,
                $this->font,
                'es',
                true,    // unicode?
                'UTF-8', // tipo de encoding
                array(
                    $this->marginLeft,
                    $this->marginTop,
                    $this->marginRight,
                    $this->marginBottom
                )
            );
            // Se define el modo de visualización a pantalla completa
            $html2pdf->pdf->SetDisplayMode($this->displayMode);
            // Se define la fuente Arial por defecto
            $html2pdf->setDefaultFont($this->defaultFont);
            // Se procesa el contenido (código HTML)
            $html2pdf->writeHTML($html);

            // Se genera el documento PDF en base a la configuracion de salida
            //  - name es el nombre o ruta completa de la salida
            //  - outputType es el tipo de salida para la libreria pdf
            $html2pdf->Output($doc_destino, $this->outputType);

            $this->lastOutput = $doc_destino;
        }
        catch(HTML2PDF_exception $e) {
            $errores[] = $e->getMessage();
        }

        return $errores;
    }

    /**
     * Obtiene el último nombre de archivo procesado como salida.
     * @return [type] [description]
     */
    public function getLastOutput()
    {
        return $this->lastOutput;
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
            $claseActual = __CLASS__;           // Obtengo la clase actual
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
}
?>
