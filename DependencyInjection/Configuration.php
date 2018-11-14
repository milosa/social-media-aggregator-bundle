<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\DependencyInjection;

use Milosa\SocialMediaAggregatorBundle\MilosaSocialMediaAggregatorPlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @var MilosaSocialMediaAggregatorPlugin[]
     */
    private $plugins;
    private $alias;
    private $debug;

    /**
     * Configuration constructor.
     *
     * @param MilosaSocialMediaAggregatorPlugin[] $plugins
     */
    public function __construct(string $alias, bool $debug = false, array $plugins = [])
    {
        $this->plugins = $plugins;
        $this->alias = $alias;
        $this->debug = $debug;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('milosa_social_media_aggregator');

//        $rootNode
//            ->children()
//                ->append($this->createPluginsNode())
//            ->end()
//        ->end();

        $this->createPluginsNode($rootNode);

//        $rootNode
//            ->children()
//                ->arrayNode('youtube')
        ////                    ->addDefaultsIfNotSet()
//                    ->children()
//                        ->arrayNode('auth_data')
//                            ->addDefaultsIfNotSet()
//                            ->isRequired()
//                                ->children()
//                                    ->scalarNode('api_key')->defaultNull()->end()
//                            ->end()
//                        ->end()
//                            ->booleanNode('enable_cache')
//                            ->defaultValue(false)
//                        ->end()
//                            ->integerNode('cache_lifetime')
//                            ->info('Cache lifetime in seconds')
//                            ->defaultValue(3600)
//                        ->end()
//                            ->integerNode('number_of_items')
//                            ->defaultValue(10)
//                        ->end()
//                            ->scalarNode('channel_id')->defaultNull()->info('Channel id of youtube channel. Click on the name of a channel when viewing a youtube video. You\'ll find the channel-id in the URL.')
//                        ->end()
//                            ->scalarNode('template')
//                            ->defaultValue('youtube.twig')
//                        ->end()
//                    ->end()
//                ->end()
//            ->end()
//        ->end();
        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function createPluginsNode(ArrayNodeDefinition $rootNode)
    {
//        $treeBuilder = new TreeBuilder();
//        $node = $treeBuilder->root('plugins');
//        $node->useAttributeAsKey('name')->prototype('array')->children();
        ////        $nodeChildren = $node;
        ///

        $pluginsNode = $rootNode
            ->children()
                ->arrayNode('plugins')
                ->children();

        foreach ($this->plugins as $plugin) {
//            $plugin->addConfiguration($node->children()
//                ->arrayNode($plugin->getPluginName()));
            $pluginNode = new ArrayNodeDefinition($plugin->getPluginName());
            $plugin->addConfiguration($pluginNode);

//            var_dump($pluginNode);

            $pluginsNode->append($pluginNode);
        }

//        return $node;
    }
}
