<?php

namespace Sunhill\Tests\TestSupport\Parser;

use Sunhill\Parser\LanguageDescriptor\LanguageDescriptor;
use Sunhill\Parser\LanguageDescriptor\TerminalDescriptor;

class DummyLanguage extends LanguageDescriptor
{
    public function __construct()
    {
        $this->addTerminal(TerminalDescriptor::INTEGER_TERMINAL);
        $this->addTerminal(TerminalDescriptor::BOOLEAN_TERMINAL);
        $this->addTerminal(TerminalDescriptor::FLOAT_TERMINAL);
        $this->addTerminal(TerminalDescriptor::DATETIME_TERMINAL);
        $this->addTerminal(TerminalDescriptor::TIME_TERMINAL);
        $this->addTerminal(TerminalDescriptor::DATE_TERMINAL);
        $this->addTerminal(TerminalDescriptor::IDENTIFIER_TERMINAL);
        $this->addTerminal(TerminalDescriptor::STRING_TERMINAL);
        $this->addTerminal('or')->aliasFor('||');
        $this->addTerminal('and')->aliasFor('&&');
        $this->addTerminal(')');
        $this->addTerminal('(');
        $this->addTerminal('||');
        $this->addTerminal('&&');
        $this->addTerminal('+');
        $this->addTerminal('-');
        $this->addTerminal('*');
        $this->addTerminal('/');
        $this->addTerminal('->');
/*        
        $this->addBinaryOperator('||')
            ->setType('binary')
            ->setPrecedence(5)
            ->addTypes('boolean', 'boolean', 'boolean')
            ->addTypes('boolean', 'pseudoboolean', 'boolean')
            ->addTypes('pseudoboolean', 'boolean', 'boolean')
            ->addTypes('pseudoboolean', 'pseudoboolean', 'boolean');
        $this->addBinaryOperator('&&')
            ->setType('binary')
            ->setPrecedence(15)
            ->addTypes('boolean', 'boolean', 'boolean')
            ->addTypes('pseudoboolean', 'boolean', 'boolean')
            ->addTypes('pseudoboolean', 'pseudoboolean', 'boolean');
        $this->addBinaryOperator('+')
            ->setType('binary')
            ->setPrecedence(35)
            ->addTypes('integer', 'integer', 'integer')
            ->addTypes('integer', 'float', 'float')
            ->addTypes('float', 'float', 'float')
            ->addTypes('float', 'integer', 'float')
            ->addTypes('string', 'string', 'string');
        $this->addBinaryOperator('-')
            ->setType('binary')
            ->setPrecedence(35)
            ->addTypes('integer', 'integer', 'integer')
            ->addTypes('integer', 'float', 'float')
            ->addTypes('float', 'float', 'float')
            ->addTypes('float', 'integer', 'float');
        $this->addBinaryOperator('/')
            ->setType('binary')
            ->setPrecedence(40)
            ->addTypes('integer', 'float', 'float');
        $this->addBinaryOperator('*')
            ->setType('binary')
            ->setPrecedence(40)
            ->addTypes('integer', 'integer', 'integer')
            ->addTypes('integer', 'float', 'float')
            ->addTypes('float', 'float', 'float')
            ->addTypes('float', 'integer', 'float');
        $this->addBinaryOperator('->')
            ->setType('binary')
            ->setPrecedence(55)
            ->addTypes('identifier', 'identifier');
*/
        $this->addRule('EXPRESSION', ['EXPRESSION', '+', 'EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION', ['EXPRESSION', '-', 'EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION', ['EXPRESSION', '*', 'EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION', ['EXPRESSION', '/', 'EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION', 'UNARYMINUS')->setPriority(50);
        $this->addRule('UNARYMINUS', ['-', 'FACTOR'])->setPriority(50)->setASTCallback('unaryOperator');
        $this->addRule('UNARYMINUS', 'FACTOR')->setPriority(50);
        $this->addRule('FACTOR', ['(', 'EXPRESSION', ')'])->setPriority(100)->setASTCallback('bracket');
        $this->addRule('FACTOR', 'CONST')->setPriority(100);
        $this->addRule('FACTOR', 'ident')->setPriority(100);
        $this->addRule('FACTOR', 'FUNCTION')->setPriority(100);
        $this->addRule('FUNCTION', ['ident', 'EXPRESSION'])->setPriority(100)->setASTCallback('functionHandler');
        $this->addRule('FUNCTION', ['ident', '(', ')'])->setPriority(100)->setASTCallback('functionHandler');
        $this->addRule('CONST', 'integer')->setPriority(100);
        $this->addRule('CONST', 'float')->setPriority(100);
        $this->addRule('CONST', 'string')->setPriority(100);
        $this->addRule('CONST', 'boolean')->setPriority(100);

        $this->addAcceptedSymbol('EXPRESSION');
    }
}
