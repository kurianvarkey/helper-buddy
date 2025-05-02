<?php

declare(strict_types=1);

namespace Net4ideas\HelperBuddy;

use stdClass;

/**
 * Json helper class to validate json structure. You can validate json structure by providing a list of keys and types.
 * Supported types: string, array, object, int, float, decimal, boolean / bool
 * Usage:
 *  path => type:default (default is optional and only used when type is string, int, float, bool)
 * Note: if the final attribute in the path doesn't exist, it will be created
 *
 * For example:
 * $keys = [
        "name" => "string:''",
        "structure.*.rows.*.columns.*.data" => "object",
        "structure.0.rows.0.columns.0.price" => "decimal:0",
    ];
 */
class JsonBuddy
{
    /**
     * Validate json structure - if the supplied type is not correct, will set the default value.
     *
     * @param  bool  $outputAsJson  - default is true
     */
    public static function validateStructure(string|array $input, array $keys, bool $outputAsJson = true): string|array
    {
        $data = is_array($input)
            ? $input
            : json_decode(json: $input, associative: true, flags: JSON_THROW_ON_ERROR);

        foreach ($keys as $path => $typeDefault) {
            [$type, $default] = array_pad(explode(':', $typeDefault, 2), 2, null);

            // Parse default value by type
            $defaultValue = self::getDefaultValue($type, $default);

            // Fix the empty string for the json output
            if ($defaultValue === '""' || $defaultValue === "''") {
                $defaultValue = '';
            }

            // If path has more than one segment, we need to apply fix recursively
            $segments = explode('.', $path);
            if (count($segments) > 1) {
                self::applyFixRecursively($data, $segments, $defaultValue, $type);
            } elseif (! self::typeMatches($data[$path] ?? null, $type)) { // path only has one segment
                $data[$path] = $defaultValue;
            }
        }

        return $outputAsJson
            ? json_encode($data, JSON_THROW_ON_ERROR)
            : $data;
    }

    /**
     * Get default value by type
     */
    private static function getDefaultValue(string $type, ?string $default): mixed
    {
        return match ($type) {
            'string' => $default === 'null' ? null : (string) $default,
            'array' => [],
            'object' => new stdClass,
            'int' => (int) $default,
            'float' => (float) $default,
            'bool', 'boolean' => filter_var($default, FILTER_VALIDATE_BOOLEAN),
            'null' => null,
            default => $default,
        };
    }

    /**
     * Apply json fix recursively
     */
    private static function applyFixRecursively(?array &$data, array $segments, mixed $defaultValue, string $expectedType): void
    {
        if (! is_array($data)) {
            return;
        }

        $currentSegment = array_shift($segments);

        if ($currentSegment === '*') {
            foreach ($data as &$subArray) {
                self::applyFixRecursively($subArray, $segments, $defaultValue, $expectedType);
            }
        } else {
            if (empty($segments)) {
                if (! self::typeMatches($data[$currentSegment] ?? null, $expectedType)) {
                    $data[$currentSegment] = $defaultValue;
                }
            } elseif (array_key_exists($currentSegment, $data)) {
                self::applyFixRecursively($data[$currentSegment], $segments, $defaultValue, $expectedType);
            }
        }
    }

    /**
     * Check if type matches
     */
    private static function typeMatches(mixed $value, string $expectedType): bool
    {
        if ($value === null) {
            return $expectedType === 'null';
        }

        return match ($expectedType) {
            'string' => is_string($value),
            'array' => is_array($value),
            'object' => is_object($value) || (is_array($value) && ! array_is_list($value)),
            'int', 'integer' => is_int($value),
            'float' => is_float($value),
            'decimal' => is_numeric($value),
            'bool' => is_bool($value),
            default => false,
        };
    }
}
