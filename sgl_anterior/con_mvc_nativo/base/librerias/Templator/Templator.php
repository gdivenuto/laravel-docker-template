<?php
/**
 * Templator
 * ---------
 * 
 * Manejador de formularios dinámicos y consumo de plantillas para la generación
 * de textos prearmados.
 * 
 * @author Vergara, Carlos Gabriel
 */
class Templator
{
    // ************************************************************************
    // Definición de Atributos ************************************************
    // ************************************************************************
    public $plantilla;           //!< // Nombre del archivo de plantilla relacionado.
    public $plantilla_config;    //!< // Configuración extraída de la plantilla.
    public $plantilla_texto;     //!< // Texto de la plantilla.
    public $campos_predefinidos; //!< // Array de campos predefinidos para la plantilla.
    private $default_options;    //!< // Parametros por defecto de generación de formulario.

    // ************************************************************************
    // Getters & Setters ******************************************************
    // ************************************************************************
    public function getPlantilla() { 
        return $this->plantilla; 
    }
    
    public function setPlantilla($value) { 
        // Validaciónes de parametro         
        if ( (!is_null($value)) && (!is_string($value)) ) 
            throw new InvalidArgumentException(sprintf("Error en %s.setPlantilla(): el atributo 'plantilla' solo permite valores de tipo string.", get_class($this)));
        
        // Existe el archivo?
        if (!file_exists($value))
            throw new InvalidArgumentException(sprintf("Error en %s.setPlantilla(): el archivo '%s' no existe, o es inaccesible.", get_class($this), basename($value)));

        // Extraigo mas datos de la definicion de la plantilla y verifico contenido del archivo
        $xml_data = new SimpleXMLElement(file_get_contents($value));
        
        if (!property_exists($xml_data, 'config'))
            throw new InvalidArgumentException(sprintf("Error en %s.setPlantilla(): el nodo 'config' no existe en la definición de la plantilla.", get_class($this)));

        if (!property_exists($xml_data, 'texto'))
            throw new InvalidArgumentException(sprintf("Error en %s.setPlantilla(): el nodo 'texto' no existe en la definición de la plantilla.", get_class($this)));

        // Actualizo atributos
        $this->plantilla = $value;
        $this->plantilla_config = json_decode($xml_data->config);
        $this->plantilla_texto = $xml_data->texto->asXML();

        // Fix: quito los tags '<texto>...</texto>' de la plantilla
        $this->plantilla_texto = preg_replace('/^<texto>(.*)<\/texto>$/is', '$1', $this->plantilla_texto);
    }

    // ************************************************************************
    // Definición de Métodos **************************************************
    // ************************************************************************
    /**
     * Constructor de clase
     * @param string $plantilla Nombre del archivo XML de definición de plantilla.
     */
    /**
     * Constructor de clase
     * @param string $plantilla Nombre del archivo XML de definición de plantilla.
     * @param array  $campos_predefinidos Lista de campos predefinidos para las plantillas.
     */
    public function __construct($plantilla, $campos_predefinidos = [])
    {
        $this->default_options = [
            'form_generar_tag' => true,
            'form_id' => 'formDefaultId',
            'form_action' => '#',
            'form_method' => 'POST',
            'form_con_submit' => true,
            'form_con_cabecera' => true
        ];

        $this->campos_predefinidos = $campos_predefinidos;
        //$this->campos_predefinidos['pred_fecha_str'] = DateTimeHelper::get()->getTimestampStr('d/m/Y');
        $this->campos_predefinidos['pred_fecha_str'] = DateTimeHelper::get()->mostrarFechaLetras(DateTimeHelper::get()->getTimestampStr('Y/m/d'));
        $this->campos_predefinidos['pred_hora_str'] = DateTimeHelper::get()->getTimestampStr('H:i');

        $this->campos_predefinidos['pred_fecha_anio_str'] = date("Y");

        $this->setPlantilla($plantilla);
	}

