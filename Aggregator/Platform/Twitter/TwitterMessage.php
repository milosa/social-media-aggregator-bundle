<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Message;

class TwitterMessage extends Message
{
    /**
     * @var TwitterMessage|null
     */
    private $retweet;

    /**
     * @return TwitterMessage|null
     */
    public function getRetweet(): ?self
    {
        return $this->retweet;
    }

    /**
     * @param TwitterMessage $retweet
     */
    public function setRetweet(self $retweet): void
    {
        $this->retweet = $retweet;
    }
}
