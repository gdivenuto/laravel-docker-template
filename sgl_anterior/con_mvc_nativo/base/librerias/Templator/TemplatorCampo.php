<?php
/**
 * Clase base de manejo de definición de campos.
 *
 * @author Carlos XXXX
 */
abstract class TemplatorCampo
{
    // ************************************************************************
    // Definición de Atributos ************************************************
    // ************************************************************************
    protected $default_options;  //!< // Opciones de configuración por defecto.
    public $config;              //!< // Configuración extraída de la plantilla.

    // ************************************************************************
    // Definición de Métodos **************************************************
    // ************************************************************************
    /**
     * Constructor de clase
     * @param stdClass $campo_config Configuración del campo.
     */
    public function __construct(stdClass $campo_config)
    {
        $this->config = $this->mergeOptions($campo_config);
	}

    /**
     * Hace el merge de las opciones de configuración contra los
     * defaults del campo.
     * @param  stdClass $campo_config [description]
     * @return stdClass               [description]
     */
    protected function mergeOptions(stdClass $campo_config)
    {
        return (object) array_merge($this->default_options, (array)$campo_config);
    }

    /**
     * Obtiene el patron de reemplazo de un determinado campo.
     * @return [type] [description]
     */
    public function obtenerPatronReemplazo()
    {
        return sprintf('/{{%s}}/', $this->config->id);
    }

    /**
     * Obtiene el valor de reemplazo de un determinado campo a partir de
     * una bolsa de parametros de REQUEST.
     * @return [type] [description]
     */
    public function obtenerValorReemplazo($request_data)
    {
        return (array_key_exists($this->config->id, $request_data))
            ? $request_data[$this->config->id]
            : '';
    }

    /**
     * Genera los atributos básicos para un campo de un formulario HTML.
     * @return [type] [description]
     */
    protected function generarAtributosBaseHTML()
    {
        // Logica de 'parche' para regexps en JS
        if (is_null($this->config->regex_js)) {
            // Si no tengo parche, trabajo "normal" con regex
            $regex = (! is_null($this->config->regex))
                ? base64_encode($this->config->regex)
                : '';
        } else {
            // Si hay parche, uso 'regex_js'
            $regex = base64_encode($this->config->regex_js);
        }

        return sprintf('id="%1$s" name="%1$s" class="form-control input-sm campo-plantilla" data-nombre="%2$s" data-obligatorio="%3$s" data-regex="%4$s"',
            $this->config->id,
            $this->config->nombre,
            ($this->config->obligatorio) ? 'si' : 'no',
            $regex
        );
    }

    /**
     * Genera el código HTML de un campo a partir de su configuración.
     * @return [type] [description]
     */
    abstract protected function generarHTML();

    /**
     * Lleva a cabo la validación de un campo.
     * @param  [type] $valor [description]
     * @return [type]        [description]
     */
    protected function validar($valor)
    {
        $conf = $this->config;

        $res = (is_null($conf->regex))
            ? true
            : preg_match($conf->regex, $valor);

        if ($conf->obligatorio)
            $res = $res && (trim($valor) != '');

        return $res;
    }
}
?>
