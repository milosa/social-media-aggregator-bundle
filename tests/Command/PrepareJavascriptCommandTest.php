<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\tests\Command;

use Milosa\SocialMediaAggregatorBundle\Command\PrepareJavascriptCommand;
use Milosa\SocialMediaAggregatorBundle\MilosaSocialMediaAggregatorBundle;
use Milosa\SocialMediaAggregatorBundle\MilosaSocialMediaAggregatorPlugin;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class PrepareJavascriptCommandTest extends TestCase
{
    private $fs;
    /** @var TestAppKernel */
    private $kernel;

    protected function setUp()
    {
        $this->fs = new Filesystem();
        $this->kernel = new TestAppKernel('test', true);
        $this->fs->mkdir($this->kernel->getProjectDir());
    }

    protected function tearDown()
    {
        $this->fs->remove($this->kernel->getProjectDir());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /.*milosa-social already exists$/
     * @
     */
    public function testWhenOverwriteIsDisabledThrowsExceptionIfDirectoryExists(): void
    {
        $kernel = new TestAppKernel('test', true);
        $application = new Application($kernel);
        $fileSystem = $this->prophesize(Filesystem::class);
        $fileSystem->exists(Argument::exact($kernel->getProjectDir().\DIRECTORY_SEPARATOR.'assets'.\DIRECTORY_SEPARATOR.'milosa-social'))->willReturn(true);

        $command = new PrepareJavascriptCommand($fileSystem->reveal(), []);
        $application->add($command);
        $tester = new CommandTester($application->find('milosa-social:prepare-javascript'));
        $tester->execute([]);
    }

    public function testWhenOverwriteIsEnabledAndDirectoryExistsDeletesDirectory(): void
    {
        $application = new Application($this->kernel);
        $fileSystem = $this->prophesize(Filesystem::class);
        $absolutePath = $this->kernel->getProjectDir().\DIRECTORY_SEPARATOR.'assets'.\DIRECTORY_SEPARATOR.'milosa-social';
        $fileSystem->exists(Argument::exact($absolutePath))->willReturn(true);
        $fileSystem->exists(Argument::exact('test_plugin_path'.\DIRECTORY_SEPARATOR.'js'))->willReturn(true);
        $fileSystem->remove(Argument::exact($absolutePath))->shouldBeCalled();
        $fileSystem->mkdir(Argument::exact($absolutePath))->shouldBeCalled();
        $fileSystem->mirror(Argument::any(), Argument::any(), Argument::exact(null), Argument::exact(['override' => true, 'copy_on_windows' => true]))->shouldBeCalled();
        $fileSystem->exists(Argument::exact('test_plugin_path'.\DIRECTORY_SEPARATOR.'js'.\DIRECTORY_SEPARATOR.'testplugin.js'))->willReturn(true)->shouldBeCalled();
        $expectedPluginFileContents =
'import testplugin from "./networks/testplugin.js";

const networks = {testplugin: Testplugin};

export default networks;';

        $fileSystem->dumpFile(Argument::any(), Argument::exact($expectedPluginFileContents))->shouldBeCalled();

        $fileSystem->mirror(Argument::any(), Argument::any(), Argument::exact(null), Argument::exact(['override' => true, 'copy_on_windows' => true]))->shouldBeCalled();

        $command = new PrepareJavascriptCommand($fileSystem->reveal(), ['testplugin' => 'test_plugin_path']);
        $application->add($command);
        $tester = new CommandTester($application->find('milosa-social:prepare-javascript'));
        $tester->execute(['--overwrite' => true]);

        $this->assertContains('Removing directory', $tester->getDisplay());
    }

    public function getKernel(): TestAppKernel
    {
        return new TestAppKernel('test', true);
    }
}

class TestAppKernel extends Kernel
{
    public function __construct(string $environment, bool $debug, array $plugins = [])
    {
        parent::__construct($environment, $debug);
    }

    public function registerBundles()
    {
        return [
            new MilosaSocialMediaAggregatorBundle([
                new TestPlugin(),
            ]),
        ];
    }

    public function getProjectDir()
    {
        return 'fake_dir'.\DIRECTORY_SEPARATOR.'test';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.\DIRECTORY_SEPARATOR.'config.yml');
    }

    protected function build(ContainerBuilder $container)
    {
        $container->register('logger', NullLogger::class);
    }
}

class TestPlugin extends Bundle implements MilosaSocialMediaAggregatorPlugin
{
    public function getPluginName(): string
    {
        return 'testplugin';
    }

    public function getResourcesPath(): string
    {
        return 'test_plugin_path';
    }

    public function addConfiguration(ArrayNodeDefinition $pluginNode): void
    {
        // TODO: Implement addConfiguration() method.
    }

    public function load(array $config, ContainerBuilder $container): void
    {
        // TODO: Implement load() method.
    }

    public function setContainerParameters(array $config, ContainerBuilder $container): void
    {
        // TODO: Implement setContainerParameters() method.
    }

    public function configureCaching(array $config, ContainerBuilder $container): void
    {
        // TODO: Implement configureCaching() method.
    }
}
