<?php

/**
 * @tests /src/Helpers/sunhill_helpers.php
 * 
 */
test('makeStdClass() works as expected', function()
{
   $result = makeStdclass(['keyA'=>'valueA','keyB'=>'valueB']);
   expect($result->keyA)->toBe('valueA');
   expect($result->keyB)->toBe('valueB');   
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