<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle;

use Milosa\SocialMediaAggregatorBundle\Sites\Fetcher;

class Handler
{
    /**
     * @var Fetcher
     */
    private $fetcher;

    /**
     * @var string
     */
    private $factory;

    public function __construct(Fetcher $fetcher, string $factory)
    {
        $this->fetcher = $fetcher;
        $this->factory = $factory;
    }

    public function getMessages(): array
    {
        $messagesJson = $this->fetcher->fetch();
        $return = [];
        if (\count($messagesJson) !== 0) {
            foreach ($messagesJson as $messageJson) {
                $return[] = $this->factory::createMessage($messageJson);
            }
        }

        return $return;
    }
}
