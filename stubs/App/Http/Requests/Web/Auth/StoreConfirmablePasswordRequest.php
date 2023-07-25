<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Auth;

use App\DataTransferObjects\Auth\StoreConfirmablePasswordData;
use Illuminate\Foundation\Http\FormRequest;

final class StoreConfirmablePasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string'],
        ];
    }

    public function toData(): StoreConfirmablePasswordData
    {
        return new StoreConfirmablePasswordData(
            password: $this->string('password')->value(),
        );
    }
}
