# TikTok-API-PHP
Unofficial TikTok API for PHP

[![GitHub issues](https://img.shields.io/github/issues/ssovit/TikTok-API-PHP?style=for-the-badge)](https://github.com/ssovit/TikTok-API-PHP/issues) [![GitHub forks](https://img.shields.io/github/forks/ssovit/TikTok-API-PHP?style=for-the-badge)](https://github.com/ssovit/TikTok-API-PHP/network) [![GitHub stars](https://img.shields.io/github/stars/ssovit/TikTok-API-PHP?style=for-the-badge)](https://github.com/ssovit/TikTok-API-PHP/stargazers) [![GitHub license](https://img.shields.io/github/license/ssovit/TikTok-API-PHP?style=for-the-badge)](https://github.com/ssovit/TikTok-API-PHP/blob/master/LICENSE) ![GitHub language count](https://img.shields.io/github/languages/count/ssovit/TikTok-API-PHP?style=for-the-badge)


# Usage
Follow examples in `/example` directory

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