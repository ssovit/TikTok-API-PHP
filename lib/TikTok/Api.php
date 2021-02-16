<?php

namespace Sovit\TikTok;

if (!\class_exists('\Sovit\TikTok\Api')) {
    class Api
    {
        const API_BASE = "https://www.tiktok.com/node/";

        private $_config = [];

        private $cache = false;

        private $cacheEnabled = false;

        private $defaults = [
            "user-agent"     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36',
            "proxy-host"     => false,
            "proxy-port"     => false,
            "proxy-username" => false,
            "proxy-password" => false,
            "cache-timeout"  => 3600, // in seconds
            "nwm_endpoint"   => false,
            "api_key"   => false
        ];
        public function __construct(array $config = [], ICacheEngine $cacheEngine = null)
        {
            $this->_config = array_merge(['cookie_file' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tiktok.txt'], $this->defaults, $config);
            if ($cacheEngine) {
                $this->cacheEnabled = true;
                $this->cache        = $cacheEngine;
            }
            if (file_exists($this->_config['cookie_file'])) {
                @touch($this->_config['cookie_file']);
            }
            // if (!file_exists($this->_config['cookie_file'])) {
            //     $this->remote_call("https://www.tiktok.com/foryou?lang=en", 'tiktok-init');
            // }
        }

        public function getChallenge($challenge = "")
        {
            if (empty($challenge)) {
                throw new \Exception("Invalid Challenge");
            }
            $challenge = urlencode($challenge);
            $result = $this->remote_call(self::API_BASE . "share/tag/{$challenge}", 'challenge-' . $challenge);
            if (isset($result->challengeInfo)) {
                return $result->challengeInfo;
            }
            return false;
        }

        public function getChallengeFeed($challenge_name = "", $maxCursor = 0)
        {
            if (empty($challenge_name)) {
                throw new \Exception("Invalid Challenge");
            }
            $challenge = $this->getChallenge($challenge_name);
            if ($challenge) {
                $param = [
                    "type"      => 3,
                    "secUid"    => "",
                    "id"        => $challenge->challenge->id,
                    "count"     => 30,
                    "minCursor" => 0,
                    "maxCursor" => $maxCursor,
                    "shareUid"  => "",
                    "lang"      => "",
                    "verifyFp"  => "",
                ];
                $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param), 'challenge-' . $challenge_name . '-' . $maxCursor);
                if (isset($result->body->itemListData)) {
                    return (object) [
                        "statusCode" => 0,
                        "info"       => (object) [
                            'type'   => 'challenge',
                            'detail' => $challenge,
                        ],
                        "items"      => Helper::parseData($result->body->itemListData),
                        "hasMore"    => @$result->body->hasMore,
                        "minCursor"  => $maxCursor,
                        "maxCursor"  => $maxCursor + 30,
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
            if (isset($result->musicInfo)) {
                return $result->musicInfo;
            }
            return false;
        }

        public function getMusicFeed($music_id = "", $maxCursor = 0)
        {
            if (empty($music_id)) {
                throw new \Exception("Invalid Music ID");
            }
            $music = $this->getMusic($music_id);
            if ($music) {
                $param = [
                    "type"      => 4,
                    "secUid"    => "",
                    "id"        => $music->music->id,
                    "count"     => 30,
                    "minCursor" => 0,
                    "maxCursor" => $maxCursor,
                    "shareUid"  => "",
                    "lang"      => "",
                    "verifyFp"  => "",
                ];
                $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param), 'music-feed-' . $music_id . '-' . $maxCursor);
                if (isset($result->body->itemListData)) {
                    return (object) [
                        "statusCode" => 0,
                        "info"       => (object) [
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

        public function getNoWatermark($url = false)
        {
            // This is old way to get non-watermarked video url for videos posted before August 2020. 
            // To obtain non-watermaked video url for newer videos, there is no easy way to so.
            // Contact me via my profile contact details to purchase a copy of my script that works with newer videos.
            if (!preg_match("/https?:\/\/([^\.]+)?\.tiktok\.com/", $url)) {
                throw new \Exception("Invalid VIDEO URL");
            }
            $data = $this->getVideoByUrl($url);
            if ($data) {
                $video = $data->items[0];
                
                if ($video->createTime < 1595894400) {
                    // only attempt to get video ID before 28th July 2020 using video id in video file meta comment
                    $ch = curl_init();

                    $options = [
                        CURLOPT_URL            => $video->video->downloadAddr,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HEADER         => false,
                        CURLOPT_HTTPHEADER     => [
                            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                            'Accept-Encoding: gzip, deflate, br',
                            'Accept-Language: en-US,en;q=0.9',
                            'Range: bytes=0-200000',
                        ],
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_USERAGENT      => 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
                        CURLOPT_ENCODING       => "utf-8",
                        CURLOPT_AUTOREFERER    => false,
                        CURLOPT_REFERER        => 'https://www.tiktok.com/',
                        CURLOPT_CONNECTTIMEOUT => 30,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_TIMEOUT        => 30,
                        CURLOPT_MAXREDIRS      => 10,
                        CURLOPT_COOKIEJAR      => $this->_config['cookie_file'],
                        CURLOPT_COOKIEFILE => $this->_config['cookie_file'],
                    ];
                    curl_setopt_array($ch, $options);
                    if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
                        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                    }
                    $data     = curl_exec($ch);
                    curl_close($ch);
                    $parts = explode("vid:", $data);
                    if (count($parts) > 1) {
                        $video_id = trim(explode("%", $parts[1])[0]);
                        return (object) [
                            "id" => $video_id,
                            "url"                 => Helper::finalUrl("https://api-h2.tiktokv.com/aweme/v1/play/?video_id={$video_id}&vr_type=0&is_play_url=1&source=PackSourceEnum_FEED&media_type=4&ratio=default&improve_bitrate=1"),
                        ];
                    }
                }
                if($this->_config['nwm_endpoint']!=false && $this->_config['api_key']!=false){
                    $result = $this->remote_call($this->_config['nwm_endpoint']."/nwm/".$video->id."?key=".$this->_config['api_key'], 'aweme-'.$video->id);
                    if($result){
                        return $result; 
                    }

                }
            }
            return false;
        }

        public function getTrendingFeed($maxCursor = 0)
        {
            $param = [
                "type"      => 5,
                "secUid"    => "",
                "id"        => 1,
                "count"     => 30,
                "minCursor" => 0,
                "maxCursor" => $maxCursor > 0 ? 1 : 0,
                "shareUid"  => "",
                "lang"      => "en",
                "verifyFp"  => "",
            ];
            $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param), 'trending-' . $maxCursor);
            if (isset($result->body->itemListData)) {
                return (object) [
                    "statusCode" => 0,
                    "info"       => (object) [
                        'type'   => 'trending',
                        'detail' => false,
                    ],
                    "items"      => Helper::parseData($result->body->itemListData),
                    "hasMore"    => @$result->body->hasMore,
                    "minCursor"  => $maxCursor,
                    "maxCursor"  => ++$maxCursor,
                ];
            }

            return false;
        }

        public function getUser($username = "")
        {
            if (empty($username)) {
                throw new \Exception("Invalid Username");
            }
            $username = urlencode($username);

            $result = $this->remote_call(self::API_BASE . "share/user/@{$username}", 'user-' . $username . '-json');
            if (isset($result->userInfo->user)) {
                return $result->userInfo;
            } else {
                $result = $this->remote_call("https://www.tiktok.com/@{$username}", 'user-' . $username, false);
                if (preg_match('/<script id="__NEXT_DATA__"([^>]+)>([^<]+)<\/script>/', $result, $matches)) {
                    $result = json_decode($matches[2], false);
                    if (isset($result->props->pageProps->userInfo)) {
                        return $result->props->pageProps->userInfo;
                    }
                }
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
                if ($feed = $this->getUserFeedByUserId($user->user->id, $maxCursor)) {
                    $feed->info->detail = $user;
                    return $feed;
                }
            }
            return false;
        }

        public function getUserFeedByUserId(int $userID, int $maxCursor = 0, int $minCursor = 0, int $items = 30)
        {
            $param = [
                'type'      => 1,
                'secUid'    => '',
                'id'        => $userID,
                'count'     => $items,
                'minCursor' => $minCursor,
                'maxCursor' => $maxCursor,
                'shareUid'  => '',
                'lang'      => '',
                'verifyFp'  => '',
            ];
            $result = $this->remote_call(
                self::API_BASE . 'video/feed?' . http_build_query($param),
                'user-feed-' . $userID . '-' . $items. '-' . $maxCursor. '-' . $minCursor
            );
            if (isset($result->body->itemListData)) {
                return (object) [
                    'statusCode' => $result->statusCode,
                    'info'       => (object) [
                        'type'   => 'user',
                        'detail' => false,
                    ],
                    'items'      => Helper::parseData($result->body->itemListData),
                    'hasMore'    => @$result->body->hasMore,
                    'minCursor'  => @$result->body->minCursor,
                    'maxCursor'  => @$result->body->maxCursor,
                ];
            }
            return false;
        }

        public function getVideoByID($video_id = "")
        {
            if (empty($video_id)) {
                throw new \Exception("Invalid VIDEO ID");
            }
            return $this->getVideoByUrl('https://m.tiktok.com/v/' . $video_id . '.html');
        }

        public function getVideoByUrl($url = "")
        {

            if (!preg_match("/https?:\/\/([^\.]+)?\.tiktok\.com/", $url)) {
                throw new \Exception("Invalid VIDEO URL");
            }
            $result      = $this->remote_call($url, Helper::normalize($url), false);
            $result = Helper::string_between($result, '{"props":{"initialProps":{', "</script>");
            if (!empty($result)) {
                $jsonData = json_decode('{"props":{"initialProps":{' . $result);
                if (isset($jsonData->props->pageProps->itemInfo->itemStruct)) {
                    return (object) [
                        'statusCode' => 0,
                        'info'       => (object) [
                            'type'   => 'video',
                            'detail' => $url,
                        ],
                        "items"      => [$jsonData->props->pageProps->itemInfo->itemStruct],
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
            if ($this->cacheEnabled) {
                if ($this->cache->get($cacheKey)) {
                    return $this->cache->get($cacheKey);
                }
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
                CURLOPT_COOKIEJAR      => $this->_config['cookie_file'],
                CURLOPT_COOKIEFILE => $this->_config['cookie_file'],
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
            if ($this->cacheEnabled) {
                $this->cache->set($cacheKey, $data, $this->_config['cache-timeout']);
            }
            return $data;
        }
        private function remote_post($url, $body = [], $headers = [], $isJson = true)
        {
            $ch      = curl_init();
            $options = [
                CURLOPT_URL            => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($body),
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
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_COOKIEJAR      => $this->_config['cookie_file'],
            ];
            curl_setopt_array($ch, $options);
            if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            }
            $data = curl_exec($ch);
            curl_close($ch);
            if ($isJson) {
                $data = json_decode($data);
            }
            return $data;
        }
    }
}
