<?php

namespace App\Http\Requests;

use App\Helpers\FakeAttrib;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HcdExpedienteSaveRequest extends FormRequest
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
            'hcd_expedientes.*.id' => 'integer|nullable|exists:hcd_expedientes,id',
            'hcd_expedientes.*.hcd_exped' => 'required|string|min:1'
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
