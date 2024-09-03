<?php

namespace Sunhill\Framework\Tests\Responses;

use Sunhill\Framework\Response\ViewResponses\ViewResponse;

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