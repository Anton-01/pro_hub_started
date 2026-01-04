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
            ->max('sort_order') ?? 0;

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 porque Excel empieza en 1 y tiene header

            try {
                // Validar la fila
                $validator = $this->validateRow($row->toArray(), $rowNumber);

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
                if (!empty($row['email'])) {
                    $existingContact = Contact::where('company_id', $this->companyId)
                        ->where('email', $row['email'])
                        ->first();
                }

                $contactData = $this->prepareContactData($row);

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
     * Valida una fila del Excel
     */
    private function validateRow(array $row, int $rowNumber): \Illuminate\Validation\Validator
    {
        return Validator::make($row, [
            'nombre' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'departamento' => 'nullable|string|max:255',
            'cargo' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'extension' => 'nullable|string|max:20',
            'movil' => 'nullable|string|max:50',
            'estado' => 'nullable|in:activo,inactivo,active,inactive',
        ], [
            'nombre.required' => 'El nombre es obligatorio en la fila ' . $rowNumber,
            'nombre.max' => 'El nombre es demasiado largo en la fila ' . $rowNumber,
            'email.email' => 'El email no es válido en la fila ' . $rowNumber,
            'email.max' => 'El email es demasiado largo en la fila ' . $rowNumber,
            'estado.in' => 'El estado debe ser "activo" o "inactivo" en la fila ' . $rowNumber,
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
