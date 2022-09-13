<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            ['full_name' => 'Satrya Wiguna', 'nick_name' => 'Satrya', 'email' => 'satrya@freshcms.net', 'password' => bcrypt('12345678'), 'created_by' => 'system'],
        ]);
    }
}
