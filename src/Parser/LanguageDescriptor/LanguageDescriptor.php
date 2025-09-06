<?php

/**
 * @file LanguageDescriptor.php
 * A helper class that makes it easier to define a language that can be interpreted by the
 * lexer, parser and analyzer
 * Lang en
 * Reviewstatus: 2025-09-04
 * Create date: 2025-03-19
 * Localization: complete
 * Documentation: complete
 * Tests: /tests/Unit/Parser/LanguageDescriptor/LanguageDescriptorTest.php
 * Coverage Unit:
 */

namespace Sunhill\Parser\LanguageDescriptor;

use Sunhill\Basic\Base;
use Sunhill\Parser\Exceptions\LanguageDescriptorException;
use Sunhill\Parser\ParserRule;

class LanguageDescriptor extends Base
{
    /**
     * Stores the accepted terminals for the lexer and for the parser (look ahead)
     * @var array
     */
    protected $terminals = [];
    
    /**
     * A collection of the rules that apply to this parser
     * 
     * @var array
     */
    protected $parser_rules = [];
    
    /**
     * All the parser rules that are accepted as a valid expression if only this is left on the stack
     * 
     * @var array
     */
    protected $accepted_finals = [];
    
    /**
     * The list of defined functions
     * 
     * @var array
     */
    protected $functions = [];
    
    /**
     * Adds a terminal to this language. A terminal is a piece that the lexer returns on request. 
     * There are some default terminals and some user defined. The default terminals are identified by
     * an integer, the user defined have to represent the string that should be in the input stream.
     *  
     * @param string|int $terminal
     * @return TerminalDescriptor
     */
    public function addTerminal(string|int $terminal): TerminalDescriptor
    {
        $terminal = new TerminalDescriptor($terminal);
        $this->terminals[] = $terminal;
        return $terminal;
    }
    
    /**
     * Returns the list of terminals as an array of TerminalDescriptor objects. 
     * @return array
     */
    public function getTerminals(): array
    {
        return $this->terminals;
    }
    
    /**
     * Creates a new parser rule, adds it to the desciptor and returns it for further manipulations
     * 
     * @param string $left_side
     * @param string|array $right_side
     * @return ParserRule
     */
    public function addRule(string $left_side, string|array $right_side): ParserRule
    {
        $rule = new ParserRule($left_side, $right_side);
        $this->parser_rules[] = $rule; 
        
        return $rule;
    }
    
    /**
     * Returns the defined parser rules (used by parser and analyzer)
     * 
     * @return array
     */
    public function getParserRules(): array
    {
        return $this->parser_rules;
    }
    
    /**
     * Adds one or more non-terminals that are accepted as a last element on the stack
     * 
     * @param array|string $final
     */
    public function addAcceptedFinal(array|string $final)
    {
        if (is_array($final)) {
            $this->accepted_finals = array_merge($this->accepted_finals,$final);
        } else {
            $this->accepted_finals[] = $final;
        }
    }
    
    /**
     * Returns the accepted finals array
     * 
     * @return array
     */
    public function getAcceptedFinals(): array
    {
        return $this->accepted_finals;
    }
    
    /**
     * Adds an function
     * 
     * @param string $name
     * @return FunctionDescriptor
     */
    public function addFunction(string $name): FunctionDescriptor
    {
        $result = new FunctionDescriptor($name);
        $this->functions[] = $result;
        
        return $result;
    }
    
    /**
     * Returns the list of functions
     * 
     * @return array
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }
}
