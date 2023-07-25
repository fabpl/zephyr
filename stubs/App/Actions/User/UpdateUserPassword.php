<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\DataTransferObjects\User\UpdateUserProfilePasswordData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class UpdateUserPassword
{
    public function handle(User $user, UpdateUserProfilePasswordData $data): void
    {
        $user->update([
            'password' => Hash::make($data->password),
            'remember_token' => Str::random(60),
        ]);
    }
}
