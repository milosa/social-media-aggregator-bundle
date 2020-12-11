<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle;

use Milosa\SocialMediaAggregatorBundle\DependencyInjection\MilosaSocialMediaAggregatorExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MilosaSocialMediaAggregatorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    public function boot()
    {
    }

    public function getContainerExtension(): ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new MilosaSocialMediaAggregatorExtension();
        }

        return $this->extension;
    }
}
