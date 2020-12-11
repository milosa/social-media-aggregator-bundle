<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Parser;

class PhotoParser implements Parser
{
    private static $size = 'thumb';
    private static $allowedSizes = ['thumb', 'small', 'medium', 'large'];

    public static function setSize(string $size): void
    {
        if (!\in_array($size, self::$allowedSizes, true)) {
            throw new \InvalidArgumentException('Invalid size given');
        }
        self::$size = $size;
    }

    public static function parse(string $context, array $media = []): string
    {
        $returnContext = $context;
        if (\count($media) > 0 && $media[0]->type === 'photo') {
            $returnContext = str_replace($media[0]->url, '<img src="'.$media[0]->media_url_https.':'.self::$size.'"/>', $context);
        }

        return $returnContext;
    }
}
