<?php

namespace App\Services\QuickBooks;

use App\Models\QuickBookSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Setting;

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
        $this->redirectUri = config('quickbooks.redirect_uri');
        $this->realmId = config('quickbooks.realm_id');
    }

    public function refreshAccessToken()
    {
        $settings = QuickBookSetting::first(); // Fetch stored tokens

        if (!$settings || !$settings->qb_refresh_token) {
            Log::error('QuickBooks: No refresh token found.');
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

            Log::info('QuickBooks: Access token refreshed successfully.');
            return $data['access_token'];
        }

        Log::error('QuickBooks: Failed to refresh access token', ['error' => $response->body()]);
        return null;
    }
}
