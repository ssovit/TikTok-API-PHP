<?php
include __DIR__."/../vendor/autoload.php";
$api = new \Sovit\TikTok\Api();
$result = $api->getVideoByUrl("https://www.tiktok.com/@zachking/video/6829303572832750853");
$downloader=new \Sovit\TikTok\Download();
$downloader->url($result->items[0]->video->playAddr,"video-file",'mp4');
