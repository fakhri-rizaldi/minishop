<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);
        $slug = Str::slug($name);

        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'description' => fake()->sentence(8),
            'price' => fake()->numberBetween(10, 500) * 1000,
            'stock' => fake()->numberBetween(0, 50),
            'image_url' => "https://picsum.photos/seed/{$slug}/600/600",
        ];
    }
}
