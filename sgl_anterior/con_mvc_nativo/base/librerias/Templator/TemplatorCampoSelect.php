<?php
/**
 * Clase de implementación de campos de tipo select (combo box).
 *
 * @author Carlos XXXX
 */
class TemplatorCampoSelect extends TemplatorCampo
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
            'tipo' => 'CampoSelect',
            'nombre' => 'CampoSelect',
            'placeholder' => 'Seleccione una opción',
            'obligatorio' => true,
            'valores' => ["Opción #1", "Opción #2", "Opción #3"],
            'default' => "Opción #1",
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
            '    <select %s placeholder="%s">%s</select>' .
            '</div>',
            $conf->id,
            ($conf->obligatorio) ? '(*) ' : '',
            $conf->nombre,
            $this->generarAtributosBaseHTML(),
            $conf->placeholder,
            join('', array_map(function ($v) { return sprintf('<option>%s</option>', $v); }, $conf->valores))
        );
    }

    /**
     * Lleva a cabo la validación de un campo.
     * @param  [type] $valor [description]
     * @return [type]        [description]
     */
    public function validar($valor) {
        // Ademas de la validación normal, verifico que el valor esté
        // en la lista de valores posibles.
        return parent::validar($valor) && in_array($valor, $this->config->valores);
    }

}
?>
