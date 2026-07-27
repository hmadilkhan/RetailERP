<?php

namespace App\Exports;

use App\Customer;
use Generator;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MergedCustomersByMobileExport implements FromGenerator, WithHeadings, WithMapping, WithTitle, WithEvents
{
    protected Customer $customer;

    protected int $companyId;

    public function __construct(Customer $customer, int $companyId)
    {
        $this->customer = $customer;
        $this->companyId = $companyId;
    }

    public function generator(): Generator
    {
        yield from $this->customer->yieldCustomerDetailRowsByMobileForCompanyExport($this->companyId);
    }

    public function title(): string
    {
        return 'Customers by Mobile';
    }

    public function headings(): array
    {
        return [
            'Mobile',
            'Duplicate Mobile',
            'Customer #',
            'Customer Name',
            'CNIC',
            'Membership Card',
            'Address',
            'Branch (Orders via sales_receipts)',
            'Profile Branches',
            'Orders (this branch)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->mobile,
            $row->duplicate_label,
            $row->customer_id,
            $row->name,
            $row->nic,
            $row->membership_card_no,
            $row->address,
            $row->order_branch,
            $row->profile_branches,
            $row->orders_at_branch,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = (int) $sheet->getHighestRow();
                $lastColumn = 'J';

                if ($highestRow < 1) {
                    return;
                }

                $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '0F172A']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2E8F0'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                if ($highestRow >= 2) {
                    $sheet->getStyle('A2:' . $lastColumn . $highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CBD5E1'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                    ]);

                    // Skip heavy cell merges on very large exports (avoids timeout / memory).
                    if ($highestRow <= 25000) {
                        $groupStart = 2;
                        $previousMobile = null;

                        for ($row = 2; $row <= $highestRow + 1; $row++) {
                            $currentMobile = $row <= $highestRow
                                ? (string) $sheet->getCell('A' . $row)->getCalculatedValue()
                                : null;

                            if ($previousMobile !== null && ($row > $highestRow || $currentMobile !== $previousMobile)) {
                                $groupEnd = $row - 1;
                                if ($groupEnd > $groupStart) {
                                    $sheet->mergeCells('A' . $groupStart . ':A' . $groupEnd);
                                    $sheet->mergeCells('B' . $groupStart . ':B' . $groupEnd);
                                    $sheet->getStyle('A' . $groupStart . ':B' . $groupEnd)->applyFromArray([
                                        'alignment' => [
                                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                                            'vertical' => Alignment::VERTICAL_CENTER,
                                        ],
                                        'fill' => [
                                            'fillType' => Fill::FILL_SOLID,
                                            'startColor' => ['rgb' => 'F8FAFC'],
                                        ],
                                    ]);
                                }
                                $groupStart = $row;
                            }

                            if ($row <= $highestRow) {
                                $previousMobile = $currentMobile;
                            }
                        }

                        $customerStart = 2;
                        $previousCustomerId = null;

                        for ($row = 2; $row <= $highestRow + 1; $row++) {
                            $currentCustomerId = $row <= $highestRow
                                ? (string) $sheet->getCell('C' . $row)->getCalculatedValue()
                                : null;

                            if ($previousCustomerId !== null && ($row > $highestRow || $currentCustomerId !== $previousCustomerId)) {
                                $customerEnd = $row - 1;
                                if ($customerEnd > $customerStart) {
                                    foreach (['C', 'D', 'E', 'F', 'G', 'I'] as $col) {
                                        $sheet->mergeCells($col . $customerStart . ':' . $col . $customerEnd);
                                    }
                                }
                                $customerStart = $row;
                            }

                            if ($row <= $highestRow) {
                                $previousCustomerId = $currentCustomerId;
                            }
                        }
                    }
                }

                $sheet->getColumnDimension('A')->setWidth(16);
                $sheet->getColumnDimension('B')->setWidth(18);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(22);
                $sheet->getColumnDimension('E')->setWidth(16);
                $sheet->getColumnDimension('F')->setWidth(16);
                $sheet->getColumnDimension('G')->setWidth(28);
                $sheet->getColumnDimension('H')->setWidth(28);
                $sheet->getColumnDimension('I')->setWidth(24);
                $sheet->getColumnDimension('J')->setWidth(14);
            },
        ];
    }
}
