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
        return PostModel::query()->paginate(config('cms.per_page.posts'));
    }

    public function find(int $id): ?PostModel
    {
        return PostModel::find($id);
    }

    public function create(array $data): PostModel
    {
        return PostModel::create($data);
    }

    public function update(PostModel $post, array $data): PostModel
    {
        $post->update($data);

        return $post->fresh();
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
        return PostModel::published()->latest('published_at')->limit($limit)->get();
    }
}
