<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Token;

uses(SunhillTestCase::class);

test('Simple token', function()
{
   $test = new Token('+');
   
   expect($test->getSymbol())->toBe('+');
});

test('Position', function()
{
    $test = new Token('+');
    $test->setPosition(10, 15);
    
    expect($test->getLine())->toBe(10);
    expect($test->getColumn())->toBe(15);
});

test('Value', function()
{
   $test = new Token('+');
   $test->setValue(10);
   
   expect($test->getValue())->toBe(10);
});