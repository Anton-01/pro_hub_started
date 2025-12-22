<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'avatar_url' => ['nullable', 'url', 'max:500'],
            'role' => ['required', Rule::in(['super_admin', 'admin', 'user'])],
            'status' => [Rule::in(['active', 'inactive', 'pending', 'suspended'])],
        ];

        if ($this->isMethod('POST')) {
            // Reglas para creación
            $rules['company_id'] = ['required', 'uuid', 'exists:companies,id'];
            $rules['email'] = [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('company_id', $this->input('company_id'));
                }),
            ];
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            // Reglas para actualización
            $rules['email'] = [
                'sometimes',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)->where(function ($query) {
                    return $query->where('company_id', $this->user()->company_id);
                }),
            ];
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'company_id.required' => 'La empresa es obligatoria.',
            'company_id.uuid' => 'El identificador de empresa no es válido.',
            'company_id.exists' => 'La empresa seleccionada no existe.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede exceder :max caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado en esta empresa.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser texto.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.min' => 'El nombre debe tener al menos :min caracteres.',
            'name.max' => 'El nombre no puede exceder :max caracteres.',
            'last_name.string' => 'El apellido debe ser texto.',
            'last_name.max' => 'El apellido no puede exceder :max caracteres.',
            'phone.string' => 'El teléfono debe ser texto.',
            'phone.max' => 'El teléfono no puede exceder :max caracteres.',
            'avatar_url.url' => 'La URL del avatar no es válida.',
            'avatar_url.max' => 'La URL del avatar no puede exceder :max caracteres.',
            'role.required' => 'El rol es obligatorio.',
            'role.in' => 'El rol seleccionado no es válido.',
            'status.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
