<?php
/**
 * @file Token.php
 * A basic class for a token returned by the parser and processed by the parser
 * Lang en
 * Reviewstatus: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/TokenTest.php
 * Coverage:
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;
use phpDocumentor\Reflection\Types\Mixed_;

class Token extends Base
{
    
    protected $symbol = '';
    
    protected $line = 0;
    
    protected $column = 0;
    
    protected $value;
    
    protected $type_hint = 'unknown';
    
    /**
     * Constructor. Takes the symbol (because every token needs at least a symbol)
     * 
     * @param string $symbol
     */
    public function __construct(string $symbol)
    {
        $this->symbol = $symbol;    
    }
    
    public function getSymbol(): string
    {
        return $this->symbol;   
    }
    
    public function setValue(mixed $value): static
    {
        $this->value = $value;
        
        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;    
    }
    
    public function setPosition(int $line, int $column): static
    {
        $this->line = $line;
        $this->column = $column;
        
        return $this;
    }
    
    public function getLine(): int
    {
        return $this->line;
    }
    
    public function getColumn(): int
    {
        return $this->column;
    }
    

    public function setTypeHint(string $type_hint): static
    {
        $this->type_hint = $type_hint;
        return $this;
    }
    
    public function getTypeHint(): string
    {
        return $this->type_hint;
    }
}