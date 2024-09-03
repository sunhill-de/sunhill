<?php

namespace Sunhill\Tests\Responses;

use Sunhill\Response\ViewResponses\ViewResponse;

class SampleParameterResponse extends ViewResponse
{
    
    protected $id;
    
    public function set_id($id)
    {
        $this->id = $id;
    }
    
    protected $optional = 'ABC';
    
    public function set_optional($optional)
    {
        $this->optional = $optional;
    }
    
    protected function getViewElements(): array
    {
        return ['id'=>$this->id,'optional'=>$this->optional];    
    }
    
    public function setTemplate(string $template)
    {
        $this->template = $template;
    }
}