<?php
/**
 * @file AbstractSimpleProperty.php
 * Defines a property as base for all properties that are a simple type (not array, not record)
 * Lang de,en
 * Reviewstatus: 2024-02-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: 41.67 % (2024-11-13)
 */

namespace Sunhill\Properties;

use Sunhill\Properties\Exceptions\InvalidValueException;
use Illuminate\Support\Facades\Log;

abstract class AbstractSimpleProperty extends AbstractProperty
{
    
    /**
     * Tries to pass a verbouse error message to the log
     *
     * @param string $message
     */
    protected function error(string $message)
    {
        if (empty($this->owner)) {
            Log::error($message);
        } else {
            Log::error($this->owner->getName().': '.$message);
        }
    }
    

    protected function handleNullValue()
    {
        if (!$this->getNullable()) {
            parent::handleNullValue();
        }
    }
        
    protected function handleUninitialized()
    {
        if ($default = $this->getDefault()) {
            if ($default == DefaultNull::class) {
                $this->setValue(null);
                return null;
            } else {
                $this->setValue($default);
                return $default;
            }
        }
        parent::handleUninitialized();
    }
    
    
}