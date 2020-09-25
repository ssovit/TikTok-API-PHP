# TikTok-API-PHP
[![GitHub issues](https://img.shields.io/github/issues/ssovit/TikTok-API-PHP?style=for-the-badge)](https://github.com/ssovit/TikTok-API-PHP/issues) ![Packagist Downloads](https://img.shields.io/packagist/dm/ssovit/TikTok-API-PHP?style=for-the-badge) ![GitHub Workflow Status](https://img.shields.io/github/workflow/status/ssovit/TikTok-API-PHP/Create%20Tag?style=for-the-badge) [![GitHub license](https://img.shields.io/github/license/ssovit/TikTok-API-PHP?style=for-the-badge)](https://github.com/ssovit/TikTok-API-PHP/blob/master/LICENSE)

Unofficial TikTok API for PHP

# Installation
`composer require ssovit/tiktok-api`

# Usage
Follow examples in `/example` directory

```php
$api=new \SovitTikTok\Api(array());

$trendingFeed=$api->getTrendingFeed($maxCursor=0);

$userData=$api->getUser("tiktok");

$userFeed=$api->getUserFeed("tiktok",$maxCursor=0);

$challenge=$api->getChallenge("foryourpage");

$challengeFeed=$api->getChallengeFeed("foryourpage",$maxCursor=0);

$musc=$api->getMusic("6798898508385585925");

$musicFeed=$api->getMusicFeed("6798898508385585925",$maxCursor=0);

$videoData=$api->getVideoByID("6829540826570296577");

$videoData=$api->getVideoByUrl("https://www.tiktok.com/@zachking/video/6829303572832750853");

```

# Available Options
```php
$api=new \SovitTikTok\Api(array(
		"user-agent"     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36',
        "proxy-host"     => false,
        "proxy-port"     => false,
        "proxy-username" => false,
        "proxy-password" => false,
        "cache-timeout"  => 3600 // 1 hours cache timeout
        "cookie_file"  => sys_get_temp_dir() . 'tiktok.txt' // cookie file, necessary for trending feed
    ));
```

# Proxy Support
To use proxy, provide `proxy-host`, `proxy-port`, `proxy-username`, `proxy-password`
It's highly recommended that you use proxy to prevent your IP from getting banned.

# Available methods
- `getTrendingFeed` - Get trending feed `getUser($maxCursor)`
- `getUser` - Get profile data for TikTok User `getUser($username)`
- `getUserFeed` - Get user feed by ID `getUserFeed($user_id,$maxCursor)`
- `getUserFeedByName` - Get user feed by Name `getUserFeedByName($username,$maxCursor)`
- `getChallenge` - Get challenge/hashtag info `getChallenge($challenge)`
- `getChallengeFeed` - Get challenge feed by ID `getChallengeFeed($challenge_id, $maxCursor)`
- `getChallengeFeedByName` - Get challenge feed by name `getChallengeFeedByName($challenge,$maxCursor)`
- `getMusic` - Get music info `getMusic($music_id)`
- `getMusicFeed` - Get music feed `getMusicFeed($music_id,$maxCursor)`
- `getVideoByID` - Get video by ID `getVideoByID($video_id)`
- `getVideoByUrl` - Get video by URL `getVideoByUrl($video_url)`

`$maxCursor` defaults to `0`, and is offset for results page. `maxCursor` for next page is exposed on current page call feed data.

# Want to improve this? Want to contribute?
Don't hesitate to create pull requests.

# Disclaimer
TikTok is always updating their API endpoints and have watchdogs everywhere, and I take no responsibility if you or your IP gets banned using this API. It's recommended that you use proxy.