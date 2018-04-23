<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests;

use Milosa\SocialMediaAggregatorBundle\Message;
use Milosa\SocialMediaAggregatorBundle\Sites\Fetcher;
use Milosa\SocialMediaAggregatorBundle\Sites\TwitterFetcher;
use Milosa\SocialMediaAggregatorBundle\SocialMediaAggregator;
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
}
