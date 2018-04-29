<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Sites;

use GuzzleHttp\Client;
use Milosa\SocialMediaAggregatorBundle\Message;

class YoutubeFetcher extends Fetcher
{
    private const CACHE_KEY = 'youtube_videos';
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $channelId;

    /**
     * @var int
     */
    private $numberOfVideos;

    /**
     * @var string
     */
    private $apiKey;

    public function __construct(Client $client, string $channelId, int $numberOfVideos, string $apiKey)
    {
        $this->client = $client;
        $this->channelId = $channelId;
        $this->numberOfVideos = $numberOfVideos;
        $this->apiKey = $apiKey;
    }

    public function getData(): array
    {
        if ($this->data === null) {
            $this->data = $this->getVideos();
        }

        $result = [];

        foreach ($this->data->items as $key => $value) {
            $result[$key] = $this->createMessage($value);
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
            $videos = $cacheItem->get();
            $videos->items = $this->injectSource($videos->items, 'cache');
        }

        return $videos;
    }

    private function getVideosFromAPI()
    {
        $res = $this->client->request('GET', 'https://www.googleapis.com/youtube/v3/search?part=snippet,id&channelId='.$this->channelId.'&maxResults='.$this->numberOfVideos.'&order=date&type=video&key='.$this->apiKey);

        $result = json_decode($res->getBody()->getContents());
        $result->items = $this->injectSource($result->items, 'API');

        return $result;
    }

    /**
     * @param $value
     * @param string $source
     *
     * @return Message
     */
    private function createMessage(\stdClass $value): Message
    {
        $message = new Message($value->fetchSource ?? null, 'youtube.twig');
        $message->setBody($value->snippet->description);
        $message->setAuthor($value->snippet->channelTitle);
        $message->setTitle($value->snippet->title);
        $message->setAuthorURL('https://www.youtube.com/channel/'.$value->snippet->channelId);
        $message->setURL('https://www.youtube.com/watch?v='.$value->id->videoId);
        $message->setDate(\DateTime::createFromFormat('Y-m-d\TH:i:s\.000\Z', $value->snippet->publishedAt));
        $message->setId($value->id->videoId);

        return $message;
    }
}
