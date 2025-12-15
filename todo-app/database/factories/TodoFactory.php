<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'completed' => false,
            'user_id' => User::factory(),
            'due_date' => null,
        ];
    }

    /**
     * 完了済み状態
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed' => true,
        ]);
    }

    /**
     * 明日が期限
     */
    public function dueTomorrow(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => now()->addDay()->toDateString(),
        ]);
    }

    /**
     * 今日が期限
     */
    public function dueToday(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => now()->toDateString(),
        ]);
    }

    /**
     * 期限切れ
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => now()->subDays(2)->toDateString(),
        ]);
    }
}
