<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Rinel Benjamim',
            'email' => 'benjamimrinel192@gmail.com',
            'password' => '12345'
        ]);

        User::factory()->create([
            'name' => 'Rubem Benjamim',
            'email' => 'rubem@gmail.com',
            'password' => '12345'
        ]);

        User::factory()->create([
            'name' => 'Mauro Vemba',
            'email' => 'mauro@gmail.com',
            'password' => '12345'
        ]);

        Category::factory()->create([
            'category' => 'Reciclagem'
        ]);

        Category::factory()->create([
            'category' => 'Energia'
        ]);

        Category::factory()->create([
            'category' => 'Água'
        ]);

        Category::factory()->create([
            'category' => 'Mobilidade'
        ]);
    }
}
