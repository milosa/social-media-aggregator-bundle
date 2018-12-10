<?php

declare(strict_types=1);

namespace Milosa\SocialMediaAggregatorBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class PrepareJavascriptCommand extends Command
{
    private $filesystem;
    protected static $defaultName = 'milosa-social:prepare-javascript';
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

    protected function configure()
    {
        $this->setDescription('Loads javascript modules from modules into temporary directory so they can be bundled with Webpack.')
            ->addOption('target-dir', null, InputOption::VALUE_REQUIRED, 'The directory used to store the files', 'assets'.\DIRECTORY_SEPARATOR.'milosa-social')
            ->addOption('overwrite', null, InputOption::VALUE_REQUIRED, 'Overwrite content of target directory if it already exists', false)
            ->addOption('no-cleanup', null, InputOption::VALUE_NONE, 'Do not remove the temporary directory')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Milosa Social Media Aggregator');

        /** @var KernelInterface $kernel */
        $kernel = $this->getApplication()->getKernel();
        $targetDir = rtrim($input->getOption('target-dir'), \DIRECTORY_SEPARATOR);

        if ($this->filesystem->exists($kernel->getProjectDir().\DIRECTORY_SEPARATOR.$targetDir)) {
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
    }

    private function copyMainBundleJavascript(string $targetDir, SymfonyStyle $io): void
    {
        $mainBundleJavascriptLocation = __DIR__.\DIRECTORY_SEPARATOR.'..'.\DIRECTORY_SEPARATOR.'Resources'.\DIRECTORY_SEPARATOR.'assets';

        $this->checkMainBundleJavascript($mainBundleJavascriptLocation);

        $this->filesystem->mirror($mainBundleJavascriptLocation, $targetDir, null,
            ['override' => true, 'copy_on_windows' => true]);

        $io->success('Copied main bundle javascripts');
    }

    /**
     * @param string       $targetDir
     * @param SymfonyStyle $io
     */
    private function handlePlugins(string $targetDir, SymfonyStyle $io): void
    {
        $pluginConstEntry = [];
        $pluginLines = [];
        $fullTargetDir = $targetDir.\DIRECTORY_SEPARATOR.'js'.\DIRECTORY_SEPARATOR.'Components';

        foreach ($this->pluginPaths as $pluginPath) {
            $pluginJsDir = $pluginPath.\DIRECTORY_SEPARATOR.'js';
            if ($this->filesystem->exists($pluginJsDir)) {
                $pluginFiles = scandir($pluginJsDir);
                if (\count($pluginFiles) !== 3) {
                    continue;
                }

                $pluginFileName = $pluginFiles[2];
                $pluginName = mb_substr($pluginFileName, 0, -3);
                $pluginConstEntry[] = $this->generateConstLine($pluginName);
                $pluginLines[] = $this->generateImportLine($pluginName, $pluginFileName);

                $this->copyPluginFile($pluginJsDir, $fullTargetDir, $io);
            }
        }

        $this->generatePluginJsFile($fullTargetDir, $pluginConstEntry, $pluginLines);
    }

    private function generateConstLine(string $pluginName): string
    {
        return mb_strtolower($pluginName).': '.$pluginName;
    }

    private function generateImportLine(string $pluginName, string $pluginFileName): string
    {
        return sprintf('import %s from "./networks/%s";', $pluginName, $pluginFileName);
    }

    /**
     * @param string $mainBundleJavascriptLocation
     */
    private function checkMainBundleJavascript(string $mainBundleJavascriptLocation): void
    {
        if (!is_dir($mainBundleJavascriptLocation)) {
            throw new RuntimeException(sprintf('Main Bundle assets not found in path %s',
                $mainBundleJavascriptLocation));
        }
    }

    /**
     * @param SymfonyStyle $io
     * @param string       $message
     */
    private function writelnIfVerbose(SymfonyStyle $io, string $message): void
    {
        if (OutputInterface::VERBOSITY_VERBOSE <= $io->getVerbosity()) {
            $io->text($message);
        }
    }

    /**
     * @param string       $pluginJsDir
     * @param string       $fullTargetDir
     * @param SymfonyStyle $io
     */
    private function copyPluginFile(string $pluginJsDir, string $fullTargetDir, SymfonyStyle $io): void
    {
        $this->writelnIfVerbose($io,
            sprintf('Copying directory <info>%s</info> to <info>%s</info>', $pluginJsDir, $fullTargetDir));
        $this->filesystem->mirror($pluginJsDir, $fullTargetDir.\DIRECTORY_SEPARATOR.'networks', null,
            ['override' => true, 'copy_on_windows' => true]);
    }

    /**
     * @param string $fullTargetDir
     * @param array  $pluginConstEntries
     * @param array  $pluginLines
     */
    private function generatePluginJsFile(string $fullTargetDir, array $pluginConstEntries, array $pluginLines): void
    {
        $pluginConst = 'const networks = {'.implode(",\n", $pluginConstEntries).'};';
        $pluginExports = 'export default networks;';

        $indexJsFile = implode("\n", $pluginLines)."\n\n".$pluginConst."\n\n".$pluginExports;

        $this->filesystem->dumpFile($fullTargetDir.\DIRECTORY_SEPARATOR.'networks.js', $indexJsFile);
    }
}
