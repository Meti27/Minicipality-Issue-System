<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'     => User::factory(),
            'category_id' => Category::factory(),
            'title'       => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'location'    => $this->faker->streetAddress(),
            'image_path'  => null,
            'status'      => 'submitted',
            'priority'    => 'medium',
        ];
    }

    public function withStatus(string $status): static
    {
        return $this->state(['status' => $status]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status'           => 'rejected',
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }
}
