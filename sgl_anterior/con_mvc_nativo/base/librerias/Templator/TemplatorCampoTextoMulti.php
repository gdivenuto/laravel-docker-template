<?php
/**
 * Clase de implementación de campos de tipo texto multilínea.
 *
 * @author Carlos XXXX
 */
class TemplatorCampoTextoMulti extends TemplatorCampo
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
            'tipo' => 'CampoTextoMulti',
            'nombre' => 'CampoTextoMulti',
            'placeholder' => 'Ingrese un texto',
            'obligatorio' => true,
            'default' => [],
            'regex' => null,
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
            '    <textarea %s cols="50" rows="10" placeholder="%s">%s</textarea>' .
            '</div>',
            $conf->id,
            ($conf->obligatorio) ? '(*) ' : '',
            $conf->nombre,
            $this->generarAtributosBaseHTML(),
            $conf->placeholder,
            join("\n", $conf->default)
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
