<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator;

class SocialMediaAggregator
{
    /**
     * @var Handler[]
     */
    private $handlers = [];

    /**
     * @return Handler[]
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @return Message[]
     */
    public function getMessages(): array
    {
        if (\count($this->handlers) === 0) {
            throw new \RuntimeException('No handlers available');
        }

        $messages = [];

        foreach ($this->handlers as $handler) {
            $messages = array_merge($messages, $handler->getMessages());
        }

        return $this->sortMessages($messages);
    }

    public function addHandler(Handler $handler): void
    {
        $this->handlers[] = $handler;
    }

    private function sortMessages(array $messages): array
    {
        usort($messages, function (Message $a, Message $b): int {
            return $b->getDate() <=> $a->getDate();
        });

        return $messages;
    }
}
