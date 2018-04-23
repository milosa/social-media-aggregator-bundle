<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class MilosaSocialMediaAggregatorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $configuration = new Configuration();
        $loader->load('milosa_social.xml');
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('milosa_social_media_aggregator.twitter_consumer_key', $config['twitter']['auth_data']['consumer_key']);
        $container->setParameter('milosa_social_media_aggregator.twitter_consumer_secret', $config['twitter']['auth_data']['consumer_secret']);
        $container->setParameter('milosa_social_media_aggregator.twitter_oauth_token', $config['twitter']['auth_data']['oauth_token']);
        $container->setParameter('milosa_social_media_aggregator.twitter_oauth_token_secret', $config['twitter']['auth_data']['oauth_token_secret']);
        $container->setParameter('milosa_social_media_aggregator.twitter_numtweets', $config['twitter']['number_of_tweets']);
        $container->setParameter('milosa_social_media_aggregator.twitter_account', $config['twitter']['account_to_fetch']);
    }
}
