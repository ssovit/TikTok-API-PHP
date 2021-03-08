<?php
header("Content-Type: application/json");
include __DIR__."/../vendor/autoload.php";
$api = new \Sovit\TikTok\Api();
$result = $api->getVideoByUrl("https://www.tiktok.com/@zachking/video/6932846417820126470");
echo json_encode($result,JSON_PRETTY_PRINT);
