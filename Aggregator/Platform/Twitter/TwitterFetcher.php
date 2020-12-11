<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter;

use Milosa\SocialMediaAggregatorBundle\Aggregator\ClientWrapper;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Fetcher;

class TwitterFetcher extends Fetcher
{
    private const CACHE_KEY = 'twitter_messages';

    public function __construct(ClientWrapper $client, array $config)
    {
        parent::__construct($client, $config);
    }

    /**
     * @return string[]
     */
    public function fetch(): array
    {
        if ($this->data === null) {
            $this->data = $this->getTweets();
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
    private function getTweets()
    {
        if ($this->cache === null) {
            return $this->getTweetsFromAPI();
        }

        $cacheItem = $this->cache->getItem(self::CACHE_KEY.'_'.$this->config['search_type'].'_'.$this->config['search_term']);

        if (!$cacheItem->isHit()) {
            $messages = $this->getTweetsFromAPI();
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
    private function getTweetsFromAPI(): array
    {
        if ($this->config['search_type'] === 'profile') {
            $res = $this->client->get('statuses/user_timeline.json', ['screen_name' => $this->config['search_term'], 'count' => $this->config['number_of_tweets'], 'tweet_mode' => 'extended']);

            return $this->injectSource(json_decode($res->getBody()->getContents()), 'API');
        }

        $res = $this->client->get('search/tweets.json', ['q' => $this->config['search_term'], 'count' => $this->config['number_of_tweets'], 'tweet_mode' => 'extended', 'result_type' => 'recent']);

        return $this->injectSource(json_decode($res->getBody()->getContents())->statuses, 'API');
    }
}
