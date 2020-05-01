<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Location;

class LocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Ojota',
            'state' => 'lagos'
        ]);

        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Lagos Island',
            'state' => 'lagos'
        ]);

        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Lekki Phase 1',
            'state' => 'lagos'
        ]);

        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Lekki Phase 2',
            'state' => 'lagos'
        ]);

        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Ikoyi',
            'state' => 'lagos'
        ]);

        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Victoria Island',
            'state' => 'lagos'
        ]);

        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Ikeja',
            'state' => 'lagos'
        ]);

        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Yaba',
            'state' => 'lagos'
        ]);
        
        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Omole phase 1',
            'state' => 'lagos'
        ]);
        
        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Omole phase 2',
            'state' => 'lagos'
        ]);

        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Gbagada',
            'state' => 'lagos'
        ]);

        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Illupeju',
            'state' => 'lagos'
        ]);

        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Surulere',
            'state' => 'lagos'
        ]);
        
        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Ogudu',
            'state' => 'lagos'
        ]);
        
        DB::table('locations')->insert([
            'id' => uniqid('', true),
            'name' => 'Magodo',
            'state' => 'lagos'
        ]);
    }
}
