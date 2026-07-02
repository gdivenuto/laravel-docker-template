<?php 

namespace App\Traits;

/**
 * StringAttributeToUppercase
 *
 * Trait que afectará a las clases del modelo, pasando a mayúsculas cualquier 
 * atributo que se asigne como string.
 *
 * Para excluir atributos, se debe definir el array $exclude_uppercase en el
 * modelo afectado:
 *
 * class Activo extends Model
 * {
 *     use StringAttributeToUppercase;
 *     protected $exclude_uppercase = [ 'apellido', 'nombre' ];
 *     [....]
 * }
 */
trait StringAttributeToUppercase
{
    /**
     * Atributos que por defecto no van a ser afectados por el trait.
     * @var array Atributos que por defecto no van a ser afectados por el trait.
     */
    protected $default_exclude_uppercase = [
        'password',
        'username',
        'email',
        'remember_token',
        'slug',
    ];

    /**
     * Trait que afectará a las clases del modelo, pasando a mayúsculas cualquier 
     * atributo que se asigne como string.
     * @param [type] $key   [description]
     * @param [type] $value [description]
     */
    public function setAttribute($key, $value)
    {
        if (is_string($value)) {
            if ($this->exclude_uppercase !== null) {
                if ( in_array($key, $this->default_exclude_uppercase) || in_array($key, $this->exclude_uppercase) ) 
                    parent::setAttribute($key, $value); // Comportamiento por defecto
                else
                    parent::setAttribute($key, mb_strtoupper($value));
            } else {
                if ( in_array($key, $this->default_exclude_uppercase) ) 
                    parent::setAttribute($key, $value); // Comportamiento por defecto
                else
                    parent::setAttribute($key, mb_strtoupper($value));
            }
        } else 
            parent::setAttribute($key, $value); // Comportamiento por defecto
    }
}