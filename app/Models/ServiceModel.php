<?php

namespace App\Models;

use Database\Factories\ServiceModelFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ServiceModel extends Model implements HasMedia
{
    /** @use HasFactory<ServiceModelFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected static function newFactory(): ServiceModelFactory
    {
        return ServiceModelFactory::new();
    }

    protected $table = 'services';

    protected $fillable = [
        'title', 'slug', 'short_description', 'content', 'icon',
        'featured_image', 'features', 'cta_text', 'cta_link',
        'sort_order', 'status', 'published_at', 'meta_title',
        'meta_description', 'og_image', 'author_id', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'features' => 'array',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'author_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'updated_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
    }
}
