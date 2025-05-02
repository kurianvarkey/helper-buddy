<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use RuntimeException;
use stdClass;

/**
 * Unit tests for the JsonBuddy class.
 */
final class JsonBuddyDataProvider
{
    // Define the path to the test data JSON file
    private const TEST_DATA_FILE = __DIR__.'/test_data.json';

    /**
     * Provides data for testing the validateStructure method, loaded from a JSON file.
     */
    public static function jsonDataProvider(): array
    {
        if (! file_exists(self::TEST_DATA_FILE)) {
            throw new RuntimeException(sprintf('Test data file not found: %s', self::TEST_DATA_FILE));
        }

        $jsonData = file_get_contents(self::TEST_DATA_FILE);
        $testCases = json_decode($jsonData, true);

        if ($testCases === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Failed to decode test data JSON: '.json_last_error_msg());
        }

        $data = [];
        foreach ($testCases as $case) {
            if (! isset($case['name'], $case['inputJson'], $case['keys'], $case['expectedJson'])) {
                throw new RuntimeException('Invalid test case structure in JSON data.');
            }
            $data[$case['name']] = [$case['inputJson'], $case['keys'], $case['expectedJson']];
        }

        return $data;
    }

    /**
     * Provides data for testing the private getDefaultValue method.
     */
    public static function defaultValueDataProvider(): array
    {
        return [
            'string_empty' => ['string', '', ''],
            'string_default' => ['string', 'hello', 'hello'],
            'string_null_default' => ['string', 'null', null], // Testing the specific 'string:null' behavior
            'array_type' => ['array', null, []], // Array type default is always empty array
            'object_type' => ['object', null, new stdClass], // Object type default is always empty object
            'int_default_zero' => ['int', '0', 0],
            'int_default_positive' => ['int', '123', 123],
            'int_default_negative' => ['int', '-45', -45],
            'float_default_zero' => ['float', '0.0', 0.0],
            'float_default_positive' => ['float', '1.23', 1.23],
            'float_default_negative' => ['float', '-4.5', -4.5],
            'bool_true' => ['bool', 'true', true],
            'bool_false' => ['bool', 'false', false],
            'bool_1' => ['bool', '1', true], // Filter_var handles '1' as true
            'bool_0' => ['bool', '0', false], // Filter_var handles '0' as false
            'bool_on' => ['bool', 'on', true], // Filter_var handles 'on' as true
            'bool_off' => ['bool', 'off', false], // Filter_var handles 'off' as false
            'null_type' => ['null', null, null], // Null type always defaults to null
            'unsupported_type_with_default' => ['unsupported', 'some_value', 'some_value'], // Should return the default string for unknown types
            'unsupported_type_no_default' => ['unsupported', null, null], // Should return null for unknown types with no default specified
        ];
    }

    /**
     * Provides data for testing the private typeMatches method.
     */
    public static function typeMatchesDataProvider(): array
    {
        return [
            'string_matches' => ['hello', 'string', true],
            'string_does_not_match_int' => ['hello', 'int', false],
            'int_matches' => [123, 'int', true],
            'int_matches_integer_alias' => [123, 'integer', true], // Testing the 'integer' alias
            'int_does_not_match_string' => [123, 'string', false],
            'float_matches' => [1.23, 'float', true],
            'float_matches_decimal' => [1.23, 'decimal', true], // 'decimal' checks if it's numeric
            'int_matches_decimal' => [123, 'decimal', true], // 'decimal' checks if it's numeric
            'float_does_not_match_int' => [1.23, 'int', false],
            'bool_true_matches_bool' => [true, 'bool', true],
            'bool_false_matches_bool' => [false, 'bool', true],
            'bool_does_not_match_string' => [true, 'string', false],
            'array_matches' => [[1, 2], 'array', true],
            'array_does_not_match_object' => [[1, 2], 'object', false], // Standard list array is not an object
            'associative_array_matches_object' => [['key' => 'value'], 'object', true], // Associative array is treated as an object by the check
            'object_matches' => [new stdClass, 'object', true],
            'object_does_not_match_array' => [new stdClass, 'array', false],
            'null_matches_null' => [null, 'null', true],
            'null_does_not_match_string' => [null, 'string', false],
            'string_does_not_match_null' => ['hello', 'null', false],
            'empty_array_matches_array' => [[], 'array', true],
            'empty_array_does_not_match_object' => [[], 'object', false], // Empty array is a list
            'empty_object_matches_object' => [new stdClass, 'object', true],
            'string_numeric_does_not_match_int' => ['123', 'int', false], // String numeric is not an int
            'string_numeric_matches_decimal' => ['123', 'decimal', true], // String numeric is numeric
            'string_float_does_not_match_float' => ['1.23', 'float', false], // String float is not a float
            'string_float_matches_decimal' => ['1.23', 'decimal', true], // String float is numeric
        ];
    }
}
