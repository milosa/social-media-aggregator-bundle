<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites;

use GuzzleHttp\Client;
use Milosa\SocialMediaAggregatorBundle\Sites\YoutubeFetcher;

class TestableYoutubeFetcher extends YoutubeFetcher
{
    use TestDataTrait;

    public function __construct(Client $client)
    {
        parent::__construct($client, 'test-id', 2, 'test-key');

        $this->data = self::decodedYoutubeSampleData();
    }
}
