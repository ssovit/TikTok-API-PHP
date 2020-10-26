<?php
error_reporting(E_ALL);
include __DIR__."/../vendor/autoload.php";
$api = new \Sovit\TikTok\Api(['cookie_file'=>__DIR__.'/cookie.txt']);
$result = $api->getVideoByUrl("https://www.tiktok.com/@zachking/video/6829303572832750853");
$streamer=new \Sovit\TikTok\Stream(['cookie_file'=>__DIR__.'/cookie.txt']);
$streamer->stream($result->items[0]->video->playAddr);
