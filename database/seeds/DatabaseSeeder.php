<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminTableSeeder::class);
        $this->call(AreasTableSeeder::class);
        $this->call(ExtrasTableSeeder::class);   
        $this->call(LocationTableSeeder::class);    
    }
}
