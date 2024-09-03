<?php

namespace Sunhill\Properties\Tests\TestSupport\Storages;

use Sunhill\Properties\Storage\CallbackStorage;

class DummyCallbackStorage extends CallbackStorage
{

     public $readwrite_val = 'DEF';
    
     public $uninitialized_val;
     
     public $arrayitem_val = ['ABC','DEF','GHI'];
     
     protected function get_readonly()
     {
         return 'ABC';
     }
     
     protected function set_readwrite($value)
     {
         $this->readwrite_val = $value;
     }
     
     protected function get_readwrite()
     {
         return $this->readwrite_val;
     }
     
     protected function getcap_restricted(string $capability)
     {
         return $capability.'_cap';
     }
     
     protected function set_uninitialized($value)
     {
         $this->uninitialized_val = $value;
     }
     
     protected function get_uninitialized()
     {
         return $this->uninitialized_val;
     }
     
     protected function getinitialized_uninitialized()
     {
         return !is_null($this->uninitialized_val);
     }
     
     protected function getcount_arrayitem()
     {
         return count($this->arrayitem_val);
     }
     
     protected function get_arrayitem($index)
     {
        return $this->arrayitem_val[$index];
     }
     
     protected function set_arrayitem($index, $value)
     {
        $this->arrayitem_val[$index] = $value;    
     }
     
     protected function getoffsetexists_arrayitem($index): bool
     {
         return isset($this->arrayitem_val[$index]);
     }
}
