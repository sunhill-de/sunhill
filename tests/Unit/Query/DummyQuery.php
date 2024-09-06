<?php

namespace Sunhill\Tests\Unit\Query;

use Sunhill\Query\BasicQuery;
use Illuminate\Support\Collection;

class DummyQuery extends BasicQuery
{
   
    protected function getInfo()
    {
        $result = '';
        
        if ($this->offset) {
            $result .= "offset:".$this->offset;
        }
        if ($this->limit) {
            $result .= "limit:".$this->limit;
        }
        if ($this->order_key) {
            $result .= 'order:'.$this->order_key.'dir:'.$this->order_direction;
        }
        return $result;    
    }
    
    protected function assmebleQuery()
    {
        $result = new \StdClass();
        $result->count = 5;
        $result->element = [
            makeStdClass(['payload'=>$this->getInfo(),'name'=>'name','id'=>1]),
            makeStdClass(['payload'=>'A','name'=>'nameA','id'=>1]),
            makeStdClass(['payload'=>'B','name'=>'nameB','id'=>2]),
            makeStdClass(['payload'=>'C','name'=>'nameC','id'=>3]),
            makeStdClass(['payload'=>'D','name'=>'nameD','id'=>4])
        ];    
        return $result;
    }
    
    /**
     * Returns the count of record that the previously assembled query returns
     *
     * @param unknown $assambled_query
     * @return int
     */
    protected function doGetCount($assambled_query): int
    {
        return $assambled_query->count;
    }
    
    protected function doGet($assembled_query): Collection
    {
        return collect($assembled_query->element);
    }
    
    protected function fieldExists(string $field): bool
    {
        return in_array($field,['payload','name','id']);            
    }
    
    protected function fieldOrderable(string $field): bool
    {
        return in_array($field,['name','id']);
    }
    
    protected function getRecord($key, $element)
    {
        $element->payload = $key.':'.$element->payload;
        return $element;
    }
}