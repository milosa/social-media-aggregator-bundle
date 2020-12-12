<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Youtube;

use DateTime;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Message;
use Milosa\SocialMediaAggregatorBundle\Aggregator\MessageFactory;

class YoutubeMessageFactory implements MessageFactory
{
    public static function createMessage(string $json): Message
    {
        $result = json_decode($json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON');
        }

        return self::createFromDecodedJson($result);
    }

    private static function createFromDecodedJson($result): YoutubeMessage
    {
        $message = new YoutubeMessage($result->fetchSource ?? 'API', 'youtube.twig');
        $message->setNetwork('youtube');
        $message->setBody($result->snippet->description);
        $message->setDate(DateTime::createFromFormat('Y-m-d\TH:i:s\Z', $result->snippet->publishedAt));
        $message->setAuthor($result->snippet->channelTitle);
        $message->setAuthorURL('https://www.youtube.com/channel/'.$result->snippet->channelId);
        $message->setURL('https://www.youtube.com/watch?v='.$result->id->videoId);
        $message->setId($result->id->videoId);

        return $message;
    }
}
