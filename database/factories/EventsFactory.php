<?php

namespace Database\Factories;

use App\Models\Events;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Events::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->unique()->title,
            'content' => $this->faker->text,
            'valid_from' => $this->faker->dateTimeBetween('+0 hours', '+2 hours'),
            'valid_to' => $this->faker->dateTimeBetween('+3 hours', '+2 days'),
            'gps_lat' => $this->faker->latitude,
            'gps_lng' => $this->faker->longitude,
        ];
    }
}
