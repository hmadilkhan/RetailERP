<?php

namespace App\Services\Easypaisa;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use RuntimeException;
use Throwable;

class EasypaisaService
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private string $storeId;
    private string $accountNum;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.easypaisa.base_url'), '/');
        $this->username = config('services.easypaisa.username');
        $this->password = config('services.easypaisa.password');
        $this->storeId = (string) config('services.easypaisa.store_id');
        $this->accountNum = (string) config('services.easypaisa.account_num');
        $this->timeout = (int) config('services.easypaisa.timeout', 30);
    }

    public function initiateOtc(array $data): array
    {
        $payload = [
            'orderId' => $data['order_id'] ?? $this->makeOrderId('OTC'),
            'storeId' => $data['store_id'] ?? $this->storeId,
            'transactionAmount' => number_format((float) $data['amount'], 2, '.', ''),
            'transactionType' => 'OTC',
            'msisdn' => $data['msisdn'],
            'emailAddress' => $data['email'],
            'tokenExpiry' => $data['token_expiry'] ?? Carbon::now()->addDay()->format('Ymd His'),
        ];

        return $this->post('/initiate-otc-transaction', $payload);
    }

    public function initiateMa(array $data): array
    {
        $payload = [
            'orderId' => $data['order_id'] ?? $this->makeOrderId('MA'),
            'storeId' => $data['store_id'] ?? $this->storeId,
            'transactionAmount' => number_format((float) $data['amount'], 2, '.', ''),
            'transactionType' => 'MA',
            'mobileAccountNo' => $data['mobile_account_no'],
            'emailAddress' => $data['email'],
        ];

        return $this->post('/initiate-ma-transaction', $payload);
    }

    public function inquireTransaction(array $data): array
    {
        $payload = [
            'orderId' => $data['order_id'],
            'storeId' => $data['store_id'] ?? $this->storeId,
            'accountNum' => $data['account_num'] ?? $this->accountNum,
        ];

        return $this->post('/inquire-transaction', $payload);
    }

    private function post(string $endpoint, array $payload): array
    {
        try {
            $response = Http::withHeaders([
                    'Credentials' => $this->credentialsHeader(),
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->timeout($this->timeout)
                ->post($this->baseUrl . $endpoint, $payload);

            $json = $response->json();

            if (! $response->successful()) {
                throw new RuntimeException('Easypaisa HTTP Error: ' . $response->status() . ' - ' . $response->body());
            }

            return [
                'success' => ($json['responseCode'] ?? null) === '0000',
                'request' => $payload,
                'response' => $json,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'request' => $payload,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function credentialsHeader(): string
    {
        return base64_encode($this->username . ':' . $this->password);
    }

    private function makeOrderId(string $prefix): string
    {
        return $prefix . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(5));
    }
}
