<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsRequest extends FormRequest
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
        $rules = [
            'content' => 'required|string|max:500',
            'url' => 'nullable|url|max:500',
            'published_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'priority' => ['nullable', 'boolean'],
            'status' => [Rule::in(['active', 'inactive', 'pending', 'suspended'])],
        ];

        if ($this->isMethod('POST')) {
            $rules['company_id'] = ['required', 'uuid', 'exists:companies,id'];
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
            'text.required' => 'El texto de la noticia es obligatorio.',
            'text.string' => 'El texto debe ser una cadena de caracteres.',
            'text.min' => 'El texto debe tener al menos :min caracteres.',
            'url.url' => 'La URL no tiene un formato válido.',
            'url.max' => 'La URL no puede exceder :max caracteres.',
            'starts_at.date' => 'La fecha de inicio no es válida.',
            'ends_at.date' => 'La fecha de fin no es válida.',
            'ends_at.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'sort_order.integer' => 'El orden debe ser un número entero.',
            'sort_order.min' => 'El orden no puede ser negativo.',
            'is_priority.boolean' => 'El campo prioridad debe ser verdadero o falso.',
            'status.in' => 'El estado no es válido.',
        ];
    }
}
