<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Tests;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Fetcher;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Handler;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Message;
use Milosa\SocialMediaAggregatorBundle\Aggregator\MessageFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class HandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testWhenFetcherReturnsEmptyArrayNoCallsToCreateMessageAreMade(): void
    {
        $fetcherProphecy = $this->getFetcherProphecy();
        $fetcherProphecy->fetch()->willReturn([])->shouldBeCalledTimes(1);

        $handler = new Handler(
            [$fetcherProphecy->reveal()],
            SpyFactory::class
        );

        $handler->getMessages();
        $this->assertEquals(0, SpyFactory::$createMessageCalled);
    }

    public function testWhenFetcherReturnsArrayWithOneElementOneCallToCreateMessageIsMade(): void
    {
        $fetcherProphecy = $this->getFetcherProphecy();
        $fetcherProphecy->fetch()->willReturn(['something'])->shouldBeCalledTimes(1);

        $handler = new Handler(
            [$fetcherProphecy->reveal()],
            SpyFactory::class
        );

        $handler->getMessages();
        $this->assertEquals(1, SpyFactory::$createMessageCalled);
    }

    public function testWhenFetcherReturnsArrayWithTwoElementsTwoCallsToCreateMessageAreMade(): void
    {
        $fetcherProphecy = $this->getFetcherProphecy();
        $fetcherProphecy->fetch()->willReturn(['something', 'something_else'])->shouldBeCalledTimes(1);

        $handler = new Handler(
            [$fetcherProphecy->reveal()],
            SpyFactory::class
        );

        $handler->getMessages();
        $this->assertEquals(2, SpyFactory::$createMessageCalled);
    }

    private function getFetcherProphecy(): ObjectProphecy
    {
        return $this->prophesize(Fetcher::class);
    }

    private function getFactoryProphecy(): ObjectProphecy
    {
        return $this->prophesize(MessageFactory::class);
    }

    public function tearDown(): void
    {
        SpyFactory::resetCalls();
    }
}

class SpyFactory implements MessageFactory
{
    /**
     * @var int
     */
    public static $createMessageCalled = 0;

    public static function createMessage(string $json): Message
    {
        ++self::$createMessageCalled;

        return new Message();
    }

    public static function resetCalls(): void
    {
        self::$createMessageCalled = 0;
    }

    public static function getName(): string
    {
        return '';
    }
}
