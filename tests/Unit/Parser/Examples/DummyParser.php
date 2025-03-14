<?php

namespace Sunhill\Tests\Unit\Parser\Examples;

use Sunhill\Parser\Parser;

class DummyParser extends Parser
{
    
    protected $accepted_finals = ['EXPRESSION'];
    
    public function __construct()
    {
        $this->addRule('EXPRESSION',['EXPRESSION','+','PRODUCT'])->setPriority(10)->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION',['EXPRESSION','-','PRODUCT'])->setPriority(10)->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION','PRODUCT')->setPriority(10);
        $this->addRule('PRODUCT',['PRODUCT','*','UNARYMINUS'])->setPriority(20)->setASTCallback('twoSideOperator');
        $this->addRule('PRODUCT',['PRODUCT','/','UNARYMINUS'])->setPriority(20)->setASTCallback('twoSideOperator');
        $this->addRule('PRODUCT','UNARYMINUS')->setPriority(20);
        $this->addRule('UNARYMINUS',['-','FACTOR'])->setPriority(100)->setASTCallback('unaryOperator');
        $this->addRule('UNARYMINUS','FACTOR')->setPriority(100);
        $this->addRule('FACTOR',['(','EXPRESSION',')'])->setPriority(100)->setASTCallback('bracket');
        $this->addRule('FACTOR','CONST')->setPriority(100);
        $this->addRule('FACTOR','ident')->setPriority(100);
        $this->addRule('FACTOR','FUNCTION')->setPriority(100);
        $this->addRule('FUNCTION',['ident','EXPRESSION'])->setPriority(100)->setASTCallback('functionHandler');
        $this->addRule('FUNCTION',['ident','(',')'])->setPriority(100)->setASTCallback('functionHandler');
        $this->addRule('CONST', 'integer')->setPriority(100);
        $this->addRule('CONST', 'float')->setPriority(100);
        $this->addRule('CONST', 'string')->setPriority(100);
        $this->addRule('CONST', 'boolean')->setPriority(100);
        $this->addOperatorPrecedence('+', 10);
        $this->addOperatorPrecedence('-', 10);
        $this->addOperatorPrecedence('*', 20);
        $this->addOperatorPrecedence('/', 20);
        $this->addOperatorPrecedence('(', 150);
    }
}