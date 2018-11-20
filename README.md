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

### Installation of a plugin
In your Symfony application, find kernel.php. Replace the `registerBundles` method with:

        public function registerBundles()
        {
            $contents = require $this->getProjectDir().'/config/bundles.php';
            foreach ($contents as $class => $envs) {
                if (isset($envs['all']) || isset($envs[$this->environment])) {
                    if($class === \Milosa\SocialMediaAggregatorBundle\MilosaSocialMediaAggregatorBundle::class)
                    {
                        //Each plugin class needs to be added to the array below 
                        yield new $class([
                            new \Milosa\SocialMediaAggregatorBundle\Twitter\TwitterPlugin(),
                        ]);
                    }
                    else {
                        yield new $class();
                    }
                }
            }
        }

## Usage

This bundle needs plugins in order to do something.

## Plugins

* [Twitter Plugin](https://github.com/milosa/social-media-aggregator-twitter-plugin)
* [Youtube Plugin](https://github.com/milosa/social-media-aggregator-youtube-plugin)
