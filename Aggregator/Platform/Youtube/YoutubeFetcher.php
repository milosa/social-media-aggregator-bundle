<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Youtube;

use Milosa\SocialMediaAggregatorBundle\Aggregator\ClientWrapper;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Fetcher;

class YoutubeFetcher extends Fetcher
{
    private const CACHE_KEY = 'youtube_videos';

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
            $this->data = $this->getVideos();
        }

        $result = [];
        foreach ($this->data as $key => $value) {
            $result[] = json_encode($value);
        }

        return $result;
    }

    private function getVideos()
    {
        if ($this->cache === null) {
            return $this->getVideosFromAPI();
        }

        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        if (!$cacheItem->isHit()) {
            $videos = $this->getVideosFromAPI();
            $cacheItem->set($videos);
            $this->cache->save($cacheItem);
        } else {
            $videos = $this->injectSource($cacheItem->get(), 'cache');
        }

        return $videos;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return object[]
     */
    private function getVideosFromAPI(): array
    {
        $res = $this->client->get('', [
            'channelId' => $this->config['search_term'],
            'maxResults' => $this->config['number_of_videos'],
            'order' => 'date', ]);

        $result = json_decode($res->getBody()->getContents());
        $result->items = $this->injectSource($result->items, 'API');

        return $result->items;
    }
}
