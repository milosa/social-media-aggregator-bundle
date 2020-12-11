<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter;

trait SafeReplace
{
    private static function safeReplace(string $text, string $regex, string $prefix, string $urlPrefix = '', int $index = 1): string
    {
        return preg_replace_callback($regex, function ($matches) use ($prefix, $index, $urlPrefix) {
            $name = htmlentities($matches[$index], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            return '<a href="https://twitter.com/'.$urlPrefix.$name.'" rel="noopener noreferrer">'.$prefix.$name.'</a>';
        }, $text);
    }
}
