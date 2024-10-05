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