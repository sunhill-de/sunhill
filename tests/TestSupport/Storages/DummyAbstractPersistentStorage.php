<?php

namespace Sunhill\Tests\TestSupport\Storages;

use Sunhill\Storage\AbstractPersistentStorage;

class DummyAbstractPersistentStorage extends AbstractPersistentStorage
{
    
    public $commited = false;
    
    public function __construct()
    {
        parent::__construct();
        $this->values = ['str_value'=>'ABC','array_value'=>[11,22,33]];
    }
    
    protected function doCommit()
    {
        $this->commited = true;    
    }
 
    public function pub_structureNeeded()
    {
        return $this->structureNeeded();
    }
    
    public function pub_getModifiedValues(): array
    {
        return $this->getModifiedValues();
    }
    
    protected function handleUnloaded(): void
    {
        
    }
}