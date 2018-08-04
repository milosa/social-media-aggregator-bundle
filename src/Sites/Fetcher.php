<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Sites;

use Milosa\SocialMediaAggregatorBundle\Message;
use Symfony\Component\Cache\Adapter\AdapterInterface;

abstract class Fetcher
{
    protected $data;

    /**
     * @var AdapterInterface
     */
    protected $cache;

    /**
     * @return Message[]
     */
    abstract public function fetch(): array;

    public function setCache(AdapterInterface $adapter): void
    {
        $this->cache = $adapter;
    }

    protected function injectSource(array $messages, string $source): array
    {
        foreach ($messages as $message) {
            $message->fetchSource = $source;
        }

        return $messages;
    }
}
