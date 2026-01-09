<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class UsersTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Datos de ejemplo para la plantilla
     */
    public function array(): array
    {
        return [
            [
                'Juan',
                'Pérez García',
                'juan.perez@empresa.com',
            ],
            [
                'María',
                'González López',
                'maria.gonzalez@empresa.com',
            ],
            [
                'Carlos',
                'Rodríguez Martínez',
                'carlos.rodriguez@empresa.com',
            ],
        ];
    }

    /**
     * Encabezados de las columnas
     */
    public function headings(): array
    {
        return [
            'Nombre',
            'Apellido',
            'Correo',
        ];
    }

    /**
     * Estilos de la hoja de cálculo
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo del encabezado
        $sheet->getStyle('A1:C1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '6BA3FF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Altura de la fila del encabezado
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Agregar nota de ayuda
        $instructionsRow = count($this->array()) + 3;
        $sheet->setCellValue('A' . $instructionsRow, 'INSTRUCCIONES:');
        $sheet->setCellValue('A' . ($instructionsRow + 1), '• El campo "Nombre" es obligatorio');
        $sheet->setCellValue('A' . ($instructionsRow + 2), '• El campo "Correo" es obligatorio y debe ser único');
        $sheet->setCellValue('A' . ($instructionsRow + 3), '• El campo "Apellido" es opcional');
        $sheet->setCellValue('A' . ($instructionsRow + 4), '• La contraseña se genera automáticamente (12 caracteres)');
        $sheet->setCellValue('A' . ($instructionsRow + 5), '• Los usuarios se crean con rol "Usuario" y estado "Aprobado"');
        $sheet->setCellValue('A' . ($instructionsRow + 6), '• Al finalizar la importación, se descargará un archivo TXT con las credenciales');

        // Estilo de las instrucciones
        $sheet->getStyle('A' . $instructionsRow)->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
        ]);

        return $sheet;
    }

    /**
     * Anchos de las columnas
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25,  // Nombre
            'B' => 30,  // Apellido
            'C' => 35,  // Correo
        ];
    }
}
