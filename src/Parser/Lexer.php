<?php
/**
 * @file Lexer.php
 * A basic class for a simple lexer. 
 * Lang en
 * Reviewstatus: 2025-02-28
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/AnalyzerTest.php
 * Coverage:
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;
use Sunhill\Query\Exceptions\InvalidTokenException;

class Lexer extends Base
{
  protected $parse_string = '';

  protected $position = 0;
  
  protected $column = 0;
  
  protected $row = 0;
  
  protected $default_terminals = [];
  
  protected $terminals = [];
  
  private $longest_terminal = 0;
  
  /**
   * The constructor is passed the current parsing string. Then the terminals are sorted by length. And the 
   * longest terminal is detected.
   *  
   * @param string $parse_string
   */
  public function __construct(string $parse_string)
  {
      $this->parse_string = $parse_string;
      $this->column = 0;
      $this->row = 0;
      $this->position = 0;
      $this->sortTerminals();
  }

  /**
   * Sorts the terminals by their length (terminals have to be strings)
   */
  private function sortTerminals()
  {
      uksort($this->terminals, function($a, $b)
      {
          if (strlen($a) == strlen($b)) {
              return 0;
          }
          return (strlen($a) > strlen($b)) ? -1 : 1;
      });

      // Detect the longest entry in the terminal table (if there is at least one)
      if (!empty($this->terminals)) {
          $this->longest_terminal = strlen(array_keys($this->terminals)[0]);
      }
  }
  
  /**
   * Returns the next $count characters of the parse_string without touching the pointer
   * 
   * @param int $count
   * @return string
   */
  private function getNextCharacters(int $count): string
  {
      return $this->getNextCharactersFrom($this->position, $count);
  }

  /**
   * Returns the next $count characters of the parse_string from a given position without touching the pointer
   *
   * @param int $count
   * @return string
   */
  private function getNextCharactersFrom(int $start, int $count): string
  {
      return substr($this->parse_string, $start, $count);
  }
  
  /**
   * If the next characters define an identifier (anything that starts with _ or a letter and 
   * is followed by _, a letter or a digit). if none is found it returns null
   * 
   * @return string|NULL
   */
  private function getNextIdentifier(): ?string
  {
      if (preg_match('/^([a-zA-Z_][_[:alnum:]]*)/',substr($this->parse_string,$this->position),$matches)) {
          return $matches[1];
      } 
      return null;
  }
  
  private function createToken(string $symbol, int $row, int $column, $value = null, string $type_hint = null)
  {
      $result = new Token($symbol);
      $result->setPosition($row, $column);
      if (!is_null($value)) {
          $result->setValue($value);
      }
      if (!is_null($type_hint)) {
          $result->setTypeHint($type_hint);
      }
      return $result;
  }
  
  private function movePointer(int $by_chars)
  {
      $this->position += $by_chars;
      $this->column += $by_chars;
  }
  
  /**
   * Looks up if the next symbols match any terminals in the terminal table. Or returns null if none is found.
   *  
   * @return \stdClass|NULL
   */
  private function getSymbol(): ?Token
  {
      for ($i=$this->longest_terminal;$i>0;$i--) {
          $next = strtolower($this->getNextCharacters($i));
          if (array_key_exists($next,$this->terminals)) {
              $current_position = $this->column;
              $this->movePointer(strlen($next));
              return $this->createToken($this->terminals[$next], $this->row, $current_position);
          }
      }
      return null;
  }

  private function previewSymbol(int $start_position): string
  {
      for ($i=$this->longest_terminal;$i>0;$i--) {
          $next = strtolower($this->getNextCharactersFrom($start_position, $i));
          if (array_key_exists($next,$this->terminals)) {
              return $this->terminals[$next];
          }
      }
      return null;      
  }
  
  /**
   * If the next symbols define a identifier, create a identifier token
   * 
   * @return \stdClass|NULL
   */
  private function getIdentifier(): ?Token
  {
      if ($identifier = $this->getNextIdentifier()) {
          $current_position = $this->column; // Mark the current position
          $this->movePointer(strlen($identifier));
          return $this->createToken('ident', $this->row, $current_position, $identifier);
      }
      return null;
  }
  
  /**
   * If the next symbol begins with a " or a ' assume it a string constant. If the string is not closed,
   * raise an expcetion. Respect escape signs.
   * 
   * @return \stdClass|NULL
   */
  private function getStringConstant(): ?Token
  {
      if ($this->parse_string[$this->position] == '"') {
          $ending_symbol = '"';
      } else if ($this->parse_string[$this->position] == "'") {
          $ending_symbol = "'";
      } else {
          return null;
      }
      $current_position = $this->column;
      
      $this->movePointer(1);
      $result = '';
      while ($this->column < strlen($this->parse_string) && ($this->parse_string[$this->position]) !== $ending_symbol) {
          if ($this->parse_string[$this->column] == '\\') {
              $this->movePointer(1);  
              if ($this->position >= strlen($this->parse_string)) {
                  throw new InvalidTokenException("The string is not closed");
              }
          }
          $result .= $this->parse_string[$this->position];
          $this->movePointer(1);
      }
      if ($this->position >= strlen($this->parse_string)) {
          throw new InvalidTokenException("The string is not closed");
      }
      $this->movePointer(1);
      return $this->createToken('const', $this->row, $current_position, $result, 'string');
  }
  
  /**
   * This function tries to detect a date, time or datetime constant. 
   * 
   * @Note that at least a date constant could
   * also be a mathematical expression like 2024 - 10 - 2. If this function is called, strings matching a date
   * have a higher priority than mathematical expressions. 
   * 
   * @return StdClass|NULL
   */
  function getDateConstant(): ?Token
  {
      $current_position = $this->column;
      if (in_array('DATETIME', $this->default_terminals) && preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[01]) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/",substr($this->parse_string,$this->position),$matches)) {
          $this->movePointer(strlen($matches[0]));
          return $this->createToken('const', $this->row, $current_position, $matches[0], 'datetime');
      }
      if (in_array('DATE', $this->default_terminals) && preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[01])/",substr($this->parse_string,$this->position),$matches)) {
          $this->movePointer(strlen($matches[0]));
          return $this->createToken('const', $this->row, $current_position, $matches[0], 'date');
      }
      if (in_array('TIME', $this->default_terminals) && preg_match("/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/",substr($this->parse_string,$this->position),$matches)) {
          $this->movePointer(strlen($matches[0]));
          return $this->createToken('const', $this->row, $current_position, $matches[0], 'time');
      }
      
      return null;
  }
  
  /**
   * Tries to detect
   *  
   * @return StdClass|NULL
   */
  function getNumericConstant(): ?Token
  {
      if (preg_match('/^-?\d+(\.\d+)?/',substr($this->parse_string,$this->position),$matches)) {
          if (in_array('FLOAT', $this->default_terminals) && strpos($matches[0],'.') !== false) {
              $current_position = $this->column;
              $this->movePointer(strlen($matches[0]));
              return $this->createToken('const', $this->row, $current_position, $matches[0], 'float');
          } else if (in_array('INT',$this->default_terminals)) {
              $current_position = $this->column;
              $this->movePointer(strlen($matches[0]));
              return $this->createToken('const', $this->row, $current_position, $matches[0], 'int');
          }              
      }
      
      return null;
  }
  
  public function getNextToken(): ?Token
  {
    if ($this->position >= strlen($this->parse_string)) {
      return null; // EOL
    }

    // Ignore whitespaces
    while ($this->position < strlen($this->parse_string) && (ctype_space($this->parse_string[$this->position]))) {
        if ($this->parse_string[$this->position] == "\n") {
            $this->column = 0;
            $this->position++;
            $this->row++;
        } else {
            $this->movePointer(1);
        }
    }
    if (!empty($this->terminals) && ($symbol = $this->getSymbol())) {
        return $symbol;
    }
    if (in_array('IDENTIFIER',$this->default_terminals) && ($identifier = $this->getIdentifier())) {
        return $identifier;
    }
    if (in_array('STRING', $this->default_terminals) && ($string = $this->getStringConstant())) {
        return $string;
    }
    if ($string = $this->getDateConstant()) {
        return $string;
    }
    if ($string = $this->getNumericConstant()) {
        return $string;
    }
    throw new InvalidTokenException("Can't process token: ".substr($this->parse_string,$this->column));
  }
 
  public function previewOperator(): ?String
  {
      if ($this->position >= strlen($this->parse_string)) {
          return null; // EOL
      }
      
      $dummy_pointer = $this->position;
      
      // Ignore whitespaces
      while ($dummy_pointer < strlen($this->parse_string) && (ctype_space($this->parse_string[$dummy_pointer]))) {
        $dummy_pointer++;
      }
      if (!empty($this->terminals) && ($symbol = $this->previewSymbol($dummy_pointer))) {
          return $symbol;
      }      
  }
  
  /**
   * Returns the current position of the pointer
   * 
   * @return int
   */
  public function getPointer(): int
  {
    return $this->position;    
  }
  
  public function getColumn(): int
  {
     return $this->column;    
  }
  
  public function getRow(): int
  {
      return $this->row;
  }
  
  /**
   * Creates an array with two rows, the first row is just the parsing string the second a marker to the current 
   * position
   * 
   * @return array
   */
  public function getCurrentPositionMarker(): array
  {
      return [
          $this->parse_string,
          str_repeat(' ', $this->column).'^'
      ];
  }
}
