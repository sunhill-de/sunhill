<?php

namespace Sunhill\Parser\Helpers;

use Sunhill\Basic\Base;

class BinaryOperatorDescriptor extends Base
{
    
    protected string $operator = '';
    
    protected array $combinations = [];
    
    public function __construct(string $operator)
    {
        $this->operator = $operator;    
    }
    
    public function addOperatorType(string $left, string $right, string $result): static
    {
        $entry = new \stdClass();
        $entry->left = $left;
        $entry->right = $right;
        $entry->result = $result;
        $this->combinations[] = $entry;
        
        return $this;
    }
    
    public function matches(string $left, string $right): ?string
    {
        foreach ($this->combinations as $combination) {
            if (($left == $combination->left) && ($right == $combination->right)) {
                return $combination->result;
            }
        }
        
        return null;
    }
    
}