<?php


namespace Database\Factories;


use App\Models\Comment;
use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     */

    public function definition(): array
    {
        return [
            'content' => $this->faker->text,
        ];
    }
}
