<?php

declare(strict_types=1);

namespace Tests;

use Net4ideas\HelperBuddy\ArrayBuddy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\ArrayBuddyDataProvider;

/**
 * Unit tests for the JsonBuddy class.
 */
#[CoversClass(ArrayBuddy::class)]
final class ArrayBuddyTest extends TestCase
{
    /**
     * Test the removeNullValues method.
     */
    #[DataProviderExternal(ArrayBuddyDataProvider::class, 'removeNullValuesDataProvider')]
    public function test_remove_null_values(array $input, array $expected): void
    {
        $result = ArrayBuddy::removeNullValues($input);

        $this->assertSame($expected, $result);
    }

    /**
     * Test the removeNullValues method.
     */
    #[DataProviderExternal(ArrayBuddyDataProvider::class, 'replaceNullsWithStringDataProvider')]
    public function test_replace_nulls_with_string(array $input, ?string $replaceString, array $expected): void
    {
        $result = ArrayBuddy::replaceNullsWithString($input, $replaceString);

        $this->assertSame($expected, $result);
    }
}
