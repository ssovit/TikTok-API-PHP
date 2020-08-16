<?php
header("Content-Type: text/plain");
include "../TikTok.php";
$api = new \TikTok\Api();
$result = $api->getUser("tiktok");
echo json_encode($result,JSON_PRETTY_PRINT);
