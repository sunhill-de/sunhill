<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Lexer;
use Sunhill\Query\Exceptions\InvalidTokenException;
use Sunhill\Query\QueryLexer;

uses(SunhillTestCase::class);

test('Test EOL', function() 
{
    $test = new QueryLexer('');
    expect($test->getNextToken())->toBe(null);
});

test('Test special chars',function($input, $token, $value = null, $type = null)
{
    $test = new QueryLexer($input);
    $result = $test->getNextToken();
    expect($result->type)->toBe($token);
    if ($value) {
        expect($result->value)->toBe($value);
    }            
    if ($type) {
        expect($result->field_type)->toBe($type);
    }
})->with([
  'null'=>["null def","null"],
  'as'=>["as def","as"],
  'identifier'=>['abc def','ident','abc'],  
  'identifier with numbers and underscore'=>['abc_d3 def','ident','abc_d3'],  
  'identifier starting with underscore'=>['_abc def','ident','_abc'],  
  'integer'=>['123 def','const','123','int'],  
  'float'=>['1.23 def','const','1.23','float'],  
  '('=>['(def','('],  
  ')'=>[')def',')'],  
  '='=>['=def','='],  
  '-'=>['-def','-'],  
  '+'=>['+def','+'],  
  '*'=>['*def','*'],  
  '/'=>['/def','/'],  
  '%'=>['%def','%'],  
  '&'=>['&def','&'],  
  '|'=>['|def','|'],  
  '~'=>['~def','~'],  
  '^'=>['^def','^'],  
  '=='=>['==def','='],  
  '<='=>['<=def','<='],  
  '>='=>['>=def','>='],  
  '<'=>['<def','<'],  
  '>'=>['>def','>'],  
  '->'=>['->def','->'],
  ','=>[',def',','],  
  '['=>['[def','['],  
  ']'=>[']def',']'],  
  '>>'=>['>>def','>>'],  
  '<<'=>['<<def','<<'],         
  '"abc"'=>['"abc" def','const','abc','str'],  
  "'abc'"=>["'abc' def",'const','abc','str'],  
  '"abc\"def"'=>['"abc\"def" def','const','abc"def','str'],  
  "'abc\'def'"=>["'abc\'def' def",'const',"abc'def",'str'],  
  "."=>[".def",'.'],  
  '2025-02-25'=>['2025-02-25 def','const','2025-02-25','date'],  
  '2025-02-25 02:02:22'=>['2025-02-25 02:02:22 def','const','2025-02-25 02:02:22','datetime'],  
  '02:02:22'=>['02:02:22 def','const','02:02:22','time'],
]);

test('Test spaceship', function()
{
    $test = new QueryLexer('<=> def');
    expect($test->getNextToken()->type)->toBe('<=>');
});

