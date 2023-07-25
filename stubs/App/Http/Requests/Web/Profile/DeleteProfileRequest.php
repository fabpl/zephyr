<?php

declare(strict_types=1);

namespace App\Http\Requests\Web\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;

final class DeleteProfileRequest extends FormRequest
{
    protected $errorBag = 'userDeletion';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|Unique>>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'current_password'],
        ];
    }
}
