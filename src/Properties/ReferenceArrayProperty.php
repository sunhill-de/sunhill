<?php

namespace Sunhill\Properties;

class ReferenceArrayProperty extends ArrayProperty
{
    
    protected $refered_records = [];
    
    public function setAllowedProperty(array|string $allowed_property): static
    {
        if (!is_null($this->shadow_element)) {
            $this->shadow_element->setAllowedProperty($allowed_property);
        }
        return $this;
    }
    
    protected function doOffsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $offset = $this->count();
        }
        $this->refered_records[$offset] = $value;
        if (is_a($value,PooledRecordProperty::class)) {
            parent::doOffsetSet($offset, $value->getID());
        } else {
            parent::doOffsetSet($offset, $value);            
        }
    }
    
    /**
     * We received an ID from the storage so we try to load the referenced record
     *
     * @param unknown $id
     */
    protected function tryToLoadRecord($id)
    {
        foreach ($this->shadow_element->getAllowedProperties() as $property) {
            if (method_exists($property, 'IDexists')) {
                $test = new $property();
                if ($test->IDexists($id)) {
                    $test->load($id);
                    return $test;
                }
            }
        }
    }
    
    protected function doOffsetGet(mixed $offset): mixed
    {
        if (!isset($this->refered_records[$offset])) {
            $this->refered_records[$offset] = $this->tryToLoadRecord(parent::doOffsetGet($offset));
        }
        return $this->refered_records[$offset];
    }
    
}