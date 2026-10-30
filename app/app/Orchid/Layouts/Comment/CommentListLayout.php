<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Comment;

use App\Models\Comment;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Components\Cells\Boolean;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class CommentListLayout extends Table
{
    public $target = 'comments';

    public function columns(): array
    {
        return [
            TD::make('author_name', 'Auteur')
                ->sort()
                ->filter(Input::make())
                ->render(fn (Comment $comment) => "<strong>{$comment->author_name}</strong><br><small class=\"text-muted\">{$comment->author_email}</small>"),

            TD::make('body', 'Reactie')
                ->filter(Input::make())
                ->width('40%')
                ->render(fn (Comment $comment) => \Illuminate\Support\Str::limit($comment->body, 140)),

            TD::make('post', 'Bericht')
                ->render(fn (Comment $comment) => Link::make(\Illuminate\Support\Str::limit($comment->post?->title ?? '-', 40))
                    ->route('platform.posts.edit', $comment->post)),

            TD::make('is_approved', 'Goedgekeurd')
                ->sort()
                ->align(TD::ALIGN_CENTER)
                ->filter(Select::make()->options([1 => 'Ja', 0 => 'Nee'])->empty('Alle'))
                ->usingComponent(Boolean::class),

            TD::make('created_at', 'Geplaatst')
                ->sort()
                ->align(TD::ALIGN_RIGHT)
                ->usingComponent(DateTimeSplit::class),

            TD::make('Acties')
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn (Comment $comment) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Button::make($comment->is_approved ? 'Afkeuren' : 'Goedkeuren')
                            ->icon($comment->is_approved ? 'bs.x-circle' : 'bs.check2-circle')
                            ->method('toggle', ['id' => $comment->id]),
                        Button::make('Verwijderen')
                            ->icon('bs.trash3')
                            ->confirm('Weet je zeker dat je deze reactie wilt verwijderen?')
                            ->method('remove', ['id' => $comment->id]),
                    ])),
        ];
    }
}
