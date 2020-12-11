<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Tests\Platform\Twitter;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter\TwitterClient;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter\TwitterFetcher;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Cache\CacheItemInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class TwitterFetcherTest extends TestCase
{
    use ProphecyTrait;

    public function testWhenCallingFetchWithTypeProfileItMakesCorrectAPICall(): void
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn('[]');
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn($stream->reveal());

        $oauth = $this->prophesize(TwitterClient::class);
        $oauth->get(Argument::exact('statuses/user_timeline.json'), Argument::exact(['screen_name' => 'test_name', 'count' => 10, 'tweet_mode' => 'extended']))
            ->willReturn($response->reveal())
            ->shouldBeCalledTimes(1);

        $fetcher = new TwitterFetcher($oauth->reveal(), [
            'search_type' => 'profile',
            'search_term' => 'test_name',
            'number_of_tweets' => 10,
            'image_size' => 'thumb', ]);
        $fetcher->fetch();
    }

    public function testWhenCallingFetchWithTypeHashTagItMakesCorrectAPICall(): void
    {
        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn('{"statuses": []}');
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn($stream->reveal());

        $oauth = $this->prophesize(TwitterClient::class);
        $oauth->get(Argument::exact('search/tweets.json'), Argument::exact(['q' => 'test_hashtag', 'count' => 10, 'tweet_mode' => 'extended', 'result_type' => 'recent']))
            ->willReturn($response->reveal())
            ->shouldBeCalledTimes(1);

        $fetcher = new TwitterFetcher($oauth->reveal(), [
            'search_type' => 'hash_tag',
            'search_term' => 'test_hashtag',
            'number_of_tweets' => 10,
            'image_size' => 'thumb', ]);
        $fetcher->fetch();
    }

    public function testWhenAPIReturnsTwoTweetsFetchReturnsArrayWithTwoJsonStrings(): void
    {
        $class1 = new \stdClass();
        $class1->text = 'some test text';
        $class1->id = 123456;

        $class2 = new \stdClass();
        $class2->text = 'more test text';
        $class2->id = 7891011;

        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn(json_encode([$class1, $class2]));
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn($stream->reveal());

        $oauth = $this->prophesize(TwitterClient::class);
        $oauth->get(Argument::exact('statuses/user_timeline.json'), Argument::exact(['screen_name' => 'test_name', 'count' => 2, 'tweet_mode' => 'extended']))
            ->willReturn($response->reveal())
            ->shouldBeCalledTimes(1);

        $fetcher = new TwitterFetcher($oauth->reveal(), [
            'search_type' => 'profile',
            'search_term' => 'test_name',
            'number_of_tweets' => 2,
            'image_size' => 'thumb', ]);
        $result = $fetcher->fetch();

        $this->assertEquals(['{"text":"some test text","id":123456,"fetchSource":"API"}', '{"text":"more test text","id":7891011,"fetchSource":"API"}'], $result);
    }

    public function testWhenCacheIsEnabledAndHitItGetsDataFromCache(): void
    {
        $oauth = $this->prophesize(TwitterClient::class);

        $cacheItem = $this->prophesize(CacheItemInterface::class);
        $cacheItem->isHit()->willReturn(true)->shouldBeCalledTimes(1);
        $cacheItem->get()->willReturn([])->shouldBeCalledTimes(1);

        $cache = $this->prophesize(AdapterInterface::class);
        $cache->getItem(Argument::exact('twitter_messages_profile_test_name'))->willReturn($cacheItem->reveal())->shouldBeCalledTimes(1);

        $fetcher = new TwitterFetcher($oauth->reveal(), [
            'search_type' => 'profile',
            'search_term' => 'test_name',
            'number_of_tweets' => 2,
            'image_size' => 'thumb', ]);
        $fetcher->setCache($cache->reveal());

        $fetcher->fetch();
    }

    public function testWhenCacheIsEnabledAndCacheDoesntGetHitItGetsDataFromAPI(): void
    {
        $class1 = new \stdClass();
        $class1->text = 'some test text';
        $class1->id = 123456;
        $class1->fetchSource = 'API';

        $class2 = new \stdClass();
        $class2->text = 'more test text';
        $class2->id = 7891011;
        $class2->fetchSource = 'API';

        $data = [$class1, $class2];

        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn(json_encode([$class1, $class2]));
        $response = $this->prophesize(ResponseInterface::class);
        $response->getBody()->willReturn($stream->reveal());

        $oauth = $this->prophesize(TwitterClient::class);
        $oauth->get(Argument::exact('statuses/user_timeline.json'), Argument::exact(['screen_name' => 'test_name', 'count' => 10, 'tweet_mode' => 'extended']))
            ->willReturn($response->reveal())
            ->shouldBeCalledTimes(1);

        $cacheItemProphecy = $this->prophesize(CacheItemInterface::class);
        $cacheItemProphecy->isHit()->willReturn(false)->shouldBeCalledTimes(1);
        $cacheItemProphecy->set(Argument::exact($data))->shouldBeCalledTimes(1);
        $cacheItem = $cacheItemProphecy->reveal();

        $cache = $this->prophesize(AdapterInterface::class);
        $cache->getItem(Argument::exact('twitter_messages_profile_test_name'))->willReturn($cacheItem)->shouldBeCalledTimes(1);
        $cache->save(Argument::exact($cacheItem))->shouldBeCalledTimes(1);

        $fetcher = new TwitterFetcher($oauth->reveal(), [
            'search_type' => 'profile',
            'search_term' => 'test_name',
            'number_of_tweets' => 10,
            'image_size' => 'thumb', ]);
        $fetcher->setCache($cache->reveal());

        $fetcher->fetch();
    }
}
