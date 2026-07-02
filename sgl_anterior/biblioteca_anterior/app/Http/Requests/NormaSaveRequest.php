<?php

namespace App\Http\Requests;

use App\Helpers\FakeAttrib;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class NormaSaveRequest extends FormRequest
{
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
            'base' => ['required', 'string', 'max:10', Rule::in(array_keys(FakeAttrib::basesList()))],
            'acto' => ['present', 'max:10', Rule::in(array_keys(FakeAttrib::tipoActoList()))],
            'nro' => 'sometimes|nullable|string|max:10',
            'origen' => 'sometimes|nullable|string|max:10',
            'nro_hcd' => 'sometimes|nullable|string|max:20',
            'exped' => 'sometimes|nullable|string|max:50',
            'bloque' => 'sometimes|nullable|string|max:50',
            'hcd_exped' => 'sometimes|nullable|string|regex:/^[0-9]{4}\/[0-9]{1,5}$/',
            'fec_sancion' => 'sometimes|nullable|date|date_format:"Y-m-d"',
            'fec_promulga' => 'sometimes|nullable|date|date_format:"Y-m-d"',
            'fec_publica' => 'sometimes|nullable|date|date_format:"Y-m-d"',
            'dec_promulga' => 'sometimes|nullable|string|max:10',
            'boletin_nro' => 'sometimes|nullable|string|max:10',
            'boletin_pag' => 'sometimes|nullable|string|max:10',
            'registro_t' => 'sometimes|nullable|string|max:10',
            'registro_f' => 'sometimes|nullable|string|max:10',
            'abrogacion_a' => 'sometimes|nullable|string|max:20',
            'abrogacion_n' => 'sometimes|nullable|string|max:20',
            'contenido' => 'sometimes|nullable|string',
            'nro_tema' => 'sometimes|nullable|string|max:50',
            'alcance' => 'sometimes|nullable|string|max:10',
            'caracter' => 'sometimes|nullable|string|max:10',
            'recopila' => 'sometimes|nullable|string|max:10',
            'fec_incluido' => 'sometimes|nullable|date|date_format:"Y-m-d"',
            'fec_excluido' => 'sometimes|nullable|date|date_format:"Y-m-d"',
            'sin_nro' => 'sometimes|nullable|string|max:10',
            'ingresa' => 'sometimes|nullable|string|max:10',
            'aprobado' => 'sometimes|nullable|string|max:10',
            'ausentes' => 'sometimes|nullable|string|max:10'
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
            'base.required' => 'Debe especificar una Base.',
            'base.in' => 'La base seleccionada es inválida.',
            'acto.in' => 'El tipo de Acto seleccionado es inválido.',
            'fec_sancion.date_format' => 'La fecha de Sanción tiene un formato inválido (año-mes-día).',
            'fec_promulga.date_format' => 'La fecha de Promulgación tiene un formato inválido (año-mes-día).',
            'fec_publica.date_format' => 'La fecha de Publicación tiene un formato inválido (año-mes-día).',
            'fec_incluido.date_format' => 'La fecha de Incluído tiene un formato inválido (año-mes-día).',
            'fec_excluido.date_format' => 'La fecha de Excluído tiene un formato inválido (año-mes-día).'
        ];
    }
}
