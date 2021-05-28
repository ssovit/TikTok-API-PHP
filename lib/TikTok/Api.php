<?php

namespace Sovit\TikTok;

if (!\class_exists('\Sovit\TikTok\Api')) {
    /**
     * TikTok API Class
     */
    class Api
    {
        /**
         * API Base url
         * @var String
         */
        const API_BASE = "https://www.tiktok.com/node/";
        /**
         * Config
         *
         * @var Array
         */
        private $_config = [];
        /**
         * Cache Engine
         *
         * @var Object
         */
        private $cacheEngine;
        /**
         * If Cache is enabled
         *
         * @var boolean
         */
        private $cacheEnabled = false;
        /**
         * Default config
         *
         * @var array
         */
        private $defaults = [
            "user-agent"     => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36',
            "proxy-host"     => false,
            "proxy-port"     => false,
            "proxy-username" => false,
            "proxy-password" => false,
            "cache-timeout"  => 3600,
            "nwm_endpoint"   => false,
            "api_key"   => false
        ];
        /**
         * Class Constructor
         *
         * @param array $config API Config
         * @param boolean $cacheEngine
         * @return void
         */
        public function __construct($config = array(), $cacheEngine = false)
        {
            /**
             * Initialize the config array
             */
            $this->_config = array_merge(['cookie_file' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tiktok.txt'], $this->defaults, $config);
            /**
             * If Cache Engine is enabled
             */
            if ($cacheEngine) {
                $this->cacheEnabled = true;
                $this->cacheEngine        = $cacheEngine;
            }
        }
        /**
         * Get Challenge function
         * Accepts challenge name and returns challenge detail object or false on failure
         *
         * @param string $challenge
         * @return object
         */
        public function getChallenge($challenge = "")
        {
            /**
             * Check if challenge is not empty
             */
            if (empty($challenge)) {
                throw new \Exception("Invalid Challenge");
            }
            $cacheKey = 'challenge-' . $challenge;
            if ($this->cacheEnabled) {
                if ($this->cacheEngine->get($cacheKey)) {
                    return $this->cacheEngine->get($cacheKey);
                }
            }
            $challenge = urlencode($challenge);
            $result = $this->remote_call(self::API_BASE . "share/tag/{$challenge}");
            if (isset($result->challengeInfo)) {
                $result = $result->challengeInfo;
                if ($this->cacheEnabled) {
                    $this->cacheEngine->set($cacheKey, $result, $this->_config['cache-timeout']);
                }
                return $result;
            }
            return $this->failure();
        }
        /**
         * Get Challenge Feed
         * Accepts challenge name and returns challenge feed object or false on faliure
         *
         * @param string $challenge_name
         * @param integer $maxCursor
         * @return object
         */
        public function getChallengeFeed($challenge_name = "", $maxCursor = 0)
        {
            if (empty($challenge_name)) {
                throw new \Exception("Invalid Challenge");
            }
            $cacheKey = 'challenge-' . $challenge_name . '-' . $maxCursor;
            if ($this->cacheEnabled) {
                if ($this->cacheEngine->get($cacheKey)) {
                    return $this->cacheEngine->get($cacheKey);
                }
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
                $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param));
                if (isset($result->body->itemListData)) {
                    $result = (object) [
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
                    if ($this->cacheEnabled) {
                        $this->cacheEngine->set($cacheKey, $result, $this->_config['cache-timeout']);
                    }
                    return $result;
                }
            }
            return $this->failure();
        }
        /**
         * Get Music detail
         * Accepts music ID and returns music detail object or false on failure
         *
         * @param string $music_id
         * @return object
         */
        public function getMusic($music_id = "")
        {
            if (empty($music_id)) {
                throw new \Exception("Invalid Music ID");
            }
            $cacheKey = 'music-' . $music_id;
            if ($this->cacheEnabled) {
                if ($this->cacheEngine->get($cacheKey)) {
                    return $this->cacheEngine->get($cacheKey);
                }
            }
            $result = $this->remote_call("https://www.tiktok.com/music/original-sound-{$music_id}?lang=en", false);
            if (preg_match('/<script id="__NEXT_DATA__"([^>]+)>([^<]+)<\/script>/', $result, $matches)) {
                $result = json_decode($matches[2], false);
                if (isset($result->props->pageProps->musicInfo)) {
                    $result = $result->props->pageProps->musicInfo;
                    if ($this->cacheEnabled) {
                        $this->cacheEngine->set($cacheKey, $result, $this->_config['cache-timeout']);
                    }
                    return $result;
                }
            }
            return $this->failure();
        }
        /**
         * Get music feed
         * Accepts music id and returns music feed object or false on failure
         *
         * @param string $music_id
         * @param integer $maxCursor
         * @return object
         */
        public function getMusicFeed($music_id = "", $maxCursor = 0)
        {
            if (empty($music_id)) {
                throw new \Exception("Invalid Music ID");
            }
            $cacheKey = 'music-feed-' . $music_id . '-' . $maxCursor;
            if ($this->cacheEnabled) {
                if ($this->cacheEngine->get($cacheKey)) {
                    return $this->cacheEngine->get($cacheKey);
                }
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
                $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param));
                if (isset($result->body->itemListData)) {
                    $result = (object) [
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
                    if ($this->cacheEnabled) {
                        $this->cacheEngine->set($cacheKey, $result, $this->_config['cache-timeout']);
                    }
                    return $result;
                }
            }
            return $this->failure();
        }
        /**
         * Get Non watermarked video
         * Accepts video post url and returns non-watermarked video object or false on failure
         *
         * @param string $url
         * @return object
         */
        public function getNoWatermark($url = "")
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
                if ($this->_config['nwm_endpoint'] != false && $this->_config['api_key'] != false) {
                    /**
                     * Private endpoint which will require recurring subscription
                     * See readme for details
                     */
                    $result = $this->remote_call(trim($this->_config['nwm_endpoint'], '/') . "/video/" . $video->id . "?key=" . $this->_config['api_key']);
                    if ($result) {
                        $result = $this->remote_call($result->url, true, [
                            "user-agent: " . $result->ua,
                            "x-gorgon: " . $result->xg,
                            "x-khronos: " . $result->ts
                        ]);
                        if (isset($result->aweme_detail->video->play_addr->uri)) {
                            return (object) [
                                "id" => $result->aweme_detail->video->play_addr->uri,
                                "url" => $result->aweme_detail->video->play_addr->url_list[0],
                            ];
                        }
                    }
                }
            }
            return $this->failure();
        }
        /**
         * Trending Video Feed
         * Accepts $maxCursor offset and returns trending video feed object or false on failure
         *
         * @param integer $maxCursor
         * @return object
         */
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
            $cacheKey = 'trending-' . $maxCursor;
            if ($this->cacheEnabled) {
                if ($this->cacheEngine->get($cacheKey)) {
                    return $this->cacheEngine->get($cacheKey);
                }
            }
            $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param));
            if (isset($result->body->itemListData)) {
                $result = (object) [
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
                if ($this->cacheEnabled) {
                    $this->cacheEngine->set($cacheKey, $result, $this->_config['cache-timeout']);
                }
                return $result;
            }
            return $this->failure();
        }
        /**
         * Get User detail
         * Accepts tiktok username and returns user detail object or false on failure
         *
         * @param string $username
         * @return object
         */
        public function getUser($username = "")
        {
            if (empty($username)) {
                throw new \Exception("Invalid Username");
            }
            $cacheKey = 'user-' . $username;
            if ($this->cacheEnabled) {
                if ($this->cacheEngine->get($cacheKey)) {
                    return $this->cacheEngine->get($cacheKey);
                }
            }
            $username = urlencode($username);
            $result = $this->remote_call("https://www.tiktok.com/@{$username}?lang=en", false);
            if (preg_match('/<script id="__NEXT_DATA__"([^>]+)>([^<]+)<\/script>/', $result, $matches)) {
                $result = json_decode($matches[2], false);
                if (isset($result->props->pageProps->userInfo)) {
                    $result = $result->props->pageProps->userInfo;
                    if ($this->cacheEnabled) {
                        $this->cacheEngine->set($cacheKey, $result, $this->_config['cache-timeout']);
                    }
                    return $result;
                }
            }
            return $this->failure();
        }
        /**
         * Get user feed
         * Accepts username and $maxCursor pagination offset and returns user video feed object or false on failure
         *
         * @param string $username
         * @param integer $maxCursor
         * @return object
         */
        public function getUserFeed($username = "", $maxCursor = 0)
        {
            if (empty($username)) {
                throw new \Exception("Invalid Username");
            }
            $cacheKey = 'user-feed-' . $username . '-' . $maxCursor;
            if ($this->cacheEnabled) {
                if ($this->cacheEngine->get($cacheKey)) {
                    return $this->cacheEngine->get($cacheKey);
                }
            }
            $user = $this->getUser($username);
            if ($user) {
                $param = [
                    "type"      => 1,
                    "secUid"    => "",
                    "id"        => $user->user->id,
                    "count"     => 30,
                    "minCursor" => "0",
                    "maxCursor" => $maxCursor,
                    "shareUid"  => "",
                    "lang"      => "",
                    "verifyFp"  => "",
                ];
                $result = $this->remote_call(self::API_BASE . "video/feed?" . http_build_query($param));
                if (isset($result->body->itemListData)) {
                    $result = (object) [
                        "statusCode" => 0,
                        "info"       => (object) [
                            'type'   => 'user',
                            'detail' => $user,
                        ],
                        "items"      => Helper::parseData($result->body->itemListData),
                        "hasMore"    => @$result->body->hasMore,
                        "minCursor"  => @$result->body->minCursor,
                        "maxCursor"  => @$result->body->maxCursor,
                    ];
                    if ($this->cacheEnabled) {
                        $this->cacheEngine->set($cacheKey, $result, $this->_config['cache-timeout']);
                    }
                    return $result;
                }
            }
            return $this->failure();
        }
        /**
         * Get video by video id
         * Accept video ID and returns video detail object or false on failure
         *
         * @param string $video_id
         * @return void
         */
        public function getVideoByID($video_id = "")
        {
            if (empty($video_id)) {
                throw new \Exception("Invalid VIDEO ID");
            }
            return $this->getVideoByUrl('https://m.tiktok.com/v/' . $video_id . '.html');
        }
        /**
         * Get Video By URL
         * Accepts tiktok video url and returns video detail object or false on failure
         *
         * @param string $url
         * @return object
         */
        public function getVideoByUrl($url = "")
        {
            $cacheKey = Helper::normalize($url);
            if ($this->cacheEnabled) {
                if ($this->cacheEngine->get($cacheKey)) {
                    return $this->cacheEngine->get($cacheKey);
                }
            }
            if (!preg_match("/https?:\/\/([^\.]+)?\.tiktok\.com/", $url)) {
                throw new \Exception("Invalid VIDEO URL");
            }
            $result      = $this->remote_call($url, false);
            $result = Helper::string_between($result, '{"props":{"initialProps":{', "</script>");
            if (!empty($result)) {
                $jsonData = json_decode('{"props":{"initialProps":{' . $result);
                if (isset($jsonData->props->pageProps->itemInfo->itemStruct)) {
                    $result = (object) [
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
                    if ($this->cacheEnabled) {
                        $this->cacheEngine->set($cacheKey, $result, $this->_config['cache-timeout']);
                    }
                    return $result;
                }
            }
            return $this->failure();
        }
        /**
         * Make remote call
         * Private method that will make remote HTTP requests, parse result as JSON if $isJson is set to true
         * returns false on failure
         *
         * @param string $url
         * @param boolean $isJson
         * @return object
         */
        private function remote_call($url = "", $isJson = true, $headers = ['Referer: https://www.tiktok.com/foryou?lang=en'])
        {
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
                CURLOPT_HTTPHEADER     => array_merge([], $headers),
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
            return $data;
        }
        /**
         * Failure
         * Be a man and accept the failure
         *
         * @return void
         */
        private function failure()
        {

            @unlink($this->_config['cookie_file']);
            return false;
        }
        /**
         * Verify Fingerprint, TikTok uses this to create s_v_web_id cookie
         * Fingerprint structure has changed, will update this soon
         * @todo Update this method
         * @return void
         */
        public function verify_fp()
        {
            $chars = str_split("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz");
            $chunks = [];
            $timeStr = base_convert(microtime(true), 10, 36);
            for ($i = 0; $i < 36; $i++) {
                if (\in_array($i, [8, 13, 18, 23])) {
                    $chunks[$i] = "_";
                } elseif ($i == 14) {
                    $chunks[$i] = "4";
                } else {
                    $o = 0 | rand(0, count($chars) - 1);
                    $chunks[$i] = $chars[19 === $i ? 3 & $o | 8 : $o];
                }
            }
            return "verify_" . $timeStr . "_" . implode("", $chunks);
        }
    }
}
