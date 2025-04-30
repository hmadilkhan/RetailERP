<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ReportBuilder extends Component
{
    public $availableTables = [
        'sales_receipts' => [
            // ['value' => 'sales_receipts.id', 'label' => 'Id'],
            ['value' => 'customers.name', 'label' => 'Customer Name'],
            ['value' => 'customers.email', 'label' => 'Customer Email'],
            ['value' => 'customers.phone', 'label' => 'CustomerPhone'],
            ['value' => 'customers.address', 'label' => 'Customer Address'],
            ['value' => 'user_details.fullname', 'label' => 'User Name'],
            ['value' => 'sales_receipts.receipt_no', 'label' => 'Receipt No'],
            ['value' => 'sales_receipts.opening_id', 'label' => 'Opening Id'],
            ['value' => 'sales_order_mode.order_mode', 'label' => 'Order Type'],
            ['value' => 'sales_payment.payment_mode', 'label' => 'Payment Method'],
            ['value' => 'sales_receipts.actual_amount', 'label' => 'Actual Amount'],
            ['value' => 'sales_receipts.total_amount', 'label' => 'Total Amount'],
            ['value' => 'branch.branch_name', 'label' => 'Branch'],
            ['value' => 'terminal_details.terminal_name', 'label' => 'Terminal'],
            ['value' => 'sales_receipts.date', 'label' => 'Date'],
            ['value' => 'sales_order_status.order_status_name', 'label' => 'Status'],
            ['value' => 'sales_receipts.created_at', 'label' => 'Created At']
        ],
    ];

    public array $availableFields = [
        'sales_receipts.id',
        'sales_receipts.customer_id',
        'sales_receipts.total_amount',
        'sales_receipts.branch',
        'sales_receipts.date',
        'customers.name',
        'customers.id as customer_id_alias',
    ];

    public $selectedFields = [];
    public $filters = [];
    public $groupByFields = [];
    public $calculatedFields = [];

    public $reportResults = [];

    public function addFilter()
    {
        $this->filters[] = ['field' => '', 'operator' => '=', 'value' => ''];
    }

    public function addCalculatedField()
    {
        $this->calculatedFields[] = ['name' => '', 'formula' => ''];
    }

    public function generateReport()
    {
        $query = DB::table('sales_receipts')
            ->join('customers', 'customers.id', '=', 'sales_receipts.customer_id')
            ->join('sales_order_mode', 'sales_order_mode.order_mode_id', '=', 'sales_receipts.order_mode_id')
            ->join('sales_payment', 'sales_payment.payment_id', '=', 'sales_receipts.payment_id')
            ->join('branch', 'branch.branch_id', '=', 'sales_receipts.branch')
            ->join('user_details', 'user_details.id', '=', 'sales_receipts.userid')
            ->join('sales_order_status', 'sales_order_status.order_status_id', '=', 'sales_receipts.status')
            ->join('terminal_details', 'terminal_details.terminal_id', '=', 'sales_receipts.terminal_id');

        // Apply Filters
        foreach ($this->filters as $filter) {
            if ($filter['field'] && $filter['operator'] && $filter['value']) {
                $field = trim($filter['field']); // 🔥 fix space issue
                $query->where($field, $filter['operator'], $filter['value']);
            }
        }

        // Build select fields
        $selects = $this->selectedFields;

        // Add calculated fields
        foreach ($this->calculatedFields as $calc) {
            if ($calc['name'] && $calc['formula']) {
                $formula = trim($calc['formula']);
                $selects[] = DB::raw("{$formula} AS {$calc['name']}");
            }
        }

        $query->select($selects);

        // Group By
        if (!empty($this->groupByFields)) {
            $query->groupBy($this->groupByFields);
        }

        $this->reportResults = $query->get()->toArray();
    }

    public function removeFilter($index)
    {
        unset($this->filters[$index]);
        $this->filters = array_values($this->filters); // Reindex the array
    }

    public function updatedGroupByFields($value)
    {
        $this->dispatch('updateSelect2', value: $value);
    }

    public function render()
    {
        return view('livewire.report-builder');
    }
}
