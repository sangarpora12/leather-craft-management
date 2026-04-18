<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User 1: Admin/Pemilik
        User::create([
            'name' => 'Super Admin Berliano',
            'email' => 'winarbuk@gmail.com',
            'password' => Hash::make('passwordadmin'),
            'role' => 'admin',
            'no_hp' => '+62 812 3456 7890',
        ]);

        // User 2: Operator Pabrik
        User::create([
            'name' => 'Operator Produksi',
            'email' => 'winarbuk+operator@gmail.com',
            'password' => Hash::make('passwordoperator'),
            'role' => 'operator',
            'no_hp' => '+62 812 3456 7891',
        ]);
    }
}
