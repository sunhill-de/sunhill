<?php

uses(SunhillTestCase::class);

test('Test EOL', function() 
{
    $test = new Lexer('');
    expect($test->getNextToken())->tBe(null);
});

test('Test identifier (only characters)', function() 
{
    $test = new Lexer('abc def');
    expect($test->getNextToken())->tBe('abc');
});
