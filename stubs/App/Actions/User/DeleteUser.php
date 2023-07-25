<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class DeleteUser
{
    public function handle(User $user): void
    {
        $user->tokens->each(fn (Model $token) => $token->delete());
        $user->delete();
    }
}
