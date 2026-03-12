<?php

namespace App\Services\Interfaces;

interface SlugServiceInterface
{
    public function generate(string $title, string $modelClass, ?int $excludeId = null): string;
}
