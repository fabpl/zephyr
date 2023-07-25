<?php

declare(strict_types=1);

namespace Fabpl\Zephyr\Console;

use Fabpl\Zephyr\Console\Concerns\InstallsStack;
use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Process\Process;

final class InstallCommand extends Command
{
    use InstallsStack;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zephyr:install 
                            {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Zephyr';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->installStack();

        return self::SUCCESS;
    }

    /**
     * Removes the given Composer Packages from the application.
     *
     * @param array<int, string> $packages
     */
    protected function removeComposerPackages(array $packages, bool $asDev = false): bool
    {
        $composer = $this->option('composer');

        if ('global' !== $composer) {
            $command = ['php', $composer, 'remove'];
        }

        $command = array_merge(
            $command ?? ['composer', 'remove'],
            $packages,
            $asDev ? ['--dev'] : [],
        );

        return 0 === (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output): void {
                $this->output->write($output);
            });
    }

    /**
     * Replace a given string within a given file.
     */
    protected function replaceInFile(string $search, string $replace, string $path): void
    {
        file_put_contents(
            filename: $path,
            data: str_replace(
                search: $search,
                replace: $replace,
                subject: strval(file_get_contents($path))
            )
        );
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param  array<int, string>  $packages
     */
    protected function requireComposerPackages(array $packages, bool $asDev = false): bool
    {
        $composer = $this->option('composer');

        if ('global' !== $composer) {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            $packages,
            $asDev ? ['--dev'] : [],
        );

        return 0 === (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output): void {
                $this->output->write($output);
            });
    }

    /**
     * Run the given commands.
     *
     * @param array<int, string> $commands
     */
    protected function runCommands(array $commands): void
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line): void {
            $this->output->write('    '.$line);
        });
    }

    /**
     * Update the "package.json" file.
     */
    protected function updateNodePackages(callable $callback, bool $dev = true): void
    {
        if ( ! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        /** @var array<string, mixed> $packages */
        $packages = json_decode(
            json: strval(file_get_contents(base_path('package.json'))),
            associative: true
        );

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            filename: base_path('package.json'),
            data: json_encode(
                value: $packages,
                flags: JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }
}
