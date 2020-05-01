<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => uniqid('',true),
            'first_name' => 'Company Rep.',
            'email' => 'hello@whavit.com',
            'password' => bcrypt('greatness'),
            'type' => User::ADMIN_ONE,
            'verified' => true
        ]);
    }
}
