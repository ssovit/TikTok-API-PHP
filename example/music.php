<?php
header("Content-Type: application/json");
include __DIR__."/../vendor/autoload.php";
$api = new \SovitTikTok\Api();
$result = $api->getMusic("6798898508385585925");
echo json_encode($result,JSON_PRETTY_PRINT);
