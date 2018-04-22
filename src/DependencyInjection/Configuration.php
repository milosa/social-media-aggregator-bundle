<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\DependencyInjection;

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
                    ->children()
                        ->arrayNode('auth_data')
                            ->isRequired()
                            ->children()
                                ->scalarNode('consumer_key')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('consumer_secret')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('oauth_token')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('oauth_token_secret')
                                    ->isRequired()
                                ->end()
                            ->end()
                        ->end() //auth data
                        ->integerNode('fetch_interval')
                            ->defaultValue(60)
                            ->info('Minimum time between fetches in minutes.')
                        ->end()
                        ->integerNode('number_of_tweets')
                            ->defaultValue(10)
                        ->end()
                        ->scalarNode('account_to_fetch')
                            ->isRequired()
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
                    ->end()//children
                ->end() //twitter
            ->end()
        ->end();

        return $treeBuilder;
    }
}
