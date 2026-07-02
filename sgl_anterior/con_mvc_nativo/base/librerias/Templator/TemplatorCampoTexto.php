<?php
/**
 * Clase de implementación de campos de tipo texto.
 *
 * @author Carlos XXXX
 */
class TemplatorCampoTexto extends TemplatorCampo
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
            'tipo' => 'CampoTexto',
            'nombre' => 'CampoTexto',
            'placeholder' => 'Ingrese un texto',
            'obligatorio' => true,
            'default' => '',
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
            '    <input type="text" %s value="%s" placeholder="%s">' .
            '</div>',
            $conf->id,
            ($conf->obligatorio) ? '(*) ' : '',
            $conf->nombre,
            $this->generarAtributosBaseHTML(),
            $conf->default,
            $conf->placeholder
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
