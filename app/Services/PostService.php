<?php

namespace App\Services;

use App\Models\PostModel;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Services\Interfaces\PostServiceInterface;
use App\Services\Interfaces\SlugServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostService implements PostServiceInterface
{
    public function __construct(
        private readonly PostRepositoryInterface $postRepository,
        private readonly SlugServiceInterface $slugService
    ) {}

    public function getPaginatedList(array $filters): LengthAwarePaginator
    {
        return $this->postRepository->paginateWithFilters($filters);
    }

    public function create(array $data): PostModel
    {
        $data['slug'] = $this->slugService->generate($data['title'], PostModel::class);
        $data['author_id'] = auth()->id();

        return $this->postRepository->create($data);
    }

    public function update(PostModel $post, array $data): PostModel
    {
        if (isset($data['title']) && $data['title'] !== $post->title) {
            $data['slug'] = $this->slugService->generate($data['title'], PostModel::class, $post->id);
        }

        $data['updated_by'] = auth()->id();

        return $this->postRepository->update($post, $data);
    }

    public function delete(PostModel $post): void
    {
        $this->postRepository->delete($post);
    }

    public function bulkDelete(array $ids): void
    {
        $this->postRepository->bulkDelete($ids);
    }

    public function bulkDraft(array $ids): void
    {
        $this->postRepository->bulkUpdateStatus($ids, 'draft');
    }

    public function bulkPublish(array $ids): void
    {
        $this->postRepository->bulkUpdateStatus($ids, 'published', now()->toDateTimeString());
    }

    public function recentPublished(int $limit = 5): Collection
    {
        return $this->postRepository->recentPublished($limit);
    }
}
