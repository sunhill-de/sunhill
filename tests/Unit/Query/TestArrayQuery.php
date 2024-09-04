<?php

namespace Sunhill\Tests\Unit\Query;

use Sunhill\Query\ArrayQuery;

class TestArrayQuery extends ArrayQuery
{
    
    protected $allowed_order_keys = ['none','name','value','payload'];
    
    protected function entry($name, $value, $payload)
    {
        $result = new \StdClass();
        $result->name = $name;
        $result->value = $value;
        $result->payload = $payload;
        return $result;
    }
    
    protected function getRawData()
    {
        return [
            $this->entry('ABC',123,'ZZZ'),
            $this->entry('DEF',234,'XXX'),
            $this->entry('GHI',345,'YYY')
        ];
    }
    
}