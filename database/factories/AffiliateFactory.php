<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AffiliateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
            'name' => $this->faker->name,
        ];
    }
}
