<?php

namespace Tests\Feature;

use App\Domain\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class IpTest extends TestCase
{
    use WithFaker;

    public function setUp():void {
        parent::setUp();
        Artisan::call('passport:install',['--uuids' => true, '--no-interaction' => true]);
    }

    public function test_get_all_ip_success()
    {
        $this->seed(UserSeeder::class);

        $user = User::all()->first();

        $response = $this->actingAs($user, 'api')->get(route('api.manage.ip.all'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['datas']);
    }

    public function test_get_search_ip_success()
    {
        $this->seed(UserSeeder::class);

        $user = User::all()->first();

        $response = $this->actingAs($user, 'api')->post(route('api.manage.ip.search'), [ "search" => ""]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['datas']);
    }

    public function test_get_search_page_ip_success()
    {
        $this->seed(UserSeeder::class);

        $user = User::all()->first();

        $response = $this->actingAs($user, 'api')->post(route('api.manage.ip.search.page', ["perPage" => 10, "page" => 1]), [ "search" => ""]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['datas']);
    }

    public function test_store_ip_success()
    {
        $this->seed(UserSeeder::class);

        $user = User::all()->first();

        $response = $this->actingAs($user, 'api')->post(route('api.manage.ip.store'), [
            'ipv4' => $this->faker->ipv4(),
            'label' => [
                [
                    'id' => 0,
                    'title' => $this->faker->title()
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_update_ip_success()
    {
        $this->seed(UserSeeder::class);

        $user = User::all()->first();

        $response = $this->actingAs($user, 'api')->post(route('api.manage.ip.store'), [
            'ipv4' => $this->faker->ipv4(),
            'label' => [
                [
                    'id' => 0,
                    'title' => $this->faker->title()
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }
}
