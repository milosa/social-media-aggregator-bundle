<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Sites;

use Milosa\SocialMediaAggregator\Message;

abstract class Fetcher
{
    protected $data;

    /**
     * @return Message[]
     */
    abstract public function getData(): array;

    //public function getTimeLine(string $screenName, int $count);
}
