<?php
header("Content-Type: application/json");
include __DIR__."/../vendor/autoload.php";
$api = new \Sovit\TikTok\Api();
$result = $api->getVideoByUrl("https://www.tiktok.com/@zachking/video/6829303572832750853");
$downloader=new \Sovit\Tiktok\Download();
$downloader->url($result->items[0]->video->playAddr,"video-file",'mp4');
