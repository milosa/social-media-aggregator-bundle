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

    public function __construct(Fetcher $fetcher, string $factory)
    {
        $this->fetchers[] = $fetcher;
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
