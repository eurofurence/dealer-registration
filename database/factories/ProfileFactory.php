<?php

namespace Database\Factories;
use App\Models\Application;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'short_desc' => $this->faker->text(),
            'artist_desc' => $this->faker->text(),
            'art_desc' => $this->faker->text(),
            'website' => $this->faker->word(),
            'twitter' => $this->faker->word(),
            'telegram' => $this->faker->word(),
            'discord' => $this->faker->word(),
            'tweet' => $this->faker->text(),
            'art_preview_caption' => $this->faker->text(),
            'is_print' => $this->faker->boolean(),
            'is_artwork' => $this->faker->boolean(),
            'is_fursuit' => $this->faker->boolean(),
            'is_commissions' => $this->faker->boolean(),
            'is_misc' => $this->faker->boolean(),
            'attends_thu' => $this->faker->boolean(),
            'attends_fri' => $this->faker->boolean(),
            'attends_sat' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'application_id' => Application::factory(),
        ];
    }
}
