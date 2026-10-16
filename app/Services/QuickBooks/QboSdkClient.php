<?php

namespace App\Services\QuickBooks;

use App\Models\QuickBookSetting;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\JournalEntry;
use RuntimeException;

/** Phase 3.6: QboClient — quickbooks/v3-php-sdk, company ka token + realm (realm na ho to purana env wala). */
class QboSdkClient implements QboClient
{
    private DataService $ds;

    public function __construct(int $companyId)
    {
        $setting = QuickBookSetting::where('company_id', $companyId)->first();
        if (!$setting || !$setting->qb_access_token) {
            throw new RuntimeException('QuickBooks is not connected for this company.');
        }
        if ($setting->qb_token_expires_at && now()->greaterThan($setting->qb_token_expires_at)) {
            app(QuickBooksAuthService::class)->refreshAccessToken($setting);
            $setting->refresh();
        }
        $config = config('quickbooks');
        $this->ds = DataService::Configure([
            'auth_mode' => 'oauth2',
            'ClientID' => $config['client_id'],
            'ClientSecret' => $config['client_secret'],
            'accessTokenKey' => $setting->qb_access_token,
            'refreshTokenKey' => $setting->qb_refresh_token,
            'QBORealmID' => ($setting->realm_id ?? null) ?: $config['realm_id'],
            'baseUrl' => $config['base_url'],
        ]);
        $this->ds->throwExceptionOnError(false);
    }

    public function accounts(): array
    {
        $out = [];
        $start = 1;
        do {
            $rows = $this->ds->Query('SELECT * FROM Account WHERE Active = true', $start, 500) ?: [];
            $this->assertOk('load accounts');
            foreach ($rows as $a) {
                $out[] = ['id' => (string) $a->Id, 'name' => (string) ($a->FullyQualifiedName ?: $a->Name), 'type' => (string) $a->AccountType,
                    'number' => $a->AcctNum ? (string) $a->AcctNum : null];
            }
            $start += 500;
        } while (count($rows) === 500);
        usort($out, fn ($a, $b) => strcasecmp($a['name'], $b['name']));
        return $out;
    }

    public function findJournal(string $docNumber): ?string
    {
        $rows = $this->ds->Query("SELECT Id FROM JournalEntry WHERE DocNumber = '" . str_replace("'", "\\'", $docNumber) . "'");
        $this->assertOk('look up journal');
        return $rows ? (string) $rows[0]->Id : null;
    }

    public function createJournal(array $payload): string
    {
        $result = $this->ds->Add(JournalEntry::create($payload));
        $this->assertOk('create journal');
        if (!$result || !isset($result->Id)) {
            throw new RuntimeException('QuickBooks did not return an id.');
        }
        return (string) $result->Id;
    }

    private function assertOk(string $what): void
    {
        if ($error = $this->ds->getLastError()) {
            $body = (string) $error->getResponseBody();
            if (preg_match('/<Detail>(.*?)<\/Detail>/s', $body, $m) || preg_match('/"Detail"\s*:\s*"([^"]+)"/', $body, $m)) {
                $body = html_entity_decode($m[1]);
            }
            throw new RuntimeException('QuickBooks (' . $what . '): ' . $error->getHttpStatusCode() . ' ' . mb_substr(trim($body), 0, 400));
        }
    }
}
