<?php

namespace Tests\Feature;

use App\Domain\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function setUp():void {
        parent::setUp();
        Artisan::call('passport:install',['--uuids' => true, '--no-interaction' => true]);
    }

    public function test_user_login_success()
    {
        $this->seed(UserSeeder::class);

        $user = User::all()->first();

        $loginResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->json('POST', route('api.auth.login'), [
            'email' => $user->email,
            'password' => '12345678'
        ]);

        $loginResponse->assertOk()
            ->assertJsonStructure(['data' => ['token']]);
    }
}
