<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Sites;

use Abraham\TwitterOAuth\TwitterOAuth;
use Milosa\SocialMediaAggregator\Message;

class TwitterFetcher extends Fetcher
{
    /**
     * @var TwitterOAuth
     */
    private $oauth;
    /**
     * @var string
     */
    private $fetchScreenName;
    /**
     * @var int
     */
    private $numberOfMessages;

    public function __construct(TwitterOAuth $twitterOauth, string $fetchScreenName, int $numberOfMessages)
    {
        $this->fetchScreenName = $fetchScreenName;
        $this->numberOfMessages = $numberOfMessages;

        $this->oauth = $twitterOauth;
    }

    /**
     * @return Message[]
     */
    public function getData(): array
    {
        if ($this->data === null) {
            $this->data = $this->getTimeLine();
        }

        $result = [];
        foreach ($this->data as $key => $value) {
            $result[$key] = $this->createMessage($value);
        }

        return $result;
    }

    private function getTimeLine()
    {
        $this->oauth->get('statuses/user_timeline', ['screen_name' => $this->fetchScreenName, 'count' => $this->numberOfMessages]);

        return $this->oauth->getLastBody();
    }

    /**
     * @param $value
     *
     * @return Message
     */
    private function createMessage(\stdClass $value): Message
    {
        $message = new Message();
        $message->setBody($value->text);
        $message->setURL('https://twitter.com/statuses/'.$value->id);
        $message->setDate(\DateTime::createFromFormat('D M d H:i:s O Y', $value->created_at));
        $message->setAuthor($value->user->name);
        $message->setAuthorURL('https://twitter.com/'.$value->user->screen_name);
        $message->setAuthorDescription($value->user->description);
        $message->setScreenName($value->user->screen_name);
        $message->setTemplate('twitter.twig');

        return $message;
    }
}
