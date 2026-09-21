<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@bmkg.go.id',
            'password' => Hash::make('bmkg2024'),
            'role' => 'superadmin',
        ]);

        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@bmkg.go.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Editor Jurnal',
            'username' => 'editor.jurnal',
            'email' => 'editor.jurnal@bmkg.go.id',
            'password' => Hash::make('editor123'),
            'role' => 'editor',
        ]);
    }
}