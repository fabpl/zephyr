<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Auth;

use App\Cache\RateLimiter\LoginRateLimiter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\StoreAuthenticatedSessionRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * @throws ValidationException
     */
    public function store(StoreAuthenticatedSessionRequest $request, LoginRateLimiter $limiter): RedirectResponse
    {
        if ($limiter->tooManyAttempts($request)) {
            event(new Lockout($request));

            $seconds = $limiter->availableIn($request);

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $data = $request->toData();

        if ( ! Auth::attempt(['email' => $data->email, 'password' => $data->password], $data->remember)) {
            $limiter->increment($request);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $limiter->clear($request);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
