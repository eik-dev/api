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
                'name' => 'Admin User',
                'role' => 'Admin',
                'email' => 'admin@eik.co.ke',
                'password' => bcrypt('admin'),
            ],
            [
                'name' => 'Firm User',
                'role' => 'Firm',
                'email' => 'firm@eik.co.ke',
                'password' => bcrypt('firm'),
            ],
            [
                'name' => 'Member User',
                'role' => 'Individual',
                'email' => 'individual@eik.co.ke',
                'password' => bcrypt('individual'),
            ],
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
