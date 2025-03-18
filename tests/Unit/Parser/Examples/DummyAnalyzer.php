<?php

namespace Sunhill\Tests\Unit\Parser\Examples;

use Sunhill\Parser\Analyzer;
use Sunhill\Parser\Nodes\Node;

class DummyAnalyzer extends Analyzer
{
    
    public function __construct(Node $tree_root)
    {
        parent::__construct($tree_root);
        $this->addIdentifier('test_int', 'integer');
        $this->addIdentifier('test_string', 'string');
        $this->addFunction('sin','float')->addParameter('float',false);
        $this->addFunction('time','integer');
        $this->addFunction('test_function', 'string')->addParameter('string', true);
        $this->addFunction('test_ellipsis', 'string')->setUnlimitedParameters(2, 'string');
        $this->addBinaryOperator('+')
            ->addOperatorType('int','int','int')
            ->addOperatorType('float','int','float')
            ->addOperatorType('int','float','float')
            ->addOperatorType('string','string','string');
    }
}