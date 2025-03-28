<?php

namespace Sunhill\Query\Helpers;

use Sunhill\Basic\Base;
use Illuminate\Support\Collection;
use Sunhill\Properties\RecordProperty;
use Sunhill\Query\Query;
use Sunhill\Parser\Nodes\Node;

class MethodSignature extends Base
{
    protected array $parameter = [];

    protected $action;
    
    public function addParameter(string $parameter_signature): static
    {
        $this->parameter[] = $parameter_signature;

        return $this;
    }  

    public function addParameters(array $parameter_signatures): static
    {
        foreach ($parameter_signatures as $signature) {
            $this->addParameter($signature);
        }
        
        return $this;
    }
    
    public function setAction($action): static
    {
        $this->action = $action;

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }
    
    private function signature_matches(string $test1, string $test2): bool
    {
        $subitems = explode('|', $test2);
        foreach ($subitems as $item) {
            switch ($test1) {
                default:
                    if ($test2 == "*") { return true; }
                    if ($test1 == $item) { return true; }                
            }
        }
        return false;
    }
    
    public function matches(array $test_parameters): bool
    {
        if (count($test_parameters) != count($this->parameter)) {
            return false;
        }
        for ($i=0; $i<count($this->parameter); $i++) {
            if (!$this->signature_matches(static::getSignature($test_parameters[$i]), $this->parameter[$i])) {
                return false;
            }    
        }
        return true;
    }    

    static private function getSignatureOfArrayElements(array|\Traversable $param): string
    {
        $current_signature = '';
        foreach ($param as $element) {
            $test = static::getSignature($element);
            if (empty($current_signature)) {
                $current_signature = $test;
                continue;
            } else if (($current_signature == $test) || (($test == 'integer') && ($current_signature == 'float'))) {
                continue;
            } else if (($test == 'float') && ($current_signature == 'integer')) {
                $current_signature = 'float';
            } else {
                $current_signature = 'mixed';
            }
        } 
        return $current_signature;
    }    
    
    static public function getSignature($param): string
    {
        if (is_string($param)) {
            return 'string';
        }
        if (is_int($param)) {
            return 'integer';
        }
        if (is_float($param)) {
            return 'float';
        }  
        if (is_bool($param)) {
            return 'boolean';
        }
        if (is_array($param) || is_a($param, Collection::class)) {
            return 'array of '.static::getSignatureOfArrayElements($param);
        }
        if (is_callable($param)) {
            return 'callback';
        }
        if (is_a($param, RecordProperty::class)) {
            return 'record';
        }
        if (is_a($param, Query::class)) {
            return 'subquery';
        }
        if (is_a($param, Node::class)) {
            return 'node';
        }
        if (is_object($param)) {
            return 'object';
        }
    }    
}  
