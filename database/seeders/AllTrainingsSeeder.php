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
            'StartDate'=>'2024-01-31',
            'EndDate'=>'2024-01-31',
            'View'=>'certificates.training',
            'Background'=>'/system/training.jpg',
            'Info'=>'For attending the EIK Webinar on Developing Quality Environment Audit Report On 31st January 2024. Your Continuous Professional Developments Points are Two (2) Units.',
            'Style'=>'',
        ];
        DB::table('all_trainings')->insert($trainings);
    }
}
