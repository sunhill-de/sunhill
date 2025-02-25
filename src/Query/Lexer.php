<?php

namespace Sunhill\Query;

class Lexer extends Base
{
  protected $parse_string = '';

  protected $pointer = 0;
  
  public function __construct(string $parse_string)
  {
      $this->parse_string = $parse_string;
      $this->pointer = 0;
  }

  private function getNextCharacters(int $count): string
  {
      return substr($this->parse_string, $pointer, $count);
  }

  private function getNextIdentifier(): ?string
  {
      if (preg_match('/^([a-zA-Z_][_[:alnum:]]*)',substr($this->parse_str,$this->pointer),$matches) {
          return $matches[1];
      }    
  }
  
  public function getNextToken(): ?\stdClass
  {
    if ($this->pointer >= strlen($this->parse_string)) {
      return null; // EOL
    }
    $next = $this->getNextCharacters(3);
    if (in_array($next,['<=>'])) {
      return makeStdClass(['type'=>$next]);
    }
    $next = $this->getNextCharacters(2);
    if (in_array($next,['==','<=','>=','->','>>','<<'])) {
      return makeStdClass(['type'=>$next]);      
    }
    $next = $this->getNextCharacters(1);
    if (in_array($next,['(',')','=','-','+','*','/','<','>','[',']',','])) {
      return makeStdClass(['type'=>$next]);      
    }    
  }
 
}
