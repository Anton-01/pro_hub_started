<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModuleRequest extends FormRequest
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
            'label' => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => [Rule::in(['link', 'modal', 'external'])],
            'url' => ['nullable', 'string', 'max:500'],
            'target' => ['nullable', Rule::in(['_self', '_blank'])],
            'modal_id' => ['nullable', 'string', 'max:100'],
            'icon' => ['required', 'string'],
            'icon_type' => ['nullable', Rule::in(['svg', 'class', 'image'])],
            'highlight' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'group_name' => ['nullable', 'string', 'max:100'],
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
            'label.required' => 'La etiqueta es obligatoria.',
            'label.string' => 'La etiqueta debe ser texto.',
            'label.min' => 'La etiqueta debe tener al menos :min caracteres.',
            'label.max' => 'La etiqueta no puede exceder :max caracteres.',
            'description.string' => 'La descripción debe ser texto.',
            'type.in' => 'El tipo de módulo no es válido.',
            'url.string' => 'La URL debe ser texto.',
            'url.max' => 'La URL no puede exceder :max caracteres.',
            'target.in' => 'El destino no es válido.',
            'modal_id.string' => 'El ID del modal debe ser texto.',
            'modal_id.max' => 'El ID del modal no puede exceder :max caracteres.',
            'icon.required' => 'El ícono es obligatorio.',
            'icon.string' => 'El ícono debe ser texto.',
            'icon_type.in' => 'El tipo de ícono no es válido.',
            'highlight.regex' => 'El color de resaltado debe ser un código hexadecimal válido.',
            'background_color.regex' => 'El color de fondo debe ser un código hexadecimal válido.',
            'is_featured.boolean' => 'El campo destacado debe ser verdadero o falso.',
            'sort_order.integer' => 'El orden debe ser un número entero.',
            'sort_order.min' => 'El orden no puede ser negativo.',
            'group_name.string' => 'El nombre del grupo debe ser texto.',
            'group_name.max' => 'El nombre del grupo no puede exceder :max caracteres.',
            'status.in' => 'El estado no es válido.',
        ];
    }
}
