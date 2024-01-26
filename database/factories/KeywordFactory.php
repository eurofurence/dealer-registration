<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Keyword;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class KeywordFactory extends Factory
{
    protected $model = Keyword::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->word(),
            'category_id' => Category::factory(),
            'name' => fake()->name(),
        ];
    }
}
