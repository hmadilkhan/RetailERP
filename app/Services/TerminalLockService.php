<?php

namespace App\Services;

use App\Facades\Sunmi;
use App\Models\Terminal;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TerminalLockService
{
    public function lockTerminalById(int $terminalId, int $expireDay = 60, string $screenTip = 'Device is Locked. Please pay your dues to unlock the device.'): array
    {
        $terminal = Terminal::query()->find($terminalId, ['terminal_id', 'serial_no', 'is_locked']);

        if (!$terminal) {
            return [
                'status' => 404,
                'success' => false,
                'message' => 'Terminal not found.',
            ];
        }

        if (empty($terminal->serial_no)) {
            return [
                'status' => 422,
                'success' => false,
                'message' => 'Terminal serial number is missing.',
            ];
        }

        return $this->lockTerminals(collect([$terminal]), $expireDay, $screenTip);
    }

    public function unlockTerminalById(int $terminalId): array
    {
        $terminal = Terminal::query()->find($terminalId, ['terminal_id', 'serial_no', 'is_locked']);

        if (!$terminal) {
            return [
                'status' => 404,
                'success' => false,
                'message' => 'Terminal not found.',
            ];
        }

        if (empty($terminal->serial_no)) {
            return [
                'status' => 422,
                'success' => false,
                'message' => 'Terminal serial number is missing.',
            ];
        }

        try {
            $unlock = Sunmi::unlock([
                'msn_list' => [$terminal->serial_no],
            ]);
        } catch (Exception $exception) {
            return [
                'status' => 500,
                'success' => false,
                'message' => 'Failed to unlock device: ' . $exception->getMessage(),
            ];
        }

        $rawData = $this->parseSunmiResponse($unlock);
        if (!$rawData || !isset($rawData['code']) || (int) $rawData['code'] !== 1) {
            return [
                'status' => 500,
                'success' => false,
                'message' => 'Failed to unlock device.',
                'response' => $rawData,
            ];
        }

        Terminal::query()->where('terminal_id', $terminalId)->update([
            'is_locked' => 0,
            'lock_password' => null,
        ]);

        return [
            'status' => 200,
            'success' => true,
            'message' => 'Device unlocked successfully.',
            'response' => $rawData,
        ];
    }

    public function checkTerminalStatusById(int $terminalId): array
    {
        $terminal = Terminal::query()->find($terminalId, ['terminal_id', 'serial_no']);

        if (!$terminal) {
            return [
                'status' => 404,
                'success' => false,
                'message' => 'Terminal not found.',
            ];
        }

        if (empty($terminal->serial_no)) {
            return [
                'status' => 422,
                'success' => false,
                'message' => 'Terminal serial number is missing.',
            ];
        }

        try {
            $status = Sunmi::status([
                'msn_list' => [$terminal->serial_no],
            ]);
        } catch (Exception $exception) {
            return [
                'status' => 500,
                'success' => false,
                'message' => 'Failed to fetch device status: ' . $exception->getMessage(),
            ];
        }

        $rawData = $this->parseSunmiResponse($status);
        if ($rawData && isset($rawData['code']) && (int) $rawData['code'] === 1) {
            return [
                'status' => 200,
                'success' => true,
                'message' => 'Device status fetched successfully.',
                'data' => $status,
            ];
        }

        return [
            'status' => 500,
            'success' => false,
            'message' => 'Failed to fetch device status.',
            'data' => $status,
        ];
    }

    public function lockCompanyTerminals(
        int $companyId,
        int $expireDay = 7,
        string $screenTip = 'Device is Locked. Please pay your dues to unlock the device.',
        bool $refreshAlreadyLocked = false
    ): array {
        $terminals = Terminal::query()
            ->select('terminal_details.terminal_id', 'terminal_details.serial_no', 'terminal_details.is_locked')
            ->join('branch', 'branch.branch_id', '=', 'terminal_details.branch_id')
            ->where('branch.company_id', $companyId)
            ->get();

        if ($terminals->isEmpty()) {
            return [
                'status' => 404,
                'success' => false,
                'message' => 'No terminals found for this company.',
                'locked_terminal_ids' => [],
                'already_locked_terminal_ids' => [],
                'refreshed_terminal_ids' => [],
                'skipped_terminal_ids' => [],
            ];
        }

        return $this->lockTerminals($terminals, $expireDay, $screenTip, $refreshAlreadyLocked);
    }

    private function lockTerminals(Collection $terminals, int $expireDay, string $screenTip, bool $refreshAlreadyLocked = false): array
    {
        $alreadyLockedIds = $terminals
            ->filter(fn ($terminal) => (int) ($terminal->is_locked ?? 0) === 1)
            ->pluck('terminal_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $lockableTerminals = $terminals->filter(function ($terminal) use ($refreshAlreadyLocked) {
            if (empty($terminal->serial_no)) {
                return false;
            }

            return $refreshAlreadyLocked || (int) ($terminal->is_locked ?? 0) !== 1;
        })->values();

        $refreshedTerminalIds = $refreshAlreadyLocked
            ? $lockableTerminals
                ->filter(fn ($terminal) => (int) ($terminal->is_locked ?? 0) === 1)
                ->pluck('terminal_id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all()
            : [];

        $skippedTerminalIds = $terminals
            ->filter(function ($terminal) {
                return (int) ($terminal->is_locked ?? 0) !== 1 && empty($terminal->serial_no);
            })
            ->pluck('terminal_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if ($lockableTerminals->isEmpty()) {
            return [
                'status' => 200,
                'success' => true,
                'message' => 'No unlocked terminals required locking.',
                'locked_terminal_ids' => [],
                'already_locked_terminal_ids' => $alreadyLockedIds,
                'refreshed_terminal_ids' => [],
                'skipped_terminal_ids' => $skippedTerminalIds,
            ];
        }

        $lockPassword = $this->generateLockPassword();

        try {
            $lock = Sunmi::lock([
                'passwd' => $lockPassword,
                'screen_tip' => $screenTip,
                'expire_day' => $expireDay,
                'msn_list' => $lockableTerminals->pluck('serial_no')->values()->all(),
            ]);
        } catch (Exception $exception) {
            return [
                'status' => 500,
                'success' => false,
                'passwd' => $lockPassword,
                'message' => 'Failed to lock device: ' . $exception->getMessage(),
                'locked_terminal_ids' => [],
                'already_locked_terminal_ids' => $alreadyLockedIds,
                'refreshed_terminal_ids' => $refreshedTerminalIds,
                'skipped_terminal_ids' => $skippedTerminalIds,
            ];
        }

        $rawData = $this->parseSunmiResponse($lock);
        if (!$rawData || !isset($rawData['code']) || (int) $rawData['code'] !== 1) {
            return [
                'status' => 500,
                'success' => false,
                'message' => 'Failed to lock device.',
                'locked_terminal_ids' => [],
                'already_locked_terminal_ids' => $alreadyLockedIds,
                'refreshed_terminal_ids' => $refreshedTerminalIds,
                'skipped_terminal_ids' => $skippedTerminalIds,
                'response' => $rawData,
            ];
        }

        $terminalIds = $lockableTerminals->pluck('terminal_id')->map(fn ($id) => (int) $id)->values()->all();
        $newlyLockedTerminalIds = $lockableTerminals
            ->filter(fn ($terminal) => (int) ($terminal->is_locked ?? 0) !== 1)
            ->pluck('terminal_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        Terminal::query()->whereIn('terminal_id', $terminalIds)->update([
            'is_locked' => 1,
            'lock_password' => $lockPassword,
        ]);

        return [
            'status' => 200,
            'success' => true,
            'message' => 'Device locked successfully.',
            'lock_password' => $lockPassword,
            'locked_terminal_ids' => $newlyLockedTerminalIds,
            'already_locked_terminal_ids' => $alreadyLockedIds,
            'refreshed_terminal_ids' => $refreshedTerminalIds,
            'skipped_terminal_ids' => $skippedTerminalIds,
            'response' => $rawData,
        ];
    }

    private function parseSunmiResponse($response): array
    {
        if (isset($response['data']) && is_array($response['data'])) {
            return $response['data'];
        }

        if (isset($response['http_code']) && (int) $response['http_code'] === 200 && isset($response['raw'])) {
            preg_match_all('/{[^{}]*(?:{[^{}]*}[^{}]*)*}/', $response['raw'], $matches);
            if (!empty($matches[0])) {
                $lastJson = end($matches[0]);
                $decoded = json_decode($lastJson, true);
                if ($decoded !== null) {
                    return $decoded;
                }
            }
        }

        return ['code' => 0];
    }

    private function generateLockPassword(): string
    {
        $letters = collect(range('A', 'Z'))->shuffle()->take(4)->all();
        $numbers = collect(range(0, 9))->shuffle()->take(4)->map(function ($number) {
            return (string) $number;
        })->all();

        return collect(array_merge($letters, $numbers))
            ->shuffle()
            ->implode('');
    }
}
