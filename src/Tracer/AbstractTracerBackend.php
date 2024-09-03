<?php
/**
 * @file AbstractTracerBackend.php
 * Provides a basic class for tracer backends that do the work for the tracer facade
 *
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-03-24
 * Localization: unknown
 * Documentation: all public
 * Tests: 
 * Coverage: unknown
 * PSR-State: complete
 */

namespace Sunhill\Properties\Tracer;

use Sunhill\Properties\InfoMarket\Market;
use Sunhill\Properties\Tracer\Exceptions\PathAlreadyTracedException;
use Sunhill\Properties\Tracer\Exceptions\PathNotTracedException;
use Sunhill\Properties\InfoMarket\Exceptions\PathNotFoundException;
use Sunhill\Properties\Facades\InfoMarket;
use Sunhill\Properties\Tracer\Exceptions\InvalidRangeException;
use Sunhill\Properties\Properties\Exceptions\InvalidParameterException;

abstract class AbstractTracerBackend
{
    
    protected $data_cache = [];
    
    protected function getData(string $key)
    {
        return InfoMarket::requestData($key);    
    }
    
    protected function putCache(string $key, $data)
    {
        $this->data_cache[$key] = $data;
    }
    
    protected function getCache(string $key)
    {
        if (!isset($this->data_cache[$key])) {
            $data = $this->getData($key);
            $this->putCache($key, $data);
            return $data;
        } else {
            return $this->data_cache[$key];
        }
    }
    
    protected function flushCache()
    {
        $this->data_cache = [];    
    }
    
    /**
     * Tells the tracer backend to execute the tracing 
     * 
     * @param string $path
     */
    abstract protected function doTrace(string $path, \StdClass $data, int $first_stamp);
    
    private function checkPathExists(string $path)
    {
        if (!InfoMarket::pathExists($path)) {
            throw new PathNotFoundException("The path '$path' does not exist.");
        }        
    }
    
    private function checkPathTraced(string $path)
    {
        if (!$this->isTraced($path)) {
            throw new PathNotTracedException("The path '$path' is not traced.");
        }        
    }
    
    private function handleDefaultStamp(int $stamp): int
    {
        if (empty($stamp)) {
            return time();
        }
        return $stamp;
    }
    
    private function getValue(string $path)
    {
        return $this->getCache($path)->value;    
    }
    
    /**
     * Tells the tracer backend to trace the passed path in the future.
     * It raises an exception when this path is already traced
     * 
     * @param string $path The path that should be traced
     * @param int $stamp The timestamp of the first value (default = now())
     */
    public function trace(string $path, int $stamp = 0)
    {
        if ($this->isTraced($path)) {
            throw new PathAlreadyTracedException("The path '$path' is already traced.");
        }
        $this->checkPathExists($path);
        $stamp = $this->handleDefaultStamp($stamp);
        $data = $this->getCache($path);
        
        $this->doTrace($path, $data, $stamp);
    }
    
    /**
     * Tells the tracer backend to untrace the path
     * 
     * @param string $path
     */
    abstract protected function doUntrace(string $path);
    
    /**
     * Tells the tracer backend to untrace the passed path in the future.
     * It raises an exception when this path is not already traced. All history data will be deleted
     *
     * @param string $path
     */
    public function untrace(string $path)
    {
        $this->checkPathTraced($path);
        $this->doUntrace($path);
    }

    abstract protected function getIsTraced(string $path): bool;
    
    /**
     * Returns if the passed path is traced. 
     * 
     * @param string $path
     */
    public function isTraced(string $path)
    {
        $this->checkPathExists($path);
        return $this->getIsTraced($path);
    }
    
    abstract protected function updateTracee(string $tracee, int $stamp);
    
    public function updateTraces(int $timestamp = 0)
    {
        $timestamp = $this->handleDefaultStamp($timestamp);
        
        $tracies = $this->getTracedElements();
        foreach ($tracies as $tracee) {
            $this->updateTracee($tracee, $timestamp);
        }
    }
    
    abstract protected function doGetTracedElements(): array;
    
    public function getTracedElements(): array
    {
        return $this->doGetTracedElements();    
    }
    
    abstract protected function getLastPair(string $path): \StdClass;
    
    public function getLastValue(string $path)
    {
        $this->checkPathTraced($path);
        $pair = $this->getLastPair($path);
        return $pair->value;
    }
    
    public function getLastChange(string $path)
    {
        $this->checkPathTraced($path);
        $pair = $this->getLastPair($path);
        return $pair->stamp;        
    }
    
    abstract protected function doGetValueAt(string $path, int $timestamp);
    
    public function getValueAt(string $path,int $timestamp)
    {
        $this->checkPathTraced($path);
        return $this->doGetValueAt($path, $timestamp);
    }
    
    abstract protected function getFirstPair(string $path): \StdClass;
    
    public function getFirstValue(string $path)
    {
        $this->checkPathTraced($path);
        $pair = $this->getFirstPair($path);
        return $pair->value;        
    }
    
    public function getFirstChange(string $path)
    {
        $this->checkPathTraced($path);
        $pair = $this->getFirstPair($path);
        return $pair->stamp;        
    }
    
    abstract protected function doGetRangeValues(string $path, int $start, int $end): array;
    
    private function adjustRange(string $path, int &$start, int &$end)
    {
        if ($start == 0) {
            $start = $this->getFirstChange($path);
        }
        if ($end == 0) {
            $end = time();
        }        
    }
    
    protected function getRangeRaw(string $path, int &$start, int &$end): array
    {
        $this->adjustRange($path, $start, $end);
        $range = $this->doGetRangeValues($path, $start, $end);
        if ($start < $range[0]->stamp) {
            $element = new \StdClass();
            $element->stamp = $start;
            $element->value = $this->getValueAt($path, $start);
            array_unshift($range, $element);
        }
        if ($end > $range[count($range)-1]->value) {
            $element = new \StdClass();
            $element->stamp = $end;
            $element->value = $this->getValueAt($path, $end);
            array_push($range, $element);            
        }
        
        return $range;
    }
    
    public function getRangeStatistics(string $path, int $start = 0, int $end = 0): \StdClass
    {
        $result = new \StdClass();
        
        $range = $this->getRangeRaw($path, $start, $end);
        if ($start == $end) {
            throw new InvalidRangeException("Begin and end of range are the same.");
        }
        $sum = 0;
        foreach ($range as $element) {
            if (isset($result->min)) {
                if ($element->value < $result->min) {
                    $result->min = $element->value;
                }
            } else {
                $result->min = $element->value;
            }
            if (isset($result->max)) {
                if ($element->value > $result->max) {
                    $result->max = $element->value;
                }
            } else {
                $result->max = $element->value;
            }
            if (isset($last_value)) {
                $sum += $last_value * ($element->stamp - $last_stamp);
            }
            $last_value = $element->value;
            $last_stamp = $element->stamp;
        }
        $result->range = $end - $start;
        $result->avg = $sum / $result->range;
        return $result;
    }
    
    public function getRangeValues(string $path, int $start, int $end, int $step): array
    {
        if ($step <= 0) {
            throw new InvalidParameterException("step must't be 0 or below");
        }
        $this->adjustRange($path, $start, $end); 
        $pointer = $start;
        $result = [];
        
        while ($pointer <= $end) {
            $result[$pointer] = $this->doGetValueAt($path, $pointer);
            $pointer += $step;
        }
        
        return $result;
    }
}