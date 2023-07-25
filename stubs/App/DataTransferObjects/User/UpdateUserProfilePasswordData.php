<?php

declare(strict_types=1);

namespace App\DataTransferObjects\User;

final readonly class UpdateUserProfilePasswordData
{
    public function __construct(
        public string $password,
    ) {
    }
}
