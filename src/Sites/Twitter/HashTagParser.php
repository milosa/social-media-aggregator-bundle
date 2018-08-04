<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Sites\Twitter;

use Milosa\SocialMediaAggregatorBundle\Parser;

class HashTagParser implements Parser
{
    use SafeReplace;

    public static function parse(string $context, array $media = []): string
    {
        return self::safeReplace($context, "/\B(?<![=\/])#([\w]+[a-z]+([0-9]+)?)/i", '#', 'hashtag/');
    }
}
