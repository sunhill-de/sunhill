<?php

namespace Sunhill\Tests\Feature\Storages;

use Sunhill\Storage\SimpleStorage;

class SampleSimpleStorage extends SimpleStorage
{
    
    protected function readValues(): array
    {
        return ['string_key'=>'XYZ','int_key'=>123,'array_key'=>['ABC','DEF']];
    }
    
}