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
            'category' => 'Reciclagem',
            'points' => 10
        ]);

        Category::factory()->create([
            'category' => 'Energia',
            'points' => 20
        ]);

        Category::factory()->create([
            'category' => 'Água',
            'points' => 40
        ]);

        Category::factory()->create([
            'category' => 'Mobilidade',
            'points' => 80
        ]);
    }
}
