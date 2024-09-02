<?php

namespace Sunhill\Filter;

/**
 * A standard implementation for filterable items
 * 
 * @author Klaus Dimde
 *
 */
class FilterContainer
{

    protected $options = [];
    
    /**
     * Returns if the item has the given condition
     * 
     * @param string $name
     * @return bool
     */
    public function hasCondition(string $name): bool
    {
        return isset($this->options[$name]);
    }
    
    /**
     * Returns if the item is writeable
     * 
     * @param string $name
     * @return bool
     */
    public function conditionWriteable(string $name): bool
    {
        return true;
    }
    
    /**
     * Returns the actual value of the condition
     * 
     * @param string $name
     */
    public function getCondition(string $name)
    {
        if (!isset($this->options[$name])) {
            throw new \Exception("Access of unknown option '$name'");
        }
        $result = $this->options[$name];
        if (is_scalar($result)) {
            return $result;
        }
        if (is_callable($result)) {
            return $result($this);
        }        
    }
    
    /**
     * Sets the actual value of the condition
     * 
     * @param string $name
     * @param unknown $value
     */
    public function setCondition(string $name, $value = true)
    {
        $this->options[$name] = $value;        
    }
    
}