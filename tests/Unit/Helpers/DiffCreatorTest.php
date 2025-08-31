<?php

/**
 * @file DiffCreatorTest.php
 * tests: /src/Helpers/sunhill_helpers.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

/**
 * Because we use a SimpleTestCase the boot process is not performed
 */
require_once dirname(__FILE__).'/../../../src/Helpers/sunhill_helpers.php';

test('get_diff() works', function ($given, $new, $expected) {
    expect(get_diff(makeStdClass($given), makeStdClass($new), true, true))->toEqual(makeStdClass($expected));
})->with([
    'simple both same' => [
        ['key' => 'value'],
        ['key' => 'value'],
        ['given' => [], 'new' => []],
    ],
    'simple entry dropped' => [
        ['key' => 'value'],
        [],
        ['given' => ['key' => 'value'], 'new' => []],
    ],
    'simple entry added' => [
        [],
        ['key' => 'value'],
        ['given' => [], 'new' => ['key' => 'value']],
    ],
    'simple entry additional added' => [
        ['key' => 'value'],
        ['key' => 'value', 'newkey' => 'newvalue'],
        ['given' => [], 'new' => ['newkey' => 'newvalue']],
    ],
    'simple entry value changed' => [
        ['key' => 'oldvalue'],
        ['key' => 'newvalue'],
        ['given' => ['key' => 'oldvalue'], 'new' => ['key' => 'newvalue']],
    ],
    'simple entry value with asterik for given' => [
        ['key' => '*'],
        ['key' => 'newvalue'],
        ['given' => [], 'new' => []],
    ],
    'simple entry dropped value with asterik for given' => [
        ['key' => '*'],
        [],
        ['given' => ['key' => '*'], 'new' => []],
    ],
    'simple entry value with asterik for new' => [
        ['key' => 'oldvalue'],
        ['key' => '*'],
        ['given' => [], 'new' => []],
    ],
    'simple entry add value with asterik for new' => [
        [],
        ['key' => '*'],
        ['given' => [], 'new' => ['key' => '*']],
    ],
    'nested both same' => [
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'value4'],

        ],
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'value4'],

        ],
        ['given' => [], 'new' => []],
    ],
    'nested both same with asterik in element in given' => [
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => '*'],

        ],
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'value4'],

        ],
        ['given' => [], 'new' => []],
    ],
    'nested both same with asterik in element in new' => [
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'value4'],

        ],
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => '*'],

        ],
        ['given' => [], 'new' => []],
    ],
    'nested both same with asterik in tree in given' => [
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => '*',

        ],
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'value4'],

        ],
        ['given' => [], 'new' => []],
    ],
    'nested both same with asterik in tree in new' => [
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'value4'],

        ],
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => '*',

        ],
        ['given' => [], 'new' => []],
    ],
    'nested tree dropped' => [
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'value4'],

        ],
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
        ],
        ['given' => ['key2' => ['subkey3' => 'value3', 'subkey4' => 'value4']], 'new' => []],
    ],
    'nested tree added' => [
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],

        ],
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'value4'],
        ],
        ['given' => [], 'new' => ['key2' => ['subkey3' => 'value3', 'subkey4' => 'value4']]],
    ],
    'nested entry dropped' => [
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'value4'],

        ],
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3'],

        ],
        ['given' => ['key2' => ['subkey4' => 'value4']], 'new' => ['key2' => []]],
    ],
    'nested entry added' => [
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3'],

        ],
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'value4'],

        ],
        ['given' => ['key2' => []], 'new' => ['key2' => ['subkey4' => 'value4']]],
    ],
    'nested entry changed' => [
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'value4'],

        ],
        [
            'key1' => ['subkey1' => 'value1', 'subkey2' => 'value2'],
            'key2' => ['subkey3' => 'value3', 'subkey4' => 'newvalue4'],

        ],
        ['given' => ['key2' => ['subkey4' => 'value4']], 'new' => ['key2' => ['subkey4' => 'newvalue4']]],
    ],

    'super nested both same' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        ['given' => [], 'new' => []],
    ],
    'super nested both same with asterik in element in given' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => '*'], 'subkey4' => 'value6'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],

        ],
        ['given' => [], 'new' => []],
    ],
    'super nested both same with asterik in element in new' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => '*'], 'subkey4' => 'value6'],
        ],
        ['given' => [], 'new' => []],
    ],
    'super nested both same with asterik in tree in given' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => '*',
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        ['given' => [], 'new' => []],
    ],
    'super nested both same with asterik in tree in new' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => '*',
        ],
        ['given' => [], 'new' => []],
    ],
    'super nested both same with asterik in sub tree in given' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => '*', 'subkey4' => 'value6'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        ['given' => [], 'new' => []],
    ],
    'super nested both same with asterik in sub tree in new' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => '*', 'subkey4' => 'value6'],
        ],
        ['given' => [], 'new' => []],
    ],
    'super nested tree dropped' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
        ],
        ['given' => ['key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6']], 'new' => []],
    ],
    'super nested tree added' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        ['given' => [], 'new' => ['key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6']]],
    ],
    'super nested sub tree dropped' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey4' => 'value6'],
        ],
        ['given' => ['key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4']]], 'new' => ['key2' => []]],
    ],
    'super nested sub tree added' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey4' => 'value6'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        ['given' => ['key2' => []], 'new' => ['key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4']]]],
    ],
    'super nested sub tree entry dropped' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        ['given' => ['key2' => ['subkey3' => []]], 'new' => ['key2' => ['subkey3' => ['subsub3' => 'value3']]]],
    ],
    'super nested sub tree entry added' => [
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub3' => 'value3', 'subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        [
            'key1' => ['subkey1' => ['subsub1' => 'value1', 'subsub2' => 'value2'], 'subkey2' => 'value5'],
            'key2' => ['subkey3' => ['subsub4' => 'value4'], 'subkey4' => 'value6'],
        ],
        ['new' => ['key2' => ['subkey3' => []]], 'given' => ['key2' => ['subkey3' => ['subsub3' => 'value3']]]],
    ],

]);
