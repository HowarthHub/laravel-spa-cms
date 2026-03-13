<?php

namespace App\Models;

use Database\Factories\MediaItemModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaItemModel extends Model implements HasMedia
{
    /** @use HasFactory<MediaItemModelFactory> */
    use HasFactory, InteractsWithMedia;

    protected static function newFactory(): MediaItemModelFactory
    {
        return MediaItemModelFactory::new();
    }

    protected $table = 'media_items';

    protected $fillable = [
        'title',
        'alt_text',
        'uploaded_by',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'uploaded_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')
            ->useDisk(config('cms.media.disk'));
    }
}
