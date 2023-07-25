<?php

declare(strict_types=1);

namespace Fabpl\Zephyr\Console\Concerns;

use Illuminate\Filesystem\Filesystem;

trait InstallsStack
{
    /**
     * Install the Blade Breeze stack.
     */
    protected function installStack(): int
    {
        // NPM Packages...
        $this->updateNodePackages(function ($packages) {
            return [
                '@tailwindcss/forms' => '^0.5.2',
                'alpinejs' => '^3.4.2',
                'autoprefixer' => '^10.4.2',
                'postcss' => '^8.4.6',
                'tailwindcss' => '^3.1.0',
            ] + $packages;
        });

        // Actions...
        (new Filesystem())->ensureDirectoryExists(app_path('Actions'));
        (new Filesystem())->copyDirectory(__DIR__.'/../../../stubs/app/Actions', app_path('Actions'));

        // Cache...
        (new Filesystem())->ensureDirectoryExists(app_path('Cache'));
        (new Filesystem())->copyDirectory(__DIR__.'/../../../stubs/app/Cache', app_path('Cache'));

        // Concerns...
        (new Filesystem())->ensureDirectoryExists(app_path('Concerns'));
        (new Filesystem())->copyDirectory(__DIR__.'/../../../stubs/app/Concerns', app_path('Concerns'));

        // DataTransferObjects...
        (new Filesystem())->ensureDirectoryExists(app_path('DataTransferObjects'));
        (new Filesystem())->copyDirectory(__DIR__.'/../../../stubs/app/DataTransferObjects', app_path('DataTransferObjects'));

        // Http Controllers...
        (new Filesystem())->ensureDirectoryExists(app_path('Http/Controllers'));
        (new Filesystem())->copyDirectory(__DIR__.'/../../../stubs/app/Http/Controllers', app_path('Http/Controllers'));

        // Http Requests...
        (new Filesystem())->ensureDirectoryExists(app_path('Http/Requests'));
        (new Filesystem())->copyDirectory(__DIR__.'/../../../stubs/app/Http/Requests', app_path('Http/Requests'));

        // Providers...
        (new Filesystem())->ensureDirectoryExists(app_path('Providers'));
        (new Filesystem())->copyDirectory(__DIR__.'/../../../stubs/app/Providers', app_path('Providers'));

        // View Components...
        (new Filesystem())->ensureDirectoryExists(app_path('View/Components'));
        (new Filesystem())->copyDirectory(__DIR__.'/../../../stubs/app/View/Components', app_path('View/Components'));

        // Resource Views...
        (new Filesystem())->ensureDirectoryExists(resource_path('views'));
        (new Filesystem())->copyDirectory(__DIR__.'/../../../stubs/resources/views', resource_path('views'));

        // Tests...
        (new Filesystem())->ensureDirectoryExists(base_path('tests/Feature'));
        $this->removeComposerPackages(['phpunit/phpunit'], true);
        $this->requireComposerPackages(['pestphp/pest:^2.0', 'pestphp/pest-plugin-laravel:^2.0'], true);
        (new Filesystem())->copyDirectory(__DIR__.'/../../../stubs/tests/Feature', base_path('tests/Feature'));
        (new Filesystem())->copy(__DIR__.'/../../../stubs/tests/Pest.php', base_path('tests/Pest.php'));

        // Routes...
        copy(__DIR__.'/../../../stubs/routes/web.php', base_path('routes/web.php'));
        copy(__DIR__.'/../../../stubs/routes/auth.php', base_path('routes/auth.php'));

        // "Dashboard" Route...
        $this->replaceInFile('/home', '/dashboard', resource_path('views/welcome.blade.php'));
        $this->replaceInFile('Home', 'Dashboard', resource_path('views/welcome.blade.php'));
        $this->replaceInFile('/home', '/dashboard', app_path('Providers/RouteServiceProvider.php'));

        // Tailwind / Vite...
        copy(__DIR__.'/../../../stubs/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__.'/../../../stubs/postcss.config.js', base_path('postcss.config.js'));
        copy(__DIR__.'/../../../stubs/vite.config.js', base_path('vite.config.js'));
        copy(__DIR__.'/../../../stubs/resources/css/app.css', resource_path('css/app.css'));
        copy(__DIR__.'/../../../stubs/resources/js/app.js', resource_path('js/app.js'));

        $this->components->info('Installing and building Node dependencies.');

        if (file_exists(base_path('pnpm-lock.yaml'))) {
            $this->runCommands(['pnpm install', 'pnpm run build']);
        } elseif (file_exists(base_path('yarn.lock'))) {
            $this->runCommands(['yarn install', 'yarn run build']);
        } else {
            $this->runCommands(['npm install', 'npm run build']);
        }

        $this->line('');
        $this->components->info('Zephyr scaffolding installed successfully.');

        return self::SUCCESS;
    }
}
