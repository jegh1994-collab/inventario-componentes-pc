<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_make_admin_command_creates_admin_successfully(): void
    {
        $this->artisan('make:admin')
            ->expectsQuestion('Nombre completo', 'Super Admin')
            ->expectsQuestion('Correo electrónico', 'admin@sistema.local')
            ->expectsQuestion('Contraseña (mínimo 8 caracteres)', 'adminpass123')
            ->expectsQuestion('Confirmar contraseña', 'adminpass123')
            ->expectsOutputToContain('¡Administrador [admin@sistema.local] creado exitosamente!')
            ->assertSuccessful();

        $admin = User::where('email', 'admin@sistema.local')->first();

        $this->assertNotNull($admin);
        $this->assertEquals('admin', $admin->role);
        $this->assertTrue($admin->is_active);
        $this->assertTrue(Hash::check('adminpass123', $admin->password));
    }
}
