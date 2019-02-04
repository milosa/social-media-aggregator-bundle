<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator;

use Symfony\Component\Cache\Adapter\AdapterInterface;

abstract class Fetcher
{
    /**
     * @var object[]
     */
    protected $data;

    /**
     * @var AdapterInterface
     */
    protected $cache;
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $requiredSettings;

    /**
     * @var ClientWrapper
     */
    protected $client;

    public function __construct(ClientWrapper $client, array $config = [])
    {
        $this->config = $config;
        $this->client = $client;
    }

    protected function validateConfig(): void
    {
        $this->validate($this->requiredSettings, $this->config, ['single' => 'Required setting \'%s\' is missing', 'multiple' => 'Required settings \'%s\' are missing']);
    }

    private function validate(array $requiredKeys, array $toValidate, array $errors): void
    {
        $missingKeys = array_diff_key(array_flip($requiredKeys), $toValidate);
        $missingCount = \count($missingKeys);

        if ($missingCount !== 0) {
            $message = $missingCount === 1 ? $errors['single'] : $errors['multiple'];
            throw new \UnexpectedValueException(sprintf($message, implode(', ', array_flip($missingKeys))));
        }
    }

    /**
     * @return string[]
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
