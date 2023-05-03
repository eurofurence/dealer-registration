<?php

namespace Database\Factories;

use App\Enums\ApplicationType;
use App\Models\Application;
use App\Models\TableType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            'type' => ApplicationType::Dealer,
            'display_name' => $this->faker->name(),
            'website' => $this->faker->word(),
            'table_number' => $this->faker->word(),
            'invite_code_shares' => $this->faker->word(),
            'invite_code_assistants' => $this->faker->word(),
            'merchandise' => $this->faker->word(),
            'wanted_neighbors' => $this->faker->text(),
            'comment' => $this->faker->text(),
            'is_afterdark' => $this->faker->boolean(),
            'is_power' => $this->faker->boolean(),
            'is_wallseat' => $this->faker->boolean(),
            'is_notified' => $this->faker->boolean(),
            'waiting_at' => Carbon::now(),
            'offer_sent_at' => Carbon::now(),
            'offer_accepted_at' => Carbon::now(),
            'canceled_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::factory(),
            'table_type_requested' => TableType::factory(),
            'table_type_assigned' => TableType::factory(),
        ];
    }
}
