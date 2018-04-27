<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites;

use Milosa\SocialMediaAggregatorBundle\Message;
use Milosa\SocialMediaAggregatorBundle\Sites\Fetcher;
use PHPUnit\Framework\TestCase;

abstract class FetcherTestCase extends TestCase
{
    /**
     * @var Fetcher
     */
    protected $fetcher;

    abstract protected function getTestFetcher(): Fetcher;

    protected function assertMessage(Message $expected, Message $actualMessage): void
    {
        $this->assertEquals($expected, $actualMessage);
    }
}
