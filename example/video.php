<?php
header("Content-Type: application/json");
include __DIR__."/../vendor/autoload.php";
$api = new \TikTok\Api();
$result = $api->getVideoByID("6829540826570296577");
echo json_encode($result,JSON_PRETTY_PRINT);
