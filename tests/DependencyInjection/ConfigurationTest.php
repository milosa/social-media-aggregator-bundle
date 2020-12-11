<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregator\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Milosa\SocialMediaAggregatorBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }

    public function testEmptyConfigurationIsValid(): void
    {
        $this->assertConfigurationIsValid(
            [
                [],
            ]
        );
    }
}
