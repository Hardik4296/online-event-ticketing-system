<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
            'description' => 'required|string',
            'status' => 'required|in:inactive,active,cancelled',
            'event_date_time' => 'required|date|after_or_equal:now',
            'event_duration' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ticket_type' => 'required|array',
            'ticket_type.*' => 'required|string|max:255',
            'price' => 'required|array',
            'price.*' => 'required|string|max:255',
            'quantity' => 'required|array',
            'quantity.*' => 'required|string|max:255',
        ];
    }
}
