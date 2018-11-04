<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Sites\Twitter\TwitterFetcher;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class TwitterFetcherTest extends TestCase
{
    public function testWhenCallingFetchItMakesCorrectAPICall(): void
    {
        $oauth = $this->prophesize(TwitterOAuth::class);
        $oauth->get(Argument::exact('statuses/user_timeline'), Argument::exact(['screen_name' => 'test_name', 'count' => 10, 'tweet_mode' => 'extended']))->shouldBeCalledTimes(1);
        $oauth->getLastBody()->willReturn([])->shouldBeCalledTimes(1);

        $fetcher = new TwitterFetcher($oauth->reveal(), 'test_name', 10, 'thumb');
        $fetcher->fetch();
    }

    public function testWhenAPIReturnsTwoTweetsFetchReturnsArrayWithTwoJsonStrings(): void
    {
        $oauth = $this->prophesize(TwitterOAuth::class);
        $oauth->get(Argument::exact('statuses/user_timeline'), Argument::exact(['screen_name' => 'test_name', 'count' => 2, 'tweet_mode' => 'extended']))->shouldBeCalledTimes(1);

        $class1 = new \stdClass();
        $class1->text = 'some test text';
        $class1->id = 123456;

        $class2 = new \stdClass();
        $class2->text = 'more test text';
        $class2->id = 7891011;

        $oauth->getLastBody()->willReturn([$class1, $class2])->shouldBeCalledTimes(1);

        $fetcher = new TwitterFetcher($oauth->reveal(), 'test_name', 2, 'thumb');
        $result = $fetcher->fetch();

        $this->assertEquals(['{"text":"some test text","id":123456,"fetchSource":"API"}', '{"text":"more test text","id":7891011,"fetchSource":"API"}'], $result);
    }

    public function testWhenCacheIsEnabledAndHitItGetsDataFromCache(): void
    {
        $oauth = $this->prophesize(TwitterOAuth::class);

        $cacheItem = $this->prophesize(CacheItemInterface::class);
        $cacheItem->isHit()->willReturn(true)->shouldBeCalledTimes(1);
        $cacheItem->get()->willReturn([])->shouldBeCalledTimes(1);

        $cache = $this->prophesize(AdapterInterface::class);
        $cache->getItem(Argument::exact('twitter_messages'))->willReturn($cacheItem->reveal())->shouldBeCalledTimes(1);

        $fetcher = new TwitterFetcher($oauth->reveal(), 'test_name', 2, 'thumb');
        $fetcher->setCache($cache->reveal());

        $fetcher->fetch();
    }

    public function testWhenCacheIsEnabledAndCacheDoesntGetHitItGetsDataFromAPI(): void
    {
        $oauth = $this->prophesize(TwitterOAuth::class);
        $oauth->get(Argument::exact('statuses/user_timeline'), Argument::exact(['screen_name' => 'test_name', 'count' => 2, 'tweet_mode' => 'extended']))->shouldBeCalledTimes(1);

        $class1 = new \stdClass();
        $class1->text = 'some test text';
        $class1->id = 123456;

        $class2 = new \stdClass();
        $class2->text = 'more test text';
        $class2->id = 7891011;

        $data = [$class1, $class2];

        $oauth->getLastBody()->willReturn($data)->shouldBeCalledTimes(1);

        $cacheItemProphecy = $this->prophesize(CacheItemInterface::class);
        $cacheItemProphecy->isHit()->willReturn(false)->shouldBeCalledTimes(1);
        $cacheItemProphecy->set(Argument::exact($data))->shouldBeCalledTimes(1);
        $cacheItem = $cacheItemProphecy->reveal();

        $cache = $this->prophesize(AdapterInterface::class);
        $cache->getItem(Argument::exact('twitter_messages'))->willReturn($cacheItem)->shouldBeCalledTimes(1);
        $cache->save(Argument::exact($cacheItem))->shouldBeCalledTimes(1);

        $fetcher = new TwitterFetcher($oauth->reveal(), 'test_name', 2, 'thumb');
        $fetcher->setCache($cache->reveal());

        $fetcher->fetch();
    }
}
