<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Milosa\SocialMediaAggregatorBundle\Aggregator\ClientWrapper;
use Psr\Http\Message\ResponseInterface;

class TwitterClient implements ClientWrapper
{
    protected $client;

    public function __construct(array $config)
    {
        $stack = HandlerStack::create();

        $middleware = new Oauth1([
            'consumer_key' => $config['consumer_key'],
            'consumer_secret' => $config['consumer_secret'],
            'token' => $config['token'],
            'token_secret' => $config['token_secret'],
        ]);
        $stack->push($middleware);

        $this->client = new Client([
            'base_uri' => $config['base_uri'],
            'handler' => $stack,
            'auth' => 'oauth',
        ]);
    }

    public function get(string $uri, array $queryParameters = []): ResponseInterface
    {
        return $this->client->get($uri, ['query' => $queryParameters]);
    }
}
