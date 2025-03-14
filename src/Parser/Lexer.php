<?php
/**
 * @file Lexer.php
 * A basic class for a simple lexer. 
 * Lang en
 * Reviewstatus: 2025-02-28
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/LexerTest.php
 * Coverage: 90,6% (2025-03-11<9
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;
use Sunhill\Parser\Exceptions\StringNotClosedException;
use Sunhill\Parser\Exceptions\InvalidTokenException;
use Sunhill\Parser\Exceptions\UnknownDefaultTerminalException;
use PhpParser\Lexer;
use PhpParser\Token;

/**
 * The lexer class for the sunhill parser subsystem.
 * @author klaus
 *
 */
class Lexer extends Base
{
    
  
    const TERMINAL_PRIORITY = [
       'DATETIME','DATE','TIME','FLOAT','INTEGER','STRING','BOOLEAN','IDENTIFIER' 
    ];
    
 /*
  * The string that this lexer should separate into tokens
  */
  protected string $parse_string = '';

  /*
   * The current overall position in the parse string (counting whitespaces and linebreaks) 
   */
  protected int $position = 0;
  
  /*
   * The current column (respecting linebreaks of the parse string) 
   */
  protected int $column = 0;
  
  /**
   * The current row (respecting linebreaks of the parse string
   * @var integer
   */
  protected int $row = 0;
  
  protected $default_terminals = [];
  
  protected $terminals = [];
  
  /**
   * Indicator, if the lexer was initialized when getting the first token
   *  
   * @var boolean
   */
  private bool $initialized = false;
  
  private $longest_terminal = 0;
  
  public function addDefaultTerminal(string $terminal): static
  {
      switch (strtolower($terminal)) {
          case 'integer':
          case 'int':    
              $this->default_terminals[] = 'INTEGER';
              break;
          case 'float':
              $this->default_terminals[] = 'FLOAT';
              break;
          case 'string':
              $this->default_terminals[] = 'STRING';
              break;
          case 'bool':
          case 'boolean':
              $this->default_terminals[] = 'BOOLEAN';
              break;
          case 'date':
              $this->default_terminals[] = 'DATE';
              break;
          case 'time':
              $this->default_terminals[] = 'TIME';
              break;
          case 'datetime':
              $this->default_terminals[] = 'DATETIME';
              break;
          case 'identifier':
              $this->default_terminals[] = 'IDENTIFIER';
              break;
          default:
              throw new UnknownDefaultTerminalException("The default terminal '$terminal' is not known"); 
              
      }
      return $this;      
  }
  
  public function addTerminal(string $terminal, ?string $alias = null): static
  {
      if (is_null($alias)) {
          $alias = $terminal;
      }
      $this->terminals[$terminal] = $alias;
      $this->initialized = false;
      return $this;    
  }
  
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
      $this->initialized = true;
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
   * Helper function that returns the parse_string from the current position till the end
   * 
   * @return string
   */
  function getRemainingParseString(): string
  {
      return substr($this->parse_string,$this->position);
  }
  
  /**
   * Creates a token and moves the pointer
   * 
   * @param string $token_str
   * @param string $symbol
   * @param unknown $value
   * @return Token
   */
  function consumeToken(string $token_str, string $symbol, $value = null): Token
  {
      $current_position = $this->column;
      $this->movePointer(strlen($token_str));
      return $this->createToken($symbol, $this->row, $current_position, $value);
  }

  /**
   * Tries to detect a boolean value
   * 
   * @return Token|NULL
   */ 
  function getBoolean(): ?Token
  {
      if (strtolower(substr($this->getRemainingParseString(),4)) == 'true') {
          return $this->consumeToken('true','boolean',true);
      }
      if (strtolower(substr($this->getRemainingParseString(),5)) == 'false') {
          return $this->consumeToken('false','boolean',false);
      }
  }
  
  /**
   * Tries to detect a float value
   * 
   * @return Token|NULL
   */
  function getFloat(): ?Token
  {
      if (preg_match('/^\d+\.\d+/', $this->getRemainingParseString(), $matches)) {
          return $this->consumeToken($matches[0], 'float', $matches[0]);
      }
      
      return null;
  }
  
