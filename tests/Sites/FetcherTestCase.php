<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites;

use Milosa\SocialMediaAggregator\Message;
use Milosa\SocialMediaAggregator\Sites\Fetcher;
use PHPUnit\Framework\TestCase;

abstract class FetcherTestCase extends TestCase
{
    /**
     * @var Fetcher
     */
    protected $fetcher;

    abstract protected function getTestFetcher(): Fetcher;

    protected function assertMessageOld(string $expectedMessageBody, string $expectedURL, \DateTime $expectedDateTime, Message $actualMessage): void
    {
        $this->assertSame($expectedMessageBody, $actualMessage->getBody());
        $this->assertSame($expectedURL, $actualMessage->getURL());
        $this->assertEquals($expectedDateTime, $actualMessage->getDate());
    }

    protected function assertMessage(Message $expected, Message $actualMessage): void
    {
        $this->assertEquals($expected, $actualMessage);
    }
}
