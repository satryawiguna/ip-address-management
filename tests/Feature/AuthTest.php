<?php

namespace Tests\Feature;

use App\Domain\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function setUp():void {
        parent::setUp();
        Artisan::call('passport:install');
    }

    public function test_user_login_success()
    {
        $this->seed(UserSeeder::class);

        $user = User::all()->first();

        Http::fake([
            'iam_server/oauth/token' => Http::response([
                'token_type' => 'Bearer',
                'expires_in' => 86400,
                'access_token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9',
                'refresh_token' => 'def50200a9e6f7b1117627ec627f12d5adcf86560d280f400',
            ], 200)]);

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
