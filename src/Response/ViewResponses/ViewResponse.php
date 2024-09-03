<?php

namespace Sunhill\Framework\Response\ViewResponses;

use Sunhill\Framework\Response\Exceptions\MissingTemplateException;
use Sunhill\Framework\Response\AbstractResponse;

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