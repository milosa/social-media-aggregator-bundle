<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class LoadPluginAssetsCommand extends Command
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    protected static $defaultName = 'milosa-social:load-plugin-assets';

    /**
     * @var string[]
     */
    private $pluginPaths;

    public function __construct(Filesystem $filesystem, array $pluginPaths)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->pluginPaths = $pluginPaths;
    }

    protected function configure(): void
    {
        $this->setDescription('Loads assets from plugins into temporary directory so they can be bundled with Webpack.')
            ->addOption('target-dir', null, InputOption::VALUE_REQUIRED, 'The directory used to store the files', 'assets'.\DIRECTORY_SEPARATOR.'milosa-social')
            ->addOption('overwrite', null, InputOption::VALUE_REQUIRED, 'Overwrite content of target directory if it already exists', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Milosa Social Media Aggregator');

        /** @var Application $app */
        $app = $this->getApplication();

        /** @var KernelInterface $kernel */
        $kernel = $app->getKernel();
        $targetDir = $kernel->getProjectDir().\DIRECTORY_SEPARATOR.rtrim($input->getOption('target-dir'), \DIRECTORY_SEPARATOR);

        if ($this->filesystem->exists($targetDir)) {
            if (!$input->getOption('overwrite')) {
                throw new RuntimeException(sprintf('Directory %s already exists', $targetDir));
            }

            $io->success(sprintf('Removing directory %s', $targetDir));
            $this->filesystem->remove($targetDir);
        }

        $this->filesystem->mkdir($targetDir);

        $this->copyMainBundleJavascript($targetDir, $io);
        $this->handlePlugins($targetDir, $io);

        $io->text(sprintf('Add an entry for <info>%s</info> to your webpack config file.', $kernel->getProjectDir().\DIRECTORY_SEPARATOR.'assets'.\DIRECTORY_SEPARATOR.'milosa-social'.\DIRECTORY_SEPARATOR.'js'.\DIRECTORY_SEPARATOR.'app.js'));

        return 0;
    }

    private function copyMainBundleJavascript(string $targetDir, SymfonyStyle $io): void
    {
        $mainBundleJavascriptLocation = __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Resources'.\DIRECTORY_SEPARATOR.'assets';

        $this->checkMainBundleJavascript($mainBundleJavascriptLocation);

        $this->filesystem->mirror($mainBundleJavascriptLocation, $targetDir, null,
            ['override' => true, 'copy_on_windows' => true]);

        $io->success('Copied main bundle javascripts');
    }

    private function handlePlugins(string $targetDir, SymfonyStyle $io): void
    {
        $pluginConstEntry = [];
        $pluginLines = [];
        $fullJsTargetDir = $targetDir.\DIRECTORY_SEPARATOR.'js'.\DIRECTORY_SEPARATOR.'Components';
        $fullScssTargetDir = $targetDir.\DIRECTORY_SEPARATOR.'scss'.\DIRECTORY_SEPARATOR.'Components';

        foreach ($this->pluginPaths as $pluginName => $pluginPath) {
            $pluginJsDir = $pluginPath.\DIRECTORY_SEPARATOR.'js';
            $pluginScssDir = $pluginPath.\DIRECTORY_SEPARATOR.'plugin_scss';

            if ($this->filesystem->exists($pluginJsDir)) {
                $pluginJsFileName = $pluginName.'.js';
                if (!$this->filesystem->exists($pluginJsDir.\DIRECTORY_SEPARATOR.$pluginJsFileName)) {
                    continue;
                }

                $pluginConstEntry[] = $this->generateConstLine($pluginName);
                $pluginLines[] = $this->generateImportLine($pluginName, $pluginJsFileName);

                $this->copyPluginJsFile($pluginJsDir, $fullJsTargetDir, $io);
            }

            if ($this->filesystem->exists($pluginScssDir)) {
                $pluginScssFileName = $pluginName.'.scss';
                if (!$this->filesystem->exists($pluginScssDir.\DIRECTORY_SEPARATOR.$pluginScssFileName)) {
                    continue;
                }

                $this->copyPluginScssFile($pluginScssDir, $fullScssTargetDir, $io);
            }
        }

        if (\count($pluginLines) === 0) {
            throw new RuntimeException('No plugins found.');
        }

        $this->generatePluginJsFile($fullJsTargetDir, $pluginConstEntry, $pluginLines);
    }

    private function generateConstLine(string $pluginName): string
    {
        return mb_strtolower($pluginName).': '.ucfirst($pluginName);
    }

    private function generateImportLine(string $pluginName, string $pluginFileName): string
    {
        return sprintf('import %s from "./networks/%s";', ucfirst($pluginName), ucfirst($pluginFileName));
    }

    /**
     * @throws \RuntimeException
     */
    private function checkMainBundleJavascript(string $mainBundleJavascriptLocation): void
    {
        if (!$this->filesystem->exists($mainBundleJavascriptLocation)) {
            throw new RuntimeException(sprintf('Main Bundle assets not found in path %s', $mainBundleJavascriptLocation));
        }
    }

    private function writelnIfVerbose(SymfonyStyle $io, string $message): void
    {
        if (OutputInterface::VERBOSITY_VERBOSE <= $io->getVerbosity()) {
            $io->text($message);
        }
    }

    private function copyPluginJsFile(string $pluginJsDir, string $fullTargetDir, SymfonyStyle $io): void
    {
        $this->writelnIfVerbose($io,
            sprintf('Copying directory <info>%s</info> to <info>%s</info>', $pluginJsDir, $fullTargetDir));
        $this->filesystem->mirror($pluginJsDir, $fullTargetDir.\DIRECTORY_SEPARATOR.'networks', null,
            ['override' => true, 'copy_on_windows' => true]);
    }

    /**
     * @param string $pluginJsDir
     */
    private function copyPluginScssFile(string $pluginScssDir, string $fullTargetDir, SymfonyStyle $io): void
    {
        $this->writelnIfVerbose($io,
            sprintf('Copying directory <info>%s</info> to <info>%s</info>', $pluginScssDir, $fullTargetDir));
        $this->filesystem->mirror($pluginScssDir, $fullTargetDir, null,
            ['override' => true, 'copy_on_windows' => true]);
    }

    private function generatePluginJsFile(string $fullTargetDir, array $pluginConstEntries, array $pluginLines): void
    {
        $pluginConst = 'const networks = {'.implode(",\n", $pluginConstEntries).'};';
        $pluginExports = 'export default networks;';

        $indexJsFile = implode("\n", $pluginLines)."\n\n".$pluginConst."\n\n".$pluginExports;

        $this->filesystem->dumpFile($fullTargetDir.\DIRECTORY_SEPARATOR.'networks.js', $indexJsFile);
    }
}
