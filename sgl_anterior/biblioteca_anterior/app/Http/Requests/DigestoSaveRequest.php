<?php

namespace App\Http\Requests;

use App\Helpers\FakeAttrib;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Factory as ValidationFactory;

class DigestoSaveRequest extends FormRequest
{
    /**
     * Se extiende la funcionalidad del validador para campos especiales.
     * @param ValidationFactory $validationFactory [description]
     */
    public function __construct(ValidationFactory $validationFactory)
    {
        // ---- Validación de Filtro de Digesto en base64 ---------------------
        $validationFactory->extend(
            'base64_digesto_filtro',
            function ($attribute, $value, $parameters) {
                // ---- Verificacion de base64 string -------------------------
                // Check if there are valid base64 characters
                if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value)) return false;

                // Decode the string in strict mode and check the results
                $decoded = base64_decode($value, true);
                if(false === $decoded) return false;

                // Encode the string again
                if(base64_encode($decoded) != $value) return false;

                // return true;

                // ---- Verificacion de filtro de digesto ---------------------
                $filtro = json_decode($decoded);
                if (is_null($filtro)) return false;

                $flag_error = false;
                foreach ($filtro as $f) {
                    if (count($f) != 3) 
                        $flag_error = true; 

                    if (! in_array($f[0], ['abrogacion_a','abrogacion_n','acto','alcance','aprobado','ausentes','base','bloque','boletin_nro','boletin_pag','caracter','contenido','dec_promulga','exped','ingresa','nro','nro_hcd','nro_tema','origen','recopila','registro_f','registro_t','sin_nro']) ) 
                        $flag_error = true;

                    if (! in_array($f[1], ['=', '<>', 'like'])) 
                        $flag_error = true;

                    if (!preg_match('/^%{0,1}[a-zA-Z0-9ñÑáéíóúüÁÉÍÓÚÜ\s\.,:;&º-]+%{0,1}$/', $f[2]))
                        $flag_error = true;

                    if ($flag_error) break;
                }

                return !$flag_error;
            },
            'El parámetro no es un filtro de digesto válido.'
        );
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // El backend debe estar habilitado y el usuario debe estar autenticado
        return config('params.backend_enabled') && Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'integer|nullable|exists:digestos,id',
            'nombre' => 'required|string|min:1',
            'publicado' => 'required|string|in:S,N',
            'descripcion' => 'required|string|min:1',
            'filtro' => 'sometimes|string|base64_digesto_filtro',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nombre.required' => 'Debe especificar un nombre para el Digesto.',
            'descripcion.required' => 'Debe especificar una descripción para el Digesto.',
        ];
    }

}
