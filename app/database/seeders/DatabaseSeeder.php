<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with demo data.
     */
    public function run(): void
    {
        // Admin user for the Orchid dashboard (/admin)
        User::createAdmin('Admin', 'admin@example.com', 'password');

        $authors = User::factory()->count(4)->create();

        $categories = collect(['Nieuws', 'Tutorials', 'Laravel', 'PHP', 'DevOps', 'Front-end'])
            ->map(fn (string $name) => Category::create(['name' => $name, 'slug' => Str::slug($name)]));

        $tags = collect(['laravel', 'orchid', 'php', 'docker', 'testing', 'eloquent', 'vite', 'api', 'security', 'performance'])
            ->map(fn (string $name) => Tag::create(['name' => $name, 'slug' => Str::slug($name)]));

        Post::factory()
            ->count(60)
            ->state(fn () => [
                'author_id'   => $authors->random()->id,
                'category_id' => $categories->random()->id,
            ])
            ->create()
            ->each(function (Post $post) use ($tags) {
                $post->tags()->attach($tags->random(rand(1, 4))->pluck('id'));

                if ($post->status === 'published') {
                    Comment::factory()->count(rand(0, 8))->create(['post_id' => $post->id]);
                }
            });
    }
}
