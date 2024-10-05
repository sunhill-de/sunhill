<?php

namespace Sunhill\Response\ViewResponses;

use Sunhill\Response\Exceptions\MissingTemplateException;
use Sunhill\Response\AbstractResponse;

abstract class ViewResponse extends AbstractResponse
{
    protected $template = '';

    protected function prepareResponse()
    {    
        if (empty($this->template)) {
            throw new MissingTemplateException("In the view response '".static::class."' is no template defined.");
        }
        $params = array_merge($this->getViewElements(), $this->getParameters());
        return view($this->template, $params);
    }
    
    abstract protected function getViewElements(): array;
}