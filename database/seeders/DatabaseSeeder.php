<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            BannerSeeder::class,
        ]);

        // Create default admin user
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@verdevivo.com.br'],
            [
                'name'     => 'Administrador',
                'password' => bcrypt('admin@123'),
            ]
        );
    }
}
