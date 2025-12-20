<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\FavoritePoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FavoritePoint>
 */
final class FavoritePointFactory extends Factory
{
    protected $model = FavoritePoint::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'is_active' => fake()->boolean(),
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }
}