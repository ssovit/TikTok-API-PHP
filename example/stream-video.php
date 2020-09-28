<?php
header("Content-Type: application/json");
include __DIR__."/../vendor/autoload.php";
$api = new \Sovit\TikTok\Api();
$result = $api->getVideoByUrl("https://www.tiktok.com/@zachking/video/6829303572832750853");
$streamer=new \Sovit\Tiktok\Stream();
$streamer->stream($result->items[0]->video->playAddr);
