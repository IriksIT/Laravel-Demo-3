<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'post_id'      => Post::factory(),
            'author_name'  => $this->faker->name(),
            'author_email' => $this->faker->safeEmail(),
            'body'         => $this->faker->paragraph(rand(1, 3)),
            'is_approved'  => $this->faker->boolean(70),
            'created_at'   => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
