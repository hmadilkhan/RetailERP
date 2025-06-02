<?php

namespace App\Http\Livewire\Reports;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Terminal;
use App\Models\SaleReturn;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SaleReturnExport;
use Carbon\Carbon;

class SalesReturnReport extends Component
{
    public $dateFrom;
    public $dateTo;
    public $branch = '';
    public $terminal = 'all';
    public $results = [];
    public $isGenerating = false;
    public $branches;
    public $terminals = [];

    public function mount()
    {
        $this->branches = Branch::all();
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatedBranch($value)
    {
        if ($value) {
            $this->terminals = Terminal::where('branch_id', $value)->get();
        } else {
            $this->terminals = [];
        }
        $this->terminal = 'all';
    }

    public function generateReport()
    {
        $this->isGenerating = true;
        
        $query = SaleReturn::with(['order', 'inventory'])
            ->whereBetween('timestamp', [$this->dateFrom, $this->dateTo]);

        if ($this->branch) {
            $query->where('branch_id', $this->branch);
        }

        if ($this->terminal !== 'all') {
            $query->where('terminal_id', $this->terminal);
        }

        $this->results = $query->get();
        $this->isGenerating = false;
    }

    public function exportToExcel()
    {
        $this->isGenerating = true;
        
        $dates = [
            'from' => $this->dateFrom,
            'to' => $this->dateTo
        ];

        $companyName = config('app.name', 'Retail System');
        
        return Excel::download(
            new SaleReturnExport($this->results, $dates, $companyName),
            'sales-return-report-' . date('Y-m-d') . '.xlsx'
        );
    }

    

    public function render()
    {
        return view('livewire.reports.sales-return-report');
    }
} 