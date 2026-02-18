<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'image' => 'default_car.jpg',
            'description' => $this->faker->text,
            'model' => $this->faker->year,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'price_per_day' => $this->faker->numberBetween(50, 500),
            'status' => \App\Constants\CarStatus::AVAILABLE,
            'category_id' => \App\Models\Category::factory(),
        ];
    }
}
