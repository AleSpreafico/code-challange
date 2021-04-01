<?php


namespace Database\Factories;


use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     */

    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->title,
            'content' => $this->faker->text,
        ];
    }
}
