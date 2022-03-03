<?php

namespace App\Http\Livewire\Layouts;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Navigation extends Component
{
    /**
     * The component's listeners.
     *
     * @var array
     */
    protected $listeners = [
        'refresh-navigation' => '$refresh',
    ];

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
        return view('layouts.navigation');
    }
}
