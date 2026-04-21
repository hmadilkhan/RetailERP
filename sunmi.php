<?php

require "SunmiOpenApi.php";

// ✅ Initialize Sunmi API
$sunmi = new SunmiOpenApi(
    "176b849360c9430da0be7720a7f91d8c",
    "d0a50e3292e84308ad5d9adbd346a839"
);

// ✅ Get action from URL
$action = $_GET['action'] ?? null;

// ✅ Map URL actions to class methods
$map = [
    "shutdown" => "shutdown",
    "lock"     => "lock",
    "unlock"   => "unlock",
    "remote"   => "applyControl",
    "status"   => "deviceStatus",
    "location" => "location",
    "apps"     => "apps",
    "friendList"     => "friendList",
];

if (!$action) {
    http_response_code(400);
    echo json_encode(["error" => "Action parameter is required"]);
    exit;
}

// Remove trailing slash just in case
$action = rtrim($action, "/");

if (!isset($map[$action])) {
    http_response_code(404);
    echo json_encode(["error" => "Invalid action"]);
    exit;
}

$method = $map[$action];

// ✅ Get POST JSON payload if exists
$payload = json_decode(file_get_contents("php://input"), true) ?? [];

// ✅ Fallback default payloads for lock/unlock/shutdown
$defaultPayloads = [
    "shutdown" => [
        "title"      => "Sabify",
        "content"    => "Sabify",
        "alert_type" => 0,
        "msn_list"   => ["V308231A20107"]
    ],
    "lock" => [
        "passwd"     => "Sunmi9211",
        "screen_tip" => "Device is Locked. Please pay your dues to unlock the device.",
        "expire_day" => 7,
        "msn_list"   => ["VA43258NJ0638"]//["V308231A20107"]
    ],
    "unlock" => [
        "msn_list" => ["VA43258NJ0638"]
    ],
	"remote" => [
        "msn" => "V308231A20107"
    ],
	"status" => [
        "msn_list" => ["V308231A20107"]
    ],
	"location" => [
        "msn" => "V308231A20107"
    ],
	"apps" => [
        "msn" => "V308231A20107"
    ],
	"friendList" => [],
];

// If POST payload is empty, use default
if (empty($payload) && isset($defaultPayloads[$action])) {
    $payload = $defaultPayloads[$action];
}

// ✅ Call the Sunmi API
try {
    $response = $sunmi->$method($payload);
    header("Content-Type: application/json");
    echo json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
