<?php

/**
 * @file QueryParserLanguage.php
 * The language descriptor for query strings
 * Lang en
 * Reviewstatus: 2025-03-31
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/QueryParserLanguageTest.php
 * Coverage Unit:
 */

namespace Sunhill\Query\QueryParser;

use Sunhill\Parser\LanguageDescriptor\LanguageDescriptor;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Query\QueryParser\Nodes\AliasNode;
use Sunhill\Query\QueryParser\Nodes\OrderNode;

class QueryParserLanguage extends LanguageDescriptor
{
    public function __construct()
    {
        $this->addDefaultTerminal('INTEGER');
        $this->addDefaultTerminal('BOOLEAN');
        $this->addDefaultTerminal('FLOAT');
        $this->addDefaultTerminal('DATETIME');
        $this->addDefaultTerminal('TIME');
        $this->addDefaultTerminal('DATE');
        $this->addDefaultTerminal('IDENTIFIER');
        $this->addDefaultTerminal('STRING');

        $this->addTerminal('as');
        $this->addTerminal('or', '||');
        $this->addTerminal('and', '&&');
        $this->addTerminal(')');
        $this->addBracketOperator('(')->setPrecedence(150);
        $this->addTerminal(']');
        $this->addBracketOperator('[')->setPrecedence(150);
        $this->addTerminal('asc');
        $this->addTerminal('desc');
        $this->addTerminal('.');
        $this->addTerminal(',');

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
        /*        $this->addBinaryOperator('->')
                    ->setType('binary')
                    ->setPrecedence(105)
                    ->addTypes('identifier', 'identifier');
                $this->addBinaryOperator('.')
                    ->setType('binary')
                    ->setPrecedence(110)
                    ->addTypes('identifier', 'identifier'); */

        $this->addRule('EXPRESSION', ['EXPRESSION', '+', 'EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION', ['EXPRESSION', '-', 'EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION', ['EXPRESSION', '*', 'EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION', ['EXPRESSION', '/', 'EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION', 'UNARYMINUS')->setPriority(50);
        $this->addRule('UNARYMINUS', ['-', 'FACTOR'])->setPriority(50)->setASTCallback('unaryOperator');
        $this->addRule('UNARYMINUS', 'FACTOR')->setPriority(50);
        $this->addRule('FACTOR', ['(', 'EXPRESSION', ')'])->setPriority(100)->setASTCallback('bracket');
        $this->addRule('FACTOR', 'CONST')->setPriority(100);
        $this->addRule('FACTOR', 'VARIABLE')->setPriority(100);
        $this->addRule('FACTOR', 'FUNCTION')->setPriority(100);
        $this->addRule('FACTOR', ['EXPRESSION', 'as', 'ident'])->setPriority(100)->setASTCallback(function ($factor, $as, $identifier) {
            return new AliasNode($factor->getAST(), new IdentifierNode($identifier->getValue()));
        });
        $this->addRule('FUNCTION', ['ident', 'EXPRESSION'])->setPriority(100)->setASTCallback('functionHandler');
        $this->addRule('FUNCTION', ['ident', '(', ')'])->setPriority(100)->setASTCallback('functionHandler');
        $this->addRule('VARIABLE', ['ident', '.', 'ident'])->setPriority(110)->setASTCallback(function ($variable1, $dot, $variable2) {
            $result = new IdentifierNode($variable2->getValue());
            $result->reference(new IdentifierNode($variable1->getValue()));

            return $result;
        });

        $this->addRule('VARIABLE', ['ident', '->', 'ident'])->setPriority(105)->setASTCallback(function ($variable, $arrow, $subfield) {
            $result = new IdentifierNode($subfield->getValue());
            $result->parent($variable->getAST());

            return $result;
        });
        $this->addRule('VARIABLE', ['VARIABLE', '->', 'ident'])->setPriority(105)->setASTCallback(function ($variable, $arrow, $subfield) {
            $result = new IdentifierNode($subfield->getValue());
            $result->parent($variable->getAST());

            return $result;
        });
        $this->addRule('VARIABLE', 'ident')->setPriority(105);
        $this->addRule('ARRAY', ['[', 'LIST', ']'])->setPriority(150)->setASTCallback(function ($open, $expression, $close) {
            return $expression->getAST();
        });
        $this->addRule('ARRAY', ['[', 'EXPRESSION', ']'])->setPriority(150)->setASTCallback(function ($open, $expression, $close) {
            $result = new ArrayNode($expression->getAST());

            return $result;
        });
        $this->addRule('LIST', ['LIST', ',', 'EXPRESSION'])->setPriority(150)->setASTCallback(function ($list, $komma, $expression) {
            return $list->getAST()->addElement($expression->getAST());
        });
        $this->addRule('LIST', ['EXPRESSION', ',', 'EXPRESSION'])->setPriority(150)->setASTCallback(function ($expression1, $komma, $expression2) {
            $result = new ArrayNode($expression1->getAST());
            $result->addElement($expression2->getAST());

            return $result;
        });
        $this->addRule('CONST', 'integer')->setPriority(100);
        $this->addRule('CONST', 'float')->setPriority(100);
        $this->addRule('CONST', 'string')->setPriority(100);
        $this->addRule('CONST', 'boolean')->setPriority(100);
        $this->addRule('ORDER_STATEMENT', ['EXPRESSION', 'asc'])->setASTCallback(function (Node $field, $direction) {
            $result = new OrderNode;
            $result->field($field);
            $result->direction($direction);

            return $result;
        });
        $this->addRule('ORDER_STATEMENT', ['EXPRESSION', 'desc'])->setASTCallback(function (Node $field, $direction) {
            $result = new OrderNode;
            $result->field($field);
            $result->direction($direction);

            return $result;
        });
        $this->addAcceptedSymbol('EXPRESSION');
        $this->addAcceptedSymbol('ORDER_STATEMENT');
        $this->addAcceptedSymbol('ARRAY');
        $this->addAcceptedSymbol('LIST');
    }
}
