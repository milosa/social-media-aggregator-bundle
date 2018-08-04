<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Sites\Youtube;

use GuzzleHttp\Client;
use Milosa\SocialMediaAggregatorBundle\Sites\Fetcher;

class YoutubeFetcher extends Fetcher
{
    private const CACHE_KEY = 'youtube_videos';

    public function __construct(Client $client, string $channelId, int $numberOfVideos, string $apiKey)
    {
        $this->client = $client;
        $this->channelId = $channelId;
        $this->numberOfVideos = $numberOfVideos;
        $this->apiKey = $apiKey;
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
        $res = $this->client->request('GET',
            'https://www.googleapis.com/youtube/v3/search?part=snippet,id&channelId='.$this->channelId.'&maxResults='.$this->numberOfVideos.'&order=date&type=video&key='.$this->apiKey);
        $result = json_decode($res->getBody()->getContents());
        $result->items = $this->injectSource($result->items, 'API');

        return $result->items;
    }
}
