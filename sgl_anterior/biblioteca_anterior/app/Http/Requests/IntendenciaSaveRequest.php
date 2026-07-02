<?php

namespace App\Http\Requests;

use App\Helpers\FakeAttrib;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class IntendenciaSaveRequest extends FormRequest
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
            'intendente' => 'required|string|min:2|max:100',
            'nro' => 'required|integer|min:1',
            'fec_desde' => 'required|date|date_format:"Y-m-d"|before:fec_hasta',
            'fec_hasta' => 'sometimes|nullable|date|date_format:"Y-m-d"|after:fec_desde'
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
            'intendente.required' => 'Debe especificar el nombre del Intendente.',
            'nro.required' => 'Debe especificar el número de mandato del Intendente.',
            'fec_desde.date_format' => 'La fecha de inicio de mandato tiene un formato inválido (año-mes-día).',
            'fec_desde.before' => 'La fecha de inicio de mandato debe ser anterior a la fecha de fin de mandato.',
            'fec_hasta.date_format' => 'La fecha de fin de mandato tiene un formato inválido (año-mes-día).',
            'fec_hasta.after' => 'La fecha de fin de mandato debe ser posterior a la fecha de inicio de mandato.',
        ];
    }
}
