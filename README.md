# TikTok-API-PHP
[![GitHub issues](https://img.shields.io/github/issues/ssovit/TikTok-API-PHP?style=for-the-badge)](https://github.com/ssovit/TikTok-API-PHP/issues) ![Packagist Downloads](https://img.shields.io/packagist/dm/ssovit/TikTok-API-PHP?style=for-the-badge) ![GitHub Workflow Status](https://img.shields.io/github/workflow/status/ssovit/TikTok-API-PHP/Create%20Tag?style=for-the-badge) [![GitHub license](https://img.shields.io/github/license/ssovit/TikTok-API-PHP?style=for-the-badge)](https://github.com/ssovit/TikTok-API-PHP/blob/master/LICENSE)

Unofficial TikTok API for PHP

# Installation
`composer require ssovit/tiktok-api-php`


# Usage
Follow examples in `/example` directory

```php
use TikTok\Api as TikTokApi;

$api=new TikTokApi();

$userData=$api->getUser("tiktok");

$userFeed=$api->getUserFeedByName("tiktok");

$challenge=$api->getChallenge("foryourpage");

$challengeFeed=$api->getChallengeFeedByName("foryourpage");

$musc=$api->getMusic("6798898508385585925");

$musicFeed=$api->getMusicFeed("6798898508385585925");

```

# Available methods
- `getUser` - `getUser($username)` Get profile data for TikTok User
- `getUserFeed` - Get user feed by ID `getUserFeed($user_id,$maxCursor)`
- `getUserFeedByName` - Get user feed by Name `getUserFeedByName($username,$maxCursor)`
- `getChallenge` - Get challenge/hashtag info `getChallenge($challenge)`
- `getChallengeFeed` - Get challenge feed by ID `getChallengeFeed($challenge_id, $maxCursor)`
- `getChallengeFeedByName` - Get challenge feed by name `getChallengeFeedByName($challenge,$maxCursor)`
- `getMusic` - Get music info `getMusic($music_id)`
- `getMusicFeed` - Get music feed `getMusicFeed($music_id,$maxCursor)`

`$maxCursor` defaults to `0`, and is offset for results page. `maxCursor` for next page is exposed on current page call feed object.

# Want to improve this? Want to contribute?
Don't hesitate to create pull requests.

# Disclaimer
TikTok is always updating their API endpoints and have watchdogs everywhere, and I take no responsibility if you or your IP gets banned using this API. It's recommended that you use proxy.