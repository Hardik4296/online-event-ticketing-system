<?php

namespace App\Http\Requests;

use DB;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Common;
class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone_number' => [
                'required',
                'string',
                'regex:/^\+?[0-9]{10,15}$/',
                function ($attribute, $value, $fail) {
                    if (!$this->isPhoneNumberUnique($value)) {
                        $fail('The phone number has already been taken.');
                    }
                },
            ],
            'password' => 'required|string|min:8|confirmed|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
            'password_confirmation' => 'required|same:password',
            'role' => 'required|string|in:organizer,attendee',
        ];
    }

    private function isPhoneNumberUnique(string $phoneNumber): bool
    {
        $users = DB::table('users')->where('phone_number', $this->encryptData($phoneNumber))->first(); // Retrieve all users
        if ($users) {
            return false;
        }
        return true;
    }

}
