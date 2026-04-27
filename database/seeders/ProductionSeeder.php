<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->command->info('========================================');
        $this->command->info('  PRODUCTION SEED — Nezor');
        $this->command->info('========================================');

        // Criar usuario admin
        $user = User::firstOrCreate(
            ['email' => 'admin@nezor.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('nezor2026'),
            ]
        );

        $this->command->info("Usuario criado/atualizado: admin@nezor.com");
        $this->command->info('Senha: nezor2026');
        $this->command->info('========================================');
        $this->command->warn('ALTERE A SENHA APOS O PRIMEIRO LOGIN!');
        $this->command->info('========================================');
    }
}
