<?php
namespace SovitTikTok;

class Api
{
    const API_BASE = "https://www.tiktok.com/node/";

    private $_config = [];

    private $cache = false;

    private $defaults = [
        "user-agent"     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Safari/537.36',
        "proxy-host"     => false,
        "proxy-port"     => false,
        "proxy-username" => false,
        "proxy-password" => false,
        "cache-timeout"  => 3600, // in seconds

    ];

    public function __construct($config = [], $cacheEngine = false)
    {
        $this->_config = array_merge($this->defaults, $config);
        if ($cacheEngine) {
            $this->cache = $cacheEngine;
        }
    }

    public function getChallenge($challenge = "")
    {
        if (empty($challenge)) {
            throw new \Exception("Invalid Challenge");
        }
        $result = $this->remote_call(self::API_BASE . "share/tag/{$challenge}", 'challenge-' . $challenge);
        if (isset($result->body->challengeData)) {
            return [
                'coverLarger'   => @$result->body->challengeData->coversMedium[0],
                'coverMedium'   => @$result->body->challengeData->coversMedium[0],
                'coverThumb'    => @$result->body->challengeData->covers[0],
                'desc'          => @$result->body->challengeData->text,
                'id'            => @$result->body->challengeData->challengeId,
                'isCommerce'    => @$result->body->challengeData->isCommerce,
                'profileLarger' => @$result->body->challengeData->coversMedium[0],
                'profileMedium' => @$result->body->challengeData->coversMedium[0],
                'profileThumb'  => @$result->body->challengeData->covers[0],
                'title'         => @$result->body->challengeData->challengeName,
                "stats"         => [
                    'videoCount' => @$result->body->challengeData->posts,
                    'viewCount'  => @$result->body->challengeData->views],

            ];
        }
        return false;
    }

