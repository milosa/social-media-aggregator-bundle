<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests;

use Milosa\SocialMediaAggregatorBundle\Handler;
use Milosa\SocialMediaAggregatorBundle\Message;
use Milosa\SocialMediaAggregatorBundle\MessageFactory;
use Milosa\SocialMediaAggregatorBundle\Sites\Fetcher;
use Milosa\SocialMediaAggregatorBundle\SocialMediaAggregator;
use PHPUnit\Framework\TestCase;

class SocialMediaAggregatorTest extends TestCase
{
    /**
     * @var SocialMediaAggregator
     */
    private $aggregator;

    public function setUp()
    {
        $this->aggregator = new SocialMediaAggregator();
    }

    public function testWhenInstantiatingAggregatorItHasNoHandlers(): void
    {
        $this->assertCount(0, $this->aggregator->getHandlers());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No handlers available
     */
    public function testWithoutHandlersItThrowsException(): void
    {
        $this->aggregator->getMessages();
    }

    public function testCanAddHandler(): void
    {
        $this->aggregator->addHandler($this->prophesize(Handler::class)->reveal());

        $this->assertCount(1, $this->aggregator->getHandlers());
    }

    public function testWhenCallingGetMessagesCallsGetMessagesOnHandler(): void
    {
        $handler = $this->prophesize(Handler::class)
            ->willBeConstructedWith([
                $this->prophesize(Fetcher::class)->reveal(),
                MessageFactory::class,
            ]);

        $handler
            ->getMessages()
            ->willReturn([])
            ->shouldBeCalledTimes(1);

        $this->aggregator->addHandler($handler->reveal());
        $this->aggregator->getMessages();
    }

    public function testAggregatorSortsMessagesProperly(): void
    {
        $messages = $this->createSortedAndUnsortedMessageArray();
        $handler = $this->prophesize(Handler::class);

        $handler->getMessages()->willReturn($messages[0])->shouldBeCalledTimes(1);

        $this->aggregator->addHandler($handler->reveal());

        $result = $this->aggregator->getMessages();
        $this->assertEquals($messages[1], $result);
    }

    //todo: test return value type. Rendered template or array of message objects

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

    private function getMessageWithDate(\DateTime $date): Message
    {
        $message = new Message();
        $message->setDate($date);

        return $message;
    }
}
