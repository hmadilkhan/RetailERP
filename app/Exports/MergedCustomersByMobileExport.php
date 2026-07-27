<?php

namespace App\Exports;

use App\Customer;
use Generator;
use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class MergedCustomersByMobileExport implements FromGenerator, WithHeadings, WithMapping, WithTitle
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
        yield from $this->customer->yieldMergedCustomersByMobileForCompanyExport($this->companyId);
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
            'Customer Profile Count',
            'Customer IDs',
            'Customer Names',
            'CNIC',
            'Membership Card',
            'Address',
            'Branches (Orders via sales_receipts)',
            'Profile Branches (user authorization)',
            'Total Orders (company)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->mobile,
            $row->is_duplicate,
            $row->profile_count,
            $row->customer_ids,
            $row->names,
            $row->nics,
            $row->memberships,
            $row->addresses,
            $row->order_branches,
            $row->profile_branches,
            $row->total_orders,
        ];
    }
}
