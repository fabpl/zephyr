<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Auth;

final class StoreNewPasswordData
{
    public function __construct(
        public string $email,
        public string $password,
        public string $password_confirmation,
        public string $token,
    ) {
    }
}
