<?php
header("Content-Type: application/json");
include __DIR__."/../vendor/autoload.php";
$api = new \TikTok\Api();
$result = $api->getMusicFeed("6798898508385585925");
echo json_encode($result,JSON_PRETTY_PRINT);
