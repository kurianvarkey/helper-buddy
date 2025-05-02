<?php

declare(strict_types=1);

namespace Net4ideas\HelperBuddy;

/**
 * Array helper class to remove null values recursively
 */
class ArrayBuddy
{
    /**
     * Remove null values recursively
     * Note: Output array will preserve the key
     *
     * @param  array  $input  The input array
     */
    public static function removeNullValues(array $input): ?array
    {
        return array_filter(
            array_map(
                function ($element) {
                    return is_array($element) ? self::removeNullValues($element) : $element;
                },
                $input
            ),
            function ($element): bool {
                return ! is_null($element);
            }
        );
    }

    /**
     * Replace null values recursively with input string
     *
     * @param  array  $data  The input array
     * @param  string  $replaceString  The string to replace null values with
     */
    public static function replaceNullsWithString(array $data, string $replaceString = ''): array
    {
        return array_map(function ($value) use ($replaceString) {
            if (is_array($value)) {
                return self::replaceNullsWithString($value, $replaceString);
            }

            return $value === null ? $replaceString : $value;
        }, $data);
    }
}
