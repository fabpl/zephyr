<?php

declare(strict_types=1);

namespace App\Cache\RateLimiter;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class LoginRateLimiter
{
    /**
     * The login rate limiter instance.
     */
    private RateLimiter $limiter;

    /**
     * Create a new login rate limiter instance.
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Get the number of attempts for the given key.
     */
    public function attempts(Request $request): mixed
    {
        return $this->limiter->attempts($this->throttleKey($request));
    }

    /**
     * Determine if the user has too many failed login attempts.
     */
    public function tooManyAttempts(Request $request): bool
    {
        return $this->limiter->tooManyAttempts($this->throttleKey($request), 5);
    }

    /**
     * Increment the login attempts for the user.
     */
    public function increment(Request $request): void
    {
        $this->limiter->hit($this->throttleKey($request), 60);
    }

    /**
     * Determine the number of seconds until logging in is available again.
     */
    public function availableIn(Request $request): int
    {
        return $this->limiter->availableIn($this->throttleKey($request));
    }

    /**
     * Clear the login locks for the given user credentials.
     */
    public function clear(Request $request): void
    {
        $this->limiter->clear($this->throttleKey($request));
    }

    /**
     * Get the throttle key for the given request.
     */
    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('email')->value()).'|'.$request->ip());
    }
}
