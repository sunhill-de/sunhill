<?php

namespace Sunhill\Framework\Response\ViewResponses;

use Sunhill\Framework\Response\Exceptions\MissingTemplateException;
use Sunhill\Framework\Response\AbstractResponse;

class DefaultTileViewResponse extends TileViewResponse
{
    protected $template = '';

    protected function getViewElements(): array
    {
        return [];
    }
}