<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Tests\Platform\Twitter;

use GuzzleHttp\Client;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter\TwitterClient;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;

class TwitterClientTest extends TestCase
{
    use ProphecyTrait;

    public function testTwitterClient(): void
    {
        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient->get(
            Argument::exact('test.invalid'), Argument::any())
            ->shouldBeCalledTimes(1)
            ->willReturn($this->prophesize(ResponseInterface::class)->reveal());

        $twitterClient = new TestableTwitterClient([
            'consumer_key' => 'test_consumer_key',
            'consumer_secret' => 'test_consumer_secret',
            'token' => 'test_token',
            'token_secret' => 'test_token_secret',
            'base_uri' => 'test.invalid', ]);
        $twitterClient->setGuzzleClient($guzzleClient->reveal());

        $twitterClient->get('test.invalid');
    }
}

class TestableTwitterClient extends TwitterClient
{
    public function setGuzzleClient(Client $guzzleClient): void
    {
        $this->client = $guzzleClient;
    }
}
