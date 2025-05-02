<?php

declare(strict_types=1);

namespace Tests;

use JsonException;
use Net4ideas\HelperBuddy\JsonBuddy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Tests\Fixtures\JsonBuddyDataProvider;

/**
 * Unit tests for the JsonBuddy class.
 */
#[CoversClass(JsonBuddy::class)]
final class JsonBuddyTest extends TestCase
{
    /**
     * Test the validateStructure method with various inputs.
     */
    #[DataProviderExternal(JsonBuddyDataProvider::class, 'jsonDataProvider')]
    public function test_validate_structure_json(string $inputJson, array $keys, string $expectedJson): void
    {
        $result = JsonBuddy::validateStructure($inputJson, $keys);

        // Use assertJsonStringEqualsJsonString for robust comparison ignoring whitespace and key order
        $this->assertJsonStringEqualsJsonString($expectedJson, $result);
    }

    /**
     * Test the validateStructure method with an array input.
     */
    public function test_validate_structure_array(): void
    {
        $inputArray = [
            'items' => [
                ['name' => null],
            ],
        ];

        $expectedArray = [
            'items' => [
                ['name' => 'Default'],
            ],
        ];

        $keys = [
            'items.*.name' => 'string:Default',
        ];

        $validatedArray = JsonBuddy::validateStructure($inputArray, $keys, false);
        $this->assertSame($expectedArray, $validatedArray);
    }

    /**
     * Test the invalidJson static factory method.
     */
    public function test_invalid_json_string(): void
    {
        $inputJson = '{\"items\":[null]}';
        $keys = [
            'items.*.name.*' => 'string:Default',
        ];

        $expectedException = new JsonException('Syntax error', JSON_ERROR_SYNTAX);
        $this->expectExceptionObject($expectedException);

        JsonBuddy::validateStructure($inputJson, $keys);
    }

    /**
     * Test the private getDefaultValue method using Reflection.
     */
    #[DataProviderExternal(JsonBuddyDataProvider::class, 'defaultValueDataProvider')]
    public function test_get_default_value(string $type, ?string $default, mixed $expectedValue): void
    {
        // Use reflection to get access to the private method
        $method = new ReflectionMethod(JsonBuddy::class, 'getDefaultValue');
        // Make the private method accessible for testing
        $method->setAccessible(true);

        // Invoke the private static method. The first argument is null for static methods.
        $actualValue = $method->invoke(null, $type, $default);

        // Use appropriate assertion based on the expected type for accurate comparison
        if (is_object($expectedValue)) {
            $this->assertEquals($expectedValue, $actualValue); // assertEquals is suitable for object comparison
        } else {
            $this->assertSame($expectedValue, $actualValue); // assertSame checks value and type strictly
        }
    }

    /**
     * Test the private typeMatches method using Reflection.
     */
    #[DataProviderExternal(JsonBuddyDataProvider::class, 'typeMatchesDataProvider')]
    public function test_type_matches(mixed $value, string $expectedType, bool $expectedResult): void
    {
        // Use reflection to get access to the private method
        $method = new ReflectionMethod(JsonBuddy::class, 'typeMatches');
        // Make the private method accessible for testing
        $method->setAccessible(true);

        // Invoke the private static method. The first argument is null for static methods.
        $actualResult = $method->invoke(null, $value, $expectedType);

        // Assert that the actual result matches the expected boolean result
        $this->assertSame($expectedResult, $actualResult);
    }
}
