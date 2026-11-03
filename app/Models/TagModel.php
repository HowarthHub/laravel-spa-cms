<?php

namespace App\Models;

use Database\Factories\TagModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TagModel extends Model
{
    /** @use HasFactory<TagModelFactory> */
    use HasFactory;

    protected static function newFactory(): TagModelFactory
    {
        return TagModelFactory::new();
    }

    protected $table = 'tags';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(PostModel::class, 'post_tag', 'tag_id', 'post_id');
    }
}
