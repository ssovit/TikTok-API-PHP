<?php

namespace Sovit\TikTok;

if (!\class_exists('\Sovit\TikTok\Stream')) {

    class Stream
    {
        protected $buffer_size = 256 * 1024;

        protected $headers = [];

        protected $headers_sent = false;
        protected $tt_webid_v2 = null;

        public function __construct($config = [])
        {
            $this->config = array_merge(['cookie_file' => sys_get_temp_dir().DIRECTORY_SEPARATOR . 'tiktok.txt', 'user-agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36"], $config);
        }

        public function bodyCallback($ch, $data)
        {
            if (true) {
                echo $data;
                flush();
            }

            return strlen($data);
        }

        public function headerCallback($ch, $data)
        {
            if (preg_match('/HTTP\/[\d.]+\s*(\d+)/', $data, $matches)) {
                $status_code = $matches[1];

                if (200 == $status_code || 206 == $status_code || 403 == $status_code || 404 == $status_code) {
                    $this->headers_sent = true;
                    $this->sendHeader(rtrim($data));
                }
            } else {

                $forward = ['content-type', 'content-length', 'accept-ranges', 'content-range'];

                $parts = explode(':', $data, 2);

                if ($this->headers_sent && count($parts) == 2 && in_array(trim(strtolower($parts[0])), $forward)) {
                    $this->sendHeader(rtrim($data));
                }
            }

            return strlen($data);
        }

        public function stream($url)
        {
            $ch = curl_init();

            $headers   = [];
            if (isset($_SERVER['HTTP_RANGE'])) {
                $headers[] = 'Range: ' . $_SERVER['HTTP_RANGE'];
            }
            curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            curl_setopt($ch, CURLOPT_BUFFERSIZE, $this->buffer_size);
            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_USERAGENT, $this->config['user-agent']);
            curl_setopt($ch, CURLOPT_REFERER, "https://www.tiktok.com/discover");
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->config['cookie_file']);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->config['cookie_file']);
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, [$this, 'headerCallback']);

            curl_setopt($ch, CURLOPT_WRITEFUNCTION, [$this, 'bodyCallback']);

            $ret = curl_exec($ch);
            curl_close($ch);
            return true;
        }

        protected function sendHeader($header)
        {
            header($header);
        }
    }
}
