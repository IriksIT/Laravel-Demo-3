<?php

declare(strict_types=1);

use App\Orchid\Screens\Comment\CommentListScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\Post\PostEditScreen;
use App\Orchid\Screens\Post\PostListScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\Taxonomy\TaxonomyScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

// Dashboard
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Berichten
Route::screen('posts/create', PostEditScreen::class)
    ->name('platform.posts.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.posts')
        ->push('Nieuw', route('platform.posts.create')));

Route::screen('posts/{post}/edit', PostEditScreen::class)
    ->name('platform.posts.edit')
    ->breadcrumbs(fn (Trail $trail, $post) => $trail
        ->parent('platform.posts')
        ->push($post->title, route('platform.posts.edit', $post)));

Route::screen('posts', PostListScreen::class)
    ->name('platform.posts')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Berichten', route('platform.posts')));

// Reacties
Route::screen('comments', CommentListScreen::class)
    ->name('platform.comments')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Reacties', route('platform.comments')));

// Categorieën & tags
Route::screen('taxonomy', TaxonomyScreen::class)
    ->name('platform.taxonomy')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push('Categorieën & tags', route('platform.taxonomy')));

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));