    /**
     * Devuelve un array de elementos de tipo TemplatorCampos a partir
     * de la configuración de todos los campos de la plantilla.
     * @return [type] [description]
     */
    private function generarTemplatorCampos() 
    {
        $template_campos = [];

        foreach ($this->plantilla_config->campos as $c) {
            $clase_campo = sprintf('Templator%s', $c->tipo);
            if (class_exists($clase_campo))
                $template_campos[] = new $clase_campo($c);
        }

        return $template_campos;
    }
    
    /**
     * Genera el código HTML de un formulario dinámico a partir de una plantilla.
     * @param  array  $options [description]
     * @return [type]          [description]
     */
    public function generarFormularioHTML($options = [])
    {
        $conf = $this->plantilla_config;
        $options = array_merge($this->default_options, $options);

        // ---- Formulario
        $html = '';
        if ($options['form_generar_tag'])
            $html .= sprintf('<form id="%1$s" name="%1$s" action="%2$s" method="%3$s">', $options['form_id'], $options['form_action'], $options['form_method']);

        // ---- Cabecera
        if ($options['form_con_cabecera'])
            $html .= sprintf('<h2>%s</h2><p>%s</p>',
                $conf->plantilla->nombre,
                $conf->plantilla->descripcion
            );

        // ---- Campos
        $campos = $this->generarTemplatorCampos();
        foreach ($campos as $c) {
            $html .= $c->generarHTML();
        }

        // Campo oculto de control por plantilla
        $html .= sprintf('<input type="hidden" id="f_plantilla" name="f_plantilla" value="%s">', $conf->plantilla->id);

        // ---- Pie
        $html .= '<p><strong>(*) </strong>Los campos marcados con un asterisco son obligatorios.</p>';
        if ($options['form_con_submit'])
            $html .= '<button type="submit" class="btn btn-primary" id="f_submit" name="f_submit" value="enviar">Enviar</button>';

        // ---- Cierre formulario
        if ($options['form_generar_tag'])
            $html .= '</form>';

        return $html;
    }

    /**
     * Renderiza el código HTML de un formulario dinámico a partir de una plantilla.
     * @param  array  $options [description]
     * @return [type]          [description]
     */
    public function renderizarFormularioHTML($options = [])
    {
        // ---- Renderizo
        echo $this->generarFormularioHTML($options);
    }

    /**
     * Genera el texto a partir de los datos recopilados del formulario generado.
     * @return String Texto de la plantilla, con el reemplazo de los campos.
     */
    public function generarTexto($request_data)
    {
        $campos = $this->generarTemplatorCampos();

        // Obtengo los campos de la plantilla, formateados como patron de reemplazo (reg exp)
        $campos_regex = array_map(function ($c) { 
                return $c->obtenerPatronReemplazo();
            }, 
            $campos
        );

        // Obtengo los valores asociados a los campos como valor de reemplazo
        $valores_form = array_map(function ($c) use ($request_data) { 
                return $c->obtenerValorReemplazo($request_data);
            }, 
            $campos
        );

        // Agrego los campos predefinidos a cada array de campos y valores
        foreach ($this->campos_predefinidos as $c => $v) {
            $campos_regex[] = sprintf('/{{%s}}/', $c); //TODO: la lógica del valor de reemplazo esta 'cableada'
            $valores_form[] = $v;
        }

        return preg_replace($campos_regex, $valores_form, $this->plantilla_texto);
    }

    /**
     * Valida todos los campos segun su configuración contra un conjunto
     * de valores dado.
     * @param  [type] $request_data [description]
     * @return [type]               [description]
     */
    public function validarCampos($request_data)
    {
        $campos = $this->generarTemplatorCampos();

        $errores = [];
        foreach ($campos as $c) {
            if (!$c->validar($c->obtenerValorReemplazo($request_data)))
                $errores[] = sprintf('Ingrese un valor válido para el campo "%s".', $c->config->nombre);
        }

        return $errores;
    }
}
?>