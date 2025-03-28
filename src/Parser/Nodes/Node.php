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

namespace Sunhill\Parser\Nodes;

use Sunhill\Basic\Base;

class Node extends Base
{
    /**
     * The type of this node 
     */
    protected string $type;

    /**
     * The children of this node (if any)
     */
    protected array $children;

    /**
     * simple constructor that takes the type and the children as parameters
     */
    public function __construct(string $type, array $children = [])
    {
        $this->type = $type;
        $this->children = $children;
    }

    /**
     * Getter for type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Getter for children
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    protected function handleReplacingChild(string $name, $node)
    {
        if (isset($node)) {
            $this->children[$name] = $node;
            return $this;
        } else {
            return isset($this->children[$name])?$this->children[$name]:null;
        }
    }
    
    protected function handleOptionalArrayChild(string $name, $node)
    {
        if (isset($node)) {
            if (isset($this->children[$name])) {
                if (!is_a($this->children[$name], ArrayNode::class)) {
                    $this->children[$name] = new ArrayNode($this->children[$name]);
                }
                $this->children[$name]->addElement($node);
            } else {
                $this->children[$name] = $node;
            }
            return $this;
        } else {
            return isset($this->children[$name])?$this->children[$name]:null;
        }         
    }
    
}
