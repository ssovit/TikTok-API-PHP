<?php
header("Content-Type: application/json");
include "../lib/TikTok.php";
$api = new \TikTok\Api();
$result = $api->getMusic("6798898508385585925");
echo json_encode($result,JSON_PRETTY_PRINT);
