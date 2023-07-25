<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Profile;

use App\Actions\User\DeleteUser;
use App\Actions\User\UpdateUserProfile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Profile\DeleteProfileRequest;
use App\Http\Requests\Web\Profile\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

final class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request, UpdateUserProfile $command): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $command->handle(
            user: $user,
            data: $request->toData(),
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(DeleteProfileRequest $request, DeleteUser $command): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        Auth::logout();

        $command->handle(
            user: $user
        );

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
