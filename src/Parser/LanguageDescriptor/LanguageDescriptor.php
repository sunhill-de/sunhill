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
use Sunhill\Parser\ParserRule;

class LanguageDescriptor extends Base
{
    
    /**
     * The default terminals the lexer for this language should parse
     * 
     * @var array
     */
    protected array $default_terminals = [];
    
    /**
     * The list of operators
     * 
     * @var array
     */
    protected array $operators = [];
    
    /**
     * A list of all other terminals that are not operators
     * 
     * @var array
     */
    protected array $other_terminals = [];
    
    /**
     * The parser rules for this language
     * 
     * @var array
     */
    protected array $parser_rules = [];
    
    protected array $accepted_symbols = [];
    
    /**
     * Adds a default terminal to the list of default terminals 
     * 
     * @param string $default_terminal
     * @return static
     */
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
    
    public function getOperatorPrecedences(): array
    {
        $result = [];        
        foreach ($this->operators as $operator => $descriptor) {
            $result[$operator] = $descriptor->getPrecedence();
        }
        return $result;
    }
    
    /**
     * Adds a new rule to the parser
     *
     * @param string $left_hand The symbol that the stack could be reduced to
     * @param array|string $right_hand The necessary top stack elements that have to match
     * @return ParserRule
     */
    public function addRule(string $left_hand, array|string $right_hand): ParserRule
    {
        $rule = new ParserRule($left_hand, $right_hand);
        $this->parser_rules[] = $rule;
        return $rule;
    }
    
   public function getParserRules(): array
   {
        return $this->parser_rules;      
   }

   public function addAcceptedSymbol(string $symbol)
   {
       $this->accepted_symbols[] = $symbol;
       
       return $this;
   }
   
   public function getAcceptedSymbols(): array
   {
       return $this->accepted_symbols;
   }
}