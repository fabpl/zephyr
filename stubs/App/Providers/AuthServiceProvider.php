<?php

declare(strict_types=1);

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Password::defaults(function (): Password {
            if ($this->app->isProduction()) { // @phpstan-ignore-line
                return Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols();
            }

            return Password::min(8);
        });
    }
}
