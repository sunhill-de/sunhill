<?php

namespace Sunhill\Tests\TestSupport\Parser;

use Sunhill\Parser\AbstractAnalyzer;
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;

class DummyAbstractAnalyzer extends AbstractAnalyzer
{
    protected function checkAcceptedType(string $type): bool
    {
        return (in_array($type,['boolean','pseudoboolean','integer']));
    }

    protected function getProfilesOfUnaryOperator(string $operator): ?array
    {
        switch ($operator) {
            case '-':
                return [
                    ['child'=>'integer','resulting'=>'integer'],
                    ['child'=>'float','resulting'=>'float'],
                ];
                break;
            default:
                return null;
        }
    }

    protected function getTypeOfIdentifier(string $name): ?string
    {
        switch ($name) {
            case 'int_id':
                return 'integer';
            case 'float_id':
                return 'float';
            default:
                return null;
        }
    }

    protected function getProfilesOfBinaryOperator(string $operator): ?array
    {
        switch ($operator) {
            case '+':
                return [
                ['left'=>'integer','right'=>'integer','resulting'=>'integer'],
                ['left'=>'numeric','right'=>'float','resulting'=>'float'],
                ['left'=>'float','right'=>'numeric','resulting'=>'float'],
                ];
                break;
            case '<':
                return [
                ['left'=>'numeric','right'=>'numeric','resulting'=>'boolean'],
                ];
                break;
            case '&&':
                return [
                ['left'=>'boolean','right'=>'pseudoboolean','resulting'=>'boolean'],
                ['left'=>'pseudoboolean','right'=>'boolean','resulting'=>'boolean'],
                ];
                break;
            default:
                return null;
        }        
    }

    protected function getProfileOfFunction(string $name): ?FunctionDescriptor
    {
        return null; 
    }

}