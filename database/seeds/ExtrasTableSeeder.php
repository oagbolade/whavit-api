<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Extra;

class ExtrasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('extras')->insert([
            'id' => uniqid('', true),
            'title' => 'Floor Restoration',
            'description' => 'description',
            'price' => 0,
        ]);

        DB::table('extras')->insert([
            'id' => uniqid('', true),
            'title' => 'Upholstery Cleaning',
            'description' => 'description',
            'price' => 0,
        ]);

        DB::table('extras')->insert([
            'id' => uniqid('', true),
            'title' => 'Mattress Hygiene',
            'description' => 'description',
            'price' => 0,
        ]);

        DB::table('extras')->insert([
            'id' => uniqid('', true),
            'title' => 'AC Servicing & Disinfection',
            'price' => 0,
            'description' => 'description'
        ]);
    }
}
