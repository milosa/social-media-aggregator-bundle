<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Tests\Platform\Youtube;


use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Youtube\YoutubeClient;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Youtube\YoutubeFetcher;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Cache\CacheItemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Prophecy\PhpUnit\ProphecyTrait;

class YoutubeFetcherTest extends TestCase
{
    use ProphecyTrait;

    public function testItMakesCorrectAPICalls(): void
    {
        $clientWrapper = $this->createClientStub(
            '{"items": [] }',
            [
                'channelId' => 'test_name',
                'maxResults' => 10,
                'order' => 'date',
        ]);

        $fetcher = new YoutubeFetcher(
            $clientWrapper->reveal(),
            [
            'search_term' => 'test_name',
            'number_of_videos' => 10,
            ]);
        $fetcher->fetch();
    }

    public function testWhenAPIReturnsTwoVideosFetchReturnsArrayWithTwoJsonStrings(): void
    {
        $clientWrapper = $this->createClientStub(
            '{"items" : [ {"id": "123"}, {"id": "456"} ] }',
            [
                'channelId' => 'test_name',
                'maxResults' => 2,
                'order' => 'date',
            ]);

        $fetcher = new YoutubeFetcher(
            $clientWrapper->reveal(),
            [
                'search_term' => 'test_name',
                'number_of_videos' => 2,
            ]
            );

        $this->assertEquals(['{"id":"123","fetchSource":"API"}', '{"id":"456","fetchSource":"API"}'], $fetcher->fetch());
    }

    public function testWhenCacheIsEnabledAndHitItGetsDataFromCache(): void
    {
        $clientWrapper = $this->createClientDummy();
        $clientWrapper->get(Argument::Any())->shouldNotBeCalled();

        $fetcher = new YoutubeFetcher(
            $clientWrapper->reveal(),
            [
                'search_term' => 'test_name',
                'number_of_videos' => 2,
            ]);

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
        $clientWrapper = $this->createClientStub(
            '{"items" : [ {"id": "123"}, {"id": "456"} ] }',
            [
                'channelId' => 'test_name',
                'maxResults' => 2,
                'order' => 'date',
            ]);

        $fetcher = new YoutubeFetcher(
            $clientWrapper->reveal(),
            [
                'search_term' => 'test_name',
                'number_of_videos' => 2,
            ]
        );

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

    /**
     * @param string $responseBody
     * @param array  $queryParameters
     *
     * @return YoutubeClient|ObjectProphecy
     */
    private function createClientStub(string $responseBody, array $queryParameters)
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn($responseBody);
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn($stream->reveal());

        /** @var YoutubeClient|ObjectProphecy $clientWrapper */
        $clientWrapper = $this->createClientDummy();
        $clientWrapper->get(
            Argument::exact(''),
            Argument::exact($queryParameters))
            ->willReturn($response->reveal())
            ->shouldBeCalledTimes(1);

        return $clientWrapper;
    }

    /**
     * @return ObjectProphecy
     */
    private function createClientDummy(): ObjectProphecy
    {
        $clientWrapper = $this->prophesize(YoutubeClient::class);
        $clientWrapper->willBeConstructedWith([['api_key' => 'test_key']]);

        return $clientWrapper;
    }
}
