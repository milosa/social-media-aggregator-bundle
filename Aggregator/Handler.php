<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator;

class Handler
{
    /**
     * @var Fetcher[]
     */
    private $fetchers;

    /**
     * @var string
     */
    private $factory;

    /**
     * @param Fetcher[] $fetchers
     */
    public function __construct(array $fetchers, string $factory)
    {
        $this->fetchers = $fetchers;
        $this->factory = $factory;
    }

    public function getMessages(): array
    {
        $return = [];
        foreach ($this->fetchers as $fetcher) {
            $messagesJson = $fetcher->fetch();
            if (\count($messagesJson) !== 0) {
                foreach ($messagesJson as $messageJson) {
                    $return[] = $this->factory::createMessage($messageJson);
                }
            }
        }

        return $return;
    }
}
