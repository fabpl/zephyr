<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Auth;

use App\Actions\User\CreateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\StoreRegisteredUserController;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(StoreRegisteredUserController $request, CreateUser $command): RedirectResponse
    {
        /** @var User $user */
        $user = $command->handle(
            data: $request->toData(),
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
