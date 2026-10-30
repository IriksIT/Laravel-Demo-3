<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Comment;

use App\Models\Comment;
use App\Orchid\Layouts\Comment\CommentListLayout;
use Illuminate\Http\Request;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class CommentListScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'comments' => Comment::with('post')
                ->filters()
                ->defaultSort('created_at', 'desc')
                ->paginate(20),
        ];
    }

    public function name(): ?string
    {
        return 'Reacties';
    }

    public function description(): ?string
    {
        return 'Modereer reacties van bezoekers.';
    }

    public function permission(): ?iterable
    {
        return ['platform.blog.comments'];
    }

    public function layout(): iterable
    {
        return [CommentListLayout::class];
    }

    public function toggle(Request $request): void
    {
        $comment = Comment::findOrFail($request->get('id'));
        $comment->update(['is_approved' => ! $comment->is_approved]);

        Toast::info($comment->is_approved ? 'Reactie goedgekeurd.' : 'Reactie afgekeurd.');
    }

    public function remove(Request $request): void
    {
        Comment::findOrFail($request->get('id'))->delete();

        Toast::info('Reactie verwijderd.');
    }
}
