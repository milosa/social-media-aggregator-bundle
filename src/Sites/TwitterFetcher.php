<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Sites;

use Abraham\TwitterOAuth\TwitterOAuth;
use Milosa\SocialMediaAggregatorBundle\Message;
use Milosa\SocialMediaAggregatorBundle\Sites\Twitter\HashTagParser;
use Milosa\SocialMediaAggregatorBundle\Sites\Twitter\MentionParser;
use Milosa\SocialMediaAggregatorBundle\Sites\Twitter\PhotoParser;
use Milosa\SocialMediaAggregatorBundle\Sites\Twitter\TwitterMessage;
use Milosa\SocialMediaAggregatorBundle\Sites\Twitter\URLParser;

class TwitterFetcher extends Fetcher
{
    private const CACHE_KEY = 'twitter_messages';
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
        if ($this->cache === null) {
            return $this->getTimeLineFromAPI();
        }

        $cacheItem = $this->cache->getItem(self::CACHE_KEY);

        if (!$cacheItem->isHit()) {
            $messages = $this->getTimeLineFromAPI();
            $cacheItem->set($messages);
            $this->cache->save($cacheItem);
        } else {
            $messages = $this->injectSource($cacheItem->get(), 'cache');
        }

        return $messages;
    }

    private function getTimeLineFromAPI()
    {
        $this->oauth->get('statuses/user_timeline', ['screen_name' => $this->fetchScreenName, 'count' => $this->numberOfMessages, 'tweet_mode' => 'extended']);

        return $this->injectSource($this->oauth->getLastBody(), 'API');
    }

    /**
     * @param $value
     * @param string $source
     *
     * @return Message
     */
    private function createMessage(\stdClass $value): Message
    {
        $message = new TwitterMessage($value->fetchSource ?? null, 'twitter.twig');

        $message->setBody($value->full_text);
        $message->setURL('https://twitter.com/statuses/'.$value->id);
        $message->setDate(\DateTime::createFromFormat('D M d H:i:s O Y', $value->created_at));
        $message->setAuthor($value->user->name);
        $message->setAuthorURL('https://twitter.com/'.$value->user->screen_name);
        $message->setAuthorDescription($value->user->description);
        $message->setScreenName($value->user->screen_name);
        $message->setAuthorThumbnail($value->user->profile_image_url_https);

        $parsedText = HashTagParser::parse($value->full_text);
        $parsedText = MentionParser::parse($parsedText);

        if (isset($value->entities->media) && isset($value->entities->media[0]) && $value->entities->media[0]->type === 'photo') {
            PhotoParser::addMedia($value->entities->media);
            $parsedText = PhotoParser::parse($parsedText);
        }

        URLParser::addMedia($value->entities->urls);
        $parsedText = URLParser::parse($parsedText);

        $message->setParsedBody($parsedText);

        if (isset($value->retweeted_status)) {
            $message->setRetweet($this->createMessage($value->retweeted_status));
        }

        return $message;
    }
}
