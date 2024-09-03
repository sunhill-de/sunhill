<?php

namespace Sunhill\Framework\Tests\Responses;

use Sunhill\Framework\Response\AbstractResponse;

class SampleAbstractResponse extends AbstractResponse
{
    
    protected function prepareResponse()
    {
        return 'ABC';
    }
}