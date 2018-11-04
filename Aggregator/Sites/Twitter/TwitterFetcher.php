<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Sites\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Sites\Fetcher;

class TwitterFetcher extends Fetcher
{
    private const CACHE_KEY = 'twitter_messages';
    /**
     * @var TwitterOAuth
     */
    private $oauth;
    /**
     * @var string
     */
    private $fetchScreenName;
    /**
     * @var int
     */
    private $numberOfMessages;

    /**
     * @var string
     */
    private $imageSize;

    public function __construct(TwitterOAuth $twitterOauth, string $fetchScreenName, int $numberOfMessages, string $imageSize)
    {
        $this->fetchScreenName = $fetchScreenName;
        $this->numberOfMessages = $numberOfMessages;
        $this->oauth = $twitterOauth;
        $this->imageSize = $imageSize;
    }

    /**
     * @return string[]
     */
    public function fetch(): array
    {
        if ($this->data === null) {
            $this->data = $this->getTimeLine();
        }

        $result = [];
        foreach ($this->data as $key => $value) {
            $result[] = json_encode($value);
        }

        return $result;
    }

    /**
     * @return object[]
     */
    private function getTimeLine()
    {
        if ($this->cache === null) {
            return $this->getTimeLineFromAPI();
        }

        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        if (!$cacheItem->isHit()) {
            $messages = $this->getTimeLineFromAPI();
            $cacheItem->set($messages);
            $this->cache->save($cacheItem);
        } else {
            $messages = $this->injectSource($cacheItem->get(), 'cache');
        }

        return $messages;
    }

    /**
     * @return object[]
     */
    private function getTimeLineFromAPI(): array
    {
        $this->oauth->get('statuses/user_timeline', ['screen_name' => $this->fetchScreenName, 'count' => $this->numberOfMessages, 'tweet_mode' => 'extended']);

        return $this->injectSource($this->oauth->getLastBody(), 'API');
    }
}
