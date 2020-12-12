<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('milosa_social_media_aggregator');

        $rootNode = $treeBuilder->getRootNode();

        $this->createNetworksNode($rootNode);

        return $treeBuilder;
    }

    private function createNetworksNode(ArrayNodeDefinition $rootNode): void
    {
        $networksNode = $rootNode
            ->children()
            ->arrayNode('networks')
            ->children();

//        foreach ($this->plugins as $plugin) {
//            $pluginNode = new ArrayNodeDefinition($plugin->getPluginName());
//            $plugin->addConfiguration($pluginNode);
//
//            $networksNode->append($pluginNode);
//        }

        $networksNode->append($this->getTwitterNodeDefinition());
        $networksNode->append($this->getYoutubeNodeDefinition());
    }

    public function getTwitterNodeDefinition(): ArrayNodeDefinition
    {
        $twitterNode = new ArrayNodeDefinition('twitter');
        $twitterNode
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

        return $twitterNode;
    }

    public function getYoutubeNodeDefinition(): ArrayNodeDefinition
    {
        $youtubeNode = new ArrayNodeDefinition('youtube');
        $youtubeNode
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

        return $youtubeNode;
    }
}
