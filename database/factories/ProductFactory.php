<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use function Laravel\Prompts\password;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->sentence(20),
            'amount' => $this->faker->randomNumber(),
            'price' => $this->faker->numberBetween(300,500),
            'price_after_discount' => $this->faker->numberBetween(200,300),
            'image' => $this->faker->image(category: 'png'),
            'vendor_id' => 1,
            'collection_id' => 1,
        ];
    }
}