<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Tests\Platform\Twitter;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter\URLParser;
use PHPUnit\Framework\TestCase;

class URLParserTest extends TestCase
{
    public function testOneURL(): void
    {
        $entity = new \stdClass();
        $entity->display_url = 'display_url.com/webpage.html';
        $entity->url = 'https://url.com/webpage.html';
        $entity->expanded_url = 'https://expanded_url.com/webpage.html';
        $context = 'some test text with a https://url.com/webpage.html more test text';
        $expected = 'some test text with a <a href="https://expanded_url.com/webpage.html" rel="noopener noreferrer">display_url.com/webpage.html</a> more test text';

        $this->assertEquals($expected, URLParser::parse($context, [$entity]));
    }
}
