<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Auth;

use App\Actions\User\UpdateUserPassword;
use App\DataTransferObjects\User\UpdateUserProfilePasswordData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\StoreNewPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

final class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(StoreNewPasswordRequest $request, UpdateUserPassword $command): RedirectResponse
    {
        $data = $request->toData();

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise, we will parse the error and return the response.
        $status = Password::reset(
            credentials: [
                'email' => $data->email,
                'password' => $data->password,
                'password_confirmation' => $data->password_confirmation,
                'token' => $data->token,
            ],
            callback: function ($user) use ($data, $command): void {
                $command->handle(
                    user: $user,
                    data: new UpdateUserProfilePasswordData(
                        password: $data->password,
                    )
                );

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return Password::PASSWORD_RESET === $status
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
