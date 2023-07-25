<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Auth;

use App\Concerns\PasswordValidationRules;
use App\DataTransferObjects\Auth\StoreNewPasswordData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class StoreNewPasswordRequest extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|Password>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => $this->passwordRules(),
            'password_confirmation' => ['required', 'same:password'],
            'token' => ['required'],
        ];
    }

    public function toData(): StoreNewPasswordData
    {
        return new StoreNewPasswordData(
            email: $this->string('email')->value(),
            password: $this->string('password')->value(),
            password_confirmation: $this->string('password_confirmation')->value(),
            token: $this->string('token')->value(),
        );
    }
}
