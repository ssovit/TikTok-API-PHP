<?php
header("Content-Type: application/json");
include __DIR__."/../vendor/autoload.php";
$api = new \SovitTikTok\Api();
$result = $api->getUserFeed("tiktok");
echo json_encode($result,JSON_PRETTY_PRINT);
