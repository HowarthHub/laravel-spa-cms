<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaItemModel extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'media_items';

    protected $fillable = [
        'title',
        'alt_text',
        'uploaded_by',
    ];

    public function uploader()
    {
        return $this->belongsTo(UserModel::class, 'uploaded_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files')
            ->useDisk(config('cms.media.disk'));
    }
}
