<?php

declare(strict_types=1);

namespace App\Orchid;

use App\Models\Comment;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
    }

    /**
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Dashboard')
                ->icon('bs.speedometer2')
                ->title('Blog')
                ->route(config('platform.index')),

            Menu::make('Berichten')
                ->icon('bs.file-earmark-text')
                ->route('platform.posts')
                ->permission('platform.blog.posts')
                ->active(['*/posts', '*/posts/*']),

            Menu::make('Reacties')
                ->icon('bs.chat-dots')
                ->route('platform.comments')
                ->permission('platform.blog.comments')
                ->badge(fn () => Comment::where('is_approved', false)->count(), Color::WARNING),

            Menu::make('Categorieën & tags')
                ->icon('bs.tags')
                ->route('platform.taxonomy')
                ->permission('platform.blog.posts')
                ->divider(),

            Menu::make(__('Users'))
                ->icon('bs.person-gear')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),

            Menu::make('Orchid documentatie')
                ->title('Docs')
                ->icon('bs.box-arrow-up-right')
                ->url('https://orchid.software/en/docs')
                ->target('_blank')
                ->badge(fn () => Dashboard::version(), Color::DARK),
        ];
    }

    /**
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group('Blog')
                ->addPermission('platform.blog.posts', 'Berichten, categorieën & tags')
                ->addPermission('platform.blog.comments', 'Reacties'),

            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
