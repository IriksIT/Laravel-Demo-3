<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Post;

use App\Models\Post;
use App\Orchid\Layouts\Post\PostEditLayout;
use App\Orchid\Layouts\Post\PostPublishLayout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class PostEditScreen extends Screen
{
    public ?Post $post = null;

    public function query(Post $post): iterable
    {
        $post->load('tags');

        return ['post' => $post];
    }

    public function name(): ?string
    {
        return $this->post?->exists ? 'Bericht bewerken' : 'Nieuw bericht';
    }

    public function description(): ?string
    {
        return 'Schrijf en publiceer een blogbericht.';
    }

    public function permission(): ?iterable
    {
        return ['platform.blog.posts'];
    }

    public function commandBar(): iterable
    {
        return [
            Button::make('Opslaan')->icon('bs.check-circle')->method('save'),

            Button::make('Verwijderen')
                ->icon('bs.trash3')
                ->confirm('Weet je zeker dat je dit bericht wilt verwijderen?')
                ->method('remove')
                ->canSee($this->post?->exists ?? false),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::split([
                Layout::block(PostEditLayout::class)
                    ->title('Inhoud'),

                Layout::block(PostPublishLayout::class)
                    ->title('Publicatie')
                    ->description('Status en publicatiedatum.'),
            ])->ratio('70/30'),
        ];
    }

    public function save(Post $post, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'post.title'        => ['required', 'string', 'max:255'],
            'post.slug'         => ['nullable', 'string', 'max:255', Rule::unique(Post::class, 'slug')->ignore($post)],
            'post.category_id'  => ['required', 'exists:categories,id'],
            'post.author_id'    => ['required', 'exists:users,id'],
            'post.excerpt'      => ['nullable', 'string'],
            'post.body'         => ['required', 'string'],
            'post.status'       => ['required', Rule::in(array_keys(Post::STATUSES))],
            'post.published_at' => ['nullable', 'date'],
            'post.tags'         => ['nullable', 'array'],
            'post.tags.*'       => ['exists:tags,id'],
        ])['post'];

        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post->fill($data)->save();
        $post->tags()->sync($tags);

        Toast::info('Bericht opgeslagen.');

        return redirect()->route('platform.posts');
    }

    public function remove(Post $post): RedirectResponse
    {
        $post->delete();

        Toast::info('Bericht verwijderd.');

        return redirect()->route('platform.posts');
    }
}
