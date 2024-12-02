<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'event_date_time' => 'required|date|after_or_equal:now',
            'event_duration' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'city_id' => 'required|exists:cities,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => [
                'required',
                Rule::in(['inactive', 'active', 'cancelled']),
                function ($attribute, $value, $fail) {

                    if($this->value == 'cancelled' && $this->event_date_time < \Carbon\Carbon::now()) {
                        $fail('If the event is cancelled, the event date must be in the future.');
                    }
                }
            ],
            'ticket_type' => 'required|array',
            'ticket_type.*' => 'required|string|max:255',
            'price' => 'required|array',
            'price.*' => 'required|string|max:255',
            'quantity' => 'required|array',
            'quantity.*' => 'required|string|max:255',
            'ticket_id' => 'nullable|array',
            'ticket_id.*' => 'nullable|string|max:255',
        ];
    }
}
