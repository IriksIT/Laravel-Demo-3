<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Screen\AsSource;

class Category extends Model
{
    use AsSource, Filterable, HasFactory;

    protected $fillable = ['name', 'slug'];

    protected $allowedSorts = ['id', 'name'];

    protected $allowedFilters = ['name' => Like::class];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
