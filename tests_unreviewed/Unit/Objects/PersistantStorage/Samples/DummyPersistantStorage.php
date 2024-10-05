<?php

namespace Sunhill\Tests\Unit\Objects\PersistantStorage\Samples;

use Sunhill\Objects\AbstractPersistantStorage;
use Sunhill\Objects\AbstractStorageAtom;

class DummyPersistantStorage extends AbstractPersistantStorage
{
    public $atom;
    
    protected function getStorageAtom(string $action): AbstractStorageAtom
    {
        return $this->atom;
    }
    
}