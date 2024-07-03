<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $trainings = [
            'Training'=>0,
            'Email'=>$faker->email(),
            'Name'=>'Jane Doe',
            'Number'=>'EIK/24/01/0',
            'created_at'=>$faker->dateTimeThisYear(),
            'updated_at'=>$faker->dateTimeThisYear(),
        ];
        DB::table('trainings')->insert($trainings);
    }
}
