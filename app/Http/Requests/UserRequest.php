<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'password' => 'required|min:8|confirmed',
            'tel' => [
                'numeric',
                'required',
                function ($attribute, $value, $fail) {
                    if (strlen($value) !== 10) {
                        $fail('El campo teléfono debe tener exactamente 10 dígitos.');
                    }
                }
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('app_users')->ignore($this->user),
            ],
            'biography' => 'max:250',
            'contact_schedule' => [
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        if (!preg_match('/^\d{1,2}:\d{2} (am|pm) - \d{1,2}:\d{2} (am|pm)$/', $value)) {
                            $fail('El formato de la franja horaria debe ser como "8:00 am - 8:00 pm".');
                        }
                    }
                },
            ],
            'profile_image' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'password.required' => 'El campo contraseña es obligatorio.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'tel.numeric' => 'El campo teléfono debe ser numérico.',
            'tel.required' => 'El campo teléfono es obligatorio.',
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'El correo electrónico ya está en uso. Por favor, elige otro.',
            'biography.max' => 'Su biografia no debe de exceder los 250 caracteres',
            'contact_schedule.required' => 'El campo horario de contacto es obligatorio.',
            'profile_image.image' => 'El archivo debe ser una imagen.',
            'profile_image.mimes' => 'El archivo debe ser una imagen jpeg, png o jpg.',
            'profile_image.max' => 'El archivo no debe pesar más de 2MB.',
        ];
    }
}
