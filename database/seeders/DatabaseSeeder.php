<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\Category::factory(35)->create();

        \DB::table('users')->delete();
        \DB::table('users')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'name' => 'Saugat Developer',
                    'email' => 'admin@onlineshop.com',
                    'role' => 2,
                    'password' => bcrypt('admin'),
                    'remember_token' => 'd9KR06Il9Nd8uru5Uxv2MYeUdAoPR7VwBgL979gfkOD43fOlnmb8VItw0kg4',
                    'created_at' => '2018-05-28 23:58:28',
                    'updated_at' => '2020-10-05 15:39:06',
                ),
        ));
    }
}
