<?php

namespace Fabpl\Zephyr\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
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
    protected $description = 'Install the Zephyr controllers and resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                    '@tailwindcss/forms' => '^0.4.0',
                    '@tailwindcss/typography' => '^0.5.0',
                    'alpinejs' => '^3.4.2',
                    'autoprefixer' => '^10.4.2',
                    'postcss' => '^8.4.6',
                    'postcss-import' => '^14.0.2',
                    'tailwindcss' => '^3.0.18',
                ] + $packages;
        });

        // Install Livewire...
        $this->requireComposerPackages('livewire/livewire:^2.5');

        // Controllers...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers/Auth'));
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers/Profile'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/App/Http/Controllers/Auth', app_path('Http/Controllers/Auth'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/App/Http/Controllers/Profile', app_path('Http/Controllers/Profile'));

        // Livewire components...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Livewire/Layouts'));
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Livewire/Profile'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/App/Http/Livewire/Layouts', app_path('Http/Livewire/Layouts'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/App/Http/Livewire/Profile', app_path('Http/Livewire/Profile'));

        // Requests...
        (new Filesystem)->ensureDirectoryExists(app_path('Http/Requests/Auth'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/App/Http/Requests/Auth', app_path('Http/Requests/Auth'));

        // Views...
        (new Filesystem)->ensureDirectoryExists(resource_path('views/auth'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/components'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/layouts'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/profile'));

        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/resources/views/auth', resource_path('views/auth'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/resources/views/components', resource_path('views/components'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/resources/views/layouts', resource_path('views/layouts'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/resources/views/profile', resource_path('views/profile'));

        copy(__DIR__ . '/../../stubs/resources/views/dashboard.blade.php', resource_path('views/dashboard.blade.php'));

        // Components...
        (new Filesystem)->ensureDirectoryExists(app_path('View/Components'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/App/View/Components', app_path('View/Components'));

        // Tests...
        (new Filesystem)->ensureDirectoryExists(base_path('tests/Feature/Auth'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/tests/Feature/Auth', base_path('tests/Feature/Auth'));
        (new Filesystem)->ensureDirectoryExists(base_path('tests/Feature/Profile'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/tests/Feature/Profile', base_path('tests/Feature/Profile'));

        // Routes...
        copy(__DIR__ . '/../../stubs/routes/auth.php', base_path('routes/auth.php'));
        copy(__DIR__ . '/../../stubs/routes/web.php', base_path('routes/web.php'));

        // "Dashboard" Route...
        $this->replaceInFile('/home', '/dashboard', resource_path('views/welcome.blade.php'));
        $this->replaceInFile('Home', 'Dashboard', resource_path('views/welcome.blade.php'));
        $this->replaceInFile('/home', '/dashboard', app_path('Providers/RouteServiceProvider.php'));

        // Tailwind / Webpack...
        copy(__DIR__ . '/../../stubs/resources/css/app.css', resource_path('css/app.css'));
        copy(__DIR__ . '/../../stubs/resources/js/app.js', resource_path('js/app.js'));
        copy(__DIR__ . '/../../stubs/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__ . '/../../stubs/webpack.mix.js', base_path('webpack.mix.js'));

        $this->info('Zephyr scaffolding installed successfully.');
        $this->comment('Please execute the "npm install && npm run dev" command to build your assets.');
    }

    /**
     * Installs the given Composer Packages into the application.
     *
     * @param mixed $packages
     * @return void
     */
    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

    /**
     * Replace a given string within a given file.
     *
     * @param string $search
     * @param string $replace
     * @param string $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Update the "package.json" file.
     *
     * @param callable $callback
     * @param bool $dev
     * @return void
     */
    protected function updateNodePackages(callable $callback, $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }
}
