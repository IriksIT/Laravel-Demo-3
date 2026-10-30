<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminScreensTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_seeder_creates_demo_data(): void
    {
        $this->assertGreaterThan(0, Post::count());
        $this->assertGreaterThan(0, Comment::count());
        $this->assertGreaterThan(0, Tag::count());
    }

    public function test_admin_screens_render(): void
    {
        $admin = User::where('email', 'admin@example.com')->firstOrFail();

        $routes = [
            route('platform.main'),
            route('platform.posts'),
            route('platform.posts.create'),
            route('platform.posts.edit', Post::first()),
            route('platform.comments'),
            route('platform.taxonomy'),
            route('platform.systems.users'),
        ];

        foreach ($routes as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_admin_can_update_post_and_toggle_comment(): void
    {
        $admin = User::where('email', 'admin@example.com')->firstOrFail();
        $post = Post::first();
        $comment = Comment::where('is_approved', false)->firstOrFail();
        $tagIds = Tag::limit(2)->pluck('id')->all();

        $this->actingAs($admin)
            ->post(route('platform.posts.edit', [$post, 'method' => 'save']), [
                'post' => [
                    'title'        => 'Bijgewerkt bericht',
                    'slug'         => '',
                    'category_id'  => $post->category_id,
                    'author_id'    => $post->author_id,
                    'excerpt'      => 'Samenvatting',
                    'body'         => '<p>Inhoud</p>',
                    'status'       => 'published',
                    'published_at' => '',
                    'tags'         => $tagIds,
                ],
            ])->assertRedirect(route('platform.posts'));

        $post->refresh();
        $this->assertSame('Bijgewerkt bericht', $post->title);
        $this->assertSame('bijgewerkt-bericht', $post->slug);
        $this->assertNotNull($post->published_at);
        $this->assertEqualsCanonicalizing($tagIds, $post->tags->pluck('id')->all());

        $this->actingAs($admin)
            ->post(route('platform.comments', ['method' => 'toggle']), ['id' => $comment->id])
            ->assertRedirect();

        $this->assertTrue($comment->fresh()->is_approved);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('platform.posts'))->assertRedirect();
    }
}