  /**
   * Tries to detect a integer value
   * 
   * @return Token|NULL
   */
  function getInteger(): ?Token
  {
      if (preg_match('/^\d+/', $this->getRemainingParseString(), $matches)) {
          return $this->consumeToken($matches[0], 'integer', $matches[0]);
      }      
      
      return null;
  }

  /**
   * Tries to detect a datetime constant
   * 
   * @return Token|NULL
   */
  function getDatetime(): ?Token
  {
      if (preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[01]) (0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/", $this->getRemainingParseString(), $matches)) {
          return $this->consumeToken($matches[0], 'datetime', $matches[0]);
      }
      
      return null;
  }
  
  /**
   * Tries to detect a date constant
   * 
   * @Note that at least a date constant could
   * also be a mathematical expression like 2024 - 10 - 2. If this function is called, strings matching a date
   * have a higher priority than mathematical expressions. 
   *
   * @return Token|NULL
   */
  function getDate(): ?Token
  {
      if (preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|1[0-9]|2[0-9]|3[01])/", $this->getRemainingParseString(), $matches)) {
          return $this->consumeToken($matches[0], 'date', $matches[0]);
      }      
      
      return null;
  }
  
  /**
   * Tries to detect a time constant
   * 
   * @return Token|NULL
   */
  function getTime(): ?Token
  {
      if (preg_match("/^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])/", $this->getRemainingParseString(), $matches)) {
          return $this->consumeToken($matches[0], 'time', $matches[0]);
      }      
      
      return null;
  }

  /**
   * If the next symbol begins with a " or a ' assume it a string constant. If the string is not closed,
   * raise an expcetion. Respect escape signs.
   *
   * @return \stdClass|NULL
   */
  private function getString(): ?Token
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
                  throw new StringNotClosedException("The string is not closed");
              }
          }
          $result .= $this->parse_string[$this->position];
          $this->movePointer(1);
      }
      if ($this->position >= strlen($this->parse_string)) {
          throw new StringNotClosedException("The string is not closed");
      }
      $this->movePointer(1);
      return $this->createToken('string', $this->row, $current_position, $result);
  }
  
  /**
   * If the next symbols define a identifier, create a identifier token
   *
   * @return Token|NULL
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
   * Ignores whitespaces but respects them when counting columns and rows
   */
  private function skipWhitespace()
  {
      while ($this->position < strlen($this->parse_string) && (ctype_space($this->parse_string[$this->position]))) {
          if ($this->parse_string[$this->position] == "\n") {
              $this->column = 0;
              $this->position++;
              $this->row++;
          } else {
              $this->movePointer(1);
          }
      }      
  }
  
  /**
   * Checks if this is the first call of getNextToken() after setting the terminals
   */
  private function checkInitialization()
  {
      if (!$this->initialized) {
          $this->sortTerminals();
      }      
  }
  
  public function getNextToken(): ?Token
  {
      $this->checkInitialization();
      $this->skipWhitespace();
      // EOL?
      if ($this->position >= strlen($this->parse_string)) {
        return null; // EOL
      }
      if (!empty($this->terminals) && ($symbol = $this->getSymbol())) {
          return $symbol;
      }
      foreach (static::TERMINAL_PRIORITY as $terminal) {
          if (in_array($terminal, $this->default_terminals)) {
              $method = 'get'.ucfirst(strtolower($terminal));
              if ($identifier = $this->$method()) {
                  return $identifier;
              }
          }
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

function getArithmeticLexer(string $parse_string): Lexer
{
    $result = new Lexer($parse_string);
    $result->addDefaultTerminal('INTEGER');
    $result->addDefaultTerminal('FLOAT');
    $result->addDefaultTerminal('IDENTIFIER');
    $result->addTerminal('+');
    $result->addTerminal('-');
    $result->addTerminal('/');
    $result->addTerminal('*');
    $result->addTerminal('(');
    $result->addTerminal(')');
    
    return $result;
}

function getLogicalLexer(string $parse_string): Lexer
{
    $result = getArithmeticLexer($parse_string);
    $result->addDefaultTerminal('BOOLEAN');
    $result->addTerminal('&&');
    $result->addTerminal('||');
    $result->addTerminal('==');
    $result->addTerminal('!');
    $result->addTerminal('=','==');
    $result->addTerminal('and','&&');
    $result->addTerminal('or','||');
    $result->addTerminal('not','!');
    
    return $result;
    
} 