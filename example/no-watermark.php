<?php
header("Content-Type: application/json");
include __DIR__ . "/../vendor/autoload.php";
$api = new \Sovit\TikTok\Api([
    "nwm_endpoint" => "https://localhost:5000/",
    "api_key" => "some-strange-key"
]);
$result = $api->getNoWatermark("https://www.tiktok.com/@zachking/video/6918062182693424385");
echo json_encode($result, JSON_PRETTY_PRINT);
