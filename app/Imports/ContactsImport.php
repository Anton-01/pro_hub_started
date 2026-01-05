<?php

namespace App\Imports;

use App\Models\Contact;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;

class ContactsImport implements ToCollection, WithHeadingRow, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    private string $companyId;
    private array $errors = [];
    private int $successCount = 0;
    private int $errorCount = 0;
    private int $updatedCount = 0;

    public function __construct(string $companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * Procesa la colección de datos del Excel
     */
    public function collection(Collection $rows)
    {
        $lastOrder = Contact::where('company_id', $this->companyId)
            ->max('order') ?? 0;

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 porque Excel empieza en 1 y tiene header

            try {
                // Convertir todos los valores a string para evitar problemas de tipo
                $rowData = $this->normalizeRowData($row->toArray());

                // Validar la fila
                $validator = $this->validateRow($rowData, $rowNumber);

                if ($validator->fails()) {
                    $this->errorCount++;
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'errors' => $validator->errors()->all(),
                    ];
                    continue;
                }

                // Verificar si el contacto ya existe (por email)
                $existingContact = null;
                if (!empty($rowData['email'])) {
                    $existingContact = Contact::where('company_id', $this->companyId)
                        ->where('email', $rowData['email'])
                        ->first();
                }

                $contactData = $this->prepareContactData($rowData);

                if ($existingContact) {
                    // Actualizar contacto existente
                    $existingContact->update($contactData);
                    $this->updatedCount++;
                } else {
                    // Crear nuevo contacto
                    $lastOrder++;
                    Contact::create(array_merge($contactData, [
                        'company_id' => $this->companyId,
                        'order' => $lastOrder,
                    ]));
                    $this->successCount++;
                }
            } catch (\Exception $e) {
                $this->errorCount++;
                $this->errors[] = [
                    'row' => $rowNumber,
                    'errors' => ['Error inesperado: ' . $e->getMessage()],
                ];
            }
        }
    }

    /**
     * Normaliza los datos de la fila (convierte todo a string y limpia espacios)
     */
    private function normalizeRowData(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            // Convertir a string y limpiar espacios
            if ($value === null || $value === '') {
                $normalized[$key] = null;
            } else {
                $normalized[$key] = trim((string) $value);
            }
        }
        return $normalized;
    }

    /**
     * Valida una fila del Excel
     */
    private function validateRow(array $row, int $rowNumber): \Illuminate\Validation\Validator
    {
        return Validator::make($row, [
            'nombre' => 'required|max:255',
            'apellido' => 'nullable|max:255',
            'departamento' => 'nullable|max:255',
            'cargo' => 'nullable|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|max:50',
            'extension' => 'nullable|max:20',
            'movil' => 'nullable|max:50',
            'estado' => 'nullable|in:activo,inactivo,active,inactive',
        ], [
            // Nombre
            'nombre.required' => 'El campo "Nombre" es obligatorio',
            'nombre.max' => 'El campo "Nombre" no puede tener más de 255 caracteres',
            // Apellido
            'apellido.max' => 'El campo "Apellido" no puede tener más de 255 caracteres',
            // Departamento
            'departamento.max' => 'El campo "Departamento" no puede tener más de 255 caracteres',
            // Cargo
            'cargo.max' => 'El campo "Cargo" no puede tener más de 255 caracteres',
            // Email
            'email.email' => 'El campo "Email" debe ser una dirección de correo válida',
            'email.max' => 'El campo "Email" no puede tener más de 255 caracteres',
            // Teléfono
            'telefono.max' => 'El campo "Teléfono" no puede tener más de 50 caracteres',
            // Extensión
            'extension.max' => 'El campo "Extensión" no puede tener más de 20 caracteres',
            // Móvil
            'movil.max' => 'El campo "Móvil" no puede tener más de 50 caracteres',
            // Estado
            'estado.in' => 'El campo "Estado" debe ser: activo o inactivo',
        ]);
    }

    /**
     * Prepara los datos del contacto para insertar/actualizar
     */
    private function prepareContactData($row): array
    {
        // Normalizar estado
        $status = 'active';
        if (!empty($row['estado'])) {
            $statusMap = [
                'activo' => 'active',
                'active' => 'active',
                'inactivo' => 'inactive',
                'inactive' => 'inactive',
            ];
            $status = $statusMap[strtolower($row['estado'])] ?? 'active';
        }

        return [
            'name' => $row['nombre'],
            'last_name' => $row['apellido'] ?? null,
            'department' => $row['departamento'] ?? null,
            'position' => $row['cargo'] ?? null,
            'email' => !empty($row['email']) ? $row['email'] : null,
            'phone' => $row['telefono'] ?? null,
            'extension' => $row['extension'] ?? null,
            'mobile' => $row['movil'] ?? null,
            'status' => $status,
        ];
    }

    /**
     * Maneja errores de validación que Laravel Excel detecta
     */
    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->errorCount++;
            $this->errors[] = [
                'row' => $failure->row(),
                'errors' => $failure->errors(),
            ];
        }
    }

    /**
     * Tamaño del lote para inserción
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Tamaño del chunk para lectura
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Obtiene los errores de importación
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Obtiene el conteo de registros exitosos
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Obtiene el conteo de registros actualizados
     */
    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    /**
     * Obtiene el conteo de errores
     */
    public function getErrorCount(): int
    {
        return $this->errorCount;
    }

    /**
     * Obtiene un resumen de la importación
     */
    public function getSummary(): array
    {
        return [
            'success' => $this->successCount,
            'updated' => $this->updatedCount,
            'errors' => $this->errorCount,
            'total' => $this->successCount + $this->updatedCount + $this->errorCount,
        ];
    }
}
