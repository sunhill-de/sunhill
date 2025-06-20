<?php

namespace Sunhill\Tests\Unit\Parser\Examples;

use Sunhill\Parser\AbstractAnalyzer;

class DummyAbstractAnalyzer extends AbstractAnalyzer
{
    
    protected function isBinaryNodeCombinationValid(string $operator, string $left_type, string $right_type): bool
    {
        switch ($operator) {
            case '+':
            case '-':
            case '*':
            case '/':    
                return ($left_type == $right_type);
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

    
}