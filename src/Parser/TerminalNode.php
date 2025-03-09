<?php
/**
 * @file UnaryNode.php
 * A basic class for a node that has only one child
 * Lang en
 * Reviewstatus: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;

class TerminalNode extends Node
{
        
    public function __construct(string $type, $value, string $type_hint)
    {
        parent::__construct($type,['value'=>$value,'type_hint'=>$type_hint]);
    }
    
    public function getValue()
    {
        return $this->children['value'];
    }
        
    public function getTypeHint(): string
    {
        return $this->children['type_hint'];
    }
}