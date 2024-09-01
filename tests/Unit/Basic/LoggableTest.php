<?php

namespace Sunhill\Tests\Unit\Basic;

use Sunhill\Basic\Loggable;

test('LogLevel works as expected', function()
{
    $test = new Loggable();
    expect($test->setLoglevel(2)->getLoglevel())->toBe(2);
});

test('DisplayLevel works as expected', function()
{
    $test = new Loggable();
    expect($test->setDisplaylevel(2)->getDisplaylevel())->toBe(2);
});

