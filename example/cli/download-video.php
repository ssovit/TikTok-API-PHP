#!/usr/bin/php

<?php
/**
 * Usage example :
 * ./download-video.php https://www.tiktok.com/@zachking/video/6829303572832750853 my-file.mp4
 */

require __DIR__."/../../vendor/autoload.php";
$api = new \Sovit\TikTok\Api();
$result = $api->getVideoByUrl($argv[1]);
$downloader=new \Sovit\TikTok\Download();

file_put_contents($argv[2], $downloader->fetch_content($result->items[0]->video->playAddr));
