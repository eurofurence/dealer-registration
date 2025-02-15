<?php

namespace Database\Factories;

use App\Models\TableType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TableTypeFactory extends Factory
{
    protected $model = TableType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'spaces' => $this->faker->randomDigitNotZero() + 1,
            'seats' => $this->faker->randomDigitNotZero() + 1,
            'price' => $this->faker->randomNumber(),
            'package' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
