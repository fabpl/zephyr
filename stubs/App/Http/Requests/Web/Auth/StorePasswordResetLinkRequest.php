<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Auth;

use Illuminate\Foundation\Http\FormRequest;

final class StorePasswordResetLinkRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }
}
