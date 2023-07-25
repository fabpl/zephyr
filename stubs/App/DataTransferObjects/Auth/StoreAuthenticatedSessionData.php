<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Auth;

final readonly class StoreAuthenticatedSessionData
{
    public function __construct(
        public string $email,
        public string $password,
        public bool $remember,
    ) {
    }
}
