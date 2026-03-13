<?php

namespace App\Traits;

use App\Models\RevisionModel;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRevisions
{
    public function revisions(): MorphMany
    {
        return $this->morphMany(RevisionModel::class, 'revisionable');
    }

    /**
     * Snapshot the current model attributes and save as a revision.
     */
    public function createRevision(): RevisionModel
    {
        $snapshotFields = array_intersect_key(
            $this->getAttributes(),
            array_flip($this->getRevisionableFields())
        );

        return $this->revisions()->create([
            'user_id' => auth()->id(),
            'content' => $snapshotFields,
            'created_at' => now(),
        ]);
    }

    /**
     * Restore model attributes from a revision.
     */
    public function restoreRevision(RevisionModel $revision): bool
    {
        $content = $revision->content;

        $fillable = array_intersect_key($content, array_flip($this->getFillable()));

        return $this->update($fillable);
    }

    /**
     * @return array<int, string>
     */
    protected function getRevisionableFields(): array
    {
        return [
            'title', 'slug', 'content', 'excerpt', 'status',
            'meta_title', 'meta_description', 'og_image',
            'short_description', 'icon', 'features',
            'cta_text', 'cta_link', 'sort_order',
            'template', 'parent_id', 'form_id',
            'featured_image',
        ];
    }
}
