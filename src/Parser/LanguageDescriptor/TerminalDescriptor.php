<?php

/**
 * @file TerminalDescriptor.php
 * A helper class that stores all relevant informations about a terminal
 * Lang en
 * Reviewstatus: 2025-09-02
 * Creation date: 2025-03-19
 * Localization: complete
 * Documentation: complete
 * Tests: /tests/Unit/Parser/LanguageDescriptor/TerminalDescriptorTest.php
 * Coverage Unit:
 */

namespace Sunhill\Parser\LanguageDescriptor;

use Sunhill\Basic\Base;
use Sunhill\Parser\Exceptions\LanguageDescriptorException;

class TerminalDescriptor extends Base
{
    const INTEGER_TERMINAL    = 1;
    const BOOLEAN_TERMINAL    = 2;
    const FLOAT_TERMINAL      = 3;
    const DATETIME_TERMINAL   = 4;
    const TIME_TERMINAL       = 5;
    const DATE_TERMINAL       = 6;
    const IDENTIFIER_TERMINAL = 7;
    const STRING_TERMINAL     = 8;
    
    /**
     * The operator we are taling of
     */
    protected string $terminal = '';

    /**
     * The look ahead precedence of this operator
     */
    protected int $la_precedence = 0;

    /**
     * What kind of operator (unary, binary, tertiary)
     */
    protected string $type = 'symbol';

    /**
     * Processes a default terminal
     */
    private function setDefaultTerminal(int $terminal)
    {
        $this->type = 'default';
        switch ($terminal) {
            case static::INTEGER_TERMINAL:
                $this->terminal = 'INTEGER';
                break;
            case static::BOOLEAN_TERMINAL:
                $this->terminal = 'BOOLEAN';
                break;
            case static::FLOAT_TERMINAL:
                $this->terminal = 'FLOAT';
                break;
            case static::DATETIME_TERMINAL:
                $this->terminal = 'DATETIME';
                break;
            case static::TIME_TERMINAL:
                $this->terminal = 'TIME';
                break;
            case static::DATE_TERMINAL:
                $this->terminal = 'DATE';
                break;
            case static::IDENTIFIER_TERMINAL:
                $this->terminal = 'IDENTIFIER';
                break;
            case static::STRING_TERMINAL:
                $this->terminal = 'STRING';
                break;
            default:
                throw new LanguageDescriptorException("Unknown default token id '$terminal'");                
        }
    }
    
    /**
     * Processes a string symbol
     */
    private function setOtherTerminal(string $terminal)
    {
        $this->type = 'symbol';
        $this->terminal = $terminal;
    }

    /**
     * The constructor for a terminal descriptor. Accepts either an integer for one of the build in terminals
     * or a string a another terminal symbol
     */
    public function __construct(string|int $terminal)
    {
        if (is_int($terminal)) {
            $this->setDefaultTerminal($terminal);
            return;
        }
        $this->setOtherTerminal($terminal);
    }

    /**
     * Getter for the operator
     */
    public function getTerminal(): string
    {
        return $this->terminal;
    }
    
    /**
     * Getter for the type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Setter for the look-ahead precendence. This is used for the shift-reduce-parser to decide if to
     * shift or to reduce
     */
    public function setLAPrecedence(int $precedence): static
    {
        $this->la_precedence = $precedence;

        return $this;
    }

    /**
     * Getter for precendence
     */
    public function getLAPrecedence(): int
    {
        return $this->la_precedence;
    }

}
