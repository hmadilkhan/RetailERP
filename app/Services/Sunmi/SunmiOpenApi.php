<?php

namespace App\Services\Sunmi;

use Exception;

class SunmiOpenApi
{
    private string $appId;
    private string $appKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->appId   = config('sunmi.app_id');
        $this->appKey  = config('sunmi.app_key');
        $this->baseUrl = config('sunmi.base_url');
    }

    protected function call(string $endpoint, array $data): array
    {
        $url = $this->baseUrl . $endpoint;

        $timestamp = (string) time();
        $nonce     = str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_LEFT);
        $body      = json_encode($data, JSON_UNESCAPED_UNICODE);

        $sign = hash_hmac(
            'sha256',
            $body . $this->appId . $timestamp . $nonce,
            $this->appKey
        );

        $headers = [
            "Content-Type: application/json",
            "Sunmi-Appid: {$this->appId}",
            "Sunmi-Timestamp: {$timestamp}",
            "Sunmi-Nonce: {$nonce}",
            "Sunmi-Sign: {$sign}",
            "Source: openapi",
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return [
            'http_code' => $httpCode,
            'data'      => json_decode($response, true),
            'raw'       => $response,
        ];
    }

    // === Public API methods ===

    public function shutdown(array $data): array
    {
        return $this->call('cmd/shutdown', $data);
    }

    public function lock(array $data): array
    {
        return $this->call('cmd/lockDevice', $data);
    }

    public function unlock(array $data): array
    {
        return $this->call('cmd/unlockDevice', $data);
    }

    public function applyControl(array $data): array
    {
        return $this->call('device/applyControl', $data);
    }

    public function status(array $data): array
    {
        return $this->call('device/onlineStatus', $data);
    }

    public function location(array $data): array
    {
        return $this->call('device/position', $data);
    }

    public function apps(array $data): array
    {
        return $this->call('device/appList', $data);
    }

    public function friendList(array $data = []): array
    {
        return $this->call('deviceCenter/partner/getFriendList', $data);
    }
}
