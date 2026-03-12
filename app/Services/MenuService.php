<?php

namespace App\Services;

use App\Models\MenuModel;
use App\Repositories\Interfaces\MenuItemRepositoryInterface;
use App\Repositories\Interfaces\MenuRepositoryInterface;
use App\Services\Interfaces\MenuServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MenuService implements MenuServiceInterface
{
    public function __construct(
        private readonly MenuRepositoryInterface $menuRepository,
        private readonly MenuItemRepositoryInterface $menuItemRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->menuRepository->all();
    }

    public function getTree(int $menuId): array
    {
        $items = $this->menuItemRepository->getTreeByMenuId($menuId);

        return $this->buildTree($items);
    }

    public function saveTree(int $menuId, array $tree): void
    {
        DB::transaction(function () use ($menuId, $tree) {
            $this->menuItemRepository->deleteByMenuId($menuId);

            $this->insertItems($menuId, $tree, null, 0);
        });
    }

    public function create(array $data): MenuModel
    {
        return $this->menuRepository->create($data);
    }

    public function update(MenuModel $menu, array $data): MenuModel
    {
        return $this->menuRepository->update($menu, $data);
    }

    public function delete(MenuModel $menu): void
    {
        $this->menuRepository->delete($menu);
    }

    private function buildTree(Collection $items): array
    {
        return $items->map(function ($item) {
            return [
                'id' => $item->id,
                'label' => $item->label,
                'type' => $item->type,
                'linkable_type' => $item->linkable_type,
                'linkable_id' => $item->linkable_id,
                'url' => $item->url,
                'target' => $item->target,
                'children' => $item->children->isNotEmpty()
                    ? $this->buildTree($item->children)
                    : [],
            ];
        })->toArray();
    }

    private function insertItems(int $menuId, array $items, ?int $parentId, int $startOrder): void
    {
        foreach ($items as $index => $item) {
            $record = [
                'menu_id' => $menuId,
                'parent_id' => $parentId,
                'label' => $item['label'],
                'type' => $item['type'],
                'linkable_type' => $item['linkable_type'] ?? null,
                'linkable_id' => $item['linkable_id'] ?? null,
                'url' => $item['url'] ?? null,
                'target' => $item['target'] ?? '_self',
                'sort_order' => $startOrder + $index,
            ];

            $this->menuItemRepository->insert([$record]);

            if (! empty($item['children'])) {
                $newParent = \App\Models\MenuItemModel::where('menu_id', $menuId)
                    ->where('sort_order', $startOrder + $index)
                    ->where('parent_id', $parentId)
                    ->first();

                if ($newParent) {
                    $this->insertItems($menuId, $item['children'], $newParent->id, 0);
                }
            }
        }
    }
}
