<?php
header("Content-Type: application/json");
include __DIR__ . "/../vendor/autoload.php";
$api = new \Sovit\TikTok\Api([
    "proxy-host" => "p.webshare.io",
    "proxy-port" => "80",
    "proxy-username" => "proxy-username",
    "proxy-password" => "proxy-password"
]);

$result = $api->getMusicFeed("6798898508385585925");
echo json_encode($result, JSON_PRETTY_PRINT);
