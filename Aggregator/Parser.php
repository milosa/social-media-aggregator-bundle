<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator;

interface Parser
{
    public static function parse(string $context, array $media = []): string;
}
