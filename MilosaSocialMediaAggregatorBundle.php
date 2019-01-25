<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle;

use Milosa\SocialMediaAggregatorBundle\DependencyInjection\MilosaSocialMediaAggregatorExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MilosaSocialMediaAggregatorBundle extends Bundle
{
    /**
     * @var MilosaSocialMediaAggregatorPlugin[] $plugins
     */
    protected $plugins = [];

    public function __construct(array $plugins = [])
    {
        foreach ($plugins as $plugin) {
            $this->registerPlugin($plugin);
        }
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        foreach ($this->plugins as $plugin) {
            $plugin->build($container);
        }
    }

    public function boot()
    {
        foreach ($this->plugins as $plugin) {
            $plugin->boot();
        }
    }

    public function getContainerExtension() : ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new MilosaSocialMediaAggregatorExtension($this->plugins);
        }

        return $this->extension;
    }

    /**
     * @param MilosaSocialMediaAggregatorPlugin $plugin
     *
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     *
     * @return void
     */
    protected function registerPlugin(MilosaSocialMediaAggregatorPlugin $plugin): void
    {
        foreach ($this->plugins as $registeredPlugin) {
            if ($registeredPlugin->getPluginName() === $plugin->getPluginName()) {
                throw new InvalidConfigurationException(sprintf(
                    'Trying to connect two plugins with same name: %s',
                    $plugin->getPluginName()
                ));
            }
        }

        $this->plugins[] = $plugin;
    }
}
