<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Message;
use Milosa\SocialMediaAggregatorBundle\Aggregator\MessageFactory;

class TwitterMessageFactory implements MessageFactory
{
    public static function createMessage(string $json): Message
    {
        $result = json_decode($json);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON');
        }

        $message = self::createFromDecodedJson($result);

        if (isset($result->retweeted_status)) {
            $message->setRetweet(self::createFromDecodedJson($result->retweeted_status));
        }

        return $message;
    }

    private static function runParsers(object $result): string
    {
        $parsedText = HashTagParser::parse($result->full_text);
        $parsedText = MentionParser::parse($parsedText);

        if (isset($result->entities->media[0]) && $result->entities->media[0]->type === 'photo') {
            $parsedText = PhotoParser::parse($parsedText, $result->entities->media);
        }

        return  URLParser::parse($parsedText, $result->entities->urls);
    }

    private static function createFromDecodedJson(object $result): TwitterMessage
    {
        $message = new TwitterMessage($result->fetchSource ?? 'API', 'twitter.twig');
        $message->setNetwork('twitter');
        $message->setBody($result->full_text);
        $message->setURL('https://twitter.com/statuses/'.$result->id);
        $message->setDate(\DateTime::createFromFormat('D M d H:i:s O Y', $result->created_at));
        $message->setAuthor($result->user->name);
        $message->setAuthorURL('https://twitter.com/'.$result->user->screen_name);
        $message->setAuthorDescription($result->user->description);
        $message->setScreenName($result->user->screen_name);
        $message->setAuthorThumbnail($result->user->profile_image_url_https);
        $message->setParsedBody(self::runParsers($result));

        return $message;
    }
}
