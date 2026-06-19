<?php

namespace App\Domains\Sales\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalePostRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'sales' => 'required|array|min:1',
            'sales.*.department_id' => 'required',
            'sales.*.responsible_id' => 'required',
            'sales.*.client_id' => 'required',
            'sales.*.count' => 'required|numeric|min:1',
            'sales.*.price' => 'required|numeric|min:0',
            'sales.*.amount' => 'nullable|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [];
    }
}
