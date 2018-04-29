<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorTests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Milosa\SocialMediaAggregatorBundle\DependencyInjection\MilosaSocialMediaAggregatorExtension;

class MilosaSocialMediaAggregatorExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new MilosaSocialMediaAggregatorExtension(),
        ];
    }

    public function testAfterLoadingCorrectParametersHaveBeenSet()
    {
        $this->load();

        $this->checkTwitterParameters();
        $this->checkYoutubeParameters();
    }

    /**
     * @test
     */
    public function after_loading_the_correct_services_have_been_defined()
    {
        $this->load();
        $this->assertContainerBuilderHasService('Abraham\TwitterOAuth\TwitterOAuth');
        $this->assertContainerBuilderHasService('Milosa\SocialMediaAggregatorBundle\Sites\TwitterFetcher');
        $this->assertContainerBuilderHasService('twig.extension.date');
        $this->assertContainerBuilderHasService('Milosa\SocialMediaAggregatorBundle\SocialMediaAggregator');
    }

    /**
     * @test
     */
    public function after_loading_with_cache_enabled_the_correct_services_have_been_defined()
    {
        $this->load(['twitter' => ['enable_cache' => true], 'youtube' => ['enable_cache' => true]]);
        $this->assertContainerBuilderHasService('Abraham\TwitterOAuth\TwitterOAuth');
        $this->assertContainerBuilderHasService('Milosa\SocialMediaAggregatorBundle\Sites\TwitterFetcher');
        $this->assertContainerBuilderHasService('Milosa\SocialMediaAggregatorBundle\Sites\YoutubeFetcher');
        $this->assertContainerBuilderHasService('twig.extension.date');
        $this->assertContainerBuilderHasService('Milosa\SocialMediaAggregatorBundle\SocialMediaAggregator');
        $this->assertContainerBuilderHasService('milosa_social_media_aggregator.twitter_cache');
        $this->assertContainerBuilderHasService('milosa_social_media_aggregator.youtube_cache');
    }

    protected function getMinimalConfiguration()
    {
        return ['twitter' => [
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
            'youtube' => [
                'auth_data' => [
                    'api_key' => 'test_api_key',
                ],
                'channel_id' => 'test-channel-id',
                'number_of_items' => 5,
            ],
            ];
    }

    private function checkTwitterParameters(): void
    {
        $this->assertContainerBuilderHasParameter('milosa_social_media_aggregator.twitter_consumer_key', 'test_key');
        $this->assertContainerBuilderHasParameter('milosa_social_media_aggregator.twitter_consumer_secret',
            'test_secret');
        $this->assertContainerBuilderHasParameter('milosa_social_media_aggregator.twitter_oauth_token', 'test_token');
        $this->assertContainerBuilderHasParameter('milosa_social_media_aggregator.twitter_oauth_token_secret',
            'test_secret_token');
        $this->assertContainerBuilderHasParameter('milosa_social_media_aggregator.twitter_numtweets', 10);
        $this->assertContainerBuilderHasParameter('milosa_social_media_aggregator.twitter_account', 'realDonaldTrump');
    }

    private function checkYoutubeParameters(): void
    {
        $this->assertContainerBuilderHasParameter('milosa_social_media_aggregator.youtube_api_key', 'test_api_key');
        $this->assertContainerBuilderHasParameter('milosa_social_media_aggregator.youtube_channel_id', 'test-channel-id');
        $this->assertContainerBuilderHasParameter('milosa_social_media_aggregator.youtube_number_of_items', 5);
    }
}
