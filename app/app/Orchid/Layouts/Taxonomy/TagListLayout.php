<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Taxonomy;

use App\Models\Tag;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class TagListLayout extends Table
{
    public $target = 'tags';

    public function columns(): array
    {
        return [
            TD::make('name', 'Tag')
                ->render(fn (Tag $tag) => ModalToggle::make($tag->name)
                    ->modal('tagModal')
                    ->modalTitle('Tag bewerken')
                    ->method('saveTag')
                    ->asyncParameters(['tag' => $tag->id])),

            TD::make('slug', 'Slug'),

            TD::make('posts_count', 'Berichten')
                ->align(TD::ALIGN_RIGHT),

            TD::make('Acties')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Tag $tag) => Button::make('Verwijderen')
                    ->icon('bs.trash3')
                    ->confirm('Weet je zeker dat je deze tag wilt verwijderen?')
                    ->method('removeTag', ['id' => $tag->id])),
        ];
    }
}
