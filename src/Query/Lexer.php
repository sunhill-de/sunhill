<?php

namespace Sunhill\Query;

class Lexer extends Base
{
  protected $parse_string = '';

  protected $pointer = 0;
  
  public function __construct(string $parse_string)
  {
  }

  public function getNextToken(): ?\stdClass
  {
    if ($this->pointer >= strlen($this->parse_string)) {
      return null; // EOL
    }
  }
  
}
