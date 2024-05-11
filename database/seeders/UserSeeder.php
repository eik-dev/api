<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Samson Mongare',
                'username' => 'Alpha',
                'role' => 'Admin',
                'email' => 'developers@eik.co.ke',
                'password' => bcrypt('@Admin123'),
            ],
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
