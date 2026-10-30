<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\Post;

use App\Models\Post;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Rows;

class PostPublishLayout extends Rows
{
    public function fields(): array
    {
        return [
            Select::make('post.status')
                ->title('Status')
                ->options(Post::STATUSES),

            DateTimer::make('post.published_at')
                ->title('Publicatiedatum')
                ->enableTime()
                ->format('Y-m-d H:i')
                ->allowInput(),

            Input::make('post.slug')
                ->title('Slug')
                ->help('Laat leeg om automatisch te genereren uit de titel.'),
        ];
    }
}
