<?php

namespace App\Http\Livewire\Profile;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UpdateProfileInformationForm extends Component
{
    /**
     * The component's state.
     *
     * @var array
     */
    public $state = [];

    /**
     * Prepare the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->state = $this->user->withoutRelations()->toArray();
    }

    /**
     * Update the user's profile information.
     *
     * @return void
     */
    public function updateProfileInformation(): void
    {
        $this->resetErrorBag();

        Validator::make($this->state, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
        ])->validateWithBag('updateProfileInformation');

        if ($this->state['email'] !== $this->user->email &&
            $this->user instanceof MustVerifyEmail) {
            $this->user->forceFill([
                'name' => $this->state['name'],
                'email' => $this->state['email'],
                'email_verified_at' => null,
            ])->save();

            $this->user->sendEmailVerificationNotification();
        } else {
            $this->user->forceFill([
                'name' => $this->state['name'],
                'email' => $this->state['email'],
            ])->save();
        }

        $this->emit('saved');

        $this->emit('refresh-navigation');
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
        return view('profile.update-profile-information-form');
    }
}
