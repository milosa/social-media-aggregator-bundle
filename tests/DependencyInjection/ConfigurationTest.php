<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Milosa\SocialMediaAggregatorBundle\DependencyInjection\Configuration;
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

    public function testIfYoutubeIsProvidedOmittingRequiredValuesIsInvalid(): void
    {
        $this->assertConfigurationIsInvalid(
            [
                ['youtube' => []],
            ]
        );
    }

    public function testProvidingValidDataIsValid(): void
    {
        $this->assertValidTwitterConfig();
        $this->assertValidYoutubeConfig();
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
             'template' => 'twitter.twig',
             'show_images' => true,
             'hashtag_links' => true,
             'account_to_fetch' => 'realDonaldTrump',
             'number_of_tweets' => 10,
             'enable_cache' => false,
             'cache_lifetime' => 3600,
             'image_size' => 'thumb',
             ],
         ]);

        $this->assertProcessedConfigurationEquals(
        [
            ['youtube' => [
                'auth_data' => [
                    'api_key' => 'test_key',
                    ],
                'channel_id' => 'test_id',
                ],
            ],
        ],
        ['youtube' => [
            'auth_data' => [
                'api_key' => 'test_key',
                ],
            'channel_id' => 'test_id',
            'enable_cache' => false,
            'cache_lifetime' => 3600,
            'number_of_items' => 10,
            'template' => 'youtube.twig',
            ],
        ]);
    }

//    public function assertGeneralDefaultConfig(): void
//    {
//        $this->assertProcessedConfigurationEquals([], [])
//    }

    private function assertValidTwitterConfig(): void
    {
        $this->assertConfigurationIsValid(
            [
                [
                    'twitter' => [
                        'auth_data' => [
                            'consumer_key' => 'test_key',
                            'consumer_secret' => 'test_secret',
                            'oauth_token' => 'test_token',
                            'oauth_token_secret' => 'test_secret_token',
                        ],
                        'cache_lifetime' => 720,
                        'template' => 'twitter.twig',
                        'show_images' => true,
                        'hashtag_links' => true,
                        'account_to_fetch' => 'realDonaldTrump',
                    ],
                ],
            ],
            'twitter');

        $this->assertConfigurationIsValid(
            [
                [
                    'twitter' => [
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
            'twitter');
    }

    private function assertValidYoutubeConfig(): void
    {
        $this->assertConfigurationIsValid([
            ['youtube' => [
                    'auth_data' => [
                        'api_key' => 'test_key',
                    ],
                    'channel_id' => 'UCUtWNBWbFL9We-cdXkiAuJA',
                    'template' => 'youtube.twig',
                    'enable_cache' => true,
                    'cache_lifetime' => 3600,
                    'number_of_items' => 5,
                ],
            ],
        ], 'youtube');
    }
}
