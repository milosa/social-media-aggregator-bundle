<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter;

use Milosa\SocialMediaAggregatorBundle\MilosaSocialMediaAggregatorPlugin;
use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Twitter\DependencyInjection\TwitterPluginExtension;
use function realpath;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TwitterPlugin extends Bundle implements MilosaSocialMediaAggregatorPlugin
{
    public function getPluginName(): string
    {
        return 'twitter';
    }

    public function getResourcesPath(): string
    {
        return realpath(__DIR__.'/../Resources');
    }

    public function load(array $config, ContainerBuilder $container): void
    {
        $extension = new TwitterPluginExtension();
        $extension->load($config, $container);
        $this->setContainerParameters($config, $container);
        $this->configureCaching($config, $container);
        $this->registerHandler($container);
        $this->addFetchers($config, $container);
    }

    private function addFetchers(array $config, ContainerBuilder $container): void
    {
        $fetchers = [];

        foreach ($config['plugins']['twitter']['sources'] as $source) {
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

    public function addConfiguration(ArrayNodeDefinition $pluginNode): void
    {
        $pluginNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('auth_data')
                    ->addDefaultsIfNotSet()
                    ->isRequired()
                    ->children()
                        ->scalarNode('consumer_key')->defaultNull()->end()
                        ->scalarNode('consumer_secret')->defaultNull()->end()
                        ->scalarNode('oauth_token')->defaultNull()->end()
                        ->scalarNode('oauth_token_secret')->defaultNull()->end()
                    ->end()
                ->end()
                ->arrayNode('sources')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->arrayPrototype()
                        ->children()
                            ->enumNode('search_type')->values(['profile', 'hashtag'])->defaultValue('profile')->end()
                            ->scalarNode('search_term')->isRequired()->end()
                            ->integerNode('number_of_tweets')->defaultValue(10)->end()
                            ->enumNode('image_size')->values(['thumb', 'large', 'medium', 'small'])->defaultValue('thumb')->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('enable_cache')->defaultValue(false)->end()
                ->integerNode('cache_lifetime')->info('Cache lifetime in seconds')->defaultValue(3600)->end()
                ->scalarNode('template')->defaultValue('twitter.twig')->end()
            ->end();
    }

    public function setContainerParameters(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('milosa_social_media_aggregator.twitter_consumer_key', $config['plugins']['twitter']['auth_data']['consumer_key']);
        $container->setParameter('milosa_social_media_aggregator.twitter_consumer_secret', $config['plugins']['twitter']['auth_data']['consumer_secret']);
        $container->setParameter('milosa_social_media_aggregator.twitter_oauth_token', $config['plugins']['twitter']['auth_data']['oauth_token']);
        $container->setParameter('milosa_social_media_aggregator.twitter_oauth_token_secret', $config['plugins']['twitter']['auth_data']['oauth_token_secret']);
    }

    /**
     * @todo check fetcher definition
     * @todo check if this can be a abstract method?
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    public function configureCaching(array $config, ContainerBuilder $container): void
    {
        if ($config['plugins']['twitter']['enable_cache'] === true) {
            $cacheDefinition = new Definition(FilesystemAdapter::class, [
                'milosa_social',
                $config['plugins']['twitter']['cache_lifetime'],
                '%kernel.cache_dir%',
            ]);

            $container->setDefinition('milosa_social_media_aggregator.twitter_cache', $cacheDefinition)->addTag('cache.pool');
            $fetcherDefinition = $container->getDefinition('milosa_social_media_aggregator.fetcher.twitter.abstract');
            $fetcherDefinition->addMethodCall('setCache', [new Reference('milosa_social_media_aggregator.twitter_cache')]);
        }
    }

    private function registerHandler(ContainerBuilder $container): void
    {
        $aggregatorDefinition = $container->getDefinition('milosa_social_media_aggregator.aggregator');
        $aggregatorDefinition->addMethodCall('addHandler', [new Reference('milosa_social_media_aggregator.handler.twitter')]);
    }
}
