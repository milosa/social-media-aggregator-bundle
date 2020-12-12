<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Tests\Platform\Youtube;

use GuzzleHttp\Client;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Youtube\YoutubeClient;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Prophecy\PhpUnit\ProphecyTrait;

class YoutubeClientTest extends TestCase
{
    use ProphecyTrait;

    public function testItRequiresApiKey(): void
    {
        $this->expectExceptionMessage("api_key is required");
        $this->expectException(\InvalidArgumentException::class);
        new YoutubeClient(['no_api_key' => 'nope']);
    }

    public function testYoutubeClient(): void
    {
        $guzzleClient = $this->prophesize(Client::class);
        $guzzleClient->get(
            Argument::exact('https://www.googleapis.com/youtube/v3/search'),
            Argument::exact(['query' => [
                'key' => 'test_key',
                'part' => 'snippet,id',
                'type' => 'video',
            ]]))
            ->shouldBeCalledTimes(1)
            ->willReturn($this->prophesize(ResponseInterface::class)->reveal());

        $youtubeClient = new TestableYoutubeClient([
            'api_key' => 'test_key',
        ]);
        $youtubeClient->setGuzzleClient($guzzleClient->reveal());

        $youtubeClient->get('');
    }
}

class TestableYoutubeClient extends YoutubeClient
{
    public function setGuzzleClient(Client $guzzleClient): void
    {
        $this->client = $guzzleClient;
    }
}
