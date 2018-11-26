<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator;

use Psr\Http\Message\ResponseInterface;

interface ClientWrapper
{
    public function get(string $uri, array $queryParameters = []): ResponseInterface;
}
