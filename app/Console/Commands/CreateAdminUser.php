<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un usuario administrador inicial para el sistema de inventario';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('--- Creación de Usuario Administrador ---');

        $name = $this->ask('Nombre completo');
        while (empty(trim((string) $name))) {
            $this->error('El nombre no puede estar vacío.');
            $name = $this->ask('Nombre completo');
        }

        $email = $this->ask('Correo electrónico');
        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        while ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            $email = $this->ask('Correo electrónico');
            $validator = Validator::make(['email' => $email], [
                'email' => ['required', 'email', 'unique:users,email'],
            ]);
        }

        $password = $this->secret('Contraseña (mínimo 8 caracteres)');
        while (strlen((string) $password) < 8) {
            $this->error('La contraseña debe tener al menos 8 caracteres.');
            $password = $this->secret('Contraseña (mínimo 8 caracteres)');
        }

        $passwordConfirmation = $this->secret('Confirmar contraseña');
        while ($password !== $passwordConfirmation) {
            $this->error('Las contraseñas no coinciden. Intenta de nuevo.');
            $password = $this->secret('Contraseña (mínimo 8 caracteres)');
            $passwordConfirmation = $this->secret('Confirmar contraseña');
        }

        $user = User::create([
            'name' => trim((string) $name),
            'email' => strtolower(trim((string) $email)),
            'password' => Hash::make($password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->info("¡Administrador [{$user->email}] creado exitosamente!");

        return Command::SUCCESS;
    }
}
