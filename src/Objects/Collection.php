<?php

namespace Sunhill\Properties\Objects;

class Collection extends AbstractPersistantRecord
{
    
    protected static function handleInheritance(): string
    {
        return 'include';
    }
    
}