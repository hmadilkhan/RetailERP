<?php

namespace App\Livewire\Accounting;

use App\Models\Accounting\AccountingSetting;
use App\Services\Accounting\BankReconciliationService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;

/** Phase 3.1: bank statement ↔ GL bank account. Qaide BankReconciliationService me. */
class BankReconciliation extends Component
{
    use WithFileUploads;

    public ?int $viewId = null;

    // naya statement
    public $newAccount = '';
    public $newFrom = '';
    public $newTo = '';
    public $newOpening = '';
    public $newClosing = '';

    // statement line
    public $lineDate = '';
    public $lineDesc = '';
    public $lineRef = '';
    public $lineAmount = '';
    public $file;
    public $matchDays = 3;

    // matching
    public ?int $selectedLine = null;
    public array $selectedGl = []; // gl line id => true

    // bank ki entry
    public ?int $entryLine = null;
    public $entryAccount = '';
    public $entryNarration = '';

    public function mount(): void
    {
        abort_unless(AccountingSetting::isEnabled(session('company_id')), 403, 'Accounting is not enabled for this company.');
        $this->newFrom = date('Y-m-01', strtotime('first day of last month'));
        $this->newTo = date('Y-m-t', strtotime('last day of last month'));
        if ($id = (int) request()->query('rec')) {
            $this->viewId = $id;
        }
    }

    public function create(): void
    {
        $this->validate([
            'newAccount' => 'required|integer', 'newFrom' => 'required|date', 'newTo' => 'required|date',
            'newOpening' => 'required|numeric', 'newClosing' => 'required|numeric',
        ], [], ['newAccount' => 'bank account', 'newFrom' => 'from', 'newTo' => 'to', 'newOpening' => 'opening balance', 'newClosing' => 'closing balance']);
        $this->run(function () {
            $this->viewId = $this->svc()->create($this->company(), (int) $this->newAccount, $this->newFrom, $this->newTo,
                (float) $this->newOpening, (float) $this->newClosing, session('userid'));
            $this->newOpening = $this->newClosing = '';
            $this->lineDate = $this->newFrom;
        }, 'Statement created. Import the bank statement or add its lines, then Auto-match.');
    }

    public function show(int $id): void
    {
        $this->resetMatching();
        $this->viewId = $id;
        $rec = DB::table('bank_reconciliations')->where('company_id', $this->company())->find($id);
        $this->lineDate = $rec->statement_from ?? '';
    }

    public function back(): void
    {
        $this->resetMatching();
        $this->viewId = null;
    }

    public function addLine(): void
    {
        $this->validate(['lineDate' => 'required|date', 'lineAmount' => 'required|numeric'], [], ['lineDate' => 'date', 'lineAmount' => 'amount']);
        $this->run(function () {
            $this->svc()->addLine($this->company(), $this->viewId, $this->lineDate, $this->lineDesc, $this->lineRef, (float) $this->lineAmount);
            $this->lineDesc = $this->lineRef = $this->lineAmount = '';
        }, null);
    }

    public function deleteLine(int $id): void
    {
        $this->run(fn () => $this->svc()->deleteLine($this->company(), $this->viewId, $id), null);
    }

