<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Post;

use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Layouts\Rows;

class PostEditLayout extends Rows
{
    public function fields(): array
    {
        return [
            Input::make('post.title')
                ->title('Titel')
                ->required(),

            Group::make([
                Relation::make('post.category_id')
                    ->title('Categorie')
                    ->fromModel(Category::class, 'name')
                    ->required(),

                Relation::make('post.author_id')
                    ->title('Auteur')
                    ->fromModel(User::class, 'name')
                    ->required(),
            ]),

            Relation::make('post.tags.')
                ->title('Tags')
                ->fromModel(Tag::class, 'name')
                ->multiple(),

            TextArea::make('post.excerpt')
                ->title('Samenvatting')
                ->rows(3),

            Quill::make('post.body')
                ->title('Inhoud')
                ->required(),
        ];
    }
}
