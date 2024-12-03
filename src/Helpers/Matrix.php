<?php
/**
 * @file Matrix.php
 * A helper class that is used for comparison of two similar lists of liszts
 * Lang en
 * Reviewstatus: 2024-12-02
 * Localization: complete
 * Documentation: complete
 * Tests:
 * Coverage:
 *
 * Wiki:
 */

namespace Sunhill\Helpers;

use Sunhill\Basic\Base;

class Matrix extends Base
{
    
    protected $values;
    
    public function __construct()
    {
        $this->values = new \stdClass();    
    }
    
    protected function doSetValue(\stdClass &$values, array $stack, $value)
    {
        $key = array_shift($stack);
        if (empty($stack)) {
            $values->$key = $value;
        } else {
            if (!isset($values->$key)) {
                $newkey = new \stdClass();
                $values->$key = $newkey;
            }
            $this->doSetValue($values->$key, $stack, $value);
        }
    }
    
    public function setItem($key, $value)
    {
        if (is_string($key)) {
            $this->doSetValue($this->values, [$key], $value);
        } else if (is_array($key)) {
            $this->doSetValue($this->values, $key, $value);            
        }
    }
    
    protected function doGetValue(\stdClass $values, array $stack)
    {
        $key = array_shift($stack);
        if (empty($stack)) {
            return $values->$key;
        } else {
            return $this->doGetValue($values->$key, $stack);
        }
    }
    
    public function getItem($key)
    {
        if (is_string($key)) {
            return $this->doGetValue($this->values, [$key]);
        } else if (is_array($key)) {
            return $this->doGetValue($this->values, $key);
        }        
    }
    
    public function getValues()
    {
        return $this->values;    
    }
    
    private function returnStdClassOrNull($test)
    {
        $convert = (array) $test;
        if (empty($convert)) {
            return null;
        } else {
            return $test;
        }
        
    }
    
    private function doGetNew(\stdClass $original, \stdClass $compare)
    {
        $result = new \stdClass();
        foreach ($original as $key => $value) {
            if (!isset($compare->$key)) {
                $result->$key = $value;
            }
        }
        return $this->returnStdClassOrNull($result);
    }
    
    private function doGetDropped(\stdClass $original, \stdClass $compare)
    {
        $result = new \stdClass();
        foreach ($compare as $key => $value) {
            if (!isset($original->$key)) {
                $result->$key = $value;
            }
        }
        return $this->returnStdClassOrNull($result);
    }
    
    private function doGetChanged(\stdClass $original, \stdClass $compare)
    {
        $result = new \stdClass();
        
        foreach ($original as $key => $value) {
            if (isset($compare->$key)) {
                if (is_a($value,\stdClass::class)) {
                    if ($change = $this->doDiff($value, $compare->$key)) {
                        $result->$key = $change;
                    }
                } else if (($value !== '*') && ($compare->$key !== '*') && ($value !== $compare->$key)) {
                    $result->$key = $original->$key;                    
                }
            }
        }
        
        return $this->returnStdClassOrNull($result);
    }
    
    protected function doDiff(\stdClass $original, \stdClass $compare)
    {
        $result = new \stdClass();
        
        if ($new = $this->doGetNew($original, $compare)) {
            $result->new = $new;    
        }
        if ($dropped = $this->doGetDropped($original, $compare)) {
            $result->dropped = $dropped;
        }
        if ($changed = $this->doGetChanged($original, $compare)) {
            $result->changed = $changed;
        }
        
        return $this->returnStdClassOrNull($result);
    }
    
    public function diff(Matrix $compare)
    {
        return $this->doDiff($this->values, $compare->getValues());
    }
}