<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class Comment extends Model
{
    use AsSource, Filterable, HasFactory;

    protected $fillable = ['post_id', 'author_name', 'author_email', 'body', 'is_approved'];

    protected $casts = ['is_approved' => 'boolean'];

    protected $allowedSorts = ['id', 'author_name', 'is_approved', 'created_at'];

    protected $allowedFilters = [
        'author_name' => Like::class,
        'body'        => Like::class,
        'is_approved' => Where::class,
        'post_id'     => Where::class,
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
