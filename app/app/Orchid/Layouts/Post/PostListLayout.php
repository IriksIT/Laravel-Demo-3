<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Post;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class PostListLayout extends Table
{
    public $target = 'posts';

    public function columns(): array
    {
        return [
            TD::make('title', 'Titel')
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn (Post $post) => Link::make($post->title)
                    ->route('platform.posts.edit', $post)),

            TD::make('category_id', 'Categorie')
                ->filter(Relation::make()->fromModel(Category::class, 'name'))
                ->render(fn (Post $post) => $post->category?->name),

            TD::make('author_id', 'Auteur')
                ->filter(Relation::make()->fromModel(User::class, 'name'))
                ->render(fn (Post $post) => $post->author?->name),

            TD::make('status', 'Status')
                ->sort()
                ->filter(Select::make()->options(Post::STATUSES)->empty('Alle'))
                ->render(fn (Post $post) => $post->statusLabel()),

            TD::make('comments_count', 'Reacties')
                ->align(TD::ALIGN_RIGHT),

            TD::make('views', 'Weergaven')
                ->sort()
                ->align(TD::ALIGN_RIGHT),

            TD::make('published_at', 'Gepubliceerd')
                ->sort()
                ->align(TD::ALIGN_RIGHT)
                ->usingComponent(DateTimeSplit::class),

            TD::make('Acties')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Post $post) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Link::make('Bewerken')
                            ->route('platform.posts.edit', $post)
                            ->icon('bs.pencil'),
                        Button::make('Verwijderen')
                            ->icon('bs.trash3')
                            ->confirm('Weet je zeker dat je dit bericht wilt verwijderen?')
                            ->method('remove', ['id' => $post->id]),
                    ])),
        ];
    }
}
