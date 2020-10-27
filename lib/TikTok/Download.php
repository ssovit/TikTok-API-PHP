<?php

namespace Sovit\TikTok;

if (!\class_exists('\Sovit\TikTok\Download')) {
    class Download
    {
        protected $buffer_size = 1000000;
        public function __construct($config = [])
        {
            $this->config = array_merge(['cookie_file' => sys_get_temp_dir().DIRECTORY_SEPARATOR . 'tiktok.txt', 'user-agent' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36"], $config);
            $this->tt_webid_v2 = Helper::makeId();
        }
        public function file_size($url)
        {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Referer: https://www.tiktok.com/foryou?lang=en',
            ]);
            curl_setopt($ch, CURLOPT_USERAGENT, $this->config['user-agent']);
            curl_setopt($ch, CURLOPT_REFERER, "https://www.tiktok.com/");

            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->config['cookie_file']);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->config['cookie_file']);
            $data = curl_exec($ch);
            $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            curl_close($ch);
            return (int) $size;
        }

        public function url($url, $file_name = "tiktok-video", $ext = "mp4")
        {
            $file_size = $this->file_size($url);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_name . '.' . $ext . '"');
            header("Content-Transfer-Encoding: binary");
            header('Expires: 0');
            header('Pragma: public');

            if ($file_size > 100) {
                header('Content-Length: ' . $file_size);
            }
            header('Connection: Close');
            ob_clean();
            flush();
            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', 1);
            }
            @ini_set('zlib.output_compression', false);
            @ini_set('implicit_flush', true);
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, $this->config['user-agent']);
            curl_setopt($ch, CURLOPT_REFERER, "https://www.tiktok.com/");
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->config['cookie_file']);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $this->config['cookie_file']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
            echo $output;
            exit;
        }
    }
}
