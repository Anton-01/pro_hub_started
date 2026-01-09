<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;

class UsersImport implements ToCollection, WithHeadingRow, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    private string $companyId;
    private array $errors = [];
    private int $successCount = 0;
    private int $errorCount = 0;
    private array $importedUsers = [];

    public function __construct(string $companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * Procesa la colección de datos del Excel
     */
    public function collection(Collection $rows)
    {
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
                        'name' => $rowData['nombre'] ?? 'N/A',
                        'email' => $rowData['correo'] ?? 'N/A',
                        'errors' => $validator->errors()->all(),
                        'password' => null,
                        'success' => false,
                    ];
                    continue;
                }

                // Verificar si el email ya existe
                $existingUser = User::where('company_id', $this->companyId)
                    ->where('email', $rowData['correo'])
                    ->first();

                if ($existingUser) {
                    $this->errorCount++;
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'name' => $rowData['nombre'],
                        'email' => $rowData['correo'],
                        'errors' => ['El email ya está registrado en el sistema'],
                        'password' => null,
                        'success' => false,
                    ];
                    continue;
                }

                // Generar contraseña segura
                $password = $this->generateSecurePassword();

                // Crear usuario
                User::create([
                    'company_id' => $this->companyId,
                    'name' => $rowData['nombre'],
                    'last_name' => $rowData['apellido'] ?? null,
                    'email' => $rowData['correo'],
                    'password' => $password,
                    'role' => 'user',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]);

                $this->successCount++;
                $this->importedUsers[] = [
                    'row' => $rowNumber,
                    'name' => $rowData['nombre'],
                    'email' => $rowData['correo'],
                    'password' => $password,
                    'success' => true,
                    'errors' => [],
                ];
            } catch (\Exception $e) {
                $this->errorCount++;
                $this->errors[] = [
                    'row' => $rowNumber,
                    'name' => $rowData['nombre'] ?? 'N/A',
                    'email' => $rowData['correo'] ?? 'N/A',
                    'errors' => ['Error inesperado: ' . $e->getMessage()],
                    'password' => null,
                    'success' => false,
                ];
            }
        }
    }

    /**
     * Genera una contraseña segura de 12 caracteres
     * Incluye: mayúsculas, minúsculas, números y símbolos
     */
    private function generateSecurePassword(): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        // Asegurar al menos uno de cada tipo
        $password = '';
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Completar los 8 caracteres restantes con una mezcla aleatoria
        $allChars = $lowercase . $uppercase . $numbers . $symbols;
        for ($i = 0; $i < 8; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Mezclar los caracteres para que no sea predecible
        return str_shuffle($password);
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
            'correo' => 'required|email|max:255',
            'apellido' => 'nullable|max:255',
        ], [
            'nombre.required' => 'El campo "Nombre" es obligatorio',
            'nombre.max' => 'El campo "Nombre" no puede tener más de 255 caracteres',
            'correo.required' => 'El campo "Correo" es obligatorio',
            'correo.email' => 'El campo "Correo" debe ser una dirección de correo válida',
            'correo.max' => 'El campo "Correo" no puede tener más de 255 caracteres',
            'apellido.max' => 'El campo "Apellido" no puede tener más de 255 caracteres',
        ]);
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
                'name' => 'N/A',
                'email' => 'N/A',
                'errors' => $failure->errors(),
                'password' => null,
                'success' => false,
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
     * Obtiene los usuarios importados exitosamente
     */
    public function getImportedUsers(): array
    {
        return $this->importedUsers;
    }

    /**
     * Obtiene el conteo de registros exitosos
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
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
            'errors' => $this->errorCount,
            'total' => $this->successCount + $this->errorCount,
        ];
    }

    /**
     * Genera el contenido del archivo TXT con el resultado de la importación
     */
    public function generateReportContent(): string
    {
        $report = "========================================\n";
        $report .= "   REPORTE DE IMPORTACIÓN DE USUARIOS\n";
        $report .= "========================================\n";
        $report .= "Fecha: " . now()->format('Y-m-d H:i:s') . "\n";
        $report .= "----------------------------------------\n\n";

        $report .= "RESUMEN:\n";
        $report .= "  - Usuarios registrados: {$this->successCount}\n";
        $report .= "  - Registros con error: {$this->errorCount}\n";
        $report .= "  - Total procesados: " . ($this->successCount + $this->errorCount) . "\n\n";

        if (!empty($this->importedUsers)) {
            $report .= "========================================\n";
            $report .= "   USUARIOS REGISTRADOS EXITOSAMENTE\n";
            $report .= "========================================\n\n";

            foreach ($this->importedUsers as $user) {
                $report .= "Fila: {$user['row']}\n";
                $report .= "  Nombre: {$user['name']}\n";
                $report .= "  Email: {$user['email']}\n";
                $report .= "  Contraseña: {$user['password']}\n";
                $report .= "  Estado: Registrado correctamente\n";
                $report .= "----------------------------------------\n";
            }
        }

        if (!empty($this->errors)) {
            $report .= "\n========================================\n";
            $report .= "   REGISTROS CON ERROR\n";
            $report .= "========================================\n\n";

            foreach ($this->errors as $error) {
                $report .= "Fila: {$error['row']}\n";
                $report .= "  Nombre: {$error['name']}\n";
                $report .= "  Email: {$error['email']}\n";
                $report .= "  Error(es):\n";
                foreach ($error['errors'] as $errMsg) {
                    $report .= "    - {$errMsg}\n";
                }
                $report .= "----------------------------------------\n";
            }
        }

        $report .= "\n========================================\n";
        $report .= "   FIN DEL REPORTE\n";
        $report .= "========================================\n";

        return $report;
    }
}
