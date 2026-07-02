<?php

namespace App\Http\Requests;

use App\Helpers\FakeAttrib;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RelacionesSaveRequest extends FormRequest
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
            'relaciones.*.id' => 'integer|nullable|exists:relaciones,id',
            'relaciones.*.sentido' => ['required', 'string', Rule::in(['O', 'D'])],
            'relaciones.*.tipo' => ['required', 'string', Rule::in(['A', 'D', 'R', 'M'])],
            'relaciones.*.a' => 'required|string|min:1|max:50',
            'relaciones.*.n' => 'required|string|min:1|max:50',
            'relaciones.*.p' => 'string|nullable|min:1'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }
}
