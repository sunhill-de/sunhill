<?php
/**
 * @file QueryParserLanguage.php
 * The language descriptor for query strings 
 * Lang en
 * Reviewstatus: 2025-03-31
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/QueryParserLanguageTest.php
 * Coverage:
 */

namespace Sunhill\Query\QueryParser;

use Sunhill\Parser\LanguageDescriptor\LanguageDescriptor;

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
        
        $this->addTerminal('or','||');
        $this->addTerminal('and','&&');
        $this->addTerminal(')');
        $this->addOperator('(')->setType('bracket')->setPrecedence(150);
        
        $this->addOperator('||')
        ->setType('binary')
        ->setPrecedence(5)
        ->addTypes('boolean','boolean','boolean')
        ->addTypes('boolean','pseudoboolean','boolean')
        ->addTypes('pseudoboolean','boolean','boolean')
        ->addTypes('pseudoboolean','pseudoboolean','boolean');
        $this->addOperator('&&')
        ->setType('binary')
        ->setPrecedence(15)
        ->addTypes('boolean','boolean','boolean')
        ->addTypes('pseudoboolean','boolean','boolean')
        ->addTypes('pseudoboolean','pseudoboolean','boolean');
        $this->addOperator('+')
        ->setType('binary')
        ->setPrecedence(35)
        ->addTypes('integer','integer','integer')
        ->addTypes('integer','float','float')
        ->addTypes('float','float','float')
        ->addTypes('float','integer','float')
        ->addTypes('string','string','string');
        $this->addOperator('-')
        ->setType('binary')
        ->setPrecedence(35)
        ->addTypes('integer','integer','integer')
        ->addTypes('integer','float','float')
        ->addTypes('float','float','float')
        ->addTypes('float','integer','float');
        $this->addOperator('/')
        ->setType('binary')
        ->setPrecedence(40)
        ->addTypes('integer','float','float');
        $this->addOperator('*')
        ->setType('binary')
        ->setPrecedence(40)
        ->addTypes('integer','integer','integer')
        ->addTypes('integer','float','float')
        ->addTypes('float','float','float')
        ->addTypes('float','integer','float');
        $this->addOperator('->')
        ->setType('binary')
        ->setPrecedence(55)
        ->addTypes('identifier','identifier');
        
        $this->addRule('EXPRESSION',['EXPRESSION','+','EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION',['EXPRESSION','-','EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION',['EXPRESSION','*','EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION',['EXPRESSION','/','EXPRESSION'])->setASTCallback('twoSideOperator');
        $this->addRule('EXPRESSION','UNARYMINUS')->setPriority(50);
        $this->addRule('UNARYMINUS',['-','FACTOR'])->setPriority(50)->setASTCallback('unaryOperator');
        $this->addRule('UNARYMINUS','FACTOR')->setPriority(50);
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
        
        $this->addAcceptedSymbol('EXPRESSION');
    }
    
    
}