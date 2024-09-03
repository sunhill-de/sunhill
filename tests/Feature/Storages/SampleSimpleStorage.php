<?php

namespace Sunhill\Properties\Tests\Feature\Storages;

use Sunhill\Properties\Storage\SimpleStorage;

class SampleSimpleStorage extends SimpleStorage
{
    
    protected function readValues(): array
    {
        return ['string_key'=>'XYZ','int_key'=>123,'array_key'=>['ABC','DEF']];
    }
    
}