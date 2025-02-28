<?php

namespace Sunhill\Query;

use Sunhill\Query\Exceptions\InvalidTokenException;
use Sunhill\Parser\Lexer;

class QueryLexer extends Lexer
{
    protected $default_terminals = ['INT','FLOAT','DATETIME','TIME','DATE','IDENTIFIER','STRING'];
    
    protected $terminals = [
      'interval'=>'interval',  
      'between'=>'between',
      'regexp'=>'regexp',
      'collate'=>'collate',    
      'binary'=>'binary',
      'null'=>'null',
      'like'=>'like',
      'div'=>'div',
      'mod'=>'%',
      'and'=>'&&',
      'not'=>'!',
      'xor'=>'xor',
      '<=>'=>'<=>',
      'as'=>'as',
      'in'=>'in',
      'or'=>'||',
      '&&'=>'&&',
      '||'=>'||', 
      '=='=>'=',
      '<='=>'<=',
      '>='=>'>=',
      '->'=>'->',
      '>>'=>'>>',
      '<<'=>'<<',
      '!='=>'!=',
      '<>'=>'!=',
      '('=>'(',
      ')'=>')',
      '='=>'=',
      '-'=>'-',
      '+'=>'+',
      '*'=>'*',
      '/'=>'/',
      '<'=>'<',
      '>'=>'>',
      '['=>'[',
      ']'=>']',
      ','=>',',
      '%'=>'%',
      '&'=>'&',
      '|'=>'|',
      '^'=>'^',
      '~'=>'~',
      '.'=>'.'
  ];
  
}
