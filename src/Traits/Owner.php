<?php

namespace Sunhill\Framework\Traits;

trait Owner
{
    
    /**
     * The owning module of this module (or null if top)
     * @var $owner
     */
    protected $owner;
    
    /**
     * Setter for owner
     *
     * @param $owner
     * @return self
     */
    public function setOwner($owner): self
    {
        $this->owner = $owner;
        return $this;
    }
    
    /**
     * Getter for owner
     *
     * @return 
     */
    public function getOwner()
    {
        return $this->owner;
    }
    
    /**
     * Returns if the module has an owner
     *
     * @return bool
     */
    public function hasOwner(): bool
    {
        return !is_null($this->owner);
    }
    
    /**
     * Returns an associative array with the name of the module as key and a reference to the
     * module as value
     *
     * @return array
     */
    public function getHirachy(): array
    {
        if ($this->hasOwner()) {
            $result = $this->getOwner()->getHirachy();
        } else {
            $result = [];
        }
        $result[$this->getName()] = $this;
        
        return $result;
    }
    
    public function getPath(): string
    {
        if ($this->hasOwner()) {
            if (empty($this->getName())) {
                return $this->getOwner()->getPath();
            }
            return $this->getOwner()->getPath().$this->getName().'/';
        } 
        return '/'.$this->getName().'/';
    }
}