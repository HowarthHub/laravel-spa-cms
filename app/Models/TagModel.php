<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TagModel extends Model
{
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
