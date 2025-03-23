<?php

namespace Sunhill\Tests\Feature\Parser\Examples;

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
            ->addTypes('boolean','pseudoboolean','boolean')
            ->addTypes('pseudoboolean','boolean','boolean')
            ->addTypes('pseudoboolean','pseudoboolean','boolean');            
        $this->addOperator('xor')
            ->setType('binary')
            ->setPrecedence(10)
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
        $this->addOperator('=')
            ->setType('binary')
            ->setPrecedence(20)
            ->addTypes('pseudoboolean','pseudoboolean','boolean')
            ->addTypes('array','array','boolean');
        $this->addOperator('<=')
            ->setType('binary')
            ->setPrecedence(20)
            ->addTypes('numeric','numeric','boolean')
            ->addTypes('string','string','boolean');
        $this->addOperator('>=')
            ->setType('binary')
            ->setPrecedence(20)
            ->addTypes('numeric','numeric','boolean')
            ->addTypes('string','string','boolean');
        $this->addOperator('<')
            ->setType('binary')
            ->setPrecedence(20)
            ->addTypes('numeric','numeric','boolean')
            ->addTypes('string','string','boolean');
        $this->addOperator('>')
            ->setType('binary')
            ->setPrecedence(20)
            ->addTypes('numeric','numeric','boolean')
            ->addTypes('string','string','boolean');
        $this->addOperator('<>')
            ->setType('binary')
            ->setPrecedence(20)
            ->addTypes('numeric','numeric','boolean')
            ->addTypes('string','string','boolean');            
        $this->addOperator('|')
            ->setType('binary')
            ->setPrecedence(25)
            ->addTypes('integer','integer','integer');
        $this->addOperator('&')
            ->setType('binary')
            ->setPrecedence(27)
            ->addTypes('integer','integer','integer');        
        $this->addOperator('>>')
            ->setType('binary')
            ->setPrecedence(30)
            ->addTypes('integer','integer','integer');        
        $this->addOperator('<<')
            ->setType('binary')
            ->setPrecedence(30)
            ->addTypes('integer','integer','integer');        
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
        $this->addOperator('div')
            ->setType('binary')
            ->setPrecedence(40)
            ->addTypes('integer','integer','integer');
        $this->addOperator('%')
            ->setType('binary')
            ->setPrecedence(40)        
            ->addTypes('integer','integer','integer');
        $this->addOperator('^')
            ->setType('binary')
            ->setPrecedence(45)        
            ->addTypes('integer','integer','integer');
        $this->addOperator('!')
            ->setType('unary')
            ->setPrecedence(50)
            ->addTypes('boolean','boolean')
            ->addTypes('psudoboolean','boolean');
        $this->addOperator('->')
            ->setType('binary')
            ->setPrecedence(55)
            ->addTypes('identifier','identifier');
    }
    
}