<?php

namespace Sunhill\Tests\Unit\Modules\Examples;

use Sunhill\Modules\Response\Response;

class DummyResponse extends Response
{
    
    protected $id = 10;
    
    protected $optional = 'ABC';
    
    protected function prepareResponse(): string|false
    {
        return $this->optional.$this->id;
    }
    
    public function set_id($id)
    {
        $this->id = $id;
    }
    
    public function set_optional($optional)
    {
        $this->optional = $optional;
    }
}