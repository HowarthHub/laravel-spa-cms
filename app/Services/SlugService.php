<?php

namespace App\Services;

use App\Services\Interfaces\SlugServiceInterface;
use Illuminate\Support\Str;

class SlugService implements SlugServiceInterface
{
    public function generate(string $title, string $modelClass, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 2;

        while ($this->slugExists($slug, $modelClass, $excludeId)) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug, string $modelClass, ?int $excludeId): bool
    {
        $query = $modelClass::where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
