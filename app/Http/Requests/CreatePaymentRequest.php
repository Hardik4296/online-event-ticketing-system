<?php

namespace App\Http\Requests;

use App\Traits\Common;
use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    use Common;
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
            'event_id' => 'required|exists:events,id',
            'tickets' => 'required|array',
            'tickets.*.ticket_id' => 'required|exists:tickets,id',
            'tickets.*.quantity' => 'required|integer|min:1|max:10',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if ($this->has('event_id')) {
            $this->merge([
                'event_id' => $this->decryptId($this->input('event_id'))
            ]);
        }
    }
}
