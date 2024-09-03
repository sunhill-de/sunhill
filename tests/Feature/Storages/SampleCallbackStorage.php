<?php

namespace Sunhill\Properties\Tests\Feature\Storages;

use Sunhill\Properties\Storage\CallbackStorage;

class SampleCallbackStorage extends CallbackStorage
{
    
    public function get_sample_string()
    {
        return 'ABC';
    }
    
    protected $sample_integer = 123;
    
    public function get_sample_integer()
    {
        return $this->sample_integer;
    }
    
    public function set_sample_integer($value)
    {
        $this->sample_integer = $value;
    }
    
    protected $sample_array = ['ABC', 'DEF'];
    
    public function get_sample_array($index)
    {
        return $this->sample_array[$index];    
    }
    
    public function set_sample_array($index, $value)
    {
        if (is_null($index)) {
            $this->sample_array[] = $value;   
        } else {
            $this->sample_array[$index] = $value;
        }
    }
}