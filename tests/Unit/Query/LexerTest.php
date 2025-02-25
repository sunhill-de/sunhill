<?php

uses(SunhillTestCase::class);

test('Test EOL', function() 
{
    $test = new Lexer('');
    expect($test->getNextToken())->tBe(null);
});

test('Test special chars',function($input, $token, $value = null)
{
    $test = new Lexer($input);
    $token = $test->getNextToken();
    expect($token->type)->toBe($token);
    if ($value) {
        expect($token->vaue)->toBe($value);
    }            
})->with([
  'identifier'=>['abc def','ident','abc'],  
  'identifier with numbers and underscore'=>['abc_d3 def','ident','abc_d3'],  
  'identifier starting with underscore'=>['_abc def','ident','_abc'],  
  'integer'=>['123 def','int','123'],  
  'float'=>['1.23 def','float','1.23'],  
  '('=>['(def','('],  
  ')'=>[')def',')'],  
  '='=>['=def','='],  
  '-'=>['-def','-'],  
  '+'=>['+def','+'],  
  '*'=>['*def','*'],  
  '/'=>['/def','/'],  
  '=='=>['==def','=='],  
  '<='=>['<=def','<='],  
  '>='=>['>=def','>='],  
  '<'=>['<def','<'],  
  '>'=>['>def','>'],  
  '->'=>['->def','->'],  
  '<=>'=>['<=>def','<=>'],  
  ','=>[',def',','],  
  '['=>['[def','['],  
  ']'=>[']def',']'],  
  '>>'=>['>>def','>>'],  
  '<<'=>['<<def','<<'],         
  '"abc"'=>['"abc" def','string','"abc"'],  
  "'abc'"=>["'abc' def",'string','"abc"'],  
  '"abc\"def"'=>['"abc\"def" def','string','abc "def'],  
  "'abc\'def'"=>["'abc\'def' def",'string',"abc 'def"],  
  "."=>[".def",'.'],  
  '2025-02-25'=>['2025-02-25 def','date','2025-02-25'],  
  '2025-02-25 02:02:22'=>['2025-02-25 02:02:22 def','datetime','2025-02-25 02:02:22'],  
  '02:02:22'=>['02:02:22 def','time','02:02:22'],
  'null'=>["null def","null"],
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

