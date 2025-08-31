<?php

namespace Sunhill\Sunhill\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sunhill\Sunhill\Sunhill
 */
class Sunhill extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Sunhill\Sunhill\Sunhill::class;
    }
}
