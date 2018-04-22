<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests;

use Milosa\SocialMediaAggregator\Message;
use Milosa\SocialMediaAggregator\Sites\Fetcher;
use Milosa\SocialMediaAggregator\Sites\TwitterFetcher;
use Milosa\SocialMediaAggregator\SocialMediaAggregator;
use Milosa\SocialMediaAggregatorTests\Sites\TestableTwitterFetcher;
use Milosa\SocialMediaAggregatorTests\Sites\TestableYoutubeFetcher;
use PHPUnit\Framework\TestCase;

class AggregatorTest extends TestCase
{
    /**
     * @var SocialMediaAggregator
     */
    private $aggregator;

    public function setUp()
    {
        $this->aggregator = new SocialMediaAggregator();
    }

    public function testCanCreateAggregator(): void
    {
        $this->assertInstanceOf(SocialMediaAggregator::class, $this->aggregator);
    }

    /*    public function testAggregatorCorrectlyReturnsMessageObjectsFromMultipleSources()
        {
            $this->markTestSkipped("skipped because youtube isn't implemented yet");
            $this->aggregator->addFetcher(new TestableYoutubeFetcher());
            $this->aggregator->addFetcher($this->getTestableTwitterFetcher());
            $result = $this->aggregator->getMessages(4);
    
            $this->assertCount(4, $result);
            $this->assertInstanceOf(Message::class, $result[0]);
            $this->assertSame('Dit is een test text', $result[0]->getBody());
    
            $this->assertInstanceOf(Message::class, $result[1]);
            $this->assertSame('Een test text dit is', $result[1]->getBody());
    
            $this->assertInstanceOf(Message::class, $result[2]);
            $this->assertSame('This is the best test description of 2018', $result[2]->getBody());
        }*/

    public function testAggregatorSortsMessages(): void
    {
        $messagesUnordered = [
            $this->getMessageWithDate(new \DateTime('2017-04-01')),
            $this->getMessageWithDate(new \DateTime('2017-05-01')),
            $this->getMessageWithDate(new \DateTime('2016-04-01')),
            $this->getMessageWithDate(new \DateTime('2016-05-01')),
            $this->getMessageWithDate(new \DateTime('2015-04-01')),
            $this->getMessageWithDate(new \DateTime('2015-05-01')),
        ];
        $messagesOrdered = [
            $this->getMessageWithDate(new \DateTime('2017-05-01')),
            $this->getMessageWithDate(new \DateTime('2017-04-01')),
            $this->getMessageWithDate(new \DateTime('2016-05-01')),
            $this->getMessageWithDate(new \DateTime('2016-04-01')),
            $this->getMessageWithDate(new \DateTime('2015-05-01')),
            $this->getMessageWithDate(new \DateTime('2015-04-01')),
        ];
        $fetcherProphecy = $this->prophesize(TwitterFetcher::class);
        $fetcherProphecy->getData()->willReturn($messagesUnordered);
        $this->aggregator->addFetcher($fetcherProphecy->reveal());

        //fixme: count is unused
        $result = $this->aggregator->getMessages(6);

        $this->assertEquals($messagesOrdered, $result);
    }

    public function testCanAddFetchers(): void
    {
        $this->aggregator->addFetcher($this->createMock(Fetcher::class));
        $this->aggregator->addFetcher($this->createMock(Fetcher::class));

        $this->assertCount(2, $this->aggregator->getFetchers());
    }

    private function getMessageWithDate(\DateTime $date): Message
    {
        $message = new Message();
        $message->setDate($date);

        return $message;
    }

    /**
     * @return TestableTwitterFetcher
     */
    private function getTestableTwitterFetcher(): TestableTwitterFetcher
    {
        //fixme: Fetchers need to be mocked
        return new TestableTwitterFetcher('', '', '', '', 'waldo', 10);
    }
}
