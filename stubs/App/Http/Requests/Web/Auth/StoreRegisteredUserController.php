<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Auth;

use App\Concerns\PasswordValidationRules;
use App\DataTransferObjects\User\CreateUserData;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class StoreRegisteredUserController extends FormRequest
{
    use PasswordValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|Password|null>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => $this->passwordRules(),
            'password_confirmation' => ['required', 'same:password'],
        ];
    }

    public function toData(): CreateUserData
    {
        return new CreateUserData(
            name: $this->string('name')->value(),
            email: $this->string('email')->value(),
            password: $this->string('password')->value(),
        );
    }
}
