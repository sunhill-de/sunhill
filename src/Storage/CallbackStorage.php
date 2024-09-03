<?php
/**
 * @file SimpleCallbackStorage.php
 * A very simple storage that gets and puts values using callbacks
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-02-11
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: unknown
 * PSR-State: completed
 */

namespace Sunhill\Properties\Storage;

use Sunhill\Properties\Storage\Exceptions\FieldNotAvaiableException;

abstract class CallbackStorage extends AbstractStorage
{
    
    /**
     * Returns the required read capability or null if there is none
     * 
     * @param string $name
     * @return string
     */
    public function getReadCapability(string $name): ?string
    {
        $method = 'getcap_'.$name;
        if (method_exists($this, $method)) {
            return $this->$method('read');
        }
        return null;
    }
    
    /**
     * Returns if the property is readable
     * 
     * @param string $name
     * @return bool
     */
    public function getIsReadable(string $name): bool
    {
        $method = 'get_'.$name;
        return method_exists($this, $method);
    }
    
    private function getMethodName(string $name, string $method, string $message): string
    {
        $method = $method.'_'.$name;
        
        if (!method_exists($this, $method)) {            
            throw new FieldNotAvaiableException(str_replace(':name',$method, $message));
        } 
        return $method;
    }
    
    /**
     * Performs the retrievement of the value
     * 
     * @param string $name
     */
    protected function doGetValue(string $name)
    {
        $method = $this->getMethodName($name,'get',"The field :name doesn't exist or doesn't provide a read method.");
        return $this->$method();
    }
    
    protected function doGetIndexedValue(string $name, mixed $index): mixed
    {
        $method = $this->getMethodName($name,'get',"The field :name doesn't exist or doesn't provide a read method.");
        return $this->$method($index);
    }
    
    protected function doGetElementCount(string $name): int
    {
        $method = $this->getMethodName($name,'getcount',"The field :name doesn't exist or doesn't provide a element count method.");
        return $this->$method();        
    }
    
    /**
     * Returns the required write capability or null if there is none
     * 
     * @param string $name
     * @return string|NULL
     */
    public function getWriteCapability(string $name): ?string
    {
        $method = 'getcap_'.$name;
        if (method_exists($this, $method)) {
            return $this->$method('write');
        }
        return null;
    }
    
    /**
     * Returns if this property is writeable
     * @param string $name
     * @return bool
     */
    public function getIsWriteable(string $name): bool
    {
        $method = 'set_'.$name;
        return method_exists($this, $method);
    }
    
    /**
     * Returns the modify capability or null if there is none
     * 
     * @param string $name
     * @return string|NULL
     */
    public function getModifyCapability(string $name): ?string
    {
        $method = 'getcap_'.$name;
        if (method_exists($this, $method)) {
            return $this->$method('modify');
        }
        return null;
    }
        
    /**
     * Performs the setting of the value
     * 
     * @param string $name
     * @param unknown $value
     */
    protected function doSetValue(string $name, $value)
    {
        $method = $this->getMethodName($name,'set',"The field :name doesn't exist or doesn't provide a write method.");
        return $this->$method($value);
    }

    protected function doSetIndexedValue(string $name, $index, $value)
    {
        $method = $this->getMethodName($name,'set',"The field :name doesn't exist or doesn't provide a write method.");
        return $this->$method($index, $value);        
    }
    
    /**
     * Returns if this storage was modified
     *
     * @return bool
     */
    public function isDirty(): bool
    {
        return false; // Cant be dirty
    }

    protected function doGetIsInitialized(string $name): bool
    {
        $method = 'getinitialized_'.$name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        $method = 'get_'.$name;
        if (method_exists($this, $method)) {
            return true;
        }
        return false;
    }
    
    protected function doGetOffsetExists(string $name, $index): bool
    {
        $method = 'getoffsetexists_'.$name;
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return false;
    }
    
}