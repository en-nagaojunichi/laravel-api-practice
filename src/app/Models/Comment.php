<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'author_name',
        'body',
        'is_approved'
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean'
        ];
    }

    /**
     * コメント対象の記事
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * コメント者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}