<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Tests\Platform\Youtube;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Youtube\YoutubeMessage;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Youtube\YoutubeMessageFactory;
use PHPUnit\Framework\TestCase;

class YoutubeMessageFactoryTest extends TestCase
{
    /**
     * @var string[]
     */
    private $sampleVideoJson = [
        '{ "kind": "youtube#searchResult", "etag": "\"XI7nbFXulYBIpL0ayR_gDh3eu1k/m1XFDOtSGQ8s0rj8i6Qq8qN5M4Y\"", "id": { "kind": "youtube#video", "videoId": "UGozHiOXfCI" }, "snippet": { "publishedAt": "2018-09-02T16:43:37Z", "channelId": "UCLA_DiR1FfKNvjuUpBHmylQ", "title": "NASA Live: Earth Views from the Space Station", "description": "Behold, the Earth! See live views of Earth from the International Space Station coming to you by NASA\'s High Definition Earth Viewing (HDEV) experiment.", "thumbnails": { "default": { "url": "https://i.ytimg.com/vi/UGozHiOXfCI/default_live.jpg", "width": 120, "height": 90 }, "medium": { "url": "https://i.ytimg.com/vi/UGozHiOXfCI/mqdefault_live.jpg", "width": 320, "height": 180 }, "high": { "url": "https://i.ytimg.com/vi/UGozHiOXfCI/hqdefault_live.jpg", "width": 480, "height": 360 } }, "channelTitle": "NASA", "liveBroadcastContent": "live" } }',
    ];

    public function testInvalidJsonThrowsException(): void
    {
        $this->expectExceptionMessage("Invalid JSON");
        $this->expectException(\InvalidArgumentException::class);
        YoutubeMessageFactory::createMessage('string');
    }

    public function testCreateMessage(): void
    {
        $message = YoutubeMessageFactory::createMessage($this->sampleVideoJson[0]);

        $expected = new YoutubeMessage('API', 'youtube.twig');
        $expected->setDate(new \DateTime('2018-09-02 16:43:37'));
        $expected->setBody('Behold, the Earth! See live views of Earth from the International Space Station coming to you by NASA\'s High Definition Earth Viewing (HDEV) experiment.');
        $expected->setURL('https://www.youtube.com/watch?v=UGozHiOXfCI');
        $expected->setAuthor('NASA');
        $expected->setAuthorURL('https://www.youtube.com/channel/UCLA_DiR1FfKNvjuUpBHmylQ');
        $expected->setId('UGozHiOXfCI');
        $expected->setNetwork('youtube');

        $this->assertEquals($expected, $message);
    }
}
