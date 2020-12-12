# Social media aggregator
![CI](https://github.com/milosa/social-media-aggregator-bundle/workflows/CI/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/milosa/social-media-aggregator-bundle/badge.svg?branch=master)](https://coveralls.io/github/milosa/social-media-aggregator-bundle?branch=master)

Symfony Bundle to combine messages from different social media platforms into one feed. 

![Explanation of Milosa Social Media Aggregator](doc/milosa_social_media_aggregator_explanation.png)

## Features
* Easy way to get messages from various social media platforms. Such as:
  * Twitter
  * Facebook*
  * Youtube
* Twig integration*
* Sorting of messages*
  * Sorting by date (default)
  * Sorting by platform (e.g. first show youtube items, then show twitter)*
* Caching
* Render messages with PHP or React
* Multiple searches for each platform

*= Not implemented yet.
  
## Installation

`composer require milosa/social-media-aggregator-bundle`