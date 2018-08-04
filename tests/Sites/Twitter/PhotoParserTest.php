<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites\Twitter;

use Milosa\SocialMediaAggregatorBundle\Sites\Twitter\PhotoParser;
use PHPUnit\Framework\TestCase;

class PhotoParserTest extends TestCase
{
    /**
     * @dataProvider imageProvider
     */
    public function testSettingValidSizeReturnsCorrectImageTag(\stdClass $media, string $size): void
    {
        PhotoParser::setSize($size);

        $context = 'test text http://fake.org/abcde test text';
        $result = PhotoParser::parse($context, [$media]);

        $this->assertEquals('test text <img src="https://url-to-image.com/image.jpg:'.$size.'"/> test text', $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid size given
     */
    public function testSettingInvalidSizeThrowsException(): void
    {
        PhotoParser::setSize('foo');
    }

    public function imageProvider(): array
    {
        $media = new \stdClass();
        $media->url = 'http://fake.org/abcde';
        $media->media_url_https = 'https://url-to-image.com/image.jpg';
        $media->type = 'photo';

        return [
            [$media, 'thumb'],
            [$media, 'medium'],
            [$media, 'large'],
            [$media, 'small'],
        ];
    }

    public function tearDown()
    {
        PhotoParser::setSize('thumb');
    }
}
