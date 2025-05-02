<?php

declare(strict_types=1);

namespace Tests\Fixtures;

/**
 * Unit tests for the JsonBuddy class.
 */
final class ArrayBuddyDataProvider
{
    /**
     * Data provider for the testRemoveNullValues method.
     */
    public static function removeNullValuesDataProvider(): array
    {
        return [
            [
                ['a' => null, 'b' => null],
                [],
            ],
            [
                [1, 2, 3, 'a', 'b', 'c'],
                [1, 2, 3, 'a', 'b', 'c'],
            ],
            [
                [1, [null, 2, 3], 'a', ['b', 'c', null]],
                [1, [1 => 2, 2 => 3], 'a', ['b', 'c']],
            ],
            [
                ['name' => 'Jack', 'age' => 40, 'address' => null, 'hobbies' => ['reading', 'writing', null]],
                ['name' => 'Jack', 'age' => 40, 'hobbies' => ['reading', 'writing']],
            ],
        ];
    }

    /**
     * Data provider for the testReplaceNullsWithString method.
     */
    public static function replaceNullsWithStringDataProvider(): array
    {
        return [
            [
                ['a' => null, 'b' => null],
                '',
                ['a' => '', 'b' => ''],
            ],
            [
                ['a' => null, 'b' => null],
                'new string',
                ['a' => 'new string', 'b' => 'new string'],
            ],
            [
                ['name' => 'Jack', 'age' => 40, 'address' => null, 'hobbies' => ['reading', 'writing', null]],
                'default',
                ['name' => 'Jack', 'age' => 40, 'address' => 'default', 'hobbies' => ['reading', 'writing', 'default']],
            ],
        ];
    }
}
