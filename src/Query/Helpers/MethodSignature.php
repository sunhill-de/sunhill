<?php

namespace Sunhill\Query\Helpers;

use Sunhill\Basic\Base;

class MethodSignature extends Base
{
    protected string $name = '';

    protected array $parameter = [];

    protected $action;
    
    public function __construct(string $name)
    {
       $this->name = $name;
    }

    public function addParamter(string $parameter_signature): static
    {
        $this->parameters[] = $parameter_signature;

        return $this;
    }  

    public function addAction($action): static
    {
        $this->action = $action;

        return $this;
    }

    private function signature_matches(string $test1, string $test2): bool
    {
        switch ($test1) {
            default:
              return $test1 == $test2;
        }    
    }
    
    public function matches(array $test_parameters): bool
    {
        if (count($test_parameters) != count($this->parameter)) {
            return false;
        }
        for ($i=0; $i<count($this->parameter); $i++) {
            if (!$this->signature_matches($test_parameters[$i], $this->parameter[$i])) {
                return false;
            }    
        }
        return true;
    }    

    static privat function getSignatureOfArrayElements(array|Traversable $param): string
    {
        $current_signature = '';
        foreach ($param as $element) {
            $test = static::getSignature($element);
            if (empty($current_signature)) {
                $current_signature = $test;
                continue;
            } else if (($current_signature == $test) || (($test == 'integer') && ($current_signature == 'float')) {
                continue;
            } else if (($test == 'float') && ($current_signature == 'integer')) {
                $current_signature = 'float';
            } else {
                $current_signature = 'mixed';
            }
        } 
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
        if (is_array($param)) {
            return 'array of '.static::getSignatureOfArrayElements($param);
        }    
    }    
}  
