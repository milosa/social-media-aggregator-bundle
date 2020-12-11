<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Youtube;

use Milosa\SocialMediaAggregatorBundle\Aggregator\Platform\Youtube\DependencyInjection\YoutubePluginExtension;
use Milosa\SocialMediaAggregatorBundle\MilosaSocialMediaAggregatorPlugin;
use function realpath;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class YoutubePlugin extends Bundle implements MilosaSocialMediaAggregatorPlugin
{
    public function getPluginName(): string
    {
        return 'youtube';
    }

    public function getResourcesPath(): string
    {
        return realpath(__DIR__.'/../Resources');
    }

    public function load(array $config, ContainerBuilder $container): void
    {
        $extension = new YoutubePluginExtension();
        $extension->load($config, $container);
        $this->setContainerParameters($config, $container);
        $this->configureCaching($config, $container);
        $this->registerHandler($container);
        $this->addFetchers($config, $container);
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
                    ->scalarNode('api_key')->defaultNull()->end()
                ->end()
            ->end()
            ->arrayNode('sources')
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->arrayPrototype()
                    ->children()
                        ->enumNode('search_type')->values(['channel'])->defaultValue('profile')->end()
                        ->scalarNode('search_term')->isRequired()->end()
                        ->integerNode('number_of_videos')->defaultValue(10)->end()
                    ->end()
                ->end()
            ->end()
            ->booleanNode('enable_cache')->defaultFalse()->end()
            ->integerNode('cache_lifetime')->info('Cache lifetime in seconds')->defaultValue(3600)->end()
            ->scalarNode('template')->defaultValue('youtube.twig')->end()
        ->end();
    }

    private function addFetchers(array $config, ContainerBuilder $container): void
    {
        $fetchers = [];

        foreach ($config['plugins']['youtube']['sources'] as $source) {
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

    private function registerHandler(ContainerBuilder $container): void
    {
        $aggregatorDefinition = $container->getDefinition('milosa_social_media_aggregator.aggregator');
        $aggregatorDefinition->addMethodCall('addHandler', [new Reference('milosa_social_media_aggregator.handler.youtube')]);
    }

    public function setContainerParameters(array $config, ContainerBuilder $container): void
    {
        $container->setParameter('milosa_social_media_aggregator.youtube_api_key', $config['plugins']['youtube']['auth_data']['api_key']);
    }

    public function configureCaching(array $config, ContainerBuilder $container): void
    {
        if ($config['plugins']['youtube']['enable_cache'] === true) {
            $cacheDefinition = new Definition(FilesystemAdapter::class, [
                'milosa_social',
                $config['plugins']['youtube']['cache_lifetime'],
                '%kernel.cache_dir%',
            ]);

            $container->setDefinition('milosa_social_media_aggregator.youtube_cache', $cacheDefinition)->addTag('cache.pool');
            $fetcherDefinition = $container->getDefinition('milosa_social_media_aggregator.fetcher.youtube.abstract');
            $fetcherDefinition->addMethodCall('setCache', [new Reference('milosa_social_media_aggregator.youtube_cache')]);
        }
    }
}
