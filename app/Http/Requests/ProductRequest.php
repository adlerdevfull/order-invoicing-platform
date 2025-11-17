<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'sku' => 'sometimes|required|string|max:100',
            'price_cents' => 'sometimes|required|integer|min:1',
            'stock' => 'sometimes|integer|min:0',
            'description' => 'sometimes|string|max:1000',
        ];

        if ($this->isMethod('POST')) {
            $rules['name'] = 'required|string|max:255';
            $rules['sku'] = 'required|string|max:100|unique:products,sku';
            $rules['price_cents'] = 'required|integer|min:1';
        }

        return $rules;
    }
}
