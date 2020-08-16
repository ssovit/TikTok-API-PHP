<?php
header("Content-Type: application/json");
include "../lib/TikTok.php";
$api = new \TikTok\Api();
$result = $api->getChallenge("foryourpage");
echo json_encode($result,JSON_PRETTY_PRINT);
