<?php
/**
 * @file LanguageDescriptor.php
 * A helper class that makes it easier to define a language that can be interpreted by the
 * lexer, parser and analyzer
 * Lang en
 * Reviewstatus: 2025-03-19
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/LanguageDescriptorTest.php
 * Coverage:
 */

namespace Sunhill\Parser\LanguageDescriptor;

use Sunhill\Basic\Base;

class LanguageDescriptor extends Base
{
    
    protected array $default_terminals = [];
    
    protected array $operators = [];
    
    protected array $other_terminals = [];
    
    public function addDefaultTerminal(string $default_terminal): static
    {
        $this->default_terminals[] = $default_terminal;
        
        return $this;
    }
    
    public function addOperator(string $operator): OperatorDescriptor
    {
        $descriptor = new OperatorDescriptor($operator);
        $this->operators[$operator] = $descriptor;
        
        return $descriptor;
    }
    
    public function addTerminal(string $terminal, ?string $alias_for = null): static
    {
        if (is_null($alias_for)) {
            $this->other_terminals[$terminal] = $terminal;
        } else {
            $this->other_terminals[$terminal] = $alias_for;            
        }
        
        return $this;
    }
    
    public function getDefaultTerminals(): array
    {
        return $this->default_terminals;
    }
    
    public function getTerminals(): array
    {
        $result = $this->other_terminals;
        foreach ($this->operators as $operator => $descriptor) {
            $result[$operator] = $operator;
        }
        return $result;
    }
}