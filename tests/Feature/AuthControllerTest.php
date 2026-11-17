<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_run_setup_creates_and_authenticates_the_first_administrator(): void
    {
        Auth::logout();
        User::query()->delete();

        $response = $this->post(route('setup.store'), [
            'name' => 'Administrateur',
            'email' => 'admin@example.test',
            'password' => 'MotDePasseSecurise123',
            'password_confirmation' => 'MotDePasseSecurise123',
        ]);

        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.test',
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_authenticated_user_can_log_out_and_is_redirected_to_login(): void
    {
        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
