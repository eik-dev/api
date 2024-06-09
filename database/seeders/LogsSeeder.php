<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $logs = [];
        for ($i = 0; $i < 3; $i++) {
            $logs[] = [
                'user' => $faker->name,
                'email' => $faker->email,
                'action' => $faker->word,
                'created_at' => $faker->dateTimeThisYear,
                'updated_at' => $faker->dateTimeThisYear
            ];
        }
        \DB::table('logs')->insert($logs);
    }
}
