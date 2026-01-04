<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ContactsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Datos de ejemplo para la plantilla
     */
    public function array(): array
    {
        return [
            [
                'Juan',
                'Pérez',
                'Ventas',
                'Gerente de Ventas',
                'juan.perez@empresa.com',
                '555-0100',
                '101',
                '555-0199',
                'activo'
            ],
            [
                'María',
                'González',
                'Recursos Humanos',
                'Coordinadora de RRHH',
                'maria.gonzalez@empresa.com',
                '555-0200',
                '202',
                '555-0299',
                'activo'
            ],
            [
                'Carlos',
                'Rodríguez',
                'Tecnología',
                'Desarrollador Senior',
                'carlos.rodriguez@empresa.com',
                '555-0300',
                '303',
                '555-0399',
                'inactivo'
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
            'Departamento',
            'Cargo',
            'Email',
            'Teléfono',
            'Extensión',
            'Móvil',
            'Estado'
        ];
    }

    /**
     * Estilos de la hoja de cálculo
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo del encabezado
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '6BA3FF'], // Color primario del sistema
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Altura de la fila del encabezado
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Agregar nota de ayuda
        $sheet->setCellValue('A' . (count($this->array()) + 3), 'INSTRUCCIONES:');
        $sheet->setCellValue('A' . (count($this->array()) + 4), '• El campo "Nombre" es obligatorio');
        $sheet->setCellValue('A' . (count($this->array()) + 5), '• El campo "Email" debe ser un email válido (opcional)');
        $sheet->setCellValue('A' . (count($this->array()) + 6), '• El campo "Estado" puede ser: activo o inactivo');
        $sheet->setCellValue('A' . (count($this->array()) + 7), '• Si se proporciona un email existente, el contacto será actualizado');
        $sheet->setCellValue('A' . (count($this->array()) + 8), '• Los demás campos son opcionales');

        // Estilo de las instrucciones
        $instructionsRow = count($this->array()) + 3;
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
            'A' => 20,  // Nombre
            'B' => 20,  // Apellido
            'C' => 25,  // Departamento
            'D' => 30,  // Cargo
            'E' => 30,  // Email
            'F' => 18,  // Teléfono
            'G' => 12,  // Extensión
            'H' => 18,  // Móvil
            'I' => 12,  // Estado
        ];
    }
}
