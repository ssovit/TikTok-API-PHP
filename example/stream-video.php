<?php
error_reporting(E_ALL);
include __DIR__."/../vendor/autoload.php";
$api = new \Sovit\TikTok\Api();
$result = $api->getVideoByUrl("https://www.tiktok.com/@charlidamelio/video/6861102819454258437");
$streamer=new \Sovit\TikTok\Stream();
$streamer->stream($result->items[0]->video->playAddr);
