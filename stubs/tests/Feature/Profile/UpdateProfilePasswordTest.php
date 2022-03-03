<?php

namespace Tests\Feature\Profile;

use App\Http\Livewire\Profile\UpdateProfilePasswordForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UpdateProfilePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated()
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(UpdateProfilePasswordForm::class)
            ->set('state', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->call('updateProfilePassword');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_current_password_must_be_correct()
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(UpdateProfilePasswordForm::class)
            ->set('state', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])
            ->call('updateProfilePassword')
            ->assertHasErrors(['current_password']);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_new_passwords_must_match()
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(UpdateProfilePasswordForm::class)
            ->set('state', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'wrong-password',
            ])
            ->call('updateProfilePassword')
            ->assertHasErrors(['password']);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