    public function upload(): void
    {
        $this->validate(['file' => 'required|file|max:4096|mimes:csv,txt,xlsx,xls'], [], ['file' => 'statement file']);
        $this->run(function () {
            $path = $this->file->getRealPath();
            $ext = strtolower($this->file->getClientOriginalExtension());
            if (in_array($ext, ['xlsx', 'xls'], true)) {
                $sheets = \Maatwebsite\Excel\Facades\Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                    public function array(array $array): void
                    {
                    }
                }, $path);
                $rows = $sheets[0] ?? [];
            } else {
                $rows = [];
                $fh = fopen($path, 'r');
                while (($r = fgetcsv($fh)) !== false) {
                    $rows[] = array_map(fn ($v) => preg_replace('/^\xEF\xBB\xBF/', '', (string) $v), $r);
                }
                fclose($fh);
            }
            [$added, $skipped, $notes] = $this->svc()->import($this->company(), $this->viewId, $rows);
            $this->file = null;
            session()->flash('br_message', $added . ' line(s) imported' . ($skipped ? ', ' . $skipped . ' skipped' : '') . '.' . ($notes ? ' ' . implode(' ', $notes) : ''));
        }, null);
    }

    public function autoMatch(): void
    {
        $this->run(function () {
            $n = $this->svc()->autoMatch($this->company(), $this->viewId, max(0, min(15, (int) $this->matchDays)));
            session()->flash('br_message', $n . ' line(s) matched automatically.');
        }, null);
    }

    public function selectLine(int $id): void
    {
        $this->selectedLine = $this->selectedLine === $id ? null : $id;
        $this->entryLine = null;
    }

    public function match(): void
    {
        $this->run(function () {
            $this->svc()->match($this->company(), $this->viewId, (int) $this->selectedLine, array_keys(array_filter($this->selectedGl)));
            $this->resetMatching();
        }, 'Matched.');
    }

    public function unmatch(int $lineId): void
    {
        $this->run(fn () => $this->svc()->unmatch($this->company(), $this->viewId, $lineId), null);
    }

    public function startEntry(int $lineId): void
    {
        $this->entryLine = $lineId;
        $this->selectedLine = null;
        $this->entryAccount = (string) (DB::table('chart_of_accounts')->where('company_id', $this->company())->where('system_key', 'bank_charges')->value('id') ?? '');
        $this->entryNarration = '';
    }

    public function createEntry(): void
    {
        $this->validate(['entryAccount' => 'required|integer'], [], ['entryAccount' => 'account']);
        $this->run(function () {
            $this->svc()->createEntry($this->company(), $this->viewId, (int) $this->entryLine, (int) $this->entryAccount, $this->entryNarration, session('userid'));
            $this->entryLine = null;
        }, 'Entry posted to the ledger and matched.');
    }

    public function finish(): void
    {
        $this->run(fn () => $this->svc()->finish($this->company(), $this->viewId, session('userid')), 'Statement reconciled.');
    }

    public function reopen(): void
    {
        abort_unless(in_array((int) session('roleId'), [1, 2], true), 403);
        $this->run(fn () => $this->svc()->reopen($this->company(), $this->viewId), 'Reopened.');
    }

    public function deleteRec(): void
    {
        $this->run(function () {
            $this->svc()->delete($this->company(), $this->viewId);
            $this->viewId = null;
        }, 'Statement deleted.');
    }

    #[Title('Bank Reconciliation')]
    public function render()
    {
        $svc = $this->svc();
        $companyId = $this->company();
        $rec = $this->viewId ? DB::table('bank_reconciliations')->where('company_id', $companyId)->find($this->viewId) : null;
        $data = [
            'rec' => $rec,
            'banks' => $svc->bankAccounts($companyId),
            'list' => $rec ? collect() : DB::table('bank_reconciliations as r')->join('chart_of_accounts as a', 'a.id', '=', 'r.account_id')
                ->where('r.company_id', $companyId)->orderByDesc('r.statement_to')->orderByDesc('r.id')->limit(100)
                ->get(['r.*', 'a.code', 'a.name as account_name']),
        ];
        if ($rec) {
            $summary = $svc->summary($rec);
            $lines = DB::table('bank_statement_lines as s')->where('s.reconciliation_id', $rec->id)
                ->leftJoin('bank_matches as m', 'm.statement_line_id', '=', 's.id')
                ->leftJoin('journal_entry_lines as l', 'l.id', '=', 'm.journal_entry_line_id')
                ->leftJoin('journal_entries as e', 'e.id', '=', 'l.journal_entry_id')
                ->groupBy('s.id', 's.txn_date', 's.description', 's.reference', 's.amount', 's.journal_entry_id')
                ->orderBy('s.txn_date')->orderBy('s.id')
                ->selectRaw("s.id, s.txn_date, s.description, s.reference, s.amount, s.journal_entry_id, COUNT(m.id) AS matches, GROUP_CONCAT(e.entry_no ORDER BY e.entry_no SEPARATOR ', ') AS entries")
                ->get();
            $gl = $svc->openGlLines($rec);
            $selectedSum = $gl->whereIn('id', array_keys(array_filter($this->selectedGl)))->sum('amount');
            $data += [
                'summary' => $summary,
                'lines' => $lines,
                'gl' => $gl,
                'selectedSum' => round((float) $selectedSum, 2),
                'selectedAmount' => $this->selectedLine ? (float) $lines->firstWhere('id', $this->selectedLine)?->amount : null,
                'account' => DB::table('chart_of_accounts')->find($rec->account_id),
                'contraAccounts' => DB::table('chart_of_accounts')->where('company_id', $companyId)->where('is_group', 0)->where('is_active', 1)
                    ->where('id', '<>', $rec->account_id)->orderBy('code')->get(['id', 'code', 'name', 'type']),
            ];
        }
        return view('livewire.accounting.bank-reconciliation', $data)->layout('layouts.master-tailwind');
    }

    private function resetMatching(): void
    {
        $this->resetErrorBag();
        $this->selectedLine = $this->entryLine = null;
        $this->selectedGl = [];
    }

    private function svc(): BankReconciliationService
    {
        return app(BankReconciliationService::class);
    }

    private function company(): int
    {
        return (int) session('company_id');
    }

    private function run(callable $fn, ?string $message): void
    {
        $this->resetErrorBag();
        try {
            $fn();
        } catch (RuntimeException $e) {
            $this->addError('br', $e->getMessage());
            return;
        }
        if ($message) {
            session()->flash('br_message', $message);
        }
    }
}
