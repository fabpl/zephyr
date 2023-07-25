<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Auth;

use App\DataTransferObjects\Auth\StoreAuthenticatedSessionData;
use Illuminate\Foundation\Http\FormRequest;

final class StoreAuthenticatedSessionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function toData(): StoreAuthenticatedSessionData
    {
        return new StoreAuthenticatedSessionData(
            email: $this->string('email')->value(),
            password: $this->string('password')->value(),
            remember: $this->boolean('remember'),
        );
    }
}
