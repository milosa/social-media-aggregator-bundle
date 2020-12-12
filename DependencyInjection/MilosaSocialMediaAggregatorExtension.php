<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\DependencyInjection;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class MilosaSocialMediaAggregatorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('milosa_social.xml');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->setTwitterParameters($config, $container);
        $this->addTwitterFetchers($config, $container);
        $this->configureTwitterCaching($config, $container);
        $this->registerTwitterHandler($container);

        $this->setYoutubeParameters($config, $container);
        $this->addYoutubeFetchers($config, $container);
        $this->configureYoutubeCaching($config, $container);
        $this->registerYoutubeHandler($container);
    }

    private function setTwitterParameters(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('milosa_social_media_aggregator.twitter_consumer_key', $config['networks']['twitter']['auth_data']['consumer_key']);
        $container->setParameter('milosa_social_media_aggregator.twitter_consumer_secret', $config['networks']['twitter']['auth_data']['consumer_secret']);
        $container->setParameter('milosa_social_media_aggregator.twitter_oauth_token', $config['networks']['twitter']['auth_data']['oauth_token']);
        $container->setParameter('milosa_social_media_aggregator.twitter_oauth_token_secret', $config['networks']['twitter']['auth_data']['oauth_token_secret']);
    }

    public function setYoutubeParameters(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('milosa_social_media_aggregator.youtube_api_key', $config['networks']['youtube']['auth_data']['api_key']);
    }

    private function addTwitterFetchers(array $config, ContainerBuilder $container): void
    {
        $fetchers = [];

        foreach ($config['networks']['twitter']['sources'] as $source) {
            $fetcher = new ChildDefinition('milosa_social_media_aggregator.fetcher.twitter.abstract');

            $fetcherSettings = [
                'search_term' => $source['search_term'],
                'number_of_tweets' => $source['number_of_tweets'],
                'image_size' => $source['image_size'],
                'search_type' => $source['search_type'],
            ];

            $fetcher->setArgument(1, $fetcherSettings);
            $container->setDefinition('milosa_social_media_aggregator.fetcher.twitter.'.$source['search_term'], $fetcher);

            $fetchers[] = $fetcher;
        }

        $handlerDefinition = $container->findDefinition('milosa_social_media_aggregator.handler.twitter');
        $handlerDefinition->setArgument(0, $fetchers);
    }

    private function addYoutubeFetchers(array $config, ContainerBuilder $container): void
    {
        $fetchers = [];

        foreach ($config['networks']['youtube']['sources'] as $source) {
            $fetcher = new ChildDefinition('milosa_social_media_aggregator.fetcher.youtube.abstract');

            $fetcherSettings = [
                'search_term' => $source['search_term'],
                'number_of_videos' => $source['number_of_videos'],
                'search_type' => $source['search_type'],
            ];

            $fetcher->setArgument(1, $fetcherSettings);
            $container->setDefinition('milosa_social_media_aggregator.fetcher.youtube.'.$source['search_term'], $fetcher);

            $fetchers[] = $fetcher;
        }

        $handlerDefinition = $container->findDefinition('milosa_social_media_aggregator.handler.youtube');
        $handlerDefinition->setArgument(0, $fetchers);
    }

    private function configureTwitterCaching(array $config, ContainerBuilder $container): void
    {
        if ($config['networks']['twitter']['enable_cache'] === true) {
            $cacheDefinition = new Definition(FilesystemAdapter::class, [
                'milosa_social',
                $config['networks']['twitter']['cache_lifetime'],
                '%kernel.cache_dir%',
            ]);

            $container->setDefinition('milosa_social_media_aggregator.twitter_cache', $cacheDefinition)->addTag('cache.pool');
            $fetcherDefinition = $container->getDefinition('milosa_social_media_aggregator.fetcher.twitter.abstract');
            $fetcherDefinition->addMethodCall('setCache', [new Reference('milosa_social_media_aggregator.twitter_cache')]);
        }
    }

    public function configureYoutubeCaching(array $config, ContainerBuilder $container): void
    {
        if ($config['networks']['youtube']['enable_cache'] === true) {
            $cacheDefinition = new Definition(FilesystemAdapter::class, [
                'milosa_social',
                $config['networks']['youtube']['cache_lifetime'],
                '%kernel.cache_dir%',
            ]);

            $container->setDefinition('milosa_social_media_aggregator.youtube_cache', $cacheDefinition)->addTag('cache.pool');
            $fetcherDefinition = $container->getDefinition('milosa_social_media_aggregator.fetcher.youtube.abstract');
            $fetcherDefinition->addMethodCall('setCache', [new Reference('milosa_social_media_aggregator.youtube_cache')]);
        }
    }

    private function registerTwitterHandler(ContainerBuilder $container): void
    {
        $aggregatorDefinition = $container->getDefinition('milosa_social_media_aggregator.aggregator');
        $aggregatorDefinition->addMethodCall('addHandler', [new Reference('milosa_social_media_aggregator.handler.twitter')]);
    }

    private function registerYoutubeHandler(ContainerBuilder $container): void
    {
        $aggregatorDefinition = $container->getDefinition('milosa_social_media_aggregator.aggregator');
        $aggregatorDefinition->addMethodCall('addHandler', [new Reference('milosa_social_media_aggregator.handler.youtube')]);
    }

    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration();
    }
}
