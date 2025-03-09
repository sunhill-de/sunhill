<?php

namespace Sunhill\Tests\Unit\Parser\Examples;

use Sunhill\Parser\Lexer;

class DummyLexer extends Lexer
{
    protected $default_terminals = ['INT','FLOAT','DATETIME','TIME','DATE','IDENTIFIER','STRING'];
    
    protected $terminals = [
        'or'=>'||',
        'and'=>'&&',
        '&&'=>'&&',
        '||'=>'||',
        '+'=>'+',
        '-'=>'-',
        '/'=>'/',
        '*'=>'*',
        '('=>'(',
        ')'=>')',
        '->'=>'->'
    ];
    
}
