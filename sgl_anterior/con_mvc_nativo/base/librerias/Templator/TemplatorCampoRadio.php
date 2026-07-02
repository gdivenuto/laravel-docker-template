<?php
/**
 * Clase de implementación de campos de tipo radio button.
 *
 * @author Carlos XXXX
 */
class TemplatorCampoRadio extends TemplatorCampo
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
            'tipo' => 'CampoRadio',
            'nombre' => 'CampoRadio',
            'obligatorio' => false,
            'valores' => ["Si", "No"],
            'default' => "Si",
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
        // $cant_opciones = (isset($conf->valores)) ? count($conf->valores) : 0;
        $valores = ($conf->valores) ?? [];

        $html = sprintf(
            '<div class="form-group"><strong>%s%s: </strong>',
            ($conf->obligatorio) ? '(*) ' : '',
            $conf->nombre
        );

        $html .= sprintf('<div id="%s" type="radio-falso">', $conf->id);

        foreach ($valores as $v) {
            $html .= '<label>';
            $html .= '<input type="radio"';
            $html .= ' id="'.$conf->id.'_'.md5($v).'" '; // Id + valor
            $html .= ' name="'.$conf->id.'"';
            $html .= ' class="campo-plantilla"';
            $html .= ' data-obligatorio="'.($this->config->obligatorio) ? 'si' : 'no'.'"';
            $html .= ' data-regex=""';
            $html .= ' value="'.$v.'">';
            $html .= '&nbsp;'.$v;
            $html .= '</label>&nbsp;';
        }

        $html .= "</div>";// Cierre del radio-falso

        $html .= '</div>';// Cierre del form-group

        return $html;
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
