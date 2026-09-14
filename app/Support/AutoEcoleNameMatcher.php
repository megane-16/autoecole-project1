<?php

namespace App\Support;

use Illuminate\Support\Str;

class AutoEcoleNameMatcher
{
    public static function normalize(string $name): string
    {
        return Str::of($name)
            ->replaceMatches('/[^\pL\pN]+/u', ' ')
            ->squish()
            ->lower()
            ->value();
    }

    public static function matches(string $nameA, string $nameB): bool
    {
        $a = static::normalize($nameA);
        $b = static::normalize($nameB);

        if ($a === '' || $b === '') {
            return false;
        }

        if ($a === $b) {
            return true;
        }

        return str_contains($a, $b) || str_contains($b, $a);
    }
}
