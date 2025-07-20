<?php
/**
 * @file LanguageDescriptor.php
 * A helper class that makes it easier to define a language that can be interpreted by the
 * lexer, parser and analyzer
 * Lang en
 * Reviewstatus: 2025-07-15
 * Create date: 2025-03-19
 * Localization: complete
 * Documentation: complete
 * Tests: /tests/Unit/Parser/LanguageDescriptor/LanguageDescriptorTest.php
 * Coverage Unit:
 */

namespace Sunhill\Parser\LanguageDescriptor;

use Sunhill\Basic\Base;
use Sunhill\Parser\ParserRule;
use Sunhill\Parser\Exceptions\LanguageDescriptorException;

class LanguageDescriptor extends Base
{
    
    /**
     * The default terminals the lexer for this language should parse
     * 
     * @var array
     */
    protected array $default_terminals = [];
    
    /**
     * The list of unary operators
     * 
     * @var array
     */
    protected array $unary_operators = [];
    
    /**
     * The list of binary operators
     *
     * @var array
     */
    protected array $binary_operators = [];
    
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
     * Adds a default terminal to the list of default terminals (needed for the lexer) 
     * 
     * @param string $default_terminal
     * @return static
     */
    public function addDefaultTerminal(string $default_terminal): static
    {
        $default_terminal = strtoupper($default_terminal);
        if (!in_array($default_terminal, ['INTEGER','BOOLEAN','FLOAT','DATETIME','TIME','DATE','IDENTIFIER','STRING'])) {
            throw new LanguageDescriptorException("The default terminal '$default_terminal' is unknown.");
        }
        
        $this->default_terminals[] = $default_terminal;
        
        return $this;
    }
    
    /**
     * Returns the list of default terminals (needed for the lexer)
     * 
     * @return array
     */
    public function getDefaultTerminals(): array
    {
        return $this->default_terminals;
    }
    
    /**
     * Helper function that checks if this kind of operator was already defined. if yes thows exception if not add it
     * 
     * @param string $prefix
     * @param string $operator
     * @return OperatorDescriptor
     */
    private function addOperator(string $prefix, string $operator): OperatorDescriptor
    {
        $descriptor = new OperatorDescriptor($operator);
        $descriptor->setType($prefix);
        $varname = $prefix.'_operators';
        if (isset($this->$varname[$operator])) {
            throw new LanguageDescriptorException("The $prefix operator '$operator' is already defined.");
        }
        $this->$varname[$operator] = $descriptor;
        
        return $descriptor;        
    }
       
    /**
     * Add a new unary operator to the descriptor
     * 
     * @param string $operator
     * @return OperatorDescriptor
     */
    public function addUnaryOperator(string $operator): OperatorDescriptor
    {
        return $this->addOperator('unary', $operator);
    }

    /**
     * Adds a new binary operator to the descriptor
     * 
     * @param string $operator
     * @return OperatorDescriptor
     */
    public function addBinaryOperator(string $operator): OperatorDescriptor
    {
        return $this->addOperator('binary', $operator);
    }
    
    /**
     * Helper function that Searches for an operator with the given prefix.
     * 
     * @param string $prefix
     * @param string $operator
     * @return OperatorDescriptor|NULL
     */
    private function getOperator(string $prefix, string $operator): ?OperatorDescriptor
    {
        $varname = $prefix.'_operators';    
        if (!isset($this->$varname[$operator])) {
            return null;
        }
        return $this->$varname[$operator];
    }
    
    /**
     * Searches for the descriptor for a unary operator $operator. If found return it otherwise false
     * 
     * @param string $operator
     * @return OperatorDescriptor|NULL
     */
    public function getUnaryOperator(string $operator): ?OperatorDescriptor
    {
        return $this->getOperator('unary', $operator);
    }
    
    /**
     * Searches for the descriptor for a binary operator $operator. If found return it otherwise false
     *
     * @param string $operator
     * @return OperatorDescriptor|NULL
     */
    public function getBinaryOperator(string $operator): ?OperatorDescriptor
    {
        return $this->getOperator('binary', $operator);
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