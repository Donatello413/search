<?php

namespace App\Elastic;

interface SearchInterface
{
    public static function searchableAs(): string;

    public static function mapping(): array;

    public function toSearchableArray(): array;
}
