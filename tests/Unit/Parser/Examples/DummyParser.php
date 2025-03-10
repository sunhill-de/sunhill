<?php

namespace Sunhill\Tests\Unit\Parser\Examples;

use Sunhill\Parser\Parser;

class DummyParser extends Parser
{
    protected $grammar = [
        'EXPRESSION'=>[
            'priority'=>10,
            ['EXPRESSION','+','PRODUCT','!execute!'=>'twoSideOperator'],
            ['EXPRESSION','-','PRODUCT','!execute!'=>'twoSideOperator'],
            ['PRODUCT']
        ],
        'PRODUCT'=>[
            'priority'=>20,
            ['PRODUCT','*','UNARYMINUS','!execute!'=>'twoSideOperator'],
            ['PRODUCT','/','UNARYMINUS','!execute!'=>'twoSideOperator'],
            ['UNARYMINUS']
        ],
        'UNARYMINUS'=>[
            'priority'=>100,
            ['-','FACTOR','!execute!'=>'unaryOperator'],
            ['FACTOR']
        ],
        'FACTOR'=>[
            'priority'=>100,
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