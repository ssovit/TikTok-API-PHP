<?php
header("Content-Type: application/json");
include __DIR__."/../vendor/autoload.php";
$api = new \TikTok\Api();
$result = $api->getUserFeedByName("tiktok");
echo json_encode($result,JSON_PRETTY_PRINT);
