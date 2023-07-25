<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Profile;

use App\DataTransferObjects\User\UpdateUserProfileData;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

final class UpdateProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|Unique>>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->user();

        return [
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'name' => ['string', 'max:255'],
        ];
    }

    public function toData(): UpdateUserProfileData
    {
        return new UpdateUserProfileData(
            email: $this->string('email')->value(),
            name: $this->string('name')->value(),
        );
    }
}
