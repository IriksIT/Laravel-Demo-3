<?php

declare(strict_types=1);

namespace App\Orchid\Screens\Taxonomy;

use App\Models\Category;
use App\Models\Tag;
use App\Orchid\Layouts\Taxonomy\CategoryListLayout;
use App\Orchid\Layouts\Taxonomy\TagListLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class TaxonomyScreen extends Screen
{
    public function query(): iterable
    {
        return [
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
            'tags'       => Tag::withCount('posts')->orderBy('name')->get(),
        ];
    }

    public function name(): ?string
    {
        return 'Categorieën & tags';
    }

    public function description(): ?string
    {
        return 'Beheer de indeling van het blog.';
    }

    public function permission(): ?iterable
    {
        return ['platform.blog.posts'];
    }

    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Nieuwe categorie')
                ->icon('bs.plus-circle')
                ->modal('categoryModal')
                ->modalTitle('Nieuwe categorie')
                ->method('saveCategory'),

            ModalToggle::make('Nieuwe tag')
                ->icon('bs.tag')
                ->modal('tagModal')
                ->modalTitle('Nieuwe tag')
                ->method('saveTag'),
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::columns([
                Layout::block(CategoryListLayout::class)->title('Categorieën'),
                Layout::block(TagListLayout::class)->title('Tags'),
            ]),

            Layout::modal('categoryModal', Layout::rows([
                Input::make('category.name')->title('Naam')->required(),
            ]))->deferred('loadCategory'),

            Layout::modal('tagModal', Layout::rows([
                Input::make('tag.name')->title('Naam')->required(),
            ]))->deferred('loadTag'),
        ];
    }

    public function loadCategory(?Category $category = null): iterable
    {
        return ['category' => $category ?? new Category()];
    }

    public function loadTag(?Tag $tag = null): iterable
    {
        return ['tag' => $tag ?? new Tag()];
    }

    public function saveCategory(Request $request, ?Category $category = null): void
    {
        $name = $request->validate(['category.name' => ['required', 'string', 'max:100']])['category']['name'];

        $category ??= new Category();
        $category->fill(['name' => $name, 'slug' => Str::slug($name)])->save();

        Toast::info('Categorie opgeslagen.');
    }

    public function saveTag(Request $request, ?Tag $tag = null): void
    {
        $name = $request->validate(['tag.name' => ['required', 'string', 'max:100']])['tag']['name'];

        $tag ??= new Tag();
        $tag->fill(['name' => $name, 'slug' => Str::slug($name)])->save();

        Toast::info('Tag opgeslagen.');
    }

    public function removeCategory(Request $request): void
    {
        Category::findOrFail($request->get('id'))->delete();

        Toast::info('Categorie verwijderd.');
    }

    public function removeTag(Request $request): void
    {
        Tag::findOrFail($request->get('id'))->delete();

        Toast::info('Tag verwijderd.');
    }
}
