<?php

/**
 * @tests /src/Helpers/sunhill_helpers.php
 * 
 */
test('makeStdClass() works as expected (simple)', function()
{
   $result = makeStdclass(['keyA'=>'valueA','keyB'=>'valueB']);
   expect($result->keyA)->toBe('valueA');
   expect($result->keyB)->toBe('valueB');   
});

test('makeStdClass() works as expected (nested)', function()
{
    $result = makeStdclass(['keyA'=>'valueA','keyB'=>['subkeyA'=>'subvalueA','subkeyB'=>'subvalueB']]);
    expect($result->keyA)->toBe('valueA');
    expect($result->keyB->subkeyA)->toBe('subvalueA');
    expect($result->keyB->subkeyB)->toBe('subvalueB');
});

test('getScalarMessage works with scalar', function()
{
    $variable = 'scalar';
    expect(getScalarMessage("This is :variable or not", $variable))->toBe("This is 'scalar' or not");
});

test('getScalarMessage works with non-scalar', function()
{
    $variable = new \stdClass();
    expect(getScalarMessage("This is :variable or not", $variable))->toBe("This is  or not");    
});

test('getScalarMessage works with non-scalar and replacement', function()
{
    $variable = new \stdClass();
    expect(getScalarMessage("This is :variable or not", $variable,"non-scalar"))->toBe("This is non-scalar or not");
});

test('get_diff() works', function($given, $new, $expected)
{
    expect(get_diff(makeStdClass($given), makeStdClass($new),true,true))->toEqual(makeStdClass($expected));
})->with([
    'simple both same'=>[
        ['key'=>'value'],
        ['key'=>'value'],
        ['given'=>[],'new'=>[]]
    ],
    'simple entry dropped'=>[
        ['key'=>'value'],
        [],
        ['given'=>['key'=>'value'],'new'=>[]]
    ],
    'simple entry added'=>[
        [],
        ['key'=>'value'],
        ['given'=>[],'new'=>['key'=>'value']]
    ],
    'simple entry additional added'=>[
        ['key'=>'value'],
        ['key'=>'value','newkey'=>'newvalue'],
        ['given'=>[],'new'=>['newkey'=>'newvalue']]
    ],
    'simple entry value changed'=>[
        ['key'=>'oldvalue'],
        ['key'=>'newvalue'],
        ['given'=>['key'=>'oldvalue'],'new'=>['key'=>'newvalue']]
    ],
    'simple entry value with asterik for given'=>[
        ['key'=>'*'],
        ['key'=>'newvalue'],
        ['given'=>[],'new'=>[]]
    ],
    'simple entry dropped value with asterik for given'=>[
        ['key'=>'*'],
        [],
        ['given'=>['key'=>'*'],'new'=>[]]
    ],
    'simple entry value with asterik for new'=>[
        ['key'=>'oldvalue'],
        ['key'=>'*'],
        ['given'=>[],'new'=>[]]
    ],
    'simple entry add value with asterik for new'=>[
        [],
        ['key'=>'*'],
        ['given'=>[],'new'=>['key'=>'*']]
    ],
    'nested both same'=>[
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],
            'key2'=>['subkey3'=>'value3','subkey4'=>'value4'],
            
        ],
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],
            'key2'=>['subkey3'=>'value3','subkey4'=>'value4'],
            
        ],
        ['given'=>[],'new'=>[]]
    ],
    'nested tree dropped'=>[
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],
            'key2'=>['subkey3'=>'value3','subkey4'=>'value4'],
            
        ],
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],            
        ],
        ['given'=>['key2'=>['subkey3'=>'value3','subkey4'=>'value4']],'new'=>[]]        
    ],
    'nested tree added'=>[
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],
            
        ],
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],
            'key2'=>['subkey3'=>'value3','subkey4'=>'value4'],            
        ],
        ['given'=>[],'new'=>['key2'=>['subkey3'=>'value3','subkey4'=>'value4']]]
    ], 
    'nested entry dropped'=>[
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],
            'key2'=>['subkey3'=>'value3','subkey4'=>'value4'],
            
        ],
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],
            'key2'=>['subkey3'=>'value3'],
            
        ],
        ['given'=>['key2'=>['subkey4'=>'value4']],'new'=>[]]
    ],
    'nested entry added'=>[
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],
            'key2'=>['subkey3'=>'value3'],
            
        ],
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],
            'key2'=>['subkey3'=>'value3','subkey4'=>'value4'],
            
        ],
        ['given'=>[],'new'=>['key2'=>['subkey4'=>'value4']]]
    ],
    'nested entry changed'=>[
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],
            'key2'=>['subkey3'=>'value3','subkey4'=>'value4'],
            
        ],
        [
            'key1'=>['subkey1'=>'value1','subkey2'=>'value2'],
            'key2'=>['subkey3'=>'value3','subkey4'=>'newvalue4'],
            
        ],
        ['given'=>['key2'=>['subkey4'=>'value4']],'new'=>['key2'=>['subkey4'=>'newvalue4']]]
    ],
    
]);