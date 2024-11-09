<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'type' => [
        'required',
        'string'
      ],
      'name' => [
        'required',
        'string',
        Rule::unique('products', 'name')->ignore($this->product)
      ],
      'stock' => [
        'required',
        'integer'
      ],
      'number_of_sales' => [
        'required',
        'integer'
      ],
      'transaction_date' => [
        'required',
        'date'
      ]
    ];
  }
}
