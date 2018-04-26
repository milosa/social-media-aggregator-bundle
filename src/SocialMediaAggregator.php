<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle;

use Milosa\SocialMediaAggregatorBundle\Sites\Fetcher;

class SocialMediaAggregator
{
    /**
     * @var Fetcher[]
     */
    private $fetchers;

    /**
     * SocialMediaAggregatorBundle constructor.
     *
     * @param Fetcher[] $fetchers
     */
    public function __construct(array $fetchers = [])
    {
        $this->fetchers = $fetchers;
    }

    /**
     * @param int $count
     *
     * @throws \Exception
     *
     * @return Message[]
     *
     * @todo Rename this method to possibly: run(), runFetchers(), execute()?
     * @todo Only return $count messages
     */
    public function getMessages(int $count): array
    {
        $messages = [];

        if (\count($this->fetchers) === 0) {
            throw new \RuntimeException('Tried to run getData without fetchers');
        }

        foreach ($this->fetchers as $fetcher) {
            $messages = array_merge($messages, $fetcher->getData());
        }

        return  $this->sortMessages($messages);
    }

    public function addFetcher(Fetcher $fetcher): void
    {
        $this->fetchers[] = $fetcher;
    }

    public function getFetchers(): array
    {
        return $this->fetchers;
    }

    private function sortMessages(array $messages): array
    {
        usort($messages, function ($a, $b) {
            /**
             * @var $a Message
             * @var $b Message
             */
            if ($a->getDate() === $b->getDate()) {
                return 0;
            }

            return $a->getDate() < $b->getDate() ? 1 : -1;
        });

        return $messages;
    }
}
