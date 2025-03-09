<?php
/**
 * @file Node.php
 * A basic class for a node as a parsing result
 * Lang en
 * Reviewstatus: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;

class Node extends Base
{
    
    protected string $type;
    
    protected array $children;
    
    public function __construct(string $type, array $children = [])
    {
        $this->type = $type;
        $this->children = $children;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getChildren()
    {
        return $this->children;
    }
}