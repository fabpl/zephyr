<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\DataTransferObjects\User\CreateUserData;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

final class CreateUser
{
    public function handle(CreateUserData $data): Builder|Model
    {
        return User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);
    }
}
