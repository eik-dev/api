<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllTrainingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $trainings = [
            'Name'=>'Developing Quality Environment Audit Report',
            'Date'=>$faker->date(),
            'View'=>'certificates.training',
            'Background'=>'/system/training.jpg',
            'Style'=>'',
        ];
        DB::table('all_trainings')->insert($trainings);
    }
}
