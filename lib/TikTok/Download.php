<?php
namespace Sovit\TikTok;

class Download
{
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
        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);
        return (int) $size;
    }

    public function url($url, $file_name="tiktok-video", $ext = "mp4")
    {
        $file_size = $this->file_size($url);
        ob_start();
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '.' . $ext . '"');
        header("Content-Transfer-Encoding: binary");
        header('Expires: 0');
        header('Pragma: public');
        if (isset($_SERVER['HTTP_REQUEST_USER_AGENT']) && strpos($_SERVER['HTTP_REQUEST_USER_AGENT'], 'MSIE') !== false) {
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        }
        if ($file_size > 100) {
            header('Content-Length: ' . $file_size);
        }
        header('Connection: Close');
        ob_clean();
        flush();
        readfile($url, "", stream_context_create([
            "ssl"  => [
                "verify_peer"      => false,
                "verify_peer_name" => false,
            ],
            "http" => [
                "header" => [
                    "Referer: https://www.tiktok.com/foryou?lang=en",
                ],
            ],

        ]));
        exit;
    }
}
