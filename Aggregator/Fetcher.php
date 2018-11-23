<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use function count;

abstract class Fetcher
{
    protected $data;

    /**
     * @var AdapterInterface
     */
    protected $cache;
    /**
     * @var array
     */
    protected $settings;
    /**
     * @var array
     */
    protected $authData;

    protected $requiredAuthData;

    protected $requiredSettings;

    public function __construct(array $settings = [], array $authData = [])
    {
        $this->settings = $settings;
        $this->authData = $authData;
    }

    protected function validateAuthData(): void
    {
        $this->validate($this->requiredAuthData, $this->authData, ['single' => 'Required authentication data \'%s\' is missing', 'multiple' => 'Required authentication data \'%s\' are missing']);
    }

    protected function validateSettings(): void
    {
        $this->validate($this->requiredSettings, $this->settings, ['single' => 'Required setting \'%s\' is missing', 'multiple' => 'Required settings \'%s\' are missing']);
    }

    private function validate(array $requiredKeys, array $toValidate, array $errors): void
    {
        $missingKeys = array_diff_key(array_flip($requiredKeys), $toValidate);
        $missingCount = count($missingKeys);

        if ($missingCount !== 0) {
            $message = $missingCount === 1 ? $errors['single'] : $errors['multiple'];
            throw new \UnexpectedValueException(sprintf($message, implode(array_flip($missingKeys), ', ')));
        }
    }

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
