<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Sites\Twitter;

use Milosa\SocialMediaAggregatorBundle\MediaParser;

class PhotoParser implements MediaParser
{
    /**
     * @var array
     */
    private static $media;

    public static function addMedia(array $media): void
    {
        self::$media = $media;
    }

    public static function parse(string $context): string
    {
        $returnContext = $context;
        if (\count(self::$media) > 0 && self::$media[0]->type === 'photo') {
            $returnContext = str_replace(self::$media[0]->url, '<img src="'.self::$media[0]->media_url_https.':small"/>', $context);
        }

        return $returnContext;
    }
}
