<?php

namespace App\Http\Requests;

use App\Helpers\FakeAttrib;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ObservacionesSaveRequest extends FormRequest
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
            'observaciones.*.id' => 'integer|nullable|exists:observaciones,id',
            'observaciones.*.obs' => 'required|string|min:1'
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
