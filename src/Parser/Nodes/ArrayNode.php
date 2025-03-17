<?php
/**
 * @file ArrayNode.php
 * A node that represents an array of nodes
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser\Nodes;

class ArrayNode extends Node
{
        
    public function __construct($value)
    {
        parent::__construct('array',[]);
    }

}
