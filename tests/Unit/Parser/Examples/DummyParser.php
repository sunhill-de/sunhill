<?php

namespace Sunhill\Tests\Unit\Parser\Examples;

use Sunhill\Parser\Parser;

class DummyParser extends Parser
{
    protected $grammar = [
        'EXPRESSION'=>[
            'priority'=>0,
            ['EXPRESSION','+','PRODUCT','!execute!'=>'twoSideOperator'],
            ['EXPRESSION','-','PRODUCT','!execute!'=>'twoSideOperator'],
            ['PRODUCT']
        ],
        'PRODUCT'=>[
            'priority'=>10,
            ['PRODUCT','*','UNARYMINUS','!execute!'=>'twoSideOperator'],
            ['PRODUCT','/','UNARYMINUS','!execute!'=>'twoSideOperator'],
            ['UNARYMINUS']
        ],
        'UNARYMINUS'=>[
            'priority'=>20,
            ['-','FACTOR','!execute!'=>'unaryOperator'],
            ['FACTOR']
        ],
        'FACTOR'=>[
            'priority'=>30,
            ['(','EXPRESSION',')','!execute!'=>'bracket'],
            ['const'],
            ['ident']            
        ]
    ];
    
    protected $operator_precedence=[
        '+'=>10,
        '-'=>10,
        '*'=>20,
        '/'=>20,
    ];
    
    protected $accepted_finals = ['EXPRESSION'];
    
}