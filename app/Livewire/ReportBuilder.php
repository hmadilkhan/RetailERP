<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ReportBuilder extends Component
{
    public $availableTables = [
        'sales_receipts' => ['id', 'customer_id', 'total_amount', 'status', 'created_at'],
        'customers' => ['id', 'name', 'email'],
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
            ->join('customers', 'customers.id', '=', 'sales_receipts.customer_id');

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
