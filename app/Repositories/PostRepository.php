<?php

namespace App\Repositories;

use App\Models\PostModel;
use App\Repositories\Interfaces\PostRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    public function paginateWithFilters(array $filters): LengthAwarePaginator
    {
        return PostModel::query()
            ->with(['author', 'categories', 'tags'])
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['category_id'] ?? null, fn ($q, $id) => $q->whereHas('categories', fn ($q) => $q->where('categories.id', $id)))
            ->latest('updated_at')
            ->paginate(config('cms.per_page.posts'));
    }

    public function find(int $id): ?PostModel
    {
        return PostModel::find($id);
    }

    public function create(array $data): PostModel
    {
        $categoryIds = $data['category_ids'] ?? [];
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['category_ids'], $data['tag_ids']);

        $post = PostModel::create($data);
        $post->categories()->sync($categoryIds);
        $post->tags()->sync($tagIds);

        return $post;
    }

    public function update(PostModel $post, array $data): PostModel
    {
        $categoryIds = $data['category_ids'] ?? null;
        $tagIds = $data['tag_ids'] ?? null;
        unset($data['category_ids'], $data['tag_ids']);

        $post->update($data);

        if ($categoryIds !== null) {
            $post->categories()->sync($categoryIds);
        }
        if ($tagIds !== null) {
            $post->tags()->sync($tagIds);
        }

        return $post->fresh(['categories', 'tags']);
    }

    public function delete(PostModel $post): void
    {
        $post->delete();
    }

    public function bulkDelete(array $ids): void
    {
        PostModel::whereIn('id', $ids)->delete();
    }

    public function recentPublished(int $limit): Collection
    {
        return PostModel::published()
            ->with('author')
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }
}
