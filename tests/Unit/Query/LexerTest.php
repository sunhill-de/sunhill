<?php

uses(SunhillTestCase::class);

test('Test EOL', function() 
{
    $test = new Lexer('');
    expect($test->getNextToken())->tBe(null);
});

test('Test special chars',function($input, $token)
{
    $test = new Lexer($input);
    expect($test->getNextToken())->toBe($token);
})->with([
  'identifier'=>['abc def','abc'],  
  'identifier with numbers and underscore'=>['abc_d3 def','abc_d3'],  
  'identifier starting with underscore'=>['_abc def','_abc'],  
  'integer'=>['123 def','123'],  
  'float'=>['1.23 def','1.23'],  
  '('=>['(def','('],  
  '('=>[')def',')'],  
  '='=>['=def','='],  
)];

test('Test move pointer', function() 
{
    $test = new Lexer('abc def');
    $test->getNextToken();
    expect($test->getNextToken())->toBe('def');
});

test('Test move pointer and skip multiple whitespaces', function() 
{
    $test = new Lexer('abc    def');
    $test->getNextToken();
    expect($test->getNextToken())->toBe('def');
});

