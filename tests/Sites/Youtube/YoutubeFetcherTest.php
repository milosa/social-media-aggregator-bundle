<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites\Youtube;

use GuzzleHttp\Client;
use Milosa\SocialMediaAggregatorBundle\Sites\Youtube\YoutubeFetcher;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Cache\CacheItemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class YoutubeFetcherTest extends TestCase
{
    //todo: remove duplicate code. (e.g. prophecy creation)

    public function testItMakesCorrectAPICalls(): void
    {
        $streamProphecy = $this->prophesize(StreamInterface::class);
        $streamProphecy->getContents()->willReturn('{"items" : [ {"kind": "youtube#searchResult"}, {"kind": "youtube#searchResult"} ] }')->shouldBeCalledTimes(1);

        $responseProphecy = $this->prophesize(ResponseInterface::class);
        $responseProphecy->getBody()->willReturn($streamProphecy->reveal())->shouldBeCalledTimes(1);

        $clientProphecy = $this->prophesize(Client::class);
        $clientProphecy->request(
            Argument::exact('GET'),
            Argument::exact('https://www.googleapis.com/youtube/v3/search?part=snippet,id&channelId=test_id&maxResults=10&order=date&type=video&key=test_key'))
        ->willReturn($responseProphecy->reveal())
        ->shouldBeCalledTimes(1);

        $fetcher = new YoutubeFetcher($clientProphecy->reveal(), 'test_id', 10, 'test_key');
        $fetcher->fetch();
    }

    public function testWhenAPIReturnsTwoVideosFetchReturnsArrayWithTwoJsonStrings(): void
    {
        $streamProphecy = $this->prophesize(StreamInterface::class);
        $streamProphecy->getContents()->willReturn('{"items" : [ {"id": "123"}, {"id": "456"} ] }')->shouldBeCalledTimes(1);

        $responseProphecy = $this->prophesize(ResponseInterface::class);
        $responseProphecy->getBody()->willReturn($streamProphecy->reveal())->shouldBeCalledTimes(1);

        $clientProphecy = $this->prophesize(Client::class);
        $clientProphecy->request(
            Argument::exact('GET'),
            Argument::exact('https://www.googleapis.com/youtube/v3/search?part=snippet,id&channelId=test_id&maxResults=10&order=date&type=video&key=test_key'))
            ->willReturn($responseProphecy->reveal())
            ->shouldBeCalledTimes(1);

        $fetcher = new YoutubeFetcher($clientProphecy->reveal(), 'test_id', 10, 'test_key');

        $this->assertEquals(['{"id":"123","fetchSource":"API"}', '{"id":"456","fetchSource":"API"}'], $fetcher->fetch());
    }

    public function testWhenCacheIsEnabledAndHitItGetsDataFromCache(): void
    {
        $clientProphecy = $this->prophesize(Client::class);
        $clientProphecy->request(Argument::any())->shouldNotBeCalled();

        $fetcher = new YoutubeFetcher($clientProphecy->reveal(), 'test_id', 10, 'test_key');

        $cacheItem = $this->prophesize(CacheItemInterface::class);
        $cacheItem->isHit()->willReturn(true)->shouldBeCalledTimes(1);

        $class1 = new \stdClass();
        $class1->id = 123;

        $class2 = new \stdClass();
        $class2->id = 456;

        $data = [$class1, $class2];

        $cacheItem->get()->willReturn($data)->shouldBeCalledTimes(1);

        $cache = $this->prophesize(AdapterInterface::class);
        $cache->getItem(Argument::exact('youtube_videos'))->willReturn($cacheItem->reveal())->shouldBeCalledTimes(1);
        $fetcher->setCache($cache->reveal());

        $this->assertEquals(['{"id":123,"fetchSource":"cache"}', '{"id":456,"fetchSource":"cache"}'], $fetcher->fetch());
    }

    public function testWhenCacheIsEnabledAndCacheDoesntGetHitItGetsDataFromAPI(): void
    {
        $streamProphecy = $this->prophesize(StreamInterface::class);
        $streamProphecy->getContents()->willReturn('{"items" : [ {"id": "123"}, {"id": "456"} ] }')->shouldBeCalledTimes(1);

        $responseProphecy = $this->prophesize(ResponseInterface::class);
        $responseProphecy->getBody()->willReturn($streamProphecy->reveal())->shouldBeCalledTimes(1);

        $clientProphecy = $this->prophesize(Client::class);
        $clientProphecy->request(
            Argument::exact('GET'),
            Argument::exact('https://www.googleapis.com/youtube/v3/search?part=snippet,id&channelId=test_id&maxResults=10&order=date&type=video&key=test_key'))
            ->willReturn($responseProphecy->reveal())
            ->shouldBeCalledTimes(1);

        $fetcher = new YoutubeFetcher($clientProphecy->reveal(), 'test_id', 10, 'test_key');

        $class1 = new \stdClass();
        $class1->id = 123;
        $class1->fetchSource = 'API';

        $class2 = new \stdClass();
        $class2->id = 456;
        $class2->fetchSource = 'API';

        $data = [$class1, $class2];

        $cacheItemProphecy = $this->prophesize(CacheItemInterface::class);
        $cacheItemProphecy->isHit()->willReturn(false)->shouldBeCalledTimes(1);
        $cacheItemProphecy->set(Argument::exact($data))->shouldBeCalledTimes(1);
        $cacheItem = $cacheItemProphecy->reveal();

        $cache = $this->prophesize(AdapterInterface::class);
        $cache->getItem(Argument::exact('youtube_videos'))->willReturn($cacheItem)->shouldBeCalledTimes(1);
        $cache->save(Argument::exact($cacheItem))->shouldBeCalledTimes(1);
        $fetcher->setCache($cache->reveal());

        $this->assertEquals(['{"id":"123","fetchSource":"API"}', '{"id":"456","fetchSource":"API"}'], $fetcher->fetch());
    }

    //todo: make it so that fetch source is NOT stored in the cache itself
    //todo: check that ints are stored as ints in cache.
}
