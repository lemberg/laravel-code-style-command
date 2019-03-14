<?php

namespace Lemberg\LaravelCsc\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

/**
 * Class CodeStyleCommand
 * @package Lemberg\LaravelCsc\Console
 */
class CodeStyleCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'code-style';

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'code-style
        {--changes : Git, only new or changed files.}
        {--print-command : Just print phpCs full command.}
        {--dirs= : Set paths (comma separated). Relative or absolute path. Option has priority}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check your application code style. Default `app/` directory.';

    /**
     * Full path to phpcs command
     *
     * @var string
     */
    protected $phpcsPath;

    /**
     * @var array
     */
    protected $dirs = [];


    /**
     * Create a new command instance.
     *
     * @throws \ReflectionException
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $reflection = new \ReflectionClass(\Composer\Autoload\ClassLoader::class);
        $vendorDir = dirname(dirname($reflection->getFileName()));

        $this->phpcsPath = $vendorDir . DIRECTORY_SEPARATOR . 'bin/phpcs';
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (!file_exists($this->phpcsPath)) {
            $this->error('php code style not found');
            exit(1);
        }

        $this->prepareDirs();

        if (empty($this->dirs)) {
            $this->error('no directories found');
            exit(1);
        }

        $phpCsProcess = $this->getPhpCsProcess();

        // print compiled command
        if ($this->input->getOption('print-command')) {
            echo str_replace('\'', '', $phpCsProcess->getCommandLine());
            exit;
        }

        $phpCsProcess->run();

        // The exit code is 1 as the operation was not successful.
        if ($phpCsProcess->getExitCode() > 0) {
            echo $phpCsProcess->getOutput();
            exit(1);
        }

        $this->info('Finished');
    }

    /**
     * Prepare folders for validation
     *
     * @return self
     */
    protected function prepareDirs()
    {
        // Get dirs for validation from config
        $dirs = config('code-style.dirs', []);
        $dirsOption = $this->input->getOption('dirs');

        if ($dirsOption) {
            // Option has priority
            $dirs = [];

            foreach (explode(',', $dirsOption) as $dir) {
                $dirs[] = trim($dir);
            }
        }

        // Changes
        if ($this->input->getOption('changes')) {
            // TODO check git
            // git ls-files -om --exclude-standard --directory 'app/'
            $process = new Process(
                'git ls-files -om --exclude-standard --full-name --directory ' . implode(' ', $dirs)
            );
            $process->enableOutput();

            $process->run();

            $changes = explode(PHP_EOL, $process->getOutput());

            unset($process);

            $dirs = array_filter($changes, function ($item) {
                return !empty($item);
            });
        }

        $this->dirs = $dirs;

        return $this;
    }

    /**
     * @return Process
     */
    protected function getPhpCsProcess()
    {
        // vendor/bin/phpcs --standard="PSR2" --colors DIRS
        $command = sprintf(
            '%s %s %s',
            $this->phpcsPath,
            implode(' ', config('code-style.arguments')),
            implode(' ', $this->dirs)
        );

        // Build command
        $process = new Process($command, base_path());
        $process->enableOutput();

        return $process;
    }
}