    public function getChallengeFeed($challenge_name = "", $maxCursor = '0')
    {
        if (empty($challenge_name)) {
            throw new \Exception("Invalid Challenge");
        }
        $challenge = $this->getChallenge($challenge_name);
        if ($challenge) {
            $param = [
                "type"      => 3,
                "secUid"    => "",
                "id"        => $challenge['id'],
                "count"     => 30,
                "minCursor" => "0",
                "maxCursor" => $maxCursor,
                "shareUid"  => "",
                "lang"      => "",
                "verifyFp"  => "",
            ];
            $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param), 'challenge-' . $challenge_name . '-' . $maxCursor);
            if (isset($result->body->itemListData)) {
                return [
                    "statusCode" => 0,
                    "info"       => [
                        'type'   => 'challenge',
                        'detail' => $challenge,
                    ],
                    "items"      => Helper::parseData($result->body->itemListData),
                    "hasMore"    => @$result->body->hasMore,
                    "minCursor"  => @$result->body->minCursor,
                    "maxCursor"  => @$result->body->maxCursor,
                ];
            }
        }
        return false;
    }

    public function getMusic($music_id = "")
    {
        if (empty($music_id)) {
            throw new \Exception("Invalid Music ID");
        }
        $result = $this->remote_call(self::API_BASE . "share/music/original-sound-{$music_id}", 'music-' . $music_id);
        if (isset($result->body->musicData)) {
            return [
                'authorName'  => @$result->body->musicData->authorName,
                'coverLarge'  => @$result->body->musicData->coversMedium[0],
                'coverMedium' => @$result->body->musicData->coversMedium[0],
                'coverThumb'  => @$result->body->musicData->covers[0],
                'id'          => @$result->body->musicData->musicId,
                'original'    => @$result->body->musicData->original,
                'playUrl'     => @$result->body->musicData->playUrl->UrlList[0],
                'private'     => @$result->body->musicData->private,
                'title'       => @$result->body->musicData->musicName,
                'stats'       => [
                    'videoCount' => @$result->body->musicData->posts],
            ];
        }
        return false;
    }

    public function getMusicFeed($music_id = "", $maxCursor = '0')
    {
        if (empty($music_id)) {
            throw new \Exception("Invalid Music ID");
        }
        $music = $this->getMusic($music_id);
        if ($music) {
            $param = [
                "type"      => 4,
                "secUid"    => "",
                "id"        => $music_id,
                "count"     => 30,
                "minCursor" => "0",
                "maxCursor" => $maxCursor,
                "shareUid"  => "",
                "lang"      => "",
                "verifyFp"  => "",
            ];
            $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param), 'music-feed-' . $music_id . '-' . $maxCursor);
            if (isset($result->body->itemListData)) {
                return [
                    "statusCode" => 0,
                    "info"       => [
                        'type'   => 'music',
                        'detail' => $music,
                    ],
                    "items"      => Helper::parseData($result->body->itemListData),
                    "hasMore"    => @$result->body->hasMore,
                    "minCursor"  => @$result->body->minCursor,
                    "maxCursor"  => @$result->body->maxCursor,
                ];
            }
        }
        return false;
    }

    public function getUser($username = "")
    {
        if (empty($username)) {
            throw new \Exception("Invalid Username");
        }
        $result = $this->remote_call(self::API_BASE . "share/user/@{$username}", 'user-' . $username);
        if (isset($result->body->userData)) {
            return [
                'avatarLarger' => @$result->body->userData->coversMedium[0],
                'avatarMedium' => @$result->body->userData->coversMedium[0],
                'avatarThumb'  => @$result->body->userData->covers[0],
                'id'           => @$result->body->userData->userId,
                'nickname'     => @$result->body->userData->nickName,
                'openFavorite' => @$result->body->userData->openFavorite,
                'relation'     => @$result->body->userData->relation,
                'secUid'       => @$result->body->userData->secUid,
                'secret'       => @$result->body->userData->isSecret,
                'signature'    => @$result->body->userData->signature,
                'uniqueId'     => @$result->body->userData->uniqueId,
                'verified'     => @$result->body->userData->verified,
                'stats'        => [
                    'diggCount'      => @$result->body->userData->digg,
                    'followerCount'  => @$result->body->userData->fans,
                    'followingCount' => @$result->body->userData->following,
                    'heart'          => @$result->body->userData->heart,
                    'heartCount'     => @$result->body->userData->heart,
                    'videoCount'     => @$result->body->userData->video,
                ],
            ];
        }
        return false;
    }

    public function getUserFeed($username = "", $maxCursor = 0)
    {
        if (empty($username)) {
            throw new \Exception("Invalid Username");
        }
        $user = $this->getUser($username);
        if ($user) {
            $param = [
                "type"      => 1,
                "secUid"    => "",
                "id"        => $user['id'],
                "count"     => 30,
                "minCursor" => "0",
                "maxCursor" => $maxCursor,
                "shareUid"  => "",
                "lang"      => "",
                "verifyFp"  => "",
            ];
            $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param), 'user-feed-' . $username . '-' . $maxCursor);
            if (isset($result->body->itemListData)) {
                return [
                    "statusCode" => 0,
                    "info"       => [
                        'type'   => 'user',
                        'detail' => $user,
                    ],
                    "items"      => Helper::parseData($result->body->itemListData),
                    "hasMore"    => @$result->body->hasMore,
                    "minCursor"  => @$result->body->minCursor,
                    "maxCursor"  => @$result->body->maxCursor,
                ];
            }
        }
        return false;
    }

    public function getVideoByID($video_id = "")
    {
        if (empty($video_id)) {
            throw new \Exception("Invalid VIDEO ID");
        }
        $result = $this->remote_call(self::API_BASE . "embed/render/{$video_id}", 'video-' . $video_id);
        if (isset($result->body->videoData)) {
            return [
                'statusCode' => 0,
                'info'       => [
                    'type'   => 'video',
                    'detail' => 'https://m.tiktok.com/v/' . $video_id . '.html',
                ],
                "items"      => Helper::parseData([$result->body->videoData]),
                "hasMore"    => false,
                "minCursor"  => '0',
                "maxCursor"  => ' 0',
            ];
        }
        return false;
    }

    public function getVideoByUrl($url = "")
    {

        if (!preg_match("/https?:\/\/([^\.]+)?\.tiktok\.com/", $url)) {
            throw new \Exception("Invalid VIDEO URL");
        }
        $result      = $this->remote_call($url, Helper::normalize($url), false);
        $result_data = Helper::string_between($result, '{"props":{"initialProps":{', "</script>");
        if (!empty($result_data)) {
            $videoData = json_decode('{"props":{"initialProps":{' . $result_data);
            if (isset($videoData->props->pageProps->videoData)) {
                return [
                    'statusCode' => 0,
                    'info'       => [
                        'type'   => 'video',
                        'detail' => $url,
                    ],
                    "items"      => Helper::parseData([$videoData->props->pageProps->videoData]),
                    "hasMore"    => false,
                    "minCursor"  => '0',
                    "maxCursor"  => ' 0',
                ];
            }
        }
        return false;
    }

    private function remote_call($url = "", $cacheKey = false, $isJson = true)
    {
        if ($this->cache && !is_null($this->cache->get($cacheKey))) {
            return $this->cache->get($cacheKey);
        }
        $ch      = curl_init();
        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => $this->_config['user-agent'],
            CURLOPT_ENCODING       => "utf-8",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_HTTPHEADER     => [
                'Referer: https://www.tiktok.com/foryou?lang=en',
            ],
        ];
        curl_setopt_array($ch, $options);
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        if ($this->_config['proxy-host'] && $this->_config['proxy-port']) {
            curl_setopt($ch, CURLOPT_PROXY, $this->_config['proxy-host'] . ":" . $this->_config['proxy-port']);
            if ($this->_config['proxy-username'] && $this->_config['proxy-password']) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->_config['proxy-username'] . ":" . $this->_config['proxy-password']);
            }
        }
        $data = curl_exec($ch);
        curl_close($ch);
        if ($isJson) {
            $data = json_decode($data);
        }
        if ($this->cache) {
            $this->cache->set($cacheKey, $data, $this->_config['cache-timeout']);
        }
        return $data;
    }
}
