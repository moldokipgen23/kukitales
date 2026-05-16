<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kukitales.com'],
            [
                'name' => 'KukiTales Admin',
                'password' => 'password',
                'role' => 'admin',
                'bio' => 'Administrator of KukiTales — Voices of the Hills.',
                'location' => 'Churachandpur, Manipur',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'editor@kukitales.com'],
            [
                'name' => 'Lhing Kipgen',
                'password' => 'password',
                'role' => 'editor',
                'bio' => 'Senior editor focusing on folktales and oral history.',
                'location' => 'Imphal, Manipur',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'author@kukitales.com'],
            [
                'name' => 'Thangboi Haokip',
                'password' => 'password',
                'role' => 'author',
                'bio' => 'Storyteller and contributor.',
                'location' => 'Aizawl, Mizoram',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
