<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
final class RoomFactory extends Factory
{
    protected $model = Room::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'region' => fake()->word(),
        'facility_code' => fake()->word(),
        'room_number' => fake()->word(),
        'name' => fake()->name(),
        'capacity' => fake()->numberBetween(1, 100),
        'is_active' => fake()->boolean(),
        ];
    }
}
