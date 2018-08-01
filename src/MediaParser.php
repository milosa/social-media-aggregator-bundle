<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle;

interface MediaParser extends Parser
{
    public static function addMedia(array $media): void;
}
