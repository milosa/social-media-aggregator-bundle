<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator;

interface MessageFactory
{
    public static function createMessage(string $json): Message;
}
