<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Sites\Twitter;

use Milosa\SocialMediaAggregatorBundle\MediaParser;

class URLParser implements MediaParser
{
    private static $media;

    public static function addMedia(array $media): void
    {
        self::$media = $media;
    }

    public static function parse(string $context): string
    {
        $returnContext = $context;

        foreach (self::$media as $url) {
            $returnContext = str_replace($url->url, '<a href="'.$url->expanded_url.'">'.$url->display_url.'</a>', $returnContext);
        }

        return $returnContext;
    }
}
