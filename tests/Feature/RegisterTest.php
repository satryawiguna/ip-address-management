<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_register()
    {
        $registerResponse = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->json('POST', route('api.register'), [
            'full_name' => $this->faker->firstName() . ' ' . $this->faker->lastName(),
            'nick_name' => $this->faker->firstName(),
            'email' => $this->faker->email(),
            'password' => '12345678',
            'confirm_password' => '12345678'
        ]);

        $registerResponse->assertOk();
    }
}
