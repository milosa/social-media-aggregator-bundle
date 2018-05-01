# Social media aggregator
[![Build Status](https://travis-ci.org/milosa/social-media-aggregator-bundle.svg?branch=master)](https://travis-ci.org/milosa/social-media-aggregator-bundle)
[![Coverage Status](https://coveralls.io/repos/github/milosa/social-media-aggregator-bundle/badge.svg?branch=master)](https://coveralls.io/github/milosa/social-media-aggregator-bundle?branch=master)

Symfony Bundle to combine messages from different social media platforms into one feed. 

## Features
* Easy way to get messages from various social media platforms. Such as:
  * Twitter
  * Facebook*
  * Youtube
* Twig integration*
* Sorting of messages*
  * Sorting by date*
  * Sorting by platform (e.g. first show youtube items, then show twitter)*
* Caching

*= Not implemented yet.
  
## Installation

`composer require milosa/social-media-aggregator-bundle`

## Usage

Add `milosa_social_media_aggregator` to your configuration.

### Sample config file
    milosa_social_media_aggregator:
        twitter:
            auth_data:
                consumer_key: '%env(TWITTER_CONSUMER_KEY)%'
                consumer_secret: '%env(TWITTER_CONSUMER_SECRET)%'
                oauth_token: '%env(TWITTER_OAUTH_TOKEN)%'
                oauth_token_secret: '%env(TWITTER_OAUTH_TOKEN_SECRET)%'
            number_of_tweets: 20
            account_to_fetch: FamilyGuyonFOX
            template: twitter.twig
            fetch_interval: 120
        youtube:
            auth_data: 
                api_key: '%env(YOUTUBE_API_KEY)%'
            enable_cache: true
            cache_lifetime: 3600
            number_of_items: 2
            channel_id: UCLA_DiR1FfKNvjuUpBHmylQ
