<?php

namespace Database\Factories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'origin' => $this->faker->city,
            'destination' => $this->faker->city,
            'schedule_start_date' => $this->faker->date,
            'schedule_end_date' => $this->faker->date,
            'type' => 1,
            'description' => $this->faker->paragraph,
            'is_deleted' => false,
            'created_by' => 1,
        ];
    }
}
