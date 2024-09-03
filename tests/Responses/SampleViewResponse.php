<?php

namespace Sunhill\Tests\Responses;

use Sunhill\Response\ViewResponses\ViewResponse;

class SampleViewResponse extends ViewResponse
{
    
    protected function getViewElements(): array
    {
        return ['test'=>'abc'];    
    }
    
    public function setTemplate(string $template)
    {
        $this->template = $template;
    }
}