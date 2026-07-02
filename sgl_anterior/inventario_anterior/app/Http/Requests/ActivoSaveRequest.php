<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActivoSaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'tipo_id' => 'required|integer|min:1|exists:activo_tipos,id',
            'legajo' => 'required|integer|min:0|exists:responsables',
            'nro_inventario' => 'present|string|max:20',
            'orden_compra' => 'present|string|max:50',
            'marca' => 'present|string|max:100',
            'modelo' => 'present|string|max:100',
            'nro_serie' => 'present|string|max:50',
            'ubicacion' => 'present|string|max:100',
            'fecha_alta' => 'required|date_format:"Y-m-d"', // validar
            'tipo_origen' => ['required','string','max:2',Rule::in(array_keys(config('defaults.descripTipoOrigen')))],
            'titularidad' => ['required','string','max:12',Rule::in(array_keys(config('defaults.descripTitularidad')))],
            'estado' => ['required','string','max:2',Rule::in(array_keys(config('defaults.descripEstado')))],
            'condicion_uso' => ['required','string','max:2',Rule::in(array_keys(config('defaults.descripCondicionUso')))],
            'nombre_equipo' => 'present|string|max:100',
            'sistema_operativo' => 'present|string|max:100',
            'sistema_operativo_serie' => 'present|string|max:100',
            'cpu' => 'present|string|max:100',
            'memoria' => 'present|string|max:100',
            'motherboard' => 'present|string|max:100',
            'hd_marca' => 'present|string|max:100',
            'hd_capacidad' => 'present|string|max:100',
            'dvd_rw' => 'required|boolean',
            'dvd_rw_marca' => 'present|string|max:100',
            'ethernet_dinamico' => 'required|boolean',
            'ethernet_mac' => ['present','string','size:17','regex:/^([a-f0-9]{2}[:|\-]{1}?){5}[a-f0-9]{2}$/i'],
            'ethernet_ip' => 'sometimes|ip',
            'ethernet_mask' => 'sometimes|ip',
            'ethernet_gw' => 'sometimes|ip',
            'ethernet_dns' => 'sometimes|string|max:200',
            'wireless_dinamico' => 'required|boolean',
            'wireless_mac' => ['present','string','size:17','regex:/^([a-f0-9]{2}[:|\-]{1}?){5}[a-f0-9]{2}$/i'],
            'wireless_ip' => 'sometimes|ip',
            'wireless_mask' => 'sometimes|ip',
            'wireless_gw' => 'sometimes|ip',
            'wireless_dns' => 'sometimes|string|max:200',
            'fuente' => 'present|string|max:100',
            'observaciones' => 'present|string',
            'habilitado' => 'required|string|max:2',
            'archivo' => 'nullable|mimes:pdf|max:8192'
        ];
    }
}
