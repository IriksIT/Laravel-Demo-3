<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Post;

use App\Models\Post;
use App\Orchid\Layouts\Post\PostListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class PostListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'posts' => Post::with(['category', 'author'])
                ->withCount('comments')
                ->filters()
                ->defaultSort('id', 'desc')
                ->paginate(15),
        ];
    }

    public function name(): ?string
    {
        return 'Berichten';
    }

    public function description(): ?string
    {
        return 'Alle blogberichten: concepten, gepubliceerde en gearchiveerde artikelen.';
    }

    public function permission(): ?iterable
    {
        return ['platform.blog.posts'];
    }

    public function commandBar(): iterable
    {
        return [
            Link::make('Nieuw bericht')
                ->icon('bs.plus-circle')
                ->route('platform.posts.create'),
        ];
    }

    public function layout(): iterable
    {
        return [PostListLayout::class];
    }

    public function remove(Request $request): void
    {
        Post::findOrFail($request->get('id'))->delete();

        Toast::info('Bericht verwijderd.');
    }
}
