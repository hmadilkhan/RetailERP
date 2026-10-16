<?php

namespace App\Services\QuickBooks;

use App\Models\QuickBookSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use QuickBooksOnline\API\Core\OAuth\OAuth2\OAuth2LoginHelper;

class QuickBooksAuthService
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;
    protected $realmId;

    public function __construct()
    {
        $this->clientId = config('quickbooks.client_id');
        $this->clientSecret = config('quickbooks.client_secret');
        // config key 'redirect_url' hai (purana code 'redirect_uri' padhta tha → null)
        $this->redirectUri = config('quickbooks.redirect_url') ?: config('quickbooks.redirect_uri');
        $this->realmId = config('quickbooks.realm_id');
    }

    /**
     * Token refresh. $settings diya ho to sirf us company ka, warna har connected company ka
     * (pehle sirf pehli row refresh hoti thi). Return: aakhri naya access token.
     */
    public function refreshAccessToken(?QuickBookSetting $settings = null)
    {
        $token = null;
        foreach ($settings ? [$settings] : QuickBookSetting::all() as $row) {
            $token = $this->refreshOne($row) ?? $token;
        }
        return $token;
    }

    /** Intuit ka "Connect to QuickBooks" URL (Phase 3.6). */
    public function authorizationUrl(string $state): string
    {
        return $this->loginHelper($state)->getAuthorizationCodeURL();
    }

    /** OAuth callback: code → tokens, company ki setting me (realm ke saath) save. */
    public function connect(int $companyId, string $code, string $realmId): QuickBookSetting
    {
        $token = $this->loginHelper()->exchangeAuthorizationCodeForToken($code, $realmId);
        return QuickBookSetting::updateOrCreate(['company_id' => $companyId], [
            'qb_access_token' => $token->getAccessToken(),
            'qb_refresh_token' => $token->getRefreshToken(),
            'qb_token_expires_at' => Carbon::parse($token->getAccessTokenExpiresAt()),
            'realm_id' => $realmId,
        ]);
    }

    private function loginHelper(?string $state = null)
    {
        return new OAuth2LoginHelper($this->clientId, $this->clientSecret, $this->redirectUri, 'com.intuit.quickbooks.accounting', $state);
    }

    private function refreshOne(QuickBookSetting $settings)
    {
        if (!$settings->qb_refresh_token) {
            Log::error('QuickBooks: No refresh token found.', ['company_id' => $settings->company_id]);
            return null;
        }

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post('https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $settings->qb_refresh_token,
            ]);

        if ($response->successful()) {
            $data = $response->json();

            // Update tokens in DB
            $settings->update([
                'qb_access_token' => $data['access_token'],
                'qb_refresh_token' => $data['refresh_token'],
                'qb_token_expires_at' => Carbon::now()->addSeconds($data['expires_in']),
            ]);

            Log::info('QuickBooks: Access token refreshed successfully.', ['company_id' => $settings->company_id]);
            return $data['access_token'];
        }

        Log::error('QuickBooks: Failed to refresh access token', ['company_id' => $settings->company_id, 'error' => $response->body()]);
        return null;
    }
}
