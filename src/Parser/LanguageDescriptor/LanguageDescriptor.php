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
        $this->terminals[$terminal->getTerminal()] = $terminal;
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
}
