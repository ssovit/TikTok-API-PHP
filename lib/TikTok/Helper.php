<?php
namespace Sovit\TikTok;

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
                "id"                => @$item->itemInfos->id,
                "desc"              => @$item->itemInfos->text,
                "createTime"        => @$item->itemInfos->createTime,
                "video"             => [
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
                "author"            => [
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
                "music"             => [
                    "id"          => @$item->musicInfos->musicId,
                    "title"       => @$item->musicInfos->musicName,
                    "playUrl"     => @$item->musicInfos->playUrl[0],
                    "coverThumb"  => @$item->musicInfos->covers[0],
                    "coverMedium" => @$item->musicInfos->coversMedium[0],
                    "coverLarge"  => @$item->musicInfos->coversLarger[0],
                    "authorName"  => @$item->musicInfos->authorName,
                    "original"    => @$item->musicInfos->original,
                ],
                "stats"             => [
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
}
