<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites;

use Milosa\SocialMediaAggregator\Sites\YoutubeFetcher;

class TestableYoutubeFetcher extends YoutubeFetcher
{
    public function __construct()
    {
        parent::__construct();

        //todo: replace with JSON from youtube API
        $this->data = '{}';
    }
}
