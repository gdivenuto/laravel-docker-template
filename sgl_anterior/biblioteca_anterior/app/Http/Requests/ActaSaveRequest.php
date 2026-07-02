<?php

namespace App\Http\Requests;

use App\Helpers\FakeAttrib;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ActaSaveRequest extends FormRequest
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
            'actas.*.id' => 'integer|nullable|exists:actas,id',
            'actas.*.acta_n' => 'required|string|min:1',
            'actas.*.acta_r' => 'required|string|min:1',
            'actas.*.acta_t' => 'required|string|min:1'
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
            'actas.*.acta_n.required' => 'Debe ingresar el número de período.',
            'actas.*.acta_r.required' => 'Debe ingresar el número de reunión.',
            'actas.*.acta_t.required' => 'Debe seleccionar un tipo de reunión.',
        ];
    }
}
