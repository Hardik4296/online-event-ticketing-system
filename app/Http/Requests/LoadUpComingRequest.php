<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoadUpComingRequest extends FormRequest
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
            'page' => 'required|integer|min:1',
            'city_id' => 'nullable|exists:cities,id',
            'date' => 'nullable|date',
            'keyword' => 'nullable|string',
        ];
    }
}
