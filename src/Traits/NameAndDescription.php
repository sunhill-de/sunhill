<?php

namespace Sunhill\Framework\Traits;

trait NameAndDescription 
{
    
    protected string $name = '';
    
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    protected string $description = '';
    
    public function setDescription(string $name)
    {
        $this->description = $name;
        return $this;
    }
    
    public function getDescription(): string
    {
        return $this->description;
    }
}