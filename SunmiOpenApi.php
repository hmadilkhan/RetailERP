<?php

class SunmiOpenApi
{
    private string $appId;
    private string $appKey;
    private string $baseUrl = "https://openapi.sunmi.com/v2/mdm/open/open/";

    public function __construct(string $appId, string $appKey)
    {
        $this->appId  = $appId;
        $this->appKey = $appKey;
    }

    public function call(string $endpoint, array $data): array
    {
        $url = $this->baseUrl . $endpoint;
		
        $timestamp = (string) time();
        $nonce     = str_pad(mt_rand(0, 999999), 6, "0", STR_PAD_LEFT);

        $body = json_encode($data, JSON_UNESCAPED_UNICODE);

        // 🔐 SIGN
        $msg  = $body . $this->appId . $timestamp . $nonce;
        $sign = hash_hmac("sha256", $msg, $this->appKey);

        $headers = [
            "Content-Type: application/json",
            "Sunmi-Appid: {$this->appId}",
            "Sunmi-Timestamp: {$timestamp}",
            "Sunmi-Nonce: {$nonce}",
            "Sunmi-Sign: {$sign}",
            "Source: openapi"
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		print_r($response);exit();
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("Curl Error: {$error}");
        }

        curl_close($ch);

        return [
            "http_code" => $httpCode,
            "response"  => json_decode($response, true),
            "raw"       => $response
        ];
    }
	
	public function shutdown(array $data)
	{
		return $this->call("cmd/shutdown", $data);
	}

	public function lock(array $data)
	{
		return $this->call("cmd/lockDevice", $data);
	}

	public function unlock(array $data)
	{
		return $this->call("cmd/unlockDevice", $data);
	}
	
	public function applyControl(array $data)
	{
		return $this->call("device/applyControl", $data);
	}
	
	public function deviceStatus(array $data)
	{
		return $this->call("device/onlineStatus", $data);
	}
	
	public function location(array $data)
	{
		return $this->call("device/position", $data);
	}
	
	public function apps(array $data)
	{
		return $this->call("device/appList", $data);
	}
	
	public function friendList(array $data)
	{
		return $this->call("deviceCenter/partner/getFriendList", $data);
	}
}
