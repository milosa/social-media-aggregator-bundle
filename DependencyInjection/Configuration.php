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

    /**
     * Configuration constructor.
     *
     * @param MilosaSocialMediaAggregatorPlugin[] $plugins
     */
    public function __construct(array $plugins = [])
    {
        $this->plugins = $plugins;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('milosa_social_media_aggregator');

        $this->createPluginsNode($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     *
     * @return void
     */
    private function createPluginsNode(ArrayNodeDefinition $rootNode): void
    {
        $pluginsNode = $rootNode
            ->children()
                ->arrayNode('plugins')
                ->children();

        foreach ($this->plugins as $plugin) {
            $pluginNode = new ArrayNodeDefinition($plugin->getPluginName());
            $plugin->addConfiguration($pluginNode);

            $pluginsNode->append($pluginNode);
        }
    }
}
