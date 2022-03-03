<?php

namespace Tests\Feature\Profile;

use App\Http\Livewire\Profile\DeleteProfileForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DeleteProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_accounts_can_be_deleted()
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(DeleteProfileForm::class)
            ->set('password', 'password')
            ->call('deleteProfile');

        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_before_account_can_be_deleted()
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(DeleteProfileForm::class)
            ->set('password', 'wrong-password')
            ->call('deleteProfile')
            ->assertHasErrors(['password']);

        $this->assertNotNull($user->fresh());
    }
}
