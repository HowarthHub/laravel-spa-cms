<?php

namespace App\Models;

use App\Traits\HasRevisions;
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
    use HasFactory, HasRevisions, InteractsWithMedia, SoftDeletes;

    protected static function newFactory(): ServiceModelFactory
    {
        return ServiceModelFactory::new();
    }

    protected $table = 'services';

    /**
     * @var array<int, string>
     */
    protected $appends = ['missing_alt_text'];

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

    public function getMissingAltTextAttribute(): bool
    {
        $content = $this->content;

        if (! is_array($content) || empty($content)) {
            return false;
        }

        if (isset($content[0]['type'], $content[0]['id'])) {
            return $this->hasBlocksMissingAlt($content);
        }

        return $this->hasTiptapNodesMissingAlt($content);
    }

    /**
     * @param  array<int, array<string, mixed>>  $blocks
     */
    private function hasBlocksMissingAlt(array $blocks): bool
    {
        foreach ($blocks as $block) {
            $type = $block['type'] ?? null;
            $data = $block['data'] ?? [];

            if ($type === 'image' && ! empty($data['url']) && empty($data['alt'])) {
                return true;
            }

            if ($type === 'hero' && ! empty($data['backgroundImage']) && empty($data['alt'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    private function hasTiptapNodesMissingAlt(array $node): bool
    {
        if (($node['type'] ?? null) === 'image') {
            $attrs = $node['attrs'] ?? [];

            if (! empty($attrs['src']) && empty($attrs['alt'])) {
                return true;
            }
        }

        foreach ($node['content'] ?? [] as $child) {
            if (is_array($child) && $this->hasTiptapNodesMissingAlt($child)) {
                return true;
            }
        }

        return false;
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
