<?php

namespace Sunhill\Tests\Responses;

use Sunhill\Response\AbstractResponse;

class SampleAbstractResponse extends AbstractResponse
{
    
    protected function prepareResponse()
    {
        return 'ABC';
    }
}