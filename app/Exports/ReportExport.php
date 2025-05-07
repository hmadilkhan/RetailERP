<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $selectedFields;
    protected $availableTables;

    public function __construct($data, $selectedFields, $availableTables)
    {
        $this->data = $data;
        $this->selectedFields = $selectedFields;
        $this->availableTables = $availableTables;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        $headings = [];
        foreach ($this->selectedFields as $field) {
            if (!str_contains($field, 'sales_receipt_details.') && !str_contains($field, 'inventory_general.')) {
                $label = '';
                foreach ($this->availableTables as $table => $fields) {
                    foreach ($fields as $fieldInfo) {
                        if ($fieldInfo['value'] === $field) {
                            $label = $fieldInfo['label'];
                            break 2;
                        }
                    }
                }
                $headings[] = $label;
            }
        }
        return $headings;
    }

    public function map($row): array
    {
        $data = [];
        foreach ($this->selectedFields as $field) {
            if (!str_contains($field, 'sales_receipt_details.') && !str_contains($field, 'inventory_general.')) {
                $fieldName = explode('.', $field)[1] ?? $field;
                $data[] = $row->$fieldName ?? '';
            }
        }
        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
} 