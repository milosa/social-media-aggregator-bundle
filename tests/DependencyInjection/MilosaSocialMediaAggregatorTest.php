<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\tests\DependencyInjection;

use Milosa\SocialMediaAggregatorBundle\DependencyInjection\Configuration;
use Milosa\SocialMediaAggregatorBundle\DependencyInjection\MilosaSocialMediaAggregatorExtension;
use Milosa\SocialMediaAggregatorBundle\MilosaSocialMediaAggregatorPlugin;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MilosaSocialMediaAggregatorTest extends TestCase
{
    private $config;

    public function testLoad(): void
    {
        $container = $this->createContainer();
        $extension = new MilosaSocialMediaAggregatorExtension();
        $extension->load([], $container);

        $this->assertTrue($container->hasDefinition('milosa_social_media_aggregator.aggregator'));
        $this->assertTrue($container->hasAlias('Milosa\SocialMediaAggregatorBundle\Aggregator\SocialMediaAggregator'));
        $this->assertTrue($container->hasDefinition('milosa_social_media_aggregator.controller.api_controller'));
    }

    public function testLoadWithPlugin(): void
    {
        $container = $this->createContainer();
        $plugin = new PluginSpy();

        $extension = new MilosaSocialMediaAggregatorExtension([$plugin]);

        $extension->load([], $container);
        $extension->prepend($container);

        $this->assertTrue($plugin->loadIsCalled);
        $this->assertTrue($plugin->getPluginNameIsCalled);
        $this->assertTrue($plugin->getTwigPathIsCalled);
        $this->assertTrue($plugin->addConfigurationIsCalled);
    }

    public function testGetConfiguration(): void
    {
        $extension = new MilosaSocialMediaAggregatorExtension();
        $this->assertInstanceOf(Configuration::class, $extension->getConfiguration([], new ContainerBuilder()));
    }

    private function createContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->set('event_dispatcher', $this->createMock(EventDispatcherInterface::class));
        $container->set('logger', $this->createMock(LoggerInterface::class));

        return $container;
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->config = null;
    }
}

class PluginSpy implements MilosaSocialMediaAggregatorPlugin
{
    public $loadIsCalled = false;
    public $getTwigPathIsCalled = false;
    public $getPluginNameIsCalled = false;
    public $addConfigurationIsCalled = false;

    public function getPluginName(): string
    {
        $this->getPluginNameIsCalled = true;

        return 'test_plugin';
    }

    public function getTwigPath(): string
    {
        $this->getTwigPathIsCalled = true;

        return __DIR__;
    }

    public function addConfiguration(ArrayNodeDefinition $pluginNode): void
    {
        $this->addConfigurationIsCalled = true;
        $pluginNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('test_value')->defaultValue(false)->end()
            ->end();
    }

    public function load(array $config, ContainerBuilder $container): void
    {
        $this->loadIsCalled = true;
    }

    public function setContainerParameters(array $config, ContainerBuilder $container): void
    {
    }

    public function configureCaching(array $config, ContainerBuilder $container): void
    {
    }
}
