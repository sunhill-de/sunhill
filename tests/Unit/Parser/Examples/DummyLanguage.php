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
        $this->addTerminal('and','&&');
        $this->addTerminal(')');
        $this->addOperator('(')->setType('bracket');
        
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
    }
    
}