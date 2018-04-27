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

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Tried to run getData without fetchers
     */
    public function testWhenNoFetchersThrowsException(): void
    {
        $this->aggregator->getMessages(1);
    }

    public function testAggregatorSortsMessages(): void
    {
        $messages = $this->createSortedAndUnsortedMessageArray();
        $fetcherProphecy = $this->prophesize(TwitterFetcher::class);
        $fetcherProphecy->getData()->willReturn($messages[0]);
        $this->aggregator->addFetcher($fetcherProphecy->reveal());

        //fixme: count is unused
        $result = $this->aggregator->getMessages(6);

        $this->assertEquals($messages[1], $result);
    }

    /**
     * @return Message[][]
     */
    private function createSortedAndUnsortedMessageArray(): array
    {
        $datesInOrder = [
            new \DateTime('2017-05-01'),
            new \DateTime('2017-04-01'),
            new \DateTime('2016-05-01'),
            new \DateTime('2016-04-01'),
            new \DateTime('2015-05-01'),
            new \DateTime('2015-05-01'),
            new \DateTime('2015-04-01 10:00:10'),
            new \DateTime('2015-04-01 10:00:05'),
            new \DateTime('2015-04-01 08:00:30'),
        ];

        $randomOrder = [4, 3, 6, 0, 7, 2, 1, 8, 5];
        $messagesUnordered = [];
        $messagesOrdered = [];
        $max = \count($datesInOrder);

        for ($i = 0; $i < $max; ++$i) {
            $messagesUnordered[] = $this->getMessageWithDate($datesInOrder[$randomOrder[$i]]);
            $messagesOrdered[] = $this->getMessageWithDate($datesInOrder[$i]);
        }

        return [$messagesUnordered, $messagesOrdered];
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
