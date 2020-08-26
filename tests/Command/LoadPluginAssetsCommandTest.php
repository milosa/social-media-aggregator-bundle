<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\tests\Command;

use Milosa\SocialMediaAggregatorBundle\Command\LoadPluginAssetsCommand;
use Milosa\SocialMediaAggregatorBundle\MilosaSocialMediaAggregatorBundle;
use Milosa\SocialMediaAggregatorBundle\MilosaSocialMediaAggregatorPlugin;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class LoadPluginAssetsCommandTest extends TestCase
{
    use ProphecyTrait;
    /**
     * @var Filesystem
     */
    private $fs;

    /** @var TestAppKernel */
    private $kernel;

    protected function setUp(): void
    {
        $this->fs = new Filesystem();
        $this->kernel = new TestAppKernel('test', true);
        $this->fs->mkdir($this->kernel->getProjectDir());
    }

    protected function tearDown(): void
    {
        $this->fs->remove('fake_dir');
    }

    public function testWhenOverwriteIsDisabledThrowsExceptionIfDirectoryExists(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/.*milosa-social already exists$/');
        $kernel = new TestAppKernel('test', true);
        $application = new Application($kernel);
        $fileSystem = $this->prophesize(Filesystem::class);
        $fileSystem->exists(Argument::exact($kernel->getProjectDir().\DIRECTORY_SEPARATOR.'assets'.\DIRECTORY_SEPARATOR.'milosa-social'))->willReturn(true);

        $command = new LoadPluginAssetsCommand($fileSystem->reveal(), []);
        $application->add($command);
        $tester = new CommandTester($application->find('milosa-social:load-plugin-assets'));
        $tester->execute([]);
    }

    public function testWhenNoPluginsFoundThrowsException(): void
    {
        $this->expectExceptionMessage('No plugins found.');
        $this->expectException(\RuntimeException::class);
        $application = new Application($this->kernel);
        $fileSystem = $this->prophesize(Filesystem::class);
        $absolutePath = $this->kernel->getProjectDir().\DIRECTORY_SEPARATOR.'assets'.\DIRECTORY_SEPARATOR.'milosa-social';
        $fileSystem->exists(Argument::exact($absolutePath))->willReturn(false);
        $fileSystem->exists(Argument::containingString('Resources'.\DIRECTORY_SEPARATOR.'assets'))->willReturn(true);
        $fileSystem->mirror(Argument::containingString('Resources'.\DIRECTORY_SEPARATOR.'assets'), Argument::exact($absolutePath), Argument::exact(null), Argument::exact(['override' => true, 'copy_on_windows' => true]))->shouldBeCalled();
        $fileSystem->mkdir(Argument::exact($absolutePath))->shouldBeCalled();
        $fileSystem->exists(Argument::exact('test_plugin_path'.\DIRECTORY_SEPARATOR.'js'))->willReturn(true);
        $fileSystem->exists(Argument::exact('test_plugin_path'.\DIRECTORY_SEPARATOR.'js'.\DIRECTORY_SEPARATOR.'testplugin.js'))->willReturn(false);

        $command = new LoadPluginAssetsCommand($fileSystem->reveal(), ['testplugin' => 'test_plugin_path']);
        $application->add($command);
        $tester = new CommandTester($application->find('milosa-social:load-plugin-assets'));
        $tester->execute(['--overwrite' => true]);
    }

    public function testWhenNoMainBundleAssetsThrowsException(): void
    {
        $this->expectExceptionMessageMatches('/Main Bundle assets not found in path.*$/');
        $this->expectException(\RuntimeException::class);
        $application = new Application($this->kernel);
        $fileSystem = $this->prophesize(Filesystem::class);
        $absolutePath = $this->kernel->getProjectDir().\DIRECTORY_SEPARATOR.'assets'.\DIRECTORY_SEPARATOR.'milosa-social';
        $fileSystem->exists(Argument::exact($absolutePath))->willReturn(true);
        $fileSystem->exists(Argument::containingString('Resources'.\DIRECTORY_SEPARATOR.'assets'))->willReturn(false);
        $fileSystem->remove(Argument::exact($absolutePath))->shouldBeCalled();
        $fileSystem->mkdir(Argument::exact($absolutePath))->shouldBeCalled();

        $command = new LoadPluginAssetsCommand($fileSystem->reveal(), ['testplugin' => 'test_plugin_path']);
        $application->add($command);
        $tester = new CommandTester($application->find('milosa-social:load-plugin-assets'));
        $tester->execute(['--overwrite' => true]);
    }

    public function testWhenOverwriteIsEnabledAndDirectoryExistsDeletesDirectory(): void
    {
        $application = new Application($this->kernel);
        $fileSystem = $this->prophesize(Filesystem::class);
        $absolutePath = $this->kernel->getProjectDir().\DIRECTORY_SEPARATOR.'assets'.\DIRECTORY_SEPARATOR.'milosa-social';
        $fileSystem->exists(Argument::exact($absolutePath))->willReturn(true);
        $fileSystem->exists(Argument::containingString('Resources'.\DIRECTORY_SEPARATOR.'assets'))->willReturn(true);
        $fileSystem->exists(Argument::exact('test_plugin_path'.\DIRECTORY_SEPARATOR.'js'))->willReturn(true);
        $fileSystem->exists(Argument::exact('test_plugin_path'.\DIRECTORY_SEPARATOR.'plugin_scss'))->willReturn(true);
        $fileSystem->remove(Argument::exact($absolutePath))->shouldBeCalled();
        $fileSystem->mkdir(Argument::exact($absolutePath))->shouldBeCalled();
        $fileSystem->mirror(Argument::any(), Argument::any(), Argument::exact(null), Argument::exact(['override' => true, 'copy_on_windows' => true]))->shouldBeCalled();
        $fileSystem->exists(Argument::exact('test_plugin_path'.\DIRECTORY_SEPARATOR.'js'.\DIRECTORY_SEPARATOR.'testplugin.js'))->willReturn(true)->shouldBeCalled();
        $fileSystem->exists(Argument::exact('test_plugin_path'.\DIRECTORY_SEPARATOR.'plugin_scss'.\DIRECTORY_SEPARATOR.'testplugin.scss'))->willReturn(true)->shouldBeCalled();
        $expectedPluginFileContents =
'import Testplugin from "./networks/Testplugin.js";

const networks = {testplugin: Testplugin};

export default networks;';

        $fileSystem->dumpFile(Argument::any(), Argument::exact($expectedPluginFileContents))->shouldBeCalled();

        $fileSystem->mirror(Argument::any(), Argument::any(), Argument::exact(null), Argument::exact(['override' => true, 'copy_on_windows' => true]))->shouldBeCalled();

        $command = new LoadPluginAssetsCommand($fileSystem->reveal(), ['testplugin' => 'test_plugin_path']);
        $application->add($command);
        $tester = new CommandTester($application->find('milosa-social:load-plugin-assets'));
        $tester->execute(['--overwrite' => true]);

        $this->assertStringContainsString('Removing directory', $tester->getDisplay());
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

    public function registerBundles(): array
    {
        return [
            new MilosaSocialMediaAggregatorBundle([
                new TestPlugin(),
            ]),
        ];
    }

    public function getProjectDir(): string
    {
        return 'fake_dir'.\DIRECTORY_SEPARATOR.'test';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.\DIRECTORY_SEPARATOR.'config.yml');
    }

    protected function build(ContainerBuilder $container): void
    {
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
    }

    public function load(array $config, ContainerBuilder $container): void
    {
    }

    public function setContainerParameters(array $config, ContainerBuilder $container): void
    {
    }

    public function configureCaching(array $config, ContainerBuilder $container): void
    {
    }
}
