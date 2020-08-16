<?php
header("Content-Type: application/json");
include "../TikTok.php";
$api = new \TikTok\Api();
$result = $api->getUserFeedByName("tiktok");
echo json_encode($result,JSON_PRETTY_PRINT);
