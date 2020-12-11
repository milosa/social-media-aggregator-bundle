<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Parser;

class URLParser implements Parser
{
    public static function parse(string $context, array $media = []): string
    {
        $returnContext = $context;

        foreach ($media as $url) {
            $returnContext = str_replace($url->url, '<a href="'.$url->expanded_url.'" rel="noopener noreferrer">'.$url->display_url.'</a>', $returnContext);
        }

        return $returnContext;
    }
}
