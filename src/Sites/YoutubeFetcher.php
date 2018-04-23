<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Sites;

use Milosa\SocialMediaAggregatorBundle\Message;

class YoutubeFetcher extends Fetcher
{
    public function __construct()
    {
    }

    public function getData(): array
    {
        $jsonArray = json_decode($this->data, true);
        $result = [];

        foreach ($jsonArray['items'] as $key => $value) {
            $message = new Message();
            $message->setBody($value['snippet']['description']);
            $message->setURL('https://www.youtube.com/watch?v='.$value['id']['videoId']);
            $message->setDate(\DateTime::createFromFormat('Y-m-d\TH:i:s\.000\Z', $value['snippet']['publishedAt']));
            $result[$key] = $message;
        }

        return $result;
    }
}
