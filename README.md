# Social media aggregator
![CI](https://github.com/milosa/social-media-aggregator-bundle/workflows/CI/badge.svg)
[![Coverage Status](https://coveralls.io/repos/github/milosa/social-media-aggregator-bundle/badge.svg?branch=master)](https://coveralls.io/github/milosa/social-media-aggregator-bundle?branch=master)

Symfony Bundle to combine messages from different social media platforms into one feed. 

![Explanation of Milosa Social Media Aggregator](Resources/doc/milosa_social_media_aggregator_explanation.png)

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

## Configuration

Todo

## React

To use React:

* Run `php bin/console assets:install public` in your application, to copy the JS and CSS files from the bundle to your project.
* Add the following to your applications `framework.yaml` file:
  ```yaml
  framework:
      assets:
        packages:
            milosasocialmediaaggregator:
                # this package uses its own manifest (the default file is ignored)
                json_manifest_path: "%kernel.project_dir%/public/bundles/milosasocialmediaaggregator/build/manifest.json"
  ```
* Add the following tags to your page:
  ```html
  <link rel="stylesheet" type="text/css" href="{{ asset('/bundles/milosasocialmediaaggregator/build/app.css', 'milosasocialmediaaggregator') }}">
  <script src="{{ asset('/bundles/milosasocialmediaaggregator/build/app.js', 'milosasocialmediaaggregator') }}"></script>
  ```
  and
  ```html
  <div id="aggregator-app"></div>
  ```