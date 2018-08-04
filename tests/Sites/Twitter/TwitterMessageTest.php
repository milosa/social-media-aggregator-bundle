<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\Sites\Twitter;

use Milosa\SocialMediaAggregatorBundle\Sites\Twitter\TwitterMessage;
use PHPUnit\Framework\TestCase;

class TwitterMessageTest extends TestCase
{
    public function testCanGetRetweet(): void
    {
        $message = new TwitterMessage();
        $retweet = new TwitterMessage();

        $message->setRetweet($retweet);

        $this->assertEquals($retweet, $message->getRetweet());
    }
}
