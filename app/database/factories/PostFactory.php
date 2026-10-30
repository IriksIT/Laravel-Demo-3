<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = rtrim($this->faker->unique()->sentence(rand(4, 9)), '.');
        $status = $this->faker->randomElement(['draft', 'review', 'published', 'published', 'published', 'archived']);

        return [
            'author_id'    => User::factory(),
            'category_id'  => Category::factory(),
            'title'        => $title,
            'slug'         => Str::slug($title).'-'.Str::lower(Str::random(4)),
            'excerpt'      => $this->faker->sentences(2, true),
            'body'         => collect($this->faker->paragraphs(rand(4, 8)))
                ->map(fn (string $p) => "<p>{$p}</p>")
                ->implode("\n"),
            'status'       => $status,
            'views'        => $status === 'published' ? $this->faker->numberBetween(20, 8000) : 0,
            'published_at' => in_array($status, ['published', 'archived'])
                ? $this->faker->dateTimeBetween('-1 year', 'now')
                : null,
        ];
    }
}
