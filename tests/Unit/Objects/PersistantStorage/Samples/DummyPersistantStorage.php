<?php

namespace Sunhill\Properties\Tests\Unit\Objects\PersistantStorage\Samples;

use Sunhill\Properties\Objects\AbstractPersistantStorage;
use Sunhill\Properties\Objects\AbstractStorageAtom;

class DummyPersistantStorage extends AbstractPersistantStorage
{
    public $atom;
    
    protected function getStorageAtom(string $action): AbstractStorageAtom
    {
        return $this->atom;
    }
    
}