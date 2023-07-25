<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Profile;

use App\Actions\User\UpdateUserPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Profile\UpdateProfilePasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

final class ProfilePasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(UpdateProfilePasswordRequest $request, UpdateUserPassword $command): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $command->handle(
            user: $user,
            data: $request->toData(),
        );

        return back()->with('status', 'password-updated');
    }
}
