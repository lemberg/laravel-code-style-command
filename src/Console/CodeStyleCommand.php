<?php

namespace Lemberg\LaravelCsc\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

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
        {--printCommand : Just print phpCs full command.}
        {--dirs= : Set paths (comma separated). Relative or absolute path. Option has priority}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check your application code style. Default `app\` directory.';

    /**
     * Full path to phpcs command
     *
     * @var string
     */
    protected $phpcsPath;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->phpcsPath = base_path('vendor/bin/phpcs');
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

        $process = $this->prepareProcess();

        // print compiled command
        if ($this->input->getOption('printCommand')) {
            echo str_replace('\'', '', $process->getCommandLine());
            exit;
        }

        $process->run();

        echo $process->getOutput();

        $this->info('Finished');
    }

    /**
     * Prepare folders for validation
     *
     * @return string
     */
    protected function getDirs()
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
            // git ls-files -om --exclude-standard --directory 'app/'
            $process = new Process('git ls-files -om --exclude-standard --full-name --directory ' . $dirs);
            $process->enableOutput();
            $process->run();

            $changes = explode(PHP_EOL, $process->getOutput());

            unset($process);

            $dirs = array_filter($changes, function ($item) {
                return !empty($item);
            });
        }

        return implode(' ', $dirs);
    }

    /**
     * @return Process
     */
    protected function prepareProcess()
    {
        // Build command with arguments
        $builder = new ProcessBuilder();
        $builder->setPrefix($this->phpcsPath);

        // vendor/bin/phpcs --standard="PSR2" --colors dirOrFile
        $cmd = $builder
            ->setArguments(config('code-style.arguments'))
            ->getProcess()
            ->getCommandLine();

        // add $dirs to $cmd
        $process = new Process($cmd . ' ' . $this->getDirs(), base_path(''));
        $process->enableOutput();

        return $process;
    }
}
