<?php

namespace Sunhill\Helpers;

use Sunhill\Basic\Base;

class DiffCreator extends Base
{
    
    private function traverseGiven(\stdClass $given, \stdClass $new, bool $accept_given_asterik = false, bool $accept_new_asterik = false)
    {
        $result = new \stdClass();
        foreach ($given as $key => $entry) {
            if (!isset($new->$key)) {
                $result->$key = $entry;
            } else {
                if ($this->isTraversable($entry)) {
                    if (($new->$key == '*') && ($accept_new_asterik)) {
                        continue;
                    }
                    $subdiff = $this->traverseGiven($given->$key, $new->$key, $accept_given_asterik, $accept_new_asterik);
                    if (!empty((array)$subdiff)) {
                        $result->$key = $subdiff;
                    }
                } else {
                    if (($entry !== '*') && !(($new->$key == '*') && ($accept_new_asterik)) && ($given->$key !== $new->$key)) {
                        $result->$key = $given->$key;
                    }
                }
            }
        }        
        return $result;
    }
    
    private function traverseNew(\stdClass $given, \stdClass $new, bool $accept_given_asterik = false, bool $accept_new_asterik = false)
    {
        $result = new \stdClass();
        foreach ($new as $key => $entry) {
            if (!isset($given->$key)) {
                $result->$key = $entry;
            } else {
                if ($this->isTraversable($entry)) {
                    if (($given->$key == '*') && ($accept_given_asterik)) {
                        continue;
                    }
                    $subdiff = $this->traverseNew($given->$key, $new->$key, $accept_given_asterik, $accept_new_asterik);
                    if (!empty((array)$subdiff)) {
                        $result->$key = $subdiff;
                    }
                } else {
                    if (($entry !== '*') && !(($given->$key == '*') && ($accept_given_asterik)) && ($given->$key !== $new->$key)) {
                        $result->$key = $new->$key;
                    }
                }
            }
        }
        return $result;
    }
    
    private function getTraversableDiff(\stdClass $given, \stdClass $new, bool $accept_given_asterik = false, bool $accept_new_asterik = false)
    {
        $result = new \stdClass();
        $result->given = $this->traverseGiven($given, $new, $accept_given_asterik, $accept_new_asterik);
        $result->new = $this->traverseNew($given, $new, $accept_given_asterik, $accept_new_asterik);
        return $result;
    }
    
    private function isTraversable($test): bool
    {
        return is_array($test) || is_a($test, \Traversable::class) || is_a($test, \stdClass::class);    
    }
    
    public function getDiff($given, $new, bool $accept_given_asterik = false, bool $accept_new_asterik = false)
    {
        if ($this->isTraversable($given) &&  ($this->isTraversable($new))) {
            return $this->getTraversableDiff($given, $new, $accept_given_asterik, $accept_new_asterik);
        }
        throw new \Exception("Can't get a diff out of the input data");
    }
}