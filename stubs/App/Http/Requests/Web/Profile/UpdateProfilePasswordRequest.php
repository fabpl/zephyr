<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Profile;

use App\Concerns\PasswordValidationRules;
use App\DataTransferObjects\User\UpdateUserProfilePasswordData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

final class UpdateProfilePasswordRequest extends FormRequest
{
    use PasswordValidationRules;

    protected $errorBag = 'updatePassword';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|Password|null>>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => $this->passwordRules(),
            'password_confirmation' => ['required', 'same:password'],
        ];
    }

    public function toData(): UpdateUserProfilePasswordData
    {
        return new UpdateUserProfilePasswordData(
            password: $this->string('password')->value(),
        );
    }
}
