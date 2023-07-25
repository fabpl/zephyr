<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Auth;

final class StoreConfirmablePasswordData
{
    public function __construct(
        public string $password,
    ) {
    }
}
