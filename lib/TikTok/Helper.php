<?php

namespace Sovit\TikTok;

if (!\class_exists('\Sovit\TikTok\Helper')) {
    class Helper
    {
        public static function finalUrl($url)
        {
            $ch      = curl_init();
            $options = [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => false,
                CURLOPT_HTTPHEADER     => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
                    'Accept-Encoding: gzip, deflate, br',
                    'Accept-Language: en-US,en;q=0.9',
                    'Connection: keep-alive',
                ],
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT      => 'okhttp',
                CURLOPT_ENCODING       => "utf-8",
                CURLOPT_AUTOREFERER    => false,
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
            $data  = curl_exec($ch);
            $final = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);
            return $final;
        }

        public static function normalize($string)
        {
            $string = preg_replace("/([^a-z0-9])/", "-", strtolower($string));
            $string = preg_replace("/(\s+)/", "-", strtolower($string));
            $string = preg_replace("/([-]+){2,}/", "-", strtolower($string));
            return $string;
        }

        public static function parseData($items = [])
        {
            $final = [];
            foreach ($items as $item) {
                $final[] = (object) [
                    "id"                => @$item->itemInfos->id,
                    "desc"              => @$item->itemInfos->text,
                    "createTime"        => @$item->itemInfos->createTime,
                    "video"             => (object) [
                        "id"            =>"awesome",
                        "height"       => @$item->itemInfos->video->videoMeta->height,
                        "width"        => @$item->itemInfos->video->videoMeta->width,
                        "duration"     => @$item->itemInfos->video->videoMeta->duration,
                        "ratio"        => @$item->itemInfos->video->videoMeta->height,
                        "cover"        => @$item->itemInfos->covers[0],
                        "originCover"  => @$item->itemInfos->coversOrigin[0],
                        "dynamicCover" => @$item->itemInfos->coversDynamic[0],
                        "playAddr"     => @$item->itemInfos->video->urls[0],
                        "downloadAddr" => @$item->itemInfos->video->urls[0],
                    ],
                    "author"            => (object) [
                        "id"           => @$item->authorInfos->userId,
                        "uniqueId"     => @$item->authorInfos->uniqueId,
                        "nickname"     => @$item->authorInfos->nickName,
                        "avatarThumb"  => @$item->authorInfos->covers[0],
                        "avatarMedium" => @$item->authorInfos->coversMedium[0],
                        "avatarLarger" => @$item->authorInfos->coversLarger[0],
                        "signature"    => @$item->authorInfos->signature,
                        "verified"     => @$item->authorInfos->verified,
                        "secUid"       => @$item->authorInfos->secUid,
                    ],
                    "music"             => (object) [
                        "id"          => @$item->musicInfos->musicId,
                        "title"       => @$item->musicInfos->musicName,
                        "playUrl"     => @$item->musicInfos->playUrl[0],
                        "coverThumb"  => @$item->musicInfos->covers[0],
                        "coverMedium" => @$item->musicInfos->coversMedium[0],
                        "coverLarge"  => @$item->musicInfos->coversLarger[0],
                        "authorName"  => @$item->musicInfos->authorName,
                        "original"    => @$item->musicInfos->original,
                    ],
                    "stats"             => (object) [
                        "diggCount"    => @$item->itemInfos->diggCount,
                        "shareCount"   => @$item->itemInfos->shareCount,
                        "commentCount" => @$item->itemInfos->commentCount,
                        "playCount"    => @$item->itemInfos->playCount,
                    ],
                    "originalItem"      => @$item->itemInfos->isOriginal,
                    "officalItem"       => @$item->itemInfos->isOfficial,
                    "secret"            => @$item->itemInfos->secret,
                    "forFriend"         => @$item->itemInfos->forFriend,
                    "digged"            => @$item->itemInfos->liked,
                    "itemCommentStatus" => @$item->itemInfos->commentStatus,
                    "showNotPass"       => @$item->itemInfos->showNotPass,
                    "vl1"               => false,

                ];
            }
            return $final;
        }

        public static function string_between($string, $start, $end)
        {
            $string = ' ' . $string;
            $ini    = strpos($string, $start);
            if (0 == $ini) {
                return '';
            }

            $ini += strlen($start);
            $len = strpos($string, $end, $ini) - $ini;
            return substr($string, $ini, $len);
        }
        public static function makeId()
        {
            $characters = '0123456789';
            $randomString = '';
            $n = 16;
            for ($i = 0; $i < $n; $i++) {
                $index = rand(0, strlen($characters) - 1);
                $randomString .= $characters[$index];
            }

            return "68" . $randomString;
        }
    }
}
