<?php

namespace Sunhill\Query;

use Sunhill\Basic\Base;
use Sunhill\Query\Exceptions\InvalidTokenException;

class Lexer extends Base
{
  protected $parse_string = '';

  protected $pointer = 0;
  
  const MAX_SYMBOL_LEN = 8;
  
  const SYMBOLS = [
      'interval'=>'INTERVAL',  
      'between'=>'BETWEEN',
      'regexp'=>'REGEXP',
      'collate'=>'COLLATE',    
      'binary'=>'BINARY',
      'null'=>'NULL',
      'like'=>'LIKE',
      'div'=>'DIV',
      'mod'=>'%',
      'and'=>'&&',
      'not'=>'!',
      'xor'=>'XOR',
      '<=>'=>'<=>',
      'as'=>'AS',
      'in'=>'IN',
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
      '<>'=>'<>',
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
  
  public function __construct(string $parse_string)
  {
      $this->parse_string = $parse_string;
      $this->pointer = 0;
  }

  private function getNextCharacters(int $count): string
  {
      return substr($this->parse_string, $this->pointer, $count);
  }

  private function getNextIdentifier(): ?string
  {
      if (preg_match('/^([a-zA-Z_][_[:alnum:]]*)/',substr($this->parse_string,$this->pointer),$matches)) {
          return $matches[1];
      } 
      return null;
  }
  
  private function getSymbol(): ?\stdClass
  {
      for ($i=Lexer::MAX_SYMBOL_LEN;$i>0;$i--) {
          $next = strtolower($this->getNextCharacters($i));
          if (array_key_exists($next,Lexer::SYMBOLS)) {
              $this->pointer += $i;
              return makeStdClass(['type'=>Lexer::SYMBOLS[$next]]);
          }
      }
      return null;
  }
  
  private function getIdentifier(): ?\stdClass
  {
      if ($identifier = $this->getNextIdentifier()) {
          $this->pointer += strlen($identifier);
          return makeStdClass(['type'=>'ident','value'=>$identifier]);
      }
      return null;
  }
  
  private function getStringConstant(): ?\stdClass
  {
      if ($this->parse_string[$this->pointer] == '"') {
          $ending_symbol = '"';
      } else if ($this->parse_string[$this->pointer] == "'") {
          $ending_symbol = "'";
      } else {
          return null;
      }
      $this->pointer++;
      $result = '';
      while ($this->pointer < strlen($this->parse_string) && ($this->parse_string[$this->pointer]) !== $ending_symbol) {
          if ($this->parse_string[$this->pointer] == '\\') {
              $this->pointer++;
              if ($this->pointer >= strlen($this->parse_string)) {
                  throw new InvalidTokenException("The string is not closed");
              }
          }
          $result .= $this->parse_string[$this->pointer++];
      }
      if ($this->pointer >= strlen($this->parse_string)) {
          throw new InvalidTokenException("The string is not closed");
      }
      return makeStdClass(['type'=>'const','field_type'=>'str','value'=>$result]);
  }
  
  function getDateConstant()
  {
      if (preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[01]) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/",substr($this->parse_string,$this->pointer),$matches)) {
          return makeStdClass(['type'=>'const','field_type'=>'datetime','value'=>$matches[0]]);
      }
      if (preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[01])/",substr($this->parse_string,$this->pointer),$matches)) {
          return makeStdClass(['type'=>'const','field_type'=>'date','value'=>$matches[0]]);
      }
      if (preg_match("/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/",substr($this->parse_string,$this->pointer),$matches)) {
          return makeStdClass(['type'=>'const','field_type'=>'time','value'=>$matches[0]]);
      }
      return null;
  }
  
  function getNumericConstant()
  {
      if (preg_match('/^-?\d+(\.\d+)?/',substr($this->parse_string,$this->pointer),$matches)) {
          if (strpos($matches[0],'.') !== false) {
              return makeStdClass(['type'=>'const','field_type'=>'float','value'=>$matches[0]]);
          } else {
              return makeStdClass(['type'=>'const','field_type'=>'int','value'=>$matches[0]]);              
          }              
      }
  }
  
  public function getNextToken(): ?\stdClass
  {
    if ($this->pointer >= strlen($this->parse_string)) {
      return null; // EOL
    }
    while ($this->pointer < strlen($this->parse_string) && ($this->parse_string[$this->pointer] == ' ')) {
        $this->pointer++;
    }
    if ($symbol = $this->getSymbol()) {
        return $symbol;
    }
    if ($identifier = $this->getIdentifier()) {
        return $identifier;
    }
    if ($string = $this->getStringConstant()) {
        return $string;
    }
    if ($string = $this->getDateConstant()) {
        return $string;
    }
    if ($string = $this->getNumericConstant()) {
        return $string;
    }
    throw new InvalidTokenException("Can't process token: ".substr($this->parse_string,$this->pointer));
  }
 
}
