<?php
namespace Milosa\SocialMediaAggregatorBundle;


use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface MilosaSocialMediaAggregatorPlugin
{
    public function getPluginName(): string;
    public function addConfiguration(ArrayNodeDefinition $pluginNode): void;
    public function load(array $config, ContainerBuilder $container): void;
    public function setContainerParameters(array $config, ContainerBuilder $container): void;
    public function configureCaching(array $config, ContainerBuilder $container): void;
}