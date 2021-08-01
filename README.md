# Unofficial TikTok API library for PHP
[![GitHub issues](https://img.shields.io/github/issues/ssovit/TikTok-API-PHP?style=for-the-badge)](https://github.com/ssovit/TikTok-API-PHP/issues) ![Packagist Downloads](https://img.shields.io/packagist/dm/ssovit/tiktok-api?style=for-the-badge) ![GitHub Workflow Status](https://img.shields.io/github/workflow/status/ssovit/TikTok-API-PHP/Create%20Tag?style=for-the-badge) [![GitHub license](https://img.shields.io/github/license/ssovit/TikTok-API-PHP?style=for-the-badge)](https://github.com/ssovit/TikTok-API-PHP/blob/master/LICENSE) [![Discord](https://img.shields.io/discord/820856055936188456?color=%237289da&label=DISCORD&style=for-the-badge)](https://discord.gg/rSQd2QAXA8)

Unofficial TikTok API for PHP

# Installation via Composer
`composer require ssovit/tiktok-api`

[![Discord](https://discordapp.com/assets/e4923594e694a21542a489471ecffa50.svg)](https://discord.gg/rSQd2QAXA8)

# Looking for Watermark-less video API?
It's available on monthly subscription. See below for plans and contact details.

# Usage
Follow examples in `/example` directory

```php
$api=new \Sovit\TikTok\Api(array(/* config array*/));

$trendingFeed=$api->getTrendingFeed($maxCursor=0);

$userData=$api->getUser("tiktok");

$userFeed=$api->getUserFeed("tiktok",$maxCursor=0);

$challenge=$api->getChallenge("foryourpage");

$challengeFeed=$api->getChallengeFeed("foryourpage",$maxCursor=0);

$musc=$api->getMusic("6798898508385585925");

$musicFeed=$api->getMusicFeed("6798898508385585925",$maxCursor=0);

$videoData=$api->getVideoByID("6829540826570296577");

$videoData=$api->getVideoByUrl("https://www.tiktok.com/@zachking/video/6829303572832750853");

$noWatermark=$api->getNoWatermark("https://www.tiktok.com/@zachking/video/6829303572832750853");

```

# Available Options
```php
$api=new \Sovit\TikTok\Api(array(
	"user-agent"		=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36', // Valid desktop browser HTTP User Agent
	"proxy-host"		=> false,
	"proxy-port"		=> false,
	"proxy-username"	=> false,
	"proxy-password"	=> false,
	"cache-timeout"		=> 3600 // 1 hours cache timeout
	"cookie_file"		=> sys_get_temp_dir() . 'tiktok.txt', // cookie file path
	"nwm_endpoint"		=> "https://my-api.example.com" // private api endpoint
	"api_key"		=> "API_KEY" // see below on how to get API key
	), $cache_engine=false);
```

# Cache Engine
You can build your own engine that will store and fetch cache from your local storage to prevent frequent requests to TikTok server. This can help being banned from TikTok server for too frequent requests.

Cache engine should have callable `get` and `set` methods that the API class uses
```php
// Example using WordPress transient as cache engine
Class MyCacheEngine{
	function get($cache_key){
		return get_transient($cache_key);
	}
	function set($cache_key,$data,$timeout=3600){
		return set_transient($cache_key,$data,$timeout);
	}
}

```
**Usage**
```php
$cache_engine=new MyCacheEngine();
$api=new \Sovit\TikTok\Api(array(/* config array*/),$cache_engine);
```

# Stream and Download videos
Follow examples on `/example` folder for video stream and download example
Note: TikTok videos will not play directly when used in HTML video player as they require `www.tiktok.com` as http referrer.

# Proxy Support
To use proxy, provide `proxy-host`, `proxy-port`, `proxy-username`, `proxy-password`
It's highly recommended that you use proxy to prevent your IP from getting banned.

It's highly recommended to use `Rotating` Proxy service if you are making lots of requests in short interval of time. [Webshare.io Proxy Service](https://www.webshare.io/?referral_code=kv04mj5v4ubw) is good. *It's my referral link and I would get a bit from it*

# Available methods
- `getTrendingFeed` - Get trending feed `getTrendingFeed($maxCursor)`
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
- `getNoWatermark` - Get no watermark for video by URL `getNoWatermark($video_url)` *(only works for videos before 28th July 2020). Private API server availbale on subscription that works for all TikTok posts*

`$maxCursor` defaults to `0`, and is offset for results page. `maxCursor` for next page is exposed on current page call feed data.

# To-Do
- **Save the Universe**
- SOCKS proxy support
- Multiple proxy support (taking turns in round-robin. Act as rotating proxy)

# Non-watermarked video url for newer videos
TikTok videos don't have video id as meta data on watermarked video posted after ~24-28 July 2020. Check below for subscription plans for non watermarked video API.

## Pirvate API server subscription pricing
| Package | Cost(per month) | Quota(requests per day) | Quota (requests per month) |
| ------- | :---------------: | --------------: | -----------------: |
| **Basic** | 20 USD | 2,000 | ~60,000|
| **Pro** *(popular)* | 50 USD | 5,000 | ~150,000 |
| **Mega** | 100 USD | 12,000 | ~360,000 |
| **Ultra** | custom pricing | ? | ? |

## Looking for api source code?
Source code for TikTok Mobile App API available.

**Available variations**
- PHP *(standalone version with no external dependency)*
- NodeJs *(standalone version with no external dependency)*

# Private APP API wrapper

https://github.com/ssovit/TikTok-Private-API-PHP

# Contact
**Use issues ticket if you have questions regarding this library. Only inquiries regarding private API or custom works will be responded.**
- Telegram https://t.me/ssovit
- WhatsApp https://wa.link/odwv3x
- Discord https://discord.gg/rSQd2QAXA8
- Email sovit.tamrakar@gmail.com

# Empty results?
Use proxy. You are making too many API requests in short interval of time. Rotating proxy is recommended.

# Want to improve this library? Want to contribute?
Don't hesitate to create pull requests. 

# Disclaimer
TikTok is always updating their API endpoints but I will try to keep this library whenever possible. I take no responsibility if you or your IP gets banned using this API. It's recommended that you use proxy.