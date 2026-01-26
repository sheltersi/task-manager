<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Http\Livewire\UpdatePasswordForm;
use Livewire\Livewire;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $this->actingAs($user = User::factory()->create());

        Livewire::test(UpdatePasswordForm::class)
            ->set('state', [
                'current_password' => '@Secret123',
                'password' => '#Secret123',
                'password_confirmation' => '#Secret123',
            ])
            ->call('updatePassword');

        $this->assertTrue(Hash::check('#Secret123', $user->fresh()->password));
    }

 public function test_current_password_must_be_correct(): void
{
    $user = User::factory()->create([
        'password' => Hash::make('#Secret123'),
    ]);

    $this->actingAs($user);

    Livewire::test(UpdatePasswordForm::class)
        ->set('state', [
            'current_password' => 'wrong-password',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ])
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);

    $this->assertTrue(Hash::check('#Secret123', $user->fresh()->password));
}

  public function test_new_passwords_must_match(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('#Secret123'),
        ]);

        $this->actingAs($user);

        Livewire::test(UpdatePasswordForm::class)
            ->set('state', [
                'current_password' => '#Secret123',
                'password' => 'new-password123',
                'password_confirmation' => 'mismatched-password',
            ])
            ->call('updatePassword')
            ->assertHasErrors(['password']); 

        $this->assertTrue(Hash::check('#Secret123', $user->fresh()->password));
    }


}
