<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content'];

    public static function searchableAs(): string
    {
        return 'posts_index';
    }

    public function toSearchableArray(): array
    {
//        $post = $this->toArray();

        return [
            'text' => $this->text,
        ];
//        $user = $this->user ? $this->user->only(['id', 'name', 'email']) : null;
//
//        return array_merge($post, [
//            'user' => $user,
//        ]);
    }

    public function meta(): HasOne
    {
        return $this->hasOne(PostMeta::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public static function mapping(): array
    {
        // пример
        return [
            'mappings' => [
                '_source' => [
                    'enabled' => true
                ],
                'properties' => [
                    'first_name' => [
                        'type' => 'keyword'
                    ],
                    'age' => [
                        'type' => 'integer'
                    ]
                ]
            ]
        ];
    }
}
