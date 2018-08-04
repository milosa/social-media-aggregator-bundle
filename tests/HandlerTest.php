<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests;

use Milosa\SocialMediaAggregatorBundle\Handler;
use Milosa\SocialMediaAggregatorBundle\Message;
use Milosa\SocialMediaAggregatorBundle\MessageFactory;
use Milosa\SocialMediaAggregatorBundle\Sites\Fetcher;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

class HandlerTest extends TestCase
{
    public function testWhenFetcherReturnsEmptyArrayNoCallsToCreateMessageAreMade(): void
    {
        $fetcherProphecy = $this->getFetcherProphecy();
        $fetcherProphecy->fetch()->willReturn([])->shouldBeCalledTimes(1);

//        $factoryProphecy = $this->getFactoryProphecy();
//        $factoryProphecy->createMessage(Argument::any())->shouldNotBeCalled();

        $handler = new Handler(
            $fetcherProphecy->reveal(),
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
            $fetcherProphecy->reveal(),
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
            $fetcherProphecy->reveal(),
            SpyFactory::class
        );

        $handler->getMessages();
        $this->assertEquals(2, SpyFactory::$createMessageCalled);
    }

    /**
     * @return ObjectProphecy
     */
    private function getFetcherProphecy(): ObjectProphecy
    {
        return $this->prophesize(Fetcher::class);
    }

    /**
     * @return ObjectProphecy
     */
    private function getFactoryProphecy(): ObjectProphecy
    {
        return $this->prophesize(MessageFactory::class);
    }

    public function tearDown()
    {
        SpyFactory::resetCalls();
    }
}

class SpyFactory implements MessageFactory
{
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
        // TODO: Implement getName() method.
    }
}
