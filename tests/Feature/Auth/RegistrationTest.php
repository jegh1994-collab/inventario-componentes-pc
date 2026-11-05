<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_registration_is_disabled(): void
    {
        $response = $this->get('/register');
        $response->assertNotFound();

        $postResponse = $this->post('/register', [
            'name' => 'Intruder User',
            'email' => 'intruder@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $postResponse->assertNotFound();
        $this->assertGuest();
    }
}
