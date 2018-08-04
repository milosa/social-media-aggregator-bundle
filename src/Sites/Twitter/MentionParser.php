<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Sites\Twitter;

use Milosa\SocialMediaAggregatorBundle\Parser;

class MentionParser implements Parser
{
    use SafeReplace;

    public static function parse(string $context, array $media = []): string
    {
        return self::safeReplace($context, "/\B@(\w+(?!\/))\b/i", '@');
    }
}
