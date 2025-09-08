<?php

namespace Sunhill\Tests\Unit\Parser\Examples;

use Sunhill\Parser\Lexer;

class DummyLexer extends Lexer
{
    public function __construct(string $parse_string = '')
    {
        parent::__construct($parse_string);
        $this->addDefaultTerminal('INTEGER');
        $this->addDefaultTerminal('BOOLEAN');
        $this->addDefaultTerminal('FLOAT');
        $this->addDefaultTerminal('DATETIME');
        $this->addDefaultTerminal('TIME');
        $this->addDefaultTerminal('DATE');
        $this->addDefaultTerminal('IDENTIFIER');
        $this->addDefaultTerminal('STRING');
        $this->addTerminal('or', '||', false);
        $this->addTerminal('and', '&&', true);
        $this->addTerminal('&&');
        $this->addTerminal('||');
        $this->addTerminal('+');
        $this->addTerminal('-');
        $this->addTerminal('/');
        $this->addTerminal('*');
        $this->addTerminal('(');
        $this->addTerminal(')');
        $this->addTerminal('->');
    }
}
