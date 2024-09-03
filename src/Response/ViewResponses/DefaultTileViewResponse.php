<?php

namespace Sunhill\Response\ViewResponses;

use Sunhill\Response\Exceptions\MissingTemplateException;
use Sunhill\Response\AbstractResponse;

class DefaultTileViewResponse extends TileViewResponse
{
    protected $template = '';

    protected function getViewElements(): array
    {
        return [];
    }
}