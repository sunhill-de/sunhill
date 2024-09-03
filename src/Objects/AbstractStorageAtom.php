<?php

namespace Sunhill\Properties\Objects;

use Sunhill\Properties\Objects\Exceptions\StorageAtomTypeNotDefinedException;
use Sunhill\Properties\Objects\Exceptions\IDNotFoundException;
use Sunhill\Properties\Objects\Exceptions\InvalidPrefixCalledException;
use Sunhill\Properties\Objects\Exceptions\InvalidPostfixCalledException;
use Illuminate\Support\Str;

abstract class AbstractStorageAtom
{
    
    const AllowedPostfixes = ['Record','Directory','Tags','Attributes'];
    
    abstract protected function handleRecord(string $storage_id, array $descriptor, $additional1 = null, $additional2 = null);
    abstract protected function handleDirectory(string $storage_id, $additional = null);
    abstract protected function handleTags($additional = null);
    abstract protected function handleAttributes($additional = null);

    public function __call($method, $params)
    {
        if (!Str::startsWith($method, static::$prefix)) {
            throw new InvalidPrefixCalledException("The method '$method' has an invalid prefix.");
        }
        $postfix = substr($method,strlen(static::$prefix));
        if (!in_array($postfix, AbstractStorageAtom::AllowedPostfixes)) {
            throw new InvalidPostfixCalledException("The method '$method' has an invalid postfix.");            
        }
        $method = 'handle'.$postfix;
        return $this->$method(...$params);
    }
}