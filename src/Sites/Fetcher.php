<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Sites;

use Milosa\SocialMediaAggregatorBundle\Message;

abstract class Fetcher
{
    protected $data;

    /**
     * @return Message[]
     */
    abstract public function getData(): array;

    //public function getTimeLine(string $screenName, int $count);
}
