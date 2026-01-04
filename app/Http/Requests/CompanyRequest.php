<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $companyId = $isUpdate ? $this->route('company')->id : null;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:100',
                $isUpdate
                    ? Rule::unique('companies')->ignore($companyId)
                    : 'unique:companies,slug'
            ],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
            'max_admins' => ['nullable', 'integer', 'min:1', 'max:10'],
            'status' => ['required', 'in:active,inactive,pending,suspended'],
        ];

        // Validaciones adicionales solo para creación de empresa
        if (!$isUpdate) {
            $rules['admin_name'] = ['required', 'string', 'max:255'];
            $rules['admin_last_name'] = ['nullable', 'string', 'max:255'];
            $rules['admin_email'] = ['required', 'email', 'max:255'];
            $rules['admin_password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la empresa es obligatorio.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'slug.unique' => 'Este identificador ya está en uso.',
            'slug.max' => 'El identificador no puede exceder 100 caracteres.',
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El email debe ser una dirección válida.',
            'email.max' => 'El email no puede exceder 255 caracteres.',
            'website.url' => 'El sitio web debe ser una URL válida.',
            'max_admins.integer' => 'El límite de administradores debe ser un número entero.',
            'max_admins.min' => 'Debe permitir al menos 1 administrador.',
            'max_admins.max' => 'El límite máximo es de 10 administradores.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado no es válido.',
            'admin_name.required' => 'El nombre del administrador es obligatorio.',
            'admin_email.required' => 'El email del administrador es obligatorio.',
            'admin_email.email' => 'El email del administrador debe ser válido.',
            'admin_password.required' => 'La contraseña del administrador es obligatoria.',
            'admin_password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'admin_password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }
}
