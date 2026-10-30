<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Orchid\Layouts\Dashboard\CategoryChart;
use App\Orchid\Layouts\Dashboard\PublishedChart;
use App\Orchid\Layouts\Post\PostListLayout;
use Illuminate\Support\Carbon;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PlatformScreen extends Screen
{
    public function query(): iterable
    {
        $months = collect(range(5, 0))
            ->map(fn (int $i) => Carbon::now()->startOfMonth()->subMonths($i));

        $published = $months->map(fn (Carbon $month) => Post::query()
            ->whereBetween('published_at', [$month, $month->copy()->endOfMonth()])
            ->count());

        $categories = Category::withSum('posts', 'views')->orderBy('name')->get();

        return [
            'metrics' => [
                'published' => ['value' => Post::where('status', 'published')->count()],
                'drafts'    => ['value' => Post::whereIn('status', ['draft', 'review'])->count()],
                'views'     => ['value' => number_format(Post::sum('views'), 0, ',', '.')],
                'pending'   => ['value' => Comment::where('is_approved', false)->count()],
            ],
            'publishedChart' => [
                [
                    'name'   => 'Berichten',
                    'values' => $published->values()->all(),
                    'labels' => $months->map(fn (Carbon $m) => $m->translatedFormat('M Y'))->values()->all(),
                ],
            ],
            'categoryChart' => [
                [
                    'name'   => 'Weergaven',
                    'values' => $categories->map(fn (Category $c) => (int) $c->posts_sum_views)->all(),
                    'labels' => $categories->pluck('name')->all(),
                ],
            ],
            'posts' => Post::with(['category', 'author'])
                ->withCount('comments')
                ->where('status', 'published')
                ->orderByDesc('views')
                ->limit(8)
                ->get(),
        ];
    }

    public function name(): ?string
    {
        return 'Dashboard';
    }

    public function description(): ?string
    {
        return 'Overzicht van berichten, weergaven en reacties.';
    }

    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Gepubliceerd'          => 'metrics.published',
                'Concepten'             => 'metrics.drafts',
                'Totaal weergaven'      => 'metrics.views',
                'Reacties ter moderatie' => 'metrics.pending',
            ]),

            Layout::columns([
                PublishedChart::class,
                CategoryChart::class,
            ]),

            Layout::block(PostListLayout::class)
                ->title('Populairste berichten')
                ->description('Gepubliceerde berichten gesorteerd op aantal weergaven.'),
        ];
    }
}
