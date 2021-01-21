<?php
include __DIR__."/../vendor/autoload.php";
$api = new \Sovit\TikTok\Api();
$result = $api->getVideoByUrl("https://www.tiktok.com/@goodvibeskate/video/6917281259748134150");
$streamer=new \Sovit\TikTok\Stream();
$streamer->stream($result->items[0]->video->playAddr);
