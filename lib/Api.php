<?php
namespace TikTok;

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
        "cache-timeout"  => 3600, // 2 hours
    ];

    public function __construct($config = [])
    {
        $this->_config = array_merge($this->defaults, $config);
        $this->cache   = new \Phpfastcache\Helper\Psr16Adapter("Files");
    }

    public function getChallenge($challenge = "")
    {
        if (empty($challenge)) {
            throw new \Exception("Invalid Challenge");
        }
        $result = $this->remote_call(self::API_BASE . "share/tag/{$challenge}");
        if (isset($result->body->challengeData)) {
            return $result->body->challengeData;
        }
        return false;
    }

    public function getChallengeFeed($challenge_id = "", $maxCursor = '0')
    {
        if (empty($challenge_id)) {
            throw new \Exception("Invalid Challenge ID");
        }
        $param = [
            "type"      => 3,
            "secUid"    => "",
            "id"        => $challenge_id,
            "count"     => 30,
            "minCursor" => "0",
            "maxCursor" => $maxCursor,
            "shareUid"  => "",
            "lang"      => "",
            "verifyFp"  => "",
        ];
        $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param));
        if (isset($result->body->itemListData)) {
            return [
                "items"     => Helper::parseData($result->body->itemListData),
                "hasMore"   => $result->body->hasMore,
                "minCursor" => $result->body->minCursor,
                "maxCursor" => $result->body->maxCursor,
            ];
        }
        return false;
    }

    public function getChallengeFeedByName($challenge_name = "", $maxCursor = '0')
    {
        if (empty($challenge_name)) {
            throw new \Exception("Invalid Challenge");
        }
        $challenge = $this->getChallenge($challenge_name);
        if ($challenge) {
            return $this->getChallengeFeed($challenge->challengeId, $maxCursor);
        }
        return false;
    }

    public function getMusic($music_id = "")
    {
        if (empty($music_id)) {
            throw new \Exception("Invalid Music ID");
        }
        $result = $this->remote_call(self::API_BASE . "share/music/x-x-{$music_id}");
        if (isset($result->body->musicData)) {
            return $result->body->musicData;
        }
        return false;
    }

    public function getMusicFeed($music_id = "", $maxCursor = '0')
    {
        if (empty($music_id)) {
            throw new \Exception("Invalid Music ID");
        }
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
        $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param));

        if (isset($result->body->itemListData)) {
            return [
                "items"     => Helper::parseData($result->body->itemListData),
                "hasMore"   => $result->body->hasMore,
                "minCursor" => $result->body->minCursor,
                "maxCursor" => $result->body->maxCursor,
            ];
        }
        return false;
    }

    public function getUser($username = "")
    {
        if (empty($username)) {
            throw new \Exception("Invalid Username");
        }
        $result = $this->remote_call(self::API_BASE . "share/user/@{$username}");
        if (isset($result->body->userData)) {
            return $result->body->userData;
        }
        return false;
    }

    public function getUserFeed($user_id = "", $maxCursor = '0')
    {
        if (empty($user_id)) {
            throw new \Exception("Invalid User ID");
        }
        $param = [
            "type"      => 1,
            "secUid"    => "",
            "id"        => $user_id,
            "count"     => 30,
            "minCursor" => "0",
            "maxCursor" => $maxCursor,
            "shareUid"  => "",
            "lang"      => "",
            "verifyFp"  => "",
        ];
        $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param));
        if (isset($result->body->itemListData)) {
            return [
                "items"     => Helper::parseData($result->body->itemListData),
                "hasMore"   => $result->body->hasMore,
                "minCursor" => $result->body->minCursor,
                "maxCursor" => $result->body->maxCursor,
            ];
        }
        return false;
    }

    public function getUserFeedByName($username = "", $maxCursor = 0)
    {
        if (empty($username)) {
            throw new \Exception("Invalid Username");
        }
        $user = $this->getUser($username);
        if ($user) {
            return $this->getUserFeed($user->userId, $maxCursor);
        }
        return false;
    }

    public function getVideoByID($video_id = "")
    {
        if (empty($video_id)) {
            throw new \Exception("Invalid VIDEO ID");
        }
        $result = $this->remote_call(self::API_BASE . "embed/render/{$video_id}");
        if (isset($result->body->videoData)) {
            return $result->body->videoData;
        }
        return false;
    }

    public function getVideoByUrl($url = "")
    {

        if (!preg_match("/https?:\/\/([^\.]+)?\.tiktok\.com/", $url)) {
            throw new \Exception("Invalid VIDEO URL");
        }
        $result      = $this->remote_call($url, false);
        $result_data = Helper::string_between($result, '{"props":{"initialProps":{', "</script>");
        if (!empty($result_data)) {
            $videoData = json_decode('{"props":{"initialProps":{' . $result_data);
            if (isset($videoData->props->pageProps->videoData)) {
                return [
                    "items"     => Helper::parseData([$videoData->props->pageProps->videoData]),
                    "hasMore"   => false,
                    "minCursor" => '0',
                    "maxCursor" => ' 0',
                ];
            }
        }
        return false;
    }

    private function remote_call($url = "", $isJson = true)
    {
        if (!is_null($this->cache->get(Helper::normalize($url)))) {
            return $this->cache->get(Helper::normalize($url));
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
        $this->cache->set(Helper::normalize($url), $data, $this->_config['cache-timeout']);
        return $data;
    }
}
