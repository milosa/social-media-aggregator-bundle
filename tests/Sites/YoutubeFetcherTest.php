<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites;

use Milosa\SocialMediaAggregator\Message;
use Milosa\SocialMediaAggregator\Sites\Fetcher;
use Milosa\SocialMediaAggregator\Sites\YoutubeFetcher;

class YoutubeFetcherTest extends FetcherTestCase
{
    /**
     * @var Fetcher
     */
    protected $fetcher;

    public function setUp()
    {
        $this->fetcher = $this->getTestFetcher();
    }

    protected function getTestFetcher(): Fetcher
    {
        return new TestableYoutubeFetcher();
    }

    public function testCanCreateFetcher(): void
    {
        $this->assertInstanceOf(YoutubeFetcher::class, $this->fetcher);
    }

//    public function testGetMessageObjects(): void
//    {
//        $this->markTestSkipped('youtube isnt implemented');
//        $result = $this->fetcher->getData();
//
//        $this->assertNotNull($result);
//        $this->assertCount(2, $result);
//
//        $this->assertInstanceOf(Message::class, $result[0]);
//        $this->assertInstanceOf(Message::class, $result[1]);
//    }
}
