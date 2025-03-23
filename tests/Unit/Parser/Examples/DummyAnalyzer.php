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
        $this->addOperatorTypes('+',['integer','integer','integer']);
        $this->addOperatorTypes('+',['float','integer','float']);
        $this->addOperatorTypes('+',['integer','float','float']);
        $this->addOperatorTypes('+',['string','string','string']);
        $this->addOperatorTypes('-',['integer','integer','integer']);
        $this->addOperatorTypes('-',['float','integer','float']);
        $this->addOperatorTypes('-',['integer','float','float']);
        $this->addOperatorTypes('-',['string','string','string']);
        $this->addOperatorTypes('-',['integer','integer']); // Unary -
        $this->addOperatorTypes('-',['float','float']);
    }
}