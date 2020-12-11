<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Tests\DependencyInjection;

use Milosa\SocialMediaAggregatorBundle\DependencyInjection\Configuration;
use Milosa\SocialMediaAggregatorBundle\DependencyInjection\MilosaSocialMediaAggregatorExtension;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MilosaSocialMediaAggregatorTest extends TestCase
{
    public function testLoad(): void
    {
        $container = $this->createContainer();
        $extension = new MilosaSocialMediaAggregatorExtension();
        $extension->load($this->createFakeConfig(), $container);

        $this->assertTrue($container->hasDefinition('milosa_social_media_aggregator.aggregator'));
        $this->assertTrue($container->hasAlias('Milosa\SocialMediaAggregatorBundle\Aggregator\SocialMediaAggregator'));
        $this->assertTrue($container->hasDefinition('milosa_social_media_aggregator.controller.api_controller'));
    }

    public function testGetConfiguration(): void
    {
        $extension = new MilosaSocialMediaAggregatorExtension();
        $this->assertInstanceOf(Configuration::class, $extension->getConfiguration([], new ContainerBuilder()));
    }

    private function createContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->set('event_dispatcher', $this->createMock(EventDispatcherInterface::class));
        $container->set('logger', $this->createMock(LoggerInterface::class));

        return $container;
    }

    private function createFakeConfig(bool $enableCache = false): array
    {
        return [
            [
                'networks' => [
                    'twitter' => [
                        'auth_data' => [
                            'consumer_key' => 'fake_consumer_key',
                            'consumer_secret' => 'fake_consumer_secret',
                            'oauth_token' => 'fake_oauth_token',
                            'oauth_token_secret' => 'fake_oauth_token_secret',
                        ],
                        'enable_cache' => $enableCache,
                        'cache_lifetime' => 123,
                        'sources' => [
                            [
                                'search_type' => 'profile',
                                'search_term' => 'test',
                                'number_of_tweets' => 2,
                                'image_size' => 'thumb',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
