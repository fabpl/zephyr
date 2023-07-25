<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\DataTransferObjects\User\UpdateUserProfileData;
use App\Models\User;

final class UpdateUserProfile
{
    public function handle(User $user, UpdateUserProfileData $data): void
    {
        $user->fill([
            'name' => $data->name,
            'email' => $data->email,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();
    }
}
