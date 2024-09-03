<?php

namespace Sunhill\Objects;

class Collection extends AbstractPersistantRecord
{
    
    protected static function handleInheritance(): string
    {
        return 'include';
    }
    
}