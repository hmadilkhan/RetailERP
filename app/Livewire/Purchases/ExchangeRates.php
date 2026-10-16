<?php

namespace App\Livewire\Purchases;

use App\Services\Purchases\CurrencyService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use RuntimeException;

/** Phase 4.5: exchange rates (foreign currency vendor bills ke liye). */
class ExchangeRates extends Component
{
    public $currency = 'USD';
    public $rateDate = '';
    public $rate = '';
    public $history = '';

    public function mount(): void
    {
        $this->rateDate = date('Y-m-d');
    }

    public function save(): void
    {
        abort_unless(in_array((int) session('roleId'), [1, 2, 17], true), 403);
        $this->resetErrorBag();
        try {
            app(CurrencyService::class)->setRate((int) session('company_id'), (string) $this->currency, (string) $this->rateDate, $this->rate, session('userid'));
        } catch (RuntimeException $e) {
            $this->addError('fx', $e->getMessage());
            return;
        }
        $this->history = strtoupper(trim($this->currency));
        $this->rate = '';
        session()->flash('fx_message', 'Rate saved.');
    }

    public function delete(int $id): void
    {
        abort_unless(in_array((int) session('roleId'), [1, 2, 17], true), 403);
        DB::table('exchange_rates')->where('company_id', session('company_id'))->where('id', $id)->delete();
    }

    #[Title('Exchange Rates')]
    public function render()
    {
        $companyId = (int) session('company_id');
        $svc = app(CurrencyService::class);
        $ready = $svc->ready();
        return view('livewire.purchases.exchange-rates', [
            'ready' => $ready,
            'base' => $svc->base($companyId),
            'common' => array_values(array_diff(CurrencyService::COMMON, [$svc->base($companyId)])),
            'latest' => $ready ? $svc->latest($companyId) : collect(),
            'rows' => $ready && $this->history !== '' ? DB::table('exchange_rates')->where('company_id', $companyId)->where('currency', $this->history)
                ->orderByDesc('rate_date')->limit(60)->get() : collect(),
            'canEdit' => in_array((int) session('roleId'), [1, 2, 17], true),
        ])->layout('layouts.master-tailwind');
    }
}
