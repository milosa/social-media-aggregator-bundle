<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Youtube;

use GuzzleHttp\Client;
use Milosa\SocialMediaAggregatorBundle\Aggregator\ClientWrapper;
use Psr\Http\Message\ResponseInterface;

class YoutubeClient implements ClientWrapper
{
    private $apiKey;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(array $config)
    {
        if (!isset($config['api_key'])) {
            throw new \InvalidArgumentException('api_key is required');
        }

        $this->apiKey = $config['api_key'];
        $this->client = new Client();
    }

    public function get(string $uri, array $queryParameters = []): ResponseInterface
    {
        return $this->client->get('https://www.googleapis.com/youtube/v3/search', [
            'query' => array_merge([
                    'key' => $this->apiKey,
                    'part' => 'snippet,id',
                    'type' => 'video', ],
                    $queryParameters),
        ]);
    }
}
