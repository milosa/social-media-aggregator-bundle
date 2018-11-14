<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use function is_dir;

class MilosaSocialMediaAggregatorExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var \Milosa\SocialMediaAggregatorBundle\MilosaSocialMediaAggregatorPlugin[] $plugins
     */
    private $plugins;

    public function __construct(array $plugins = [])
    {
        $this->plugins = $plugins;
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('milosa_social.xml');
        $configuration = new Configuration($this->getAlias(), $container->getParameter('kernel.debug'), $this->plugins);
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($this->plugins as $plugin) {
            $container->addObjectResource(new \ReflectionClass(get_class($plugin)));
            $plugin->load($config, $container);
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        foreach ($this->plugins as $plugin) {
            if (is_dir($plugin->getTwigPath())) {
                $container->prependExtensionConfig('twig', [
                    'paths' => [$plugin->getTwigPath() => 'MilosaSocialMediaAggregator'],
                ]);
            }
        }
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($this->getAlias(), $container->getParameter('kernel.debug'), $this->plugins);
    }

//    protected function configureYoutubeCaching(ContainerBuilder $container, int $lifetime): void
//    {
//        $cacheDefinition = new Definition(FilesystemAdapter::class, [
//            'milosa_social',
//            $lifetime,
//            '%kernel.cache_dir%',
//            ]);
//
////        $cacheDefinition->setTags(['name' => 'cache.pool']);
//        $container->setDefinition('milosa_social_media_aggregator.youtube_cache', $cacheDefinition)->addTag('cache.pool');
//        $fetcherDefinition = $container->getDefinition('milosa_social_media_aggregator.fetcher.youtube');
//        $fetcherDefinition->addMethodCall('setCache', [new Reference('milosa_social_media_aggregator.youtube_cache')]);
//    }
}
