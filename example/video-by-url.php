<?php
header("Content-Type: application/json");
include __DIR__."/../vendor/autoload.php";
$api = new \TikTok\Api();
$result = $api->getVideoByUrl("https://www.tiktok.com/@zachking/video/6829303572832750853");
echo json_encode($result,JSON_PRETTY_PRINT);
