<?php

namespace App\Exports\Crm;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeadListExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function __construct(private readonly Collection $leads)
    {
    }

    public function collection(): Collection
    {
        return $this->leads;
    }

    public function headings(): array
    {
        return [
            'Lead ID',
            'Contact Person',
            'Company',
            'Phone',
            'Email',
            'Source',
            'Product Type',
            'Product',
            'Status',
            'Priority',
            'Assigned To',
            'Next Follow-up Date',
            'Created Date',
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->lead_code,
            $lead->contact_person_name,
            $lead->company_name,
            $lead->contact_number,
            $lead->email,
            $lead->leadSource?->name,
            $lead->productType?->name,
            $lead->product?->name,
            $lead->status?->name,
            ucfirst((string) $lead->priority),
            $lead->assignedUser?->fullname,
            optional($lead->next_followup_date)?->format('Y-m-d'),
            optional($lead->created_at)?->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
