<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Potholes', 'description' => 'Road surface damage and pothole complaints'],
            ['name' => 'Streetlights', 'description' => 'Broken, flickering, or missing streetlights'],
            ['name' => 'Garbage', 'description' => 'Uncollected waste, illegal dumping, overflowing bins'],
            ['name' => 'Water Leaks', 'description' => 'Burst pipes, water main leaks, water waste'],
            ['name' => 'Damaged Roads', 'description' => 'Road cracks, missing signage, damaged road markings'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
