<?php

namespace Sunhill\Query\Helpers;

use Sunhill\Basic\Base;

class MethodSignature extends Base
{
    protected string $name = '';

    protected array $params = [];

    public function __construct(string $name)
    {
       $this->name = $name;
    } 
}  
