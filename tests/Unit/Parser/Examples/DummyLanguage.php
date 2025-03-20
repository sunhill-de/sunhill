<?php

namespace Sunhill\Tests\Unit\Parser\Examples;

use Sunhill\Parser\LanguageDescriptor\LanguageDescriptor;

class DummyLanguage extends LanguageDescriptor
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
        $this->addTerminal('!=','<>');
        $this->addTerminal('not','!');
        $this->addTerminal('and','&&');
        $this->addTerminal('mod','%');
        $this->addTerminal(')');
        $this->addOperator('(')->setType('bracket');
        
        $this->addOperator('||')
            ->setType('binary')
            ->setPrecedence(5)
            ->addTypes('boolean','boolean','boolean')
            ->addTypes('pseudoboolean','boolean','boolean')
            ->addTypes('pseudoboolean','pseudoboolean','boolean');            
        $this->addOperator('xor')
            ->setType('binary')
            ->setPrecedence(10)
            ->addTypes('boolean','boolean','boolean')
            ->addTypes('pseudoboolean','boolean','boolean')
            ->addTypes('pseudoboolean','pseudoboolean','boolean');
        $this->addOperator('&&')
            ->setType('binary')
            ->setPrecedence(15)
            ->addTypes('boolean','boolean','boolean')
            ->addTypes('pseudoboolean','boolean','boolean')
            ->addTypes('pseudoboolean','pseudoboolean','boolean');            
        $this->addOperator('=')->setType('binary')->setPrecedence(20);
        $this->addOperator('<=')->setType('binary')->setPrecedence(20);
        $this->addOperator('>=')->setType('binary')->setPrecedence(20);
        $this->addOperator('<>')->setType('binary')->setPrecedence(20);
        $this->addOperator('|')->setType('binary')->setPrecedence(25);
        $this->addOperator('&')->setType('binary')->setPrecedence(30);      
        $this->addOperator('>>')->setType('binary')->setPrecedence(35);
        $this->addOperator('<<')->setType('binary')->setPrecedence(35);        
        $this->addOperator('+')->setType('binary')->setPrecedence(35);
        $this->addOperator('-')->setType('binary')->setPrecedence(35);        
        $this->addOperator('/')->setType('binary')->setPrecedence(40);
        $this->addOperator('*')->setType('binary')->setPrecedence(40);
        $this->addOperator('div')->setType('binary')->setPrecedence(40);
        $this->addOperator('%')->setType('binary')->setPrecedence(40);        
        $this->addOperator('^')->setType('binary')->setPrecedence(45);        
        $this->addOperator('!')->setType('unary')->setPrecedence(50);
        $this->addOperator('->')->setType('binary')->setPrecedence(55);
    }
    
}