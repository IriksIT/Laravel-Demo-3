<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class Post extends Model
{
    use AsSource, Attachable, Filterable, HasFactory;

    public const STATUSES = [
        'draft'     => 'Concept',
        'review'    => 'Ter beoordeling',
        'published' => 'Gepubliceerd',
        'archived'  => 'Gearchiveerd',
    ];

    protected $fillable = [
        'author_id', 'category_id', 'title', 'slug', 'excerpt', 'body', 'status', 'views', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected $allowedSorts = ['id', 'title', 'status', 'views', 'published_at', 'created_at'];

    protected $allowedFilters = [
        'title'       => Like::class,
        'status'      => Where::class,
        'category_id' => Where::class,
        'author_id'   => Where::class,
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
