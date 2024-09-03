<?php

namespace Sunhill\Framework\Traits;

use Sunhill\Framework\Exceptions\ChildNotFoundException;

/**
 * This trait is used whenever an object uses a child management
 * @author klaus
 *
 */
trait Children 
{
    
    /**
     * Stores the children. Children are always identifed by a name (that has to be returned by getName())
     * @var array
     */
    protected array $children = [];

    /**
     * Adds the child to the list. If no name is passed it fetches it via getName(). if one is submitted
     * it sets the name in the child
     * 
     * @param unknown $child
     * @param string $name
     * @return \Sunhill\Framework\Traits\Children
     */
    public function addChild($child, string $name = '')
    {
        if (empty($name)) {
            $name = $child->getName();
        } else {
            $child->setName($name);
        }
        
        $this->children[$name] = $child;
        $child->setOwner($this);
        return $this;
    }
    
    /**
     * Returns true when this object has any children
     * 
     * @return bool
     */
    public function hasChildren(): bool
    {
        return !empty($this->children);
    }
    
    /**
     * Clears the list of children
     */
    public function flushChildren()
    {
        $this->children = [];
    }
    
    /**
     * Returns true when the object has a child with this name
     * 
     * @param string $name
     * @return unknown
     */
    public function hasChild(string $name)
    {
        return isset($this->children[$name]);
    }
    
    /**
     * Returns the child with the given name of raises an exception when it doesn't exist
     * 
     * @param string $name
     */
    public function getChild(string $name)
    {
        if (isset($this->children[$name])) {
            return $this->children[$name];
        }
        throw new ChildNotFoundException("There is no child named '$name'");
    }
    
    /**
     * Deletes the child with the given name or raises an exception when it doesn't exist
     * 
     * @param string $name
     */
    public function deleteChild(string $name)
    {
        if (isset($this->children[$name])) {
            unset($this->children[$name]);
        } else {
            throw new ChildNotFoundException("There is no child named '$name'");
        }
    }
}