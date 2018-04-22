<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Milosa\SocialMediaAggregator\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }

    public function testEmptyConfigurationIsValid(): void
    {
        $this->assertConfigurationIsValid(
            [
                [],
            ]
        );
    }

    public function testIfTwitterIsProvidedOmittingRequiredValuesIsInvalid(): void
    {
        $this->assertConfigurationIsInvalid(
            [
               ['twitter' => []],
            ]
        );
    }

    public function testProvidingValidDataIsValid(): void
    {
        $this->assertConfigurationIsValid(
            [
                ['twitter' => [
                    'auth_data' => [
                        'consumer_key' => 'test_key',
                        'consumer_secret' => 'test_secret',
                        'oauth_token' => 'test_token',
                        'oauth_token_secret' => 'test_secret_token',
                    ],
                    'fetch_interval' => 720,
                    'template' => 'twitter.twig',
                    'show_images' => true,
                    'hashtag_links' => true,
                    'account_to_fetch' => 'realDonaldTrump',
                ],
            ],
        ]);

        $this->assertConfigurationIsValid(
            [
                ['twitter' => [
                    'auth_data' => [
                        'consumer_key' => 'test_key',
                        'consumer_secret' => 'test_secret',
                        'oauth_token' => 'test_token',
                        'oauth_token_secret' => 'test_secret_token',
                    ],
                    'account_to_fetch' => 'realDonaldTrump',
                ],
            ],
        ]);
    }

    public function testDefaultValues(): void
    {
        $this->assertProcessedConfigurationEquals(
            [
                ['twitter' => [
                    'auth_data' => [
                        'consumer_key' => 'test_key',
                        'consumer_secret' => 'test_secret',
                        'oauth_token' => 'test_token',
                        'oauth_token_secret' => 'test_secret_token',
                    ],
                    'account_to_fetch' => 'realDonaldTrump',
                ],
            ],
        ],
         ['twitter' => [
                'auth_data' => [
                    'consumer_key' => 'test_key',
                    'consumer_secret' => 'test_secret',
                    'oauth_token' => 'test_token',
                    'oauth_token_secret' => 'test_secret_token',
                ],
             'fetch_interval' => 60,
             'template' => 'twitter.twig',
             'show_images' => true,
             'hashtag_links' => true,
             'account_to_fetch' => 'realDonaldTrump',
             'number_of_tweets' => 10,
             ],
            ]
        );
    }
}
