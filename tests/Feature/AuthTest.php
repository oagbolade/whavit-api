<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRegister(){
        // User's data
        $data = [
            'email' => 'james@gmail.com',
            'first_name' => 'Falola',
            'type' => 'vendor',
            'password' => 'password',
        ];
        //Send post request
        $response = $this->json('POST',route('api.register'),$data);
        //Assert it was successful
        $response->assertStatus(200);
        //Assert we received a token
        $this->assertArrayHasKey('token',$response->json());
        //Delete data
        User::where('email','james@gmail.com')->delete();
        
    }
    public function testRegister2(){
        //User's data
        $data = [
            'email' => 'james@gmail.com',
            'first_name' => 'Falola',
            'type' => 'vendors',
            'password' => 'password',
        ];
        //Send post request
        $response = $this->json('POST',route('api.register'),$data);
        //Assert it was successful
        $response->assertStatus(422);
        //Delete data
        User::where('email','james@gmail.com')->delete();
        
    }
    /**
     * @test
     * Test login
     */
    public function testLogin()
    {
        // refresh db
        Artisan::call('migrate:refresh');

        //Create user
        User::create([
            'first_name' => 'Jhhames',
            'email'=>'james@gmail.com',
            'password' => bcrypt('password')
        ]);
        //attempt login
        $response = $this->json('POST',route('api.authenticate'),[
            'email' => 'james@gmail.com',
            'password' => 'password',
        ]);
        //Assert it was successful and a token was received
        $response->assertStatus(200);
        $this->assertArrayHasKey('token',$response->json());
        //Delete the user
        User::where('email','james@gmail.com')->delete();
    }
}
