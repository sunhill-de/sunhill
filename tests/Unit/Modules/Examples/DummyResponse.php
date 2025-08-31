<?php

namespace Sunhill\Tests\Unit\Modules\Examples;

use Sunhill\Exceptions\SunhillUserException;
use Sunhill\Modules\Response\Response;

class DummyResponse extends Response
{
    protected $id = 10;

    protected $optional = 'ABC';

    public $error = false;

    protected function prepareResponse(): string|false
    {
        if ($this->error) {
            throw new SunhillUserException('This is a sample user exception');
        }

        return $this->optional.$this->id;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function set_optional($optional)
    {
        $this->optional = $optional;
    }
}
