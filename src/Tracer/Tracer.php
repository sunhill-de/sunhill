<?php

namespace Sunhill\Properties\Tracer;

use Sunhill\Properties\InfoMarket\Market;
use Sunhill\Properties\Facades\InfoMarket;
use Sunhill\Properties\Tracer\Exceptions\PathNotTraceableException;

class Tracer
{
    
    const UNTRACABLE_TYPES = ['text'];
    
    protected $market;
    
    public function setMarket(Market $market): self
    {
        $this->market = $market;
        return $this;
    }

    protected $tracer_backend;
    
    public function setBackend($backend)
    {
        if ()    
    }
    
    /**
     * For the cases when not the main market should be traced this function decides wich one to use
     * 
     * @param string $path
     * @return unknown
     */
    protected function getData(string $path)
    {
        if (!empty($this->market)) {
            return $this->market->requestData($path, 'stdclass');
        }
        return InfoMarket::requestData($path, 'stdclass');
    }
    
    public function isTraced(string $path): bool
    {
        
    }
    
    private function checkType($path, $metadata)
    {
        if (in_array($metadata->type, static::UNTRACABLE_TYPES)) {
            throw new PathNotTraceableException("The path '$path' is not tracable (wrong type)");
        }        
    }
    
    public function trace(string $path)
    {
        $metadata = $this->getData($path);
        $this->checkType($path, $metadata);
    }
    
    public function untrace(string $path)
    {
        
    }
    
    public function updateTraces(int $stamp = 0)
    {
        if (!$stamp) {
            $stamp = now();
        }
    }
    
    public function getLastValue(string $path)
    {
        
    }
    
    public function getLastChange(string $path)
    {
        
    }
    
}