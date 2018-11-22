<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\tests;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Fetcher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class FetcherTest extends TestCase
{
    public function testSetCache(): void
    {
        $fetcher = new TestFetcher();
        $cache = $this->createMock(AdapterInterface::class);
        $fetcher->setCache($cache);

        $this->assertEquals($cache, $fetcher->getCache());
    }

    public function testInjectSource(): void
    {
        $fetcher = new TestFetcher();
        $messages = [new \stdClass(), new \stdClass()];
        $result = $fetcher->testableInjectSource($messages, 'test_source');

        $expectedObject = new \stdClass();
        $expectedObject->fetchSource = 'test_source';

        $this->assertEquals([
            $expectedObject,
            $expectedObject,
            ], $result);
    }
}

class TestFetcher extends Fetcher
{
    public function fetch(): array
    {
        // TODO: Implement fetch() method.
    }

    public function getCache(): AdapterInterface
    {
        return $this->cache;
    }

    public function testableInjectSource(array $messages, string $source): array
    {
        return $this->injectSource($messages, $source);
    }
}
