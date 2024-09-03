<?php

namespace Sunhill\Objects;

class ORMObject extends AbstractPersistantRecord
{
    
    protected static function handleInheritance(): string
    {
        return 'embed';
    }
    
}