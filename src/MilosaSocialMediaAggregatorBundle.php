<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\Compiler\ResolveEnvPlaceholdersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MilosaSocialMediaAggregatorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // $container->addCompilerPass(new ResolveEnvPlaceholdersPass(), PassConfig::TYPE_AFTER_REMOVING, -1000);
       // $container->compile(true);
    }
}
