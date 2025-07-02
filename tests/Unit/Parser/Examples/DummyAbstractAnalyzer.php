<?php

namespace Sunhill\Tests\Unit\Parser\Examples;

use Sunhill\Parser\AbstractAnalyzer;

class DummyAbstractAnalyzer extends AbstractAnalyzer
{
 
    private function isNumeric(string $type): bool
    {
        return (in_array($type, ['float','integer']));    
    }
    
    protected function isBinaryNodeCombinationValid(string $operator, string $left_type, string $right_type): bool
    {
        switch ($operator) {
            case '+':
            case '-':
            case '*':
            case '/':    
                return ($left_type == $right_type);
            case '<':
            case '>':
            case '<=':
            case '>=':
                return ($this->isNumeric($left_type) && $this->isNumeric($right_type));
            case '<>':
            case '=':
                return ($this->isNumeric($left_type) && $this->isNumeric($right_type)) || ($left_type == $right_type); 
        }
    }

    protected function isTypeAccepted(string $type): bool
    {
        return $type == 'boolean';
    }
    
    protected function isBinaryNodeOperatorValid(string $operator): bool
    {
        return in_array($operator,['+','-','*','/','<','>','<=','>=','=','<>']);    
    }
    
    protected function getIdentifierType(string $name): string
    {
        switch ($name) {
            case 'int_id':
                return 'integer';
            case 'float_id':
                return 'float';
            case 'string_id':
                return 'string';
        }
    }


    
}