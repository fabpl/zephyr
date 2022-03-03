<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ShowProfileController extends Controller
{
    /**
     * Show the user's profile.
     */
    public function __invoke(): View
    {
        return view('profile.show');
    }
}
