<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

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
