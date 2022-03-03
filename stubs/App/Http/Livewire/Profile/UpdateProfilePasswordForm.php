<?php

namespace App\Http\Livewire\Profile;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Livewire\Component;

class UpdateProfilePasswordForm extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [
        'current_password' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    /**
     * Update the user's password.
     *
     * @return void
     */
    public function updateProfilePassword(): void
    {
        $this->resetErrorBag();

        Validator::make($this->state, [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ])->after(function ($validator) {
            if (!isset($this->state['current_password']) || !Hash::check($this->state['current_password'], $this->user->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }
        })->validateWithBag('updateProfilePassword');

        $this->user->forceFill([
            'password' => Hash::make($this->state['password']),
        ])->save();

        $this->state = [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        $this->emit('saved');
    }

    /**
     * Get the current user of the application.
     *
     * @return mixed
     */
    public function getUserProperty(): Authenticatable|User
    {
        return Auth::user();
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('profile.update-profile-password-form');
    }
}
