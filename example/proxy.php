<?php
header("Content-Type: application/json");
include __DIR__."/../vendor/autoload.php";
$api = new \Sovit\TikTok\Api([
    'cookie_file' => __DIR__.'/tiktok.txt'
    // "proxy-host"=>"p.webshare.io",
    // "proxy-port"=>"80",
    // "proxy-username"=>"xweehaer-rotate",
    // "proxy-password"=>"64hjzvlkn5w5"
]);

$result = $api->getMusicFeed("6798898508385585925");
echo json_encode($result,JSON_PRETTY_PRINT);
