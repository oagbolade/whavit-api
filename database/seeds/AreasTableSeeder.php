<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Area;

class AreasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('areas')->insert([
            'id' => uniqid('', true),
            'title' => 'Living Room, Bedroom & Common Areas',
            'description' => 'description'
        ]);
        DB::table('areas')->insert([
            'id' => uniqid('', true),
            'title' => 'Bath Room',
            'description' => 'description'
        ]);
        DB::table('areas')->insert([
            'id' => uniqid('', true),
            'title' => 'Kitchen',
            'description' => 'description'
        ]);
    }
}
