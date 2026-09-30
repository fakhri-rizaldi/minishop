<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Tanaman Indoor', 'slug' => 'tanaman-indoor'],
            ['name' => 'Pot & Wadah', 'slug' => 'pot-dan-wadah'],
            ['name' => 'Perlengkapan Taman', 'slug' => 'perlengkapan-taman'],
            ['name' => 'Dekorasi Rumah', 'slug' => 'dekorasi-rumah'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
