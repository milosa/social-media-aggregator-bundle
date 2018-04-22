<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Sites\API;

class TwitterApiData implements APIData
{
    /**
     * @var string
     */
    private $consumerKey;

    /**
     * @var string
     */
    private $consumerSecret;

    /**
     * @var string
     */
    private $oauthToken;

    /**
     * @var string
     */
    private $oauthTokenSecret;

    public function __construct(string $consumerKey, string $consumerSecret, string $oathToken, string $oathTokenSecret)
    {
        /**
         * @todo validate values
         */
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->oauthToken = $oathToken;
        $this->oauthTokenSecret = $oathTokenSecret;
    }

    /**
     * @return string
     */
    public function getOauthTokenSecret(): string
    {
        return $this->oauthTokenSecret;
    }

    /**
     * @return string
     */
    public function getOauthToken(): string
    {
        return $this->oauthToken;
    }

    /**
     * @return string
     */
    public function getConsumerSecret(): string
    {
        return $this->consumerSecret;
    }

    /**
     * @return string
     */
    public function getConsumerKey(): string
    {
        return $this->consumerKey;
    }
}
