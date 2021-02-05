<?php
header("Content-Type: application/json");
include __DIR__."/../vendor/autoload.php";
$api = new \Sovit\TikTok\Api();
$result = $api->getNoWatermark("https://www.tiktok.com/@zachking/video/6782932477259336966");
echo json_encode($result,JSON_PRETTY_PRINT);
