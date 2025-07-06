<?php
/**
 * @file Node.php
 * A basic class for a node as a parsing result
 * Lang en
 * Reviewstatus: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit: 100 % (2025-06-06)
 */

namespace Sunhill\Parser\Nodes;

use Sunhill\Basic\Base;

abstract class Node extends Base
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

    public function addChild(string $key, $value)
    {
        $this->children[$key] = $value;    
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
    
    /**
     * Tests if the test rule $test matches the given rule $rule and takes care of pseudo
     * types like numeric and pseudoboolean
     *
     * @param string $test
     * @param string $rule
     * @return boolean
     */
    protected function typeMatch(string $test, string $rule)
    {
        switch ($rule) {
            case 'pseudoboolean':
                return in_array($test,['boolean','integer','float','string']);
            case 'numeric':
            case 'float':
                return in_array($test,['integer','float']);
            default:
                return $test == $rule;
        }
    }
    
    /**
     * Every Node should return a datatype. This is trivial for the terminals (like integer, float, etc). 
     * A little bit more complex for UnaryNode, BinaryNode, FunctionNode and IdentifierNode
     * 
     * @return string|NULL a value of null means the datatype is not detectable yet. 
     */
    public function getDatatype(): ?string
    {
        return null;
    }
    
    /**
     * Mostly for debugging purposes every node must provide this method to read it out
     * 
     * @return string
     */
    abstract public function toString(): string;
    
    /**
     * Every node must be able to validate itself. This method should throw an exception if something
     * is wrong
     * 
     */
    public function validate()
    {
        // Does nothing by default         
    }
}
