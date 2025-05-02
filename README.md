# Package with some array and json helper methods

## Installation

You can install the package via composer:

```bash
composer require net4ideas.com/helper-buddy
```

## JsonBuddy

A json helper class to validate json structure. You can validate json structure by providing a list of keys and types.

- Supported types: string, array, object, int, float, decimal, boolean / bool
- Format: "path" => "type:default" (default is optional and only used when type is string, int, float, bool)
- Note: if the final attribute in the path doesn't exist, it will be created

For example

```json:
{
  "name": null,
  "structure": [
    {
      "rows": [
        {
          "columns": [
            {
              "price": null,
              "data": []
            },
            {
              "price": 2,
              "data": []
            }
          ]
        },
        {
          "columns": [
            {
              "price": 1,
              "data": {
                "name": "data 1"
              }
            }
          ]
        }
      ]
    }
  ]
}
```

```php
<?php

use Net4ideas\HelperBuddy\JsonBuddy;

$keys = [
    "name" => "string:''",
    "structure.*.rows.*.columns.*.data" => "object",
    "structure.0.rows.0.columns.0.price" => "float:0",
];

$correctedJson = JsonBuddy::validateStructure($json, $keys);
```

will output:

```json
{
  "name": "",
  "structure": [
    {
      "rows": [
        {
          "columns": [
            {
              "price": 0,
              "data": {}
            },
            {
              "price": 2,
              "data": {}
            }
          ]
        },
        {
          "columns": [
            {
              "price": 1,
              "data": {
                "name": "data 1"
              }
            }
          ]
        }
      ]
    }
  ]
}
```

## ArrayBuddy

A array helper class to remove null values recursively.

```php
<?php

use Net4ideas\HelperBuddy\ArrayBuddy;

$input = [
    "name" => "Jack",
    "age" => 40,
    "address" => null,
    "hobbies" => [
        "reading", 
        "writing", 
        null
    ]
];

$result = ArrayBuddy::removeNullValues($input);
/* Output:
[
    "name" => "Jack", 
    "age" => 40, 
    "hobbies" => [
        "reading", 
        "writing"
    ]
]
*/
```

Another method to replace null values with a string recursively.

```php
<?php

use Net4ideas\HelperBuddy\ArrayBuddy;

$input = [
    "name" => "Jack",
    "age" => 40,
    "address" => null,
    "hobbies" => [
        "reading", 
        "writing", 
        null
    ]
];

$result = ArrayBuddy::replaceNullsWithString($input, 'default');
/* Output:
[
    "name" => "Jack",
    "age" => 40,
    "address" => "default",
    "hobbies" => [
        "reading", 
        "writing", 
        "default"
    ]
]
*/
```