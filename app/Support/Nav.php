<?php

namespace App\Support;

final class Nav
{
    /**
     * @param  list<array{href: string}>  $items
     */
    public static function active(string $path, string $href, array $items): bool
    {
        if ($href === '/exporter' || $href === '/fama') {
            return $path === $href;
        }

        $matches = array_values(array_filter(
            $items,
            fn (array $item) => $path === $item['href'] || str_starts_with($path, $item['href'].'/')
        ));
        usort($matches, fn (array $a, array $b) => strlen($b['href']) <=> strlen($a['href']));

        return ($matches[0]['href'] ?? null) === $href;
    }
}
