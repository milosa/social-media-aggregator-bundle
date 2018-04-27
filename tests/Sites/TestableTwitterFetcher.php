<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites;

use Abraham\TwitterOAuth\TwitterOAuth;
use Milosa\SocialMediaAggregatorBundle\Sites\TwitterFetcher;

class TestableTwitterFetcher extends TwitterFetcher
{
    use TestDataTrait;

    public function __construct(TwitterOAuth $OAuth, string $screenName, int $count)
    {
        parent::__construct($OAuth, $screenName, $count);

        $this->data = self::decodeSampleTwitterMessages();
    }
}
