<?php

namespace App\Exports\Crm;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesPersonPerformanceReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private readonly Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Sales Person',
            'Assigned Leads',
            'Won Leads',
            'Converted Leads',
            'Conversion Rate',
            'Pipeline Value',
        ];
    }

    public function map($row): array
    {
        return [
            $row->sales_person,
            (int) $row->total_leads,
            (int) $row->won_leads,
            (int) $row->converted_leads,
            (float) $row->conversion_rate . '%',
            (float) $row->pipeline_value,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
