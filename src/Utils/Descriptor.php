<?php

/**
 * @file Descriptor.php
 * Provides a class that bundles information for easier access
 * Lang en
 * Reviewstatus: 2020-08-06
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Utils/UtilDescriptorTest.php
 * Coverage: 98.46% (2023-03-14)
 */
namespace Sunhill\Utils;

use Sunhill\Basic\Loggable;

/**
 * A class that bundles informations in a class like style
 *
 * @author Klaus
 *        
 */
class Descriptor extends Loggable implements \Iterator
{

    private $fields = [];

    private $error = false;

    private $pointer = 0;

    protected $autoadd = true;
    
    protected $disable_triggers = false;
    
    public function __construct() 
    {
        $save_autoadd = $this->autoadd;
        $save_triggers = $this->disable_triggers;
        $this->autoadd = true;
        $this->disable_triggers = true;
        $this->setupFields();
        $this->autoadd = $save_autoadd;
        $this->disable_triggers = $save_triggers;
    }
    
    protected function setupFields() 
    {
        
    }
    
    /**
     * Catch all for setting a value
     *
     * @param unknown $name
     * @param unknown $value
     */
    public function __set(string $name, $value)
    {
        $this->checkAutoadd($name,$value);
        if (!isset($this->fields[$name])) {
            $oldvalue = null;   
        } else {
            $oldvalue = $this->fields[$name];
        }
        if ($oldvalue !== $value) {
            if (!$this->checkChangingTrigger($name,$oldvalue,$value)) {
                throw new DescriptorException("Valuechange forbidden by trigger.");
            }
            $this->fields[$name] = $value;
            $this->checkChangedTrigger($name,$oldvalue,$value);            
        }
    }

    private function checkAutoadd(string $name,$value) 
    {
        if (!isset($this->fields[$name]) && !$this->autoadd) {
            throw new DescriptorException("Autoadd forbidden.");
        }        
    }
    
    private function checkChangingTrigger(string $name,$from,$to) 
    {
        if ($this->disable_triggers) {
            return true;
        }
        $method_name = $name.'_changing';
        if (method_exists($this,$method_name)) {
            $diff = new Descriptor();
            $diff->from = $from;
            $diff->to = $to;
            return $this->$method_name($diff);
        }
        return true;
    }
    
    private function checkChangedTrigger(string $name,$from,$to) 
    {
        if ($this->disable_triggers) {
            return true;
        }
        $method_name = $name.'_changed';
        if (method_exists($this,$method_name)) {
            $diff = new Descriptor();
            $diff->from = $from;
            $diff->to = $to;
            $this->$method_name($diff);
        }        
    }
    
    /**
     * Catch all for getting a value
     *
     * @param unknown $name
     * @return mixed|NULL
     */
    public function &__get(string $name)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        } else {
            $this->fields[$name] = new Descriptor();
            return $this->fields[$name];
        }
    }

    /**
     * Checks, if the Descriptor has the field with the name $name
     * @param string $name
     */
    public function isDefined(string $name) 
    {
        return isset($this->fields[$name]);
    }
    
    /**
     * Catch all for method so we can implement set_xxx, get_xxx
     */
    public function &__call(string $name, array $params)
    {
        if (substr($name, 0, 4) == 'get_') {
            $name = substr($name, 4);
            return $this->$name;
        } else if (substr($name, 0, 4) == 'set_') {
            $name = substr($name, 4);
            $this->$name = $params[0];
            return $this;
        }
        throw new DescriptorException("Unknown method '$name'");
    }

    /**
     * Returns true, if the Descriptor is empty
     *
     * @return bool
     */
    public function empty()
    {
        return empty($this->fields);
    }

    /**
     * Returns false, if there was no error otherwise its error message
     *
     * @return boolean|\Manager\Utils\string
     */
    public function hasError()
    {
        return $this->error;
    }

    /**
     * Sets an error message and therefore an error condition
     *
     * @param string $message
     */
    public function setError(string $message)
    {
        $this->error = $message;
    }

    /**
     * Utils for the iterator interface
     * {@inheritDoc}
     * @see Iterator::current()
     */
    public function current(): mixed
    {
        return $this->fields[array_keys($this->fields)[$this->pointer]];
    }

    /**
     * Utils for the iterator interface
     * {@inheritDoc}
     * @see Iterator::key()
     */
    public function key():mixed
    {
        return array_keys($this->fields)[$this->pointer];
    }

    /**
     * Utils for the iterator interface
     * {@inheritDoc}
     * @see Iterator::next()
     */
    public function next(): void
    {
        $this->pointer ++;
    }

    /**
     * Utils for the iterator interface
     * {@inheritDoc}
     * @see Iterator::rewind()
     */
    public function rewind():void
    {
        $this->pointer = 0;
    }

    /**
     * Utils for the iterator interface
     * {@inheritDoc}
     * @see Iterator::valid()
     */
    public function valid():bool
    {
        return (($this->pointer >= 0) && ($this->pointer < count($this->fields)));
    }
    
    /**
     * Assertion that the $key exists
     */
    public function assertHasKey(string $key) 
    {
        return $this->isDefined($key);
    }
    
    /**
     * Assertion that the key exists and is value $value
     */
    public function assertKeyIs(string $key,$value) 
    {
        return $this->isDefined($key) && ($this->$key == $value);
    }
    
    /**
     * Assertion that the key exists, is an array and has the value $test
     */
    public function assertKeyHas(string $key,$test) 
    {
        if (!$this->isDefined($key) || !is_array($this->$key)) {
            return false;
        }
        foreach ($this->$key as $value) {
            if ($value == $test) {
                return true;
            }
        } 
        return false;
    }    
}
