<?php
namespace TikTok;

class Helper
{
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
            $final[] = [
                "id"                => $item->itemInfos->id,
                "desc"              => $item->itemInfos->text,
                "createTime"        => $item->itemInfos->createTime,
                "video"             => [
                    "height"       => $item->itemInfos->video->videoMeta->height,
                    "width"        => $item->itemInfos->video->videoMeta->width,
                    "duration"     => $item->itemInfos->video->videoMeta->duration,
                    "ratio"        => $item->itemInfos->video->videoMeta->height,
                    "cover"        => isset($item->itemInfos->covers[0]) ? $item->itemInfos->covers[0] : '',
                    "originCover"  => isset($item->itemInfos->coversOrigin[0]) ? $item->itemInfos->coversOrigin[0] : '',
                    "dynamicCover" => isset($item->itemInfos->coversDynamic[0]) ? $item->itemInfos->coversDynamic[0] : '',
                    "playAddr"     => isset($item->itemInfos->video->urls[0]) ? $item->itemInfos->video->urls[0] : '',
                    "downloadAddr" => isset($item->itemInfos->video->urls[0]) ? $item->itemInfos->video->urls[0] : '',
                ],
                "author"            => [
                    "id"           => $item->authorInfos->userId,
                    "uniqueId"     => $item->authorInfos->uniqueId,
                    "nickname"     => $item->authorInfos->nickName,
                    "avatarThumb"  => isset($item->authorInfos->covers[0]) ? $item->authorInfos->covers[0] : '',
                    "avatarMedium" => isset($item->authorInfos->coversMedium[0]) ? $item->authorInfos->coversMedium[0] : '',
                    "avatarLarger" => isset($item->authorInfos->coversLarger[0]) ? $item->authorInfos->coversLarger[0] : '',
                    "signature"    => $item->authorInfos->signature,
                    "verified"     => $item->authorInfos->verified,
                    "secUid"       => $item->authorInfos->secUid,
                ],
                "music"             => [
                    "id"          => $item->musicInfos->musicId,
                    "title"       => $item->musicInfos->musicName,
                    "playUrl"     => isset($item->musicInfos->playUrl[0]) ? $item->musicInfos->playUrl[0] : "",
                    "coverThumb"  => isset($item->musicInfos->covers[0]) ? $item->musicInfos->covers[0] : "",
                    "coverMedium" => isset($item->musicInfos->coversMedium[0]) ? $item->musicInfos->coversMedium[0] : "",
                    "coverLarge"  => isset($item->musicInfos->coversLarger[0]) ? $item->musicInfos->coversLarger[0] : "",
                    "authorName"  => $item->musicInfos->authorName,
                    "original"    => $item->musicInfos->original,
                ],
                "stats"             => [
                    "diggCount"    => $item->itemInfos->diggCount,
                    "shareCount"   => $item->itemInfos->shareCount,
                    "commentCount" => $item->itemInfos->commentCount,
                    "playCount"    => $item->itemInfos->playCount,
                ],
                "originalItem"      => $item->itemInfos->isOriginal,
                "officalItem"       => $item->itemInfos->isOfficial,
                "secret"            => $item->itemInfos->secret,
                "forFriend"         => $item->itemInfos->forFriend,
                "digged"            => $item->itemInfos->liked,
                "itemCommentStatus" => $item->itemInfos->commentStatus,
                "showNotPass"       => $item->itemInfos->showNotPass,
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
}
