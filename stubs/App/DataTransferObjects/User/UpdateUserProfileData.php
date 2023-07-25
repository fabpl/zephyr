<?php

declare(strict_types=1);

namespace App\DataTransferObjects\User;

final readonly class UpdateUserProfileData
{
    public function __construct(
        public string $email,
        public string $name,
    ) {
    }
}
