<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('milosa_social');
        $rootNode
            //->isRequired()
            ->children()
                ->arrayNode('twitter')
//                    ->addDefaultsIfNotSet()
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
                        ->end() //auth data
                        ->booleanNode('enable_cache')
                            ->defaultValue(false)
                        ->end()
                        ->integerNode('cache_lifetime')
                            ->info('Cache lifetime in seconds')
                            ->defaultValue(3600)
                        ->end()
                        ->integerNode('number_of_tweets')
                            ->defaultValue(10)
                        ->end()
                        ->scalarNode('account_to_fetch')
                            ->isRequired()
                            ->defaultNull()
                            ->info('Screen name of the account you want to fetch the timeline of.')
                        ->end()
                        ->scalarNode('template')
                            ->defaultValue('twitter.twig')
                        ->end()
                        ->booleanNode('show_images')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('hashtag_links')
                            ->defaultTrue()
                        ->end()
                        ->enumNode('image_size')
                            ->values(['thumb', 'large', 'medium', 'small'])
                            ->defaultValue('thumb')
                        ->end()
                    ->end()//children
                ->end() //twitter
                ->arrayNode('youtube')
//                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('auth_data')
                            ->addDefaultsIfNotSet()
                            ->isRequired()
                                ->children()
                                    ->scalarNode('api_key')->defaultNull()->end()
                            ->end()
                        ->end()
                            ->booleanNode('enable_cache')
                            ->defaultValue(false)
                        ->end()
                            ->integerNode('cache_lifetime')
                            ->info('Cache lifetime in seconds')
                            ->defaultValue(3600)
                        ->end()
                            ->integerNode('number_of_items')
                            ->defaultValue(10)
                        ->end()
                            ->scalarNode('channel_id')->defaultNull()->info('Channel id of youtube channel. Click on the name of a channel when viewing a youtube video. You\'ll find the channel-id in the URL.')
                        ->end()
                            ->scalarNode('template')
                            ->defaultValue('youtube.twig')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
