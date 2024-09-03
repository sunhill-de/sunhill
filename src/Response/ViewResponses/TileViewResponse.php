<?php

namespace Sunhill\Response\ViewResponses;

use Sunhill\Response\Exceptions\MissingTemplateException;
use Sunhill\Response\AbstractResponse;

class TileViewResponse extends ViewResponse
{
    protected $template = '';

    protected function getViewElements(): array
    {
        return [];
    }
}