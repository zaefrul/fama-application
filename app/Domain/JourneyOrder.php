<?php

namespace App\Domain;

/**
 * A perjalanan must follow one of these lines, from the start, and may stop early.
 */
final class JourneyOrder
{
    /**
     * @return list<list<PartyType>>
     */
    public static function patterns(): array
    {
        return [
            [PartyType::Supplier, PartyType::Marketer, PartyType::Exporter, PartyType::Importer, PartyType::Retailer],
            [PartyType::Supplier, PartyType::Exporter, PartyType::Importer, PartyType::Marketer, PartyType::Retailer],
            [PartyType::Exporter, PartyType::Importer, PartyType::Marketer, PartyType::Retailer],
            [PartyType::Exporter, PartyType::Importer, PartyType::Retailer],
            [PartyType::Supplier, PartyType::Marketer, PartyType::Retailer],
            [PartyType::Supplier, PartyType::Retailer],
        ];
    }

    /**
     * @param  list<PartyType>  $roles
     */
    public static function allows(array $roles): bool
    {
        if ($roles === []) {
            return false;
        }

        foreach (self::patterns() as $pattern) {
            if (count($roles) > count($pattern)) {
                continue;
            }
            $matches = true;
            foreach ($roles as $index => $role) {
                if ($pattern[$index] !== $role) {
                    $matches = false;
                    break;
                }
            }
            if ($matches) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<PartyType>  $roles
     * @return list<PartyType>
     */
    public static function nextRoles(array $roles): array
    {
        $next = [];
        foreach (self::patterns() as $pattern) {
            if (count($roles) >= count($pattern)) {
                continue;
            }
            $matches = true;
            foreach ($roles as $index => $role) {
                if ($pattern[$index] !== $role) {
                    $matches = false;
                    break;
                }
            }
            if ($matches) {
                $next[$pattern[count($roles)]->value] = $pattern[count($roles)];
            }
        }

        return array_values($next);
    }
}
