<?php

namespace App\Models;

use App\Traits\HasRevisions;
use Database\Factories\PageModelFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PageModel extends Model implements HasMedia
{
    /** @use HasFactory<PageModelFactory> */
    use HasFactory, HasRevisions, InteractsWithMedia, SoftDeletes;

    protected static function newFactory(): PageModelFactory
    {
        return PageModelFactory::new();
    }

    protected $table = 'pages';

    /**
     * @var array<int, string>
     */
    protected $appends = ['missing_alt_text'];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'template',
        'status',
        'published_at',
        'parent_id',
        'sort_order',
        'meta_title',
        'meta_description',
        'og_image',
        'author_id',
        'updated_by',
        'form_id',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
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

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(FormModel::class, 'form_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')->where('published_at', '>', now());
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

    public function getContentFormatAttribute(): string
    {
        $content = $this->content;

        if (is_array($content) && ! empty($content) && isset($content[0]['type'], $content[0]['id'])) {
            return 'blocks';
        }

        return 'tiptap';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
    }
}
