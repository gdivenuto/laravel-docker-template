<?php
/**
 * Clase de implementación de campos de tipo fecha.
 *
 * @author Carlos XXXX
 */
class TemplatorCampoFecha extends TemplatorCampo
{
    // ************************************************************************
    // Definición de Atributos ************************************************
    // ************************************************************************

    // ************************************************************************
    // Definición de Métodos **************************************************
    // ************************************************************************
    /**
     * Constructor de clase
     * @param stdClass $campo_config Configuración del campo.
     */
    public function __construct(stdClass $campo_config)
    {
        $this->default_options = [
            'id' => '',
            'tipo' => 'CampoFecha',
            'nombre' => 'CampoFecha',
            'placeholder' => 'dd/mm/yyyy',
            'obligatorio' => true,
            'default' => 'now',
            'regex' => '/^(\\d{1,2})\\/(\\d{1,2})\\/(\\d{4})$/',
            'regex_js' => null
        ];

        parent::__construct($campo_config);
	}

    /**
     * Genera el código HTML de un campo a partir de su configuración.
     * @return [type] [description]
     */
    public function generarHTML()
    {
        $conf = $this->config;

        return sprintf(
            '<div class="form-group">' .
            '    <label for="%s">%s%s:</label>' .
            '    <input type="text" %s value="%s" placeholder="%s">' .
            '</div>' .
            '<script>$("#%s").datepicker();</script>',
            $conf->id,
            ($conf->obligatorio) ? '(*) ' : '',
            $conf->nombre,
            $this->generarAtributosBaseHTML(),
            ($conf->default=='now') ? date("d/m/Y") : $conf->default,
            $conf->placeholder,
            $conf->id
        );
    }

    /**
     * Lleva a cabo la validación de un campo.
     * @param  [type] $valor [description]
     * @return [type]        [description]
     */
    public function validar($valor) {
        return parent::validar($valor);
    }

}
?>
